---
type: source
title: "LLM Wiki: A Pattern for Building Personal Knowledge Bases Using LLMs"
original_author: "Community Idea Document"
publication_date: "2026-04-02"
ingested_date: "2026-08-31"
source_url: "Local Document"
raw_file: "[[llm-wiki-concept]]"
tags:
  - type/source
  - status/ingested
  - domain/ai
  - domain/pkm
---

# LLM Wiki: A Pattern for Building Personal Knowledge Bases Using LLMs

> **Original Source:** [[llm-wiki-concept]]  
> **Author / Origin:** Community Idea Document (2026)  
> **Ingested:** 2026-08-31

---

## 📌 Executive Summary
Introduces the **LLM Wiki pattern** — a fundamental alternative to ephemeral RAG. Instead of re-deriving knowledge at query time, an LLM incrementally maintains a persistent, compounding markdown wiki. The human acts as curator and explorer in Obsidian, while the LLM acts as the maintainer/programmer who summarizes, cross-references, and logs knowledge updates.

---

## 🔑 Key Takeaways & Core Arguments
- **Compounding vs. Ephemeral (RAG):** Traditional RAG is stateless; it rediscovers context on every query. An LLM Wiki compiles knowledge once into structured markdown and updates it continuously.
- **Human-LLM Division of Labor:** Human is the Curator (sourcing, directing, asking); LLM is the Programmer/Maintainer (bookkeeping, cross-referencing, index updates, contradiction flagging).
- **Three-Layer Architecture:**
  1. `raw/`: Immutable source files.
  2. `wiki/`: LLM-maintained interlinked markdown files.
  3. `AGENTS.md` (Schema): Rules governing LLM ingest, query, and lint behaviors.
- **Zero-Cost Bookkeeping:** Resolves the historical barrier of personal wikis (maintenance fatigue) because LLMs do not get bored maintaining cross-references.

---

## 🌐 Extracted Concepts & Entities
- **Concepts:**
  - [[llm-wiki]] — Core architectural pattern of LLM-maintained personal knowledge bases.
  - [[persistent-compounding-knowledge]] — Knowledge that builds upon itself over time rather than being re-derived.
  - [[rag-vs-wiki]] — Comparative analysis between on-demand chunk retrieval and persistent wiki compilation.
- **Entities:**
  - [[obsidian]] — Markdown knowledge base IDE, graph view visualizer, and Dataview query engine.
  - [[vannevar-bush-memex]] — The 1945 conceptual foundation of associative trails and curated knowledge.

---

## ⚡ Nuances, Contradictions & Revisions
- *Key Shift:* Shifts the bottleneck in personal knowledge management from *note organization* (handled by LLM) to *curation quality and strategic questioning* (handled by Human).

---

## 📝 Raw Highlights & Quotes
> "Obsidian is the IDE; the LLM is the programmer; the wiki is the codebase."

> "The tedious part of maintaining a knowledge base is not the reading or the thinking — it's the bookkeeping... The wiki stays maintained because the cost of maintenance is near zero."

---

## 🔗 Related Wiki Pages
- [[knowledge-compounding-paradigm]]
