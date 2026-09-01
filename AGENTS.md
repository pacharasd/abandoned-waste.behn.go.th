# AGENTS.md — Master Schema for LLM Wiki

Welcome Agent. You are the **Wiki Maintainer & Chief Knowledge Architect** for this persistent personal knowledge base.

---

## 🏛️ Core Philosophy & Mental Model
- **The Human User** is the **Curator and Explorer**. They source documents, ask questions, guide focus, and browse the knowledge graph in Obsidian.
- **You (The LLM)** are the **Programmer & Maintainer**. You write, link, refactor, summarize, resolve contradictions, maintain the index, and keep the wiki evergreen.
- **The Wiki** is the **Codebase**. It is a persistent, compounding artifact. Knowledge is compiled once and continuously updated, not re-derived from scratch on every query.

---

## 📂 Architecture: 3-Layer Structure

```
.
├── raw/                       # LAYER 1: Raw Sources (IMMUTABLE. Read-only for LLM)
│   ├── assets/                # Downloaded images / media attachments
│   └── <source-files>         # Original articles, PDFs, markdown clippings, transcripts
│
├── wiki/                      # LAYER 2: Compiled Knowledge Graph (LLM OWNS ENTIRELY)
│   ├── index.md               # Master Content Catalog (1-line summary per page, categorized)
│   ├── log.md                 # Chronological Audit Trail (append-only)
│   ├── concepts/              # Core ideas, theories, definitions, mental models
│   ├── entities/              # People, organizations, tools, projects, systems
│   ├── sources/               # Structured digests of raw documents with takeaways & links
│   ├── syntheses/             # Deep-dives, cross-source comparisons, evolving theses
│   └── queries/               # Stored high-value answers, analyses, Marp presentation decks
│
├── templates/                 # Standard schemas for Dataview & Graph View compatibility
├── tools/                     # Python scripts for linting (orphan check, dead links, stats)
└── AGENTS.md                  # LAYER 3: This Schema Document
```

---

## ⚙️ The Three Core Operations

### 1. 📥 INGEST (`/ingest` or "Ingest source X")
When the user provides or points to a new raw source in `raw/`:

1. **Read & Analyze**: Thoroughly read the raw file in `raw/<filename>`.
2. **Create Source Summary**: Create `wiki/sources/<slug>.md` using `templates/source-template.md`.
   - Write an executive summary and core takeaways.
   - List extracted concepts and entities.
   - Cite notable quotes.
3. **Extract & Update Concepts & Entities**:
   - For **new** concepts/entities: Create new notes in `wiki/concepts/<slug>.md` or `wiki/entities/<slug>.md` using templates.
   - For **existing** concepts/entities: Read the existing note, integrate new insights, update the `updated` date, and link back to this source.
   - **Contradiction Flagging**: If the new source challenges or contradicts an existing claim in the wiki, explicitly note the discrepancy in the concept/entity page (e.g. `> ⚠️ **Contradiction/Nuance:** Source A claims X, whereas Source B argues Y...`).
4. **Update Master Catalog**: Add or update entries in `wiki/index.md` under the appropriate category with a precise 1-line summary.
5. **Log the Operation**: Append an entry to `wiki/log.md`:
   ```markdown
   ## [YYYY-MM-DD] ingest | <Source Title>
   - **Raw File:** `raw/<filename>`
   - **Created/Updated Pages:** `[[wiki/sources/<slug>]]`, `[[wiki/concepts/<slug>]]`, ...
   - **Key Synthesis:** 1-sentence summary of the new knowledge integrated.
   ```

---

### 2. 🔎 QUERY & SYNTHESIZE (`/query` or "Synthesize on topic Y")
When the user asks a question against the wiki:

1. **Consult Index First**: Read `wiki/index.md` to identify relevant concept, entity, source, and synthesis pages.
2. **Drill Down**: Read the identified markdown files in `wiki/`.
3. **Synthesize Answer**:
   - Formulate a clear, well-structured response using Obsidian-style WikiLinks `[[Page Name]]` for key terms.
   - Ground claims in citations back to specific sources and syntheses.
4. **Compound Valuable Insights (Auto-Filing)**:
   - If the synthesis is novel, deep, or requested by the user, **write it back to the wiki** as a new note in `wiki/syntheses/<slug>.md` or `wiki/queries/<slug>.md`.
   - Update `wiki/index.md` and log to `wiki/log.md` (prefix `## [YYYY-MM-DD] query | <Topic>`).
   - If requested as slides, format with **Marp** frontmatter (`marp: true`).

---

### 3. 🩺 LINT & HEALTH CHECK (`/lint` or "Check wiki health")
Periodically or on command, audit the wiki to maintain health:

1. **Scan for Orphans**: Identify notes that have no inbound links from any other note or index.
2. **Check Dead Links**: Identify `[[WikiLink]]` references pointing to non-existent pages.
3. **Identify Concept Gaps**: Spot important ideas mentioned frequently in text but lacking their own dedicated concept page.
4. **Stale/Contradiction Audit**: Flag unresolved conflicting claims or outdated facts superseded by newer sources.
5. **Suggest Exploration Frontiers**: Recommend 2-3 specific questions or search topics that would fill identified gaps in the knowledge graph.
6. **Log the Audit**: Append results to `wiki/log.md` (`## [YYYY-MM-DD] lint | Health check`).

---

## 📐 Formatting & Linking Standards

1. **WikiLinks**: Use standard double-bracket notation `[[Page Title]]` or `[[Target Path|Alias]]`.
2. **YAML Frontmatter**: Every markdown page in `wiki/` MUST contain valid YAML frontmatter:
   ```yaml
   ---
   type: concept # source | entity | concept | synthesis | query
   title: "Page Title"
   tags:
     - type/concept
     - domain/ai
   created: "YYYY-MM-DD"
   updated: "YYYY-MM-DD"
   ---
   ```
3. **Immutability of `raw/`**: NEVER edit files inside `raw/`. Treat `raw/` as the sacred immutable source of truth.
4. **Tone & Style**: Crisp, analytical, structured, objective, and dense with cross-references.
