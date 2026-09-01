#!/usr/bin/env node
/**
 * LLM Wiki Maintenance & Health Check CLI Tool (Node.js)
 * Used by LLM Agents and Humans to audit, inspect, and maintain the knowledge graph.
 */

const fs = require('fs');
const path = require('path');

const WIKI_ROOT = path.resolve(__dirname, '..');
const WIKI_DIR = path.join(WIKI_ROOT, 'wiki');
const RAW_DIR = path.join(WIKI_ROOT, 'raw');

const WIKILINK_REGEX = /\[\[([^\]\|]+)(?:\|[^\]]+)?\]\]/g;

function getMarkdownFiles(dir) {
    let results = [];
    if (!fs.existsSync(dir)) return results;
    const list = fs.readdirSync(dir, { withFileTypes: true });
    for (const item of list) {
        const fullPath = path.join(dir, item.name);
        if (item.isDirectory()) {
            results = results.concat(getMarkdownFiles(fullPath));
        } else if (item.name.endsWith('.md')) {
            results.push(fullPath);
        }
    }
    return results;
}

function stripCodeBlocks(content) {
    // Strip triple-backtick code blocks
    let text = content.replace(/```[\s\S]*?```/g, '');
    // Strip single-backtick inline code
    text = text.replace(/`[^`]+`/g, '');
    return text;
}

function extractWikilinks(content) {
    const textWithoutCode = stripCodeBlocks(content);
    const matches = [];
    let match;
    while ((match = WIKILINK_REGEX.exec(textWithoutCode)) !== null) {
        let target = match[1].trim().replace(/\\/g, '/');
        const parts = target.split('/');
        let slug = parts[parts.length - 1];
        if (slug.endsWith('.md')) slug = slug.slice(0, -3);
        matches.push(slug.toLowerCase());
    }
    return matches;
}


function buildGraph() {
    const files = getMarkdownFiles(WIKI_DIR);
    const pageNames = new Map(); // lowercase stem -> relative path
    const linksOut = new Map();
    const linksIn = new Map();

    for (const f of files) {
        const stem = path.basename(f, '.md').toLowerCase();
        pageNames.set(stem, path.relative(WIKI_ROOT, f));
        linksOut.set(stem, []);
        linksIn.set(stem, []);
    }

    for (const f of files) {
        const stem = path.basename(f, '.md').toLowerCase();
        try {
            const content = fs.readFileSync(f, 'utf8');
            const targets = extractWikilinks(content);
            linksOut.set(stem, targets);
            for (const t of targets) {
                if (!linksIn.has(t)) {
                    linksIn.set(t, []);
                }
                linksIn.get(t).push(stem);
            }
        } catch (e) {
            // Ignore read errors
        }
    }

    return { files, pageNames, linksOut, linksIn };
}

function cmdStatus() {
    const { files, pageNames, linksOut } = buildGraph();
    let totalLinks = 0;
    for (const targets of linksOut.values()) {
        totalLinks += targets.length;
    }

    const countCategory = (sub) => {
        const p = path.join(WIKI_DIR, sub);
        return fs.existsSync(p) ? getMarkdownFiles(p).length : 0;
    };

    let rawCount = 0;
    if (fs.existsSync(RAW_DIR)) {
        rawCount = fs.readdirSync(RAW_DIR).filter(f => f !== '.gitkeep' && !fs.statSync(path.join(RAW_DIR, f)).isDirectory()).length;
    }

    console.log('='.repeat(55));
    console.log('📊 LLM WIKI KNOWLEDGE BASE METRICS');
    console.log('='.repeat(55));
    console.log(`📁 Raw Sources Ingested:   ${rawCount}`);
    console.log(`📑 Wiki Source Summaries:  ${countCategory('sources')}`);
    console.log(`💡 Core Concepts:           ${countCategory('concepts')}`);
    console.log(`🏢 Entities (People/Tools): ${countCategory('entities')}`);
    console.log(`🔬 Syntheses & Theses:      ${countCategory('syntheses')}`);
    console.log(`💬 Stored Query Answers:    ${countCategory('queries')}`);
    console.log(`🌐 Total Wiki Pages:        ${files.length}`);
    console.log(`🔗 Total WikiLinks:         ${totalLinks}`);
    console.log('='.repeat(55));
}

function cmdLint() {
    const { files, pageNames, linksOut, linksIn } = buildGraph();
    const indexPath = path.join(WIKI_DIR, 'index.md');
    const indexContent = fs.existsSync(indexPath) ? fs.readFileSync(indexPath, 'utf8').toLowerCase() : '';

    const brokenLinks = [];
    const orphans = [];
    const missingFromIndex = [];

    for (const [stem, relPath] of pageNames.entries()) {
        if (stem === 'index' || stem === 'log') continue;

        const inbound = (linksIn.get(stem) || []).filter(s => s !== stem);
        if (inbound.length === 0 && !indexContent.includes(stem)) {
            orphans.push(relPath);
        }

        if (!indexContent.includes(stem)) {
            missingFromIndex.push(relPath);
        }

        const outTargets = linksOut.get(stem) || [];
        for (const target of outTargets) {
            const rawCandidate = path.join(RAW_DIR, target + '.md');
            if (!pageNames.has(target) && !fs.existsSync(rawCandidate)) {
                brokenLinks.push({ from: relPath, to: target });
            }
        }
    }

    console.log('='.repeat(55));
    console.log('🩺 LLM WIKI HEALTH & INTEGRITY AUDIT');
    console.log('='.repeat(55));

    let hasIssues = false;

    if (brokenLinks.length > 0) {
        hasIssues = true;
        console.log(`\n❌ Broken WikiLinks (${brokenLinks.length} found):`);
        brokenLinks.forEach(b => console.log(`   • ${b.from} -> [[${b.to}]] (target page missing)`));
    } else {
        console.log('✅ No broken WikiLinks found.');
    }

    if (orphans.length > 0) {
        hasIssues = true;
        console.log(`\n⚠️ Orphan Pages (${orphans.length} found):`);
        orphans.forEach(o => console.log(`   • ${o}`));
    } else {
        console.log('✅ No orphan pages found.');
    }

    if (missingFromIndex.length > 0) {
        hasIssues = true;
        console.log(`\n📋 Pages Missing from index.md (${missingFromIndex.length}):`);
        missingFromIndex.forEach(m => console.log(`   • ${m}`));
    } else {
        console.log('✅ All pages are cataloged in index.md.');
    }

    console.log('\n' + '='.repeat(55));
    if (!hasIssues) {
        console.log('🎉 Wiki knowledge graph is 100% healthy and fully connected!');
    } else {
        console.log('💡 Tip: Ask your AI agent to resolve missing links or index cataloging.');
    }
}

function cmdSearch(query) {
    if (!query) {
        console.error('Please provide a search term: node tools/wiki_tools.js search <term>');
        return;
    }
    const files = getMarkdownFiles(WIKI_DIR);
    const regex = new RegExp(query, 'gi');
    let totalMatches = 0;

    console.log(`🔍 Searching Wiki for: "${query}"\n`);

    for (const f of files) {
        const content = fs.readFileSync(f, 'utf8');
        const lines = content.split(/\r?\n/);
        const matchedLines = [];

        lines.forEach((line, idx) => {
            if (regex.test(line)) {
                matchedLines.push({ lineNo: idx + 1, text: line.trim() });
            }
        });

        if (matchedLines.length > 0) {
            totalMatches++;
            console.log(`📄 ${path.relative(WIKI_ROOT, f)}:`);
            matchedLines.slice(0, 3).forEach(m => {
                console.log(`   L${m.lineNo}: ${m.text}`);
            });
            if (matchedLines.length > 3) {
                console.log(`   ... and ${matchedLines.length - 3} more match(es)`);
            }
            console.log();
        }
    }

    console.log(`Found matches in ${totalMatches} file(s).`);
}

const args = process.argv.slice(2);
const command = args[0] || 'status';

if (command === 'lint') {
    cmdLint();
} else if (command === 'search') {
    cmdSearch(args[1]);
} else if (command === 'status') {
    cmdStatus();
} else {
    console.log(`Unknown command: ${command}`);
    console.log('Usage: node tools/wiki_tools.js [status | lint | search <query>]');
}
