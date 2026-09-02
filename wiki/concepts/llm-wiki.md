---
type: concept
title: "LLM Wiki"
aliases:
  - "Persistent Knowledge Base Pattern"
  - "LLM-maintained Wiki"
created: "2026-08-31"
updated: "2026-08-31"
tags:
  - type/concept
  - domain/ai
  - domain/pkm
---

# LLM Wiki

## 💡 Overview & Definition
An **LLM Wiki** is a persistent, compounding personal knowledge base structured as an interlinked collection of Markdown files maintained autonomously by an LLM agent. 

Unlike traditional retrieval-augmented generation (RAG) which retrieves disjointed raw chunks on every single prompt, an LLM Wiki continuously compiles raw sources into an evolving knowledge graph (concept pages, entity profiles, cross-source syntheses, index, and audit logs).

---

## 🧩 Core Principles & Dynamics
1. **Separation of Layers:**
   - **Raw Sources (`raw/`):** Immutable ground truth.
   - **Persistent Wiki (`wiki/`):** Dynamic, LLM-maintained knowledge graph.
   - **Schema / Rules (`AGENTS.md`):** Instructions enforcing discipline, linking standards, and workflows.
2. **Asymmetric Division of Labor:**
   - **Human:** Curates sources, directs inquiry, explores graph in [[obsidian]], asks strategic questions.
   - **LLM:** Handles bookkeeping, note creation, cross-linking (`[[WikiLinks]]`), contradiction flagging, and catalog indexing.
3. **Compounding Value:** Answers to deep research queries can be filed back into the wiki (`wiki/queries/` or `wiki/syntheses/`), making past research permanently accessible.

---

## ⚖️ Trade-offs, Contrasts & Comparisons
- **Vs. Ephemeral RAG:** See [[rag-vs-wiki]]. RAG requires re-synthesizing context on every query with no cumulative memory. LLM Wiki pre-synthesizes and cross-references continuously.
- **Advantages:** Zero human maintenance fatigue; deep cross-references; explicit contradiction tracking; works locally in plain text with Git version control.
- **Limitations:** Requires LLM tool access (file system editing); scale requires disciplined index catalogs or local search tools (e.g. `qmd`).

---

## 🧬 Evolution & Synthesis Notes
- **2026-08-31:** Initial formulation established via [[llm-wiki-concept]].

---

## 📚 Grounding Sources
- [[llm-wiki-concept]]

---

## 🔗 Related Entities & Syntheses
- [[obsidian]]
- [[vannevar-bush-memex]]
- [[entities/essential-commands|Essential Commands]]
- [[persistent-compounding-knowledge]]
- [[knowledge-compounding-paradigm]]
