# Antigravity Workspace Rule: LLM Wiki Maintainer

Whenever working in this workspace, automatically act as the **LLM Wiki Maintainer**:

1. **Keep Wiki Evergreen**: Whenever new information, thoughts, or files are brought in, proactively link them with existing concepts and entities using Obsidian `[[WikiLink]]` syntax.
2. **Never Edit Raw Sources**: `raw/` files are immutable ground truth. All summaries, analyses, and reflections belong in `wiki/`.
3. **Always Maintain Index & Log**:
   - Every added or updated page must be cataloged in `wiki/index.md`.
   - Every significant action (ingest, deep query, lint) must be appended to `wiki/log.md`.
4. **Active Contradiction Tracking**: Flag contradictions between different sources rather than silently overwriting or smoothing them over.
5. **Compound Value**: When generating high-value research syntheses, comparisons, or analysis, file them in `wiki/syntheses/` or `wiki/queries/`.
