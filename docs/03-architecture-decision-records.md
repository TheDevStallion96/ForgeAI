# 03. Architecture Decision Records (ADR Index & Specifications)

## Executive Summary

This document records the binding architectural decisions made for **ForgeAI**.

Every decision evaluates context, options considered, decision rationale, trade-offs, and consequences to ensure long-term system maintainability and architectural clarity.

---

## 🏛️ ADR Master Index

| ADR ID | Decision Title | Status | Date | Primary Impact Area |
|---|---|---|---|---|
| [ADR-0001](#adr-0001-core-tech-stack---laravel-13-inertia-v3-vue-3-tailwind-v4) | Core Tech Stack: Laravel 13, Inertia v3, Vue 3, Tailwind v4 | **Accepted** | 2026-07-24 | Full Platform Tech Stack |
| [ADR-0002](#adr-0002-standardizing-ai-engine-on-first-party-laravelai-sdk) | Standardizing AI Engine on First-Party `laravel/ai` SDK | **Accepted** | 2026-07-24 | AI Integration & Providers |
| [ADR-0003](#adr-0003-adoption-of-event-driven-modular-monolith-architecture) | Adoption of Event-Driven Modular Monolith Architecture | **Accepted** | 2026-07-24 | System Topology & Modules |
| [ADR-0004](#adr-0004-relational--vector-storage-engine-postgresql--pgvector) | PostgreSQL 16 + `pgvector` as Primary Data & Vector Engine | **Accepted** | 2026-07-24 | Storage & Search |
| [ADR-0005](#adr-0005-engineering-graph-storage-strategy) | Engineering Graph Storage via PostgreSQL Recursive Adjacency Models | **Accepted** | 2026-07-24 | Knowledge & Reasoning Graph |
| [ADR-0006](#adr-0006-asynchronous-queue-topology-via-redis-horizon) | Queue Topology & Priority Levels via Redis Horizon | **Accepted** | 2026-07-24 | Background Processing |
| [ADR-0007](#adr-0007-bi-directional-model-context-protocol-mcp-gateway) | Bi-Directional Model Context Protocol (MCP) Gateway Integration | **Accepted** | 2026-07-24 | External Integration / IDEs |
| [ADR-0008](#adr-0008-authentication--passwordless-security-fortify--passkeys) | Authentication Engine: Laravel Fortify + WebAuthn Passkeys | **Accepted** | 2026-07-24 | Security & Access Control |
| [ADR-0009](#adr-0009-sandboxed-tool-execution--process-isolation) | Subprocess & Isolated Container Sandboxing for Agent Tools | **Accepted** | 2026-07-24 | Automation & Security |
| [ADR-0010](#adr-0010-real-time-streaming-via-inertia-v3-sse-streams) | Server-Sent Events (SSE) Streaming for Agent Outputs & Graphs | **Accepted** | 2026-07-24 | Realtime Ergonomics |

---

## 📜 Detailed Decision Specifications

### ADR-0001: Core Tech Stack - Laravel 13, Inertia v3, Vue 3, Tailwind v4
* **Context**: Need a rapid, type-safe, enterprise web framework delivering SPA reactivity without REST API maintenance friction.
* **Options Considered**: (1) Python FastAPI + React, (2) Node.js NestJS + Next.js, (3) Laravel 13 + Inertia v3 + Vue 3 + Tailwind v4.
* **Decision**: **Option 3**. Inertia v3 bridges Vue 3 directly with Laravel 13 controllers; Wayfinder generates typed routes.

### ADR-0002: Standardizing AI Engine on First-Party `laravel/ai` SDK
* **Context**: Need unified multi-provider LLM support (OpenAI, Anthropic, Gemini, Ollama, DeepSeek) with failover and streaming.
* **Options Considered**: (1) Raw Guzzle HTTP vendor wrappers, (2) LangChain/LlamaIndex Python sidecar, (3) `laravel/ai` SDK (`Laravel\Ai\*`).
* **Decision**: **Option 3**. First-party package backing, fluent provider API, native testing mocks (`Ai::fake()`).

### ADR-0003: Adoption of Event-Driven Modular Monolith Architecture
* **Context**: Establish clean domain boundaries (`app/Domain/*`) without prematurely incurring microservices operational overhead.
* **Options Considered**: (1) Microservices, (2) Unstructured monolith, (3) Event-Driven Modular Monolith.
* **Decision**: **Option 3**. Maximum velocity, single deployment image, strict Pest Arch test boundary enforcement.

### ADR-0004: Relational & Vector Storage Engine (PostgreSQL + `pgvector`)
* **Context**: Storage engine must support ACID relational transactions for agents/users while providing high-speed semantic vector search.
* **Options Considered**: (1) Separate Pinecone/Weaviate vector DB + MySQL, (2) PostgreSQL 16 + `pgvector` HNSW extension.
* **Decision**: **Option 2**. Single database engine, zero data synchronization lag, transactional vector writes, sub-10ms similarity search.

### ADR-0005: Engineering Graph Storage Strategy
* **Context**: Graph storage representation linking Features, ADRs, Code, Tests, and Incidents for AI reasoning.
* **Options Considered**: (1) Dedicated Neo4j cluster, (2) PostgreSQL recursive CTEs + JSONB adjacency tables.
* **Decision**: **Option 2**. Avoids multi-database operational complexity while delivering fast recursive graph queries inside PostgreSQL.

### ADR-0006: Asynchronous Queue Topology via Redis Horizon
* **Context**: Heavy background tasks (vector embeddings, code tool sandboxing, bulk evals) must run asynchronously.
* **Options Considered**: (1) AWS SQS / RabbitMQ, (2) Redis backed Laravel Horizon with priority queue pools (`critical`, `ai-inference`, `tool-execution`).
* **Decision**: **Option 2**. Rich queue monitoring dashboard, fine-grained queue balancing, zero external cloud lock-in.

### ADR-0007: Bi-Directional Model Context Protocol (MCP) Gateway
* **Context**: ForgeAI must connect dynamically to external MCP tool servers and expose tools to external IDE clients (Cursor, Claude Desktop).
* **Options Considered**: (1) Custom JSON-RPC API, (2) Bi-Directional Model Context Protocol (MCP) Gateway.
* **Decision**: **Option 2**. Adheres to open industry standards, allowing instant interoperability with third-party tool ecosystems.

### ADR-0008: Authentication & Passwordless Security (Fortify + Passkeys)
* **Context**: Provide enterprise authentication supporting passkeys (WebAuthn), TOTP 2FA, and robust session security.
* **Options Considered**: (1) Third-party Auth0/Clerk SaaS, (2) `laravel/fortify` + `@laravel/passkeys`.
* **Decision**: **Option 2**. Full data sovereignty, passwordless WebAuthn support, zero per-user SaaS authentication cost.

### ADR-0009: Sandboxed Tool Execution & Process Isolation
* **Context**: Agent custom code execution (Bash, Python, PHP) must be safely isolated to prevent host compromise.
* **Options Considered**: (1) In-process PHP execution, (2) Restricted subprocess runners + containerized process sandboxes with 128MB memory caps and 10s execution timeouts.
* **Decision**: **Option 2**. Protects platform stability and enforces hard resource limits.

### ADR-0010: Real-Time Streaming via Inertia v3 SSE Streams
* **Context**: Real-time token generation and agent status updates streaming to user browser interfaces.
* **Options Considered**: (1) Full-duplex WebSockets (Pusher/Soketi), (2) Server-Sent Events (SSE) via Inertia v3 streaming hooks.
* **Decision**: **Option 2**. Lightweight HTTP streaming, simpler infrastructure, native Inertia integration.

---

## 🔗 Related Architecture Documents

- [02-domain-driven-design.md](file:///home/tristan/Projects/Forge_AI/docs/02-domain-driven-design.md)
- [04-system-architecture.md](file:///home/tristan/Projects/Forge_AI/docs/04-system-architecture.md)
- [09-api-strategy.md](file:///home/tristan/Projects/Forge_AI/docs/09-api-strategy.md)
