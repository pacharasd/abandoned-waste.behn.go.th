# CLAUDE.md — LLM Wiki Instructions for Claude Code

This repository is an **LLM Wiki** knowledge base.
Follow all specifications, operations (Ingest, Query, Lint), directory rules, and formatting standards defined in [AGENTS.md](file:///c:/xampp/htdocs/%E0%B8%82%E0%B8%A2%E0%B8%B0%E0%B9%84%E0%B8%A3%E0%B9%89%E0%B8%9A%E0%B9%89%E0%B8%B2%E0%B8%99/AGENTS.md).

### Core Directives:
- **Ingest (`/ingest`)**: Parse raw file, create source digest, update entity/concept graph, resolve contradictions, update `wiki/index.md`, log to `wiki/log.md`.
- **Query (`/query`)**: Read `wiki/index.md`, drill into relevant pages, synthesize with citations/WikiLinks, optionally compound result to `wiki/syntheses/`.
- **Lint (`/lint`)**: Run `python tools/wiki_tools.py lint` and provide actionable knowledge graph expansion suggestions.
