---
type: concept
title: "RAG vs. LLM Wiki"
aliases:
  - "Retrieval vs Compilation Paradigm"
created: "2026-08-31"
updated: "2026-08-31"
tags:
  - type/concept
  - domain/ai-architecture
---

# RAG vs. LLM Wiki

## 💡 Overview & Definition
The architectural and conceptual contrast between standard Retrieval-Augmented Generation (RAG) and the [[llm-wiki]] persistent knowledge compilation paradigm.

---

## ⚖️ Comparative Matrix

| Characteristic | Traditional RAG / NotebookLM | Persistent LLM Wiki |
| :--- | :--- | :--- |
| **Knowledge State** | Ephemeral (rediscovered per prompt) | Persistent & Compiled (`wiki/`) |
| **Multi-Source Synthesis** | Requires pulling & merging N chunks dynamically | Pre-synthesized in entity/concept notes |
| **Contradiction Handling** | Often causes hallucinations or blended confusion | Explicitly flagged and documented |
| **Human Interface** | Chat box window | [[obsidian]] IDE + Graph View + Dataview |
| **Portability / Ownership** | Vendor-locked vector DBs / proprietary silos | Local Plaintext Markdown + Git |
| **Compounding Utility** | Low (queries vanish in chat history) | High (query answers filed back into wiki) |

---

## 🧩 Core Principles & Dynamics
- In standard RAG, the computational burden and cognitive heavy lifting happen at **Query Time**.
- In an LLM Wiki, knowledge consolidation is shifted to **Ingest Time**, enabling instantaneous navigation through `index.md` and direct concept linkages.

---

## 📚 Grounding Sources
- [[llm-wiki-concept]]

---

## 🔗 Related Concepts & Entities
- [[llm-wiki]]
- [[persistent-compounding-knowledge]]
- [[obsidian]]
- [[knowledge-compounding-paradigm]]
