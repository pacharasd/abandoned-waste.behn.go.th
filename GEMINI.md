# GEMINI.md — LLM Wiki Instructions for Gemini / Antigravity

This repository is an **LLM Wiki** knowledge base.
Follow all specifications, operations (Ingest, Query, Lint), directory rules, and formatting standards defined in [AGENTS.md](file:///c:/xampp/htdocs/%E0%B8%82%E0%B8%A2%E0%B8%B0%E0%B9%84%E0%B8%A3%E0%B9%89%E0%B8%9A%E0%B9%89%E0%B8%B2%E0%B8%99/AGENTS.md).

### Quick Shortcuts:
- **Ingest**: Read source in `raw/`, create `wiki/sources/<slug>.md`, update concepts/entities, update `wiki/index.md`, log to `wiki/log.md`.
- **Query**: Read `wiki/index.md`, synthesize answer with `[[WikiLinks]]`, write back high-value discoveries to `wiki/syntheses/` or `wiki/queries/`.
- **Lint**: Run health audit using `tools/wiki_tools.py lint` or analyze graph gaps, orphans, and broken links.
