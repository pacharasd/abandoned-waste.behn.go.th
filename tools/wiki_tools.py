#!/usr/bin/env python3
"""
LLM Wiki Maintenance & Health Check CLI Tool
Used by LLM Agents and Humans to audit, inspect, and maintain the knowledge graph.
"""

import os
import re
import sys
import argparse
from pathlib import Path

WIKI_ROOT = Path(__file__).resolve().parent.parent
WIKI_DIR = WIKI_ROOT / "wiki"
RAW_DIR = WIKI_ROOT / "raw"

WIKILINK_PATTERN = re.compile(r'\[\[([^\]\|]+)(?:\|[^\]]+)?\]\]')

def get_all_wiki_files():
    """Return all markdown files in wiki directory."""
    if not WIKI_DIR.exists():
        return []
    return list(WIKI_DIR.rglob("*.md"))

def strip_code_blocks(content):
    # Strip triple-backtick fenced code
    text = re.sub(r'```[\s\S]*?```', '', content)
    # Strip single-backtick inline code
    text = re.sub(r'`[^`]+`', '', text)
    return text

def extract_wikilinks(content):
    """Extract all target link names from text content."""
    text_clean = strip_code_blocks(content)
    matches = WIKILINK_PATTERN.findall(text_clean)
    cleaned = []
    for m in matches:
        target = m.strip().replace('\\', '/').split('/')[-1]
        if target.endswith('.md'):
            target = target[:-3]
        cleaned.append(target.lower())
    return cleaned


def build_graph():
    """Build directed link graph of the wiki."""
    files = get_all_wiki_files()
    page_names = {} # lowercase_stem -> Path
    links_out = {} # lowercase_stem -> list of target_slugs
    links_in = {}  # lowercase_stem -> list of source_slugs

    for f in files:
        stem = f.stem.lower()
        page_names[stem] = f
        links_out[stem] = []
        links_in[stem] = []

    for f in files:
        stem = f.stem.lower()
        try:
            content = f.read_text(encoding="utf-8")
        except Exception:
            continue
        targets = extract_wikilinks(content)
        links_out[stem] = targets
        for t in targets:
            if t in links_in:
                links_in[t].append(stem)
            else:
                links_in[t] = [stem]

    return files, page_names, links_out, links_in

def cmd_status():
    files, page_names, links_out, links_in = build_graph()
    raw_files = list(RAW_DIR.glob("*.*")) if RAW_DIR.exists() else []

    counts = {
        "sources": len(list((WIKI_DIR / "sources").glob("*.md"))),
        "concepts": len(list((WIKI_DIR / "concepts").glob("*.md"))),
        "entities": len(list((WIKI_DIR / "entities").glob("*.md"))),
        "syntheses": len(list((WIKI_DIR / "syntheses").glob("*.md"))),
        "queries": len(list((WIKI_DIR / "queries").glob("*.md"))),
        "raw_sources": len([f for f in raw_files if f.name != ".gitkeep" and not f.is_dir()]),
    }
    total_links = sum(len(targets) for targets in links_out.values())

    print("=" * 50)
    print("📊 LLM WIKI KNOWLEDGE BASE STATUS")
    print("=" * 50)
    print(f"📁 Raw Sources Ingested: {counts['raw_sources']}")
    print(f"📑 Wiki Source Summaries: {counts['sources']}")
    print(f"💡 Core Concepts:         {counts['concepts']}")
    print(f"🏢 Entities (People/Org): {counts['entities']}")
    print(f"🔬 Syntheses & Theses:    {counts['syntheses']}")
    print(f"💬 Stored Query Answers:  {counts['queries']}")
    print(f"🌐 Total Wiki Pages:      {len(files)}")
    print(f"🔗 Total WikiLinks:       {total_links}")
    print("=" * 50)

def cmd_lint():
    files, page_names, links_out, links_in = build_graph()
    index_file = WIKI_DIR / "index.md"
    index_content = index_file.read_text(encoding="utf-8").lower() if index_file.exists() else ""

    broken_links = []
    orphans = []
    missing_from_index = []

    for stem, f in page_names.items():
        # Skip index.md and log.md from orphan check
        if stem in ("index", "log"):
            continue

        # Check orphan (no inbound links and not in index)
        inbound = [s for s in links_in.get(stem, []) if s != stem]
        if not inbound and stem not in index_content:
            orphans.append(f.relative_to(WIKI_ROOT))

        # Check if missing from index.md
        if stem not in index_content:
            missing_from_index.append(f.relative_to(WIKI_ROOT))

        # Check broken links
        for target in links_out.get(stem, []):
            if target not in page_names and not (RAW_DIR / f"{target}").exists():
                broken_links.append((f.relative_to(WIKI_ROOT), target))

    print("=" * 50)
    print("🩺 LLM WIKI HEALTH AUDIT & LINT")
    print("=" * 50)

    has_issues = False

    if broken_links:
        has_issues = True
        print(f"\n❌ Broken WikiLinks ({len(broken_links)} found):")
        for src, tgt in broken_links:
            print(f"  • {src} -> [[{tgt}]] (target does not exist)")
    else:
        print("✅ No broken WikiLinks found.")

    if orphans:
        has_issues = True
        print(f"\n⚠️ Orphan Pages ({len(orphans)} found - no inbound links):")
        for p in orphans:
            print(f"  • {p}")
    else:
        print("✅ No orphan pages found.")

    if missing_from_index:
        has_issues = True
        print(f"\n📋 Pages Missing from index.md ({len(missing_from_index)}):")
        for p in missing_from_index:
            print(f"  • {p}")
    else:
        print("✅ All pages are registered in index.md.")

    print("\n" + "=" * 50)
    if not has_issues:
        print("🎉 Wiki is perfectly linked and healthy!")
    else:
        print("💡 Recommendation: Ask your LLM Agent to resolve the issues above.")

def cmd_search(query):
    files = get_all_wiki_files()
    pattern = re.compile(re.escape(query), re.IGNORECASE)
    matches_found = 0

    print(f"🔍 Searching Wiki for: '{query}'\n")
    for f in files:
        try:
            lines = f.read_text(encoding="utf-8").splitlines()
        except Exception:
            continue
        matching_lines = [(i + 1, line) for i, line in enumerate(lines) if pattern.search(line)]
        if matching_lines:
            matches_found += 1
            print(f"📄 {f.relative_to(WIKI_ROOT)}:")
            for line_no, text in matching_lines[:3]:
                print(f"   L{line_no}: {text.strip()}")
            if len(matching_lines) > 3:
                print(f"   ... and {len(matching_lines) - 3} more match(es)")
            print()

    print(f"Found matches in {matches_found} file(s).")

def main():
    parser = argparse.ArgumentParser(description="LLM Wiki Maintenance CLI")
    subparsers = parser.add_subparsers(dest="command", help="Available commands")

    subparsers.add_parser("status", help="Display knowledge base metrics and node counts")
    subparsers.add_parser("lint", help="Audit wiki for broken links, orphans, and index gaps")
    
    search_parser = subparsers.add_parser("search", help="Search wiki content for keywords")
    search_parser.add_argument("query", type=str, help="Search query or keyword")

    args = parser.parse_args()

    if args.command == "status":
        cmd_status()
    elif args.command == "lint":
        cmd_lint()
    elif args.command == "search":
        cmd_search(args.query)
    else:
        cmd_status()

if __name__ == "__main__":
    main()
