# Wiki Activity Log

This is an append-only, chronological audit log tracking all major LLM Wiki operations (Ingest, Query, Lint).

---

## [2026-08-31] ingest | LLM Wiki: A Pattern for Building Personal Knowledge Bases Using LLMs
- **Operation:** Ingestion of foundational concept document.
- **Raw Source:** `raw/llm-wiki-concept.md`
- **Created Pages:**
  - `[[wiki/sources/llm-wiki-concept]]`
  - `[[wiki/concepts/llm-wiki]]`
  - `[[wiki/concepts/persistent-compounding-knowledge]]`
  - `[[wiki/concepts/rag-vs-wiki]]`
  - `[[wiki/entities/obsidian]]`
  - `[[wiki/entities/vannevar-bush-memex]]`
  - `[[wiki/syntheses/knowledge-compounding-paradigm]]`
- **Catalog Update:** Registered all 7 nodes into `[[wiki/index]]`.
- **Synthesis:** Bootstrapped the foundational principles of persistent compiled knowledge graphs versus ephemeral RAG.

---

## [2026-08-31] query | Compounding vs Ephemeral Knowledge Architectures
- **Operation:** In-depth research query synthesis filed into permanent records.
- **Created Page:** `[[wiki/queries/example-research-query]]`
- **Catalog Update:** Registered query record in `[[wiki/index]]`.

---

## [2026-08-31] lint | Initial System Bootstrap Audit
- **Operation:** Structure and graph integrity check.
- **Result:** Graph healthy; 0 broken links, 0 orphaned notes.

---

## [2026-09-02] ingest | Essential Project & Wiki Commands
- **Operation:** Ingestion and consolidation of frequently used operational, testing, server, Docker, and wiki maintenance commands.
- **Created Pages:**
  - `[[wiki/entities/essential-commands]]`
- **Catalog Update:** Registered node into `[[wiki/index]]` and linked in `[[wiki/concepts/llm-wiki]]`.
- **Key Synthesis:** Compiled a comprehensive quick-reference entity covering database migration (`migrate.php`), system verification test suite (`verify_system.php`), local server and XAMPP setups, Docker workflows, and LLM Wiki maintenance CLI tools.

---

## [2026-09-02] ingest | Security Architecture & Hardening Standards
- **Operation:** Ingestion and formal compilation of the end-to-end full-stack security framework and hardening guidelines.
- **Created Pages:**
  - `[[wiki/concepts/security-architecture]]`
- **Catalog Update:** Registered concept node into `[[wiki/index]]`.
- **Key Synthesis:** Codified the Defense-in-Depth security architecture spanning input validation (`Validator`), rate limiting and anti-scraping (`RateLimiter`), PDPA data privacy, Content-Security-Policy (CSP), and safe DOM/Leaflet rendering.

---

## [2026-09-02] refactor | Strict Nonce-based CSP Hardening & Zero-Inline-Handlers
- **Operation:** Mozilla Observatory CSP audit compliance hardening.
- **Updated Pages:**
  - `[[wiki/concepts/security-architecture]]`
- **Key Synthesis:** Eliminated `'unsafe-inline'` and `data:` from `script-src`, instituted per-request cryptographic nonces (`App\Core\CSP`), restricted `object-src 'none'`, and refactored all inline `onclick` attributes into declarative data-attributes in `app-security.js`.

---

## [2026-09-02] refactor | HTTP Strict Transport Security (HSTS) Hardening
- **Operation:** Mozilla Observatory TLS/HSTS audit compliance hardening.
- **Updated Pages:**
  - `[[wiki/concepts/security-architecture]]`
- **Key Synthesis:** Implemented `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload` across Apache (`public/.htaccess`) and PHP (`public/index.php`) satisfying hstspreload.org requirements.

---

## [2026-09-02] refactor | Subresource Integrity (SRI) Protection & Pinned CDN Scripts
- **Operation:** Mozilla Observatory SRI audit compliance hardening.
- **Updated Pages:**
  - `[[wiki/concepts/security-architecture]]`
- **Key Synthesis:** Pinned external CDN assets (Tailwind CSS v3.4.16, Leaflet v1.9.4, Lucide Icons v0.468.0, Chart.js v4.4.7, html2canvas v1.4.1) and secured each with SHA-384 cryptographic hashes (`integrity="sha384-..."`) and `crossorigin="anonymous"`.
