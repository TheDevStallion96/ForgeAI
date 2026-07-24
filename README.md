<div align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel" alt="Laravel 13" />
  <img src="https://img.shields.io/badge/PHP-8.5-777BB4?logo=php" alt="PHP 8.5" />
  <img src="https://img.shields.io/badge/Inertia-v3-9553E9?logo=inertia" alt="Inertia v3" />
  <img src="https://img.shields.io/badge/Vue-3-4FC08D?logo=vuedotjs" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Tailwind-v4-06B6D4?logo=tailwindcss" alt="Tailwind v4" />
  <img src="https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql" alt="PostgreSQL 16" />
  <br/>
  <img src="https://img.shields.io/badge/Pest-v4-CC2B2B?logo=pest" alt="Pest v4" />
  <img src="https://img.shields.io/badge/tests-183_passing-brightgreen" alt="Tests: 183 passing" />
  <img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build" />
</div>

# ForgeAI — The Operating System for Software Engineering

**ForgeAI** is an open-source AI engineering platform that orchestrates human engineers and specialized autonomous AI agents in a shared, observable environment. Together, they collaboratively plan, design, architect, build, test, review, deploy, monitor, and continuously evolve software systems.

Unlike traditional AI coding assistants that operate as isolated code auto-completers or siloed chatbots, ForgeAI provides a unified, enterprise-grade operating system bridging product management, software architecture, agent execution, codebase evolution, deployment observability, and real-time governance.

---

## Vision & Mission

**Vision** — Empower engineering organizations to build software at 10x velocity with zero compromise on architectural integrity, security, or maintainability by seamlessly fusing human creativity with autonomous AI agent orchestrations.

**Mission** — Provide a unified, enterprise-grade operating system that bridges product management, software architecture, agent execution, codebase evolution, deployment observability, and real-time governance into a single, cohesive modular application.

---

## Key Capabilities

- **Multi-Agent Orchestration** — Deploy swarms of specialized AI agents (Architect, Backend, Frontend, QA, SecOps, Release) governed by deterministic state machines with human-in-the-loop approval gates.
- **Engineering Graph** — Every entity from requirements and ADRs to commits, tests, and incidents is indexed as nodes in a connected property graph, providing deep contextual ground truth for AI reasoning.
- **Real-Time Streaming** — Agent thought streams, token outputs, and graph updates stream to the UI via Inertia v3 Server-Sent Events (SSE).
- **Enterprise Governance** — Immutable token usage ledgers, per-organization budget enforcement, AES-256-GCM secrets management, PII redaction, and prompt injection shielding.
- **Multi-Provider AI** — First-party `laravel/ai` SDK with dynamic failover across OpenAI, Anthropic, Google Gemini, DeepSeek, xAI, Mistral, and local Ollama models.
- **Sandboxed Tool Execution** — Agent tools run within restricted subprocess sandboxes with strict time limits, memory caps, and PII redaction.
- **Bi-Directional MCP Gateway** — Connect dynamically with external tool servers and IDE clients via the Model Context Protocol.

---

## Architecture

ForgeAI follows an **event-driven modular monolith** architecture built on Laravel 13, organized into clean DDD domain modules within `app/Domain/*` that communicate via typed events and Redis Horizon queues.

### Domain Modules

| Module | Status | Description |
|--------|--------|-------------|
| `AuthTenant` | ✅ Done | Multi-tenant organization models, Fortify authentication, Passkey WebAuthn |
| `AIEngine` | ✅ Done | `laravel/ai` SDK wrapper, multi-provider failover, circuit breakers, streaming |
| `Agent` | ✅ Done | Agent state machines, execution sessions, orchestration, tool definitions |
| `Governance` | ✅ Done | Token ledgers, budget enforcement, audit logs, API key secrets, PII redaction, prompt injection shields |
| `Workspace` | ✅ Done | Project workspaces with Kanban-style task boards |
| `EngineeringGraph` | 🔄 Phase 5 | Connected property graph, recursive CTE traversal |
| `KnowledgeVector` | 🔄 Phase 5 | Document chunking, embedding pipelines, HNSW vector search |
| `Automation` | 📋 Planned | Tool registry, sandbox runners, HITL gates, Graph Engine workflows |

### Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13 / PHP 8.5 |
| **AI SDK** | `laravel/ai` (OpenAI, Anthropic, Gemini, DeepSeek, Ollama, and more) |
| **Frontend** | Inertia.js v3 + Vue 3 (Composition API, `<script setup>`) |
| **Styling** | Tailwind CSS v4 + Reka UI primitives (shadcn-vue) |
| **Routing** | `@laravel/vite-plugin-wayfinder` / `laravel/wayfinder` (typed TS functions) |
| **Auth** | Laravel Fortify + WebAuthn Passkeys |
| **Database** | PostgreSQL 16 + `pgvector` HNSW indexes |
| **Queues** | Redis Horizon (priority levels: `critical`, `ai-inference`, `tool-execution`) |
| **Testing** | Pest v4 (Unit, Feature, Architecture, Evaluations) |
| **Code Quality** | Laravel Pint, Larastan Level 8 |

---

## Implementation Roadmap

ForgeAI is delivered in 6 sequential phases, each with defined exit criteria.

### Phase 1 — Platform Foundation ✅
Multi-tenant organization models, Fortify + Passkey authentication, Vue 3 Inertia app frame.

### Phase 2 — AI Engine ✅
`laravel/ai` SDK integration, OpenAI/Anthropic/Gemini/Ollama drivers, provider failover circuit breakers.

### Phase 3 — Multi-Agent Framework ✅
10 Specialist Agent personas, state machines, execution sessions, tool definitions, subprocess sandbox.

### Phase 4 — Reactive Workspace & UI ✅
Governance dashboard, agent workspace, sidebar navigation restructure, workspace Kanban boards, architecture studio, knowledge hub, deployments marketplace, monitoring, source control UI pages.

### Phase 5 — Engineering Graph & Knowledge Hub 🚧 *In Progress*
- `Domain/EngineeringGraph` — Recursive CTE traversal queries for connected artifact reasoning
- `Domain/KnowledgeVector` — Document chunking, embedding pipelines, `pgvector` HNSW similarity search
- `Domain/Automation` — Tool registry, sandboxed subprocess runners, HITL approval gates, Graph Engine workflow definitions

### Phase 6 — Enterprise & Scale 📋 *Planned*
- MCP Gateway (bi-directional JSON-RPC server/client for external IDE integration)
- Plugin extension loader and marketplace
- Production scaling, load testing, and security audits

---

## Development

### Prerequisites

- PHP 8.5+
- Composer
- Node.js 20+
- PostgreSQL 16+
- Redis

### Setup

```bash
# Clone the repository
git clone https://github.com/TheDevStallion96/ForgeAI.git
cd ForgeAI

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env
php artisan key:generate

# Configure your .env with database and AI provider credentials
# Then run migrations
php artisan migrate

# Start the development servers
composer run dev
```

### Testing & Quality

```bash
# Run all tests
php artisan test --compact

# Run specific test suite
php artisan test --compact --filter=Governance

# Static analysis
vendor/bin/phpstan analyse

# Code style
vendor/bin/pint --format agent
```

---

## Documentation

Comprehensive architecture, governance, and technical documentation is maintained in the [`docs/`](docs/INDEX.md) directory:

- [00. Overview & Strategic Foundation](docs/00-overview.md)
- [01. Product Requirements](docs/01-product-requirements.md)
- [02. Domain-Driven Design Specification](docs/02-domain-driven-design.md)
- [03. Architecture Decision Records](docs/03-architecture-decision-records.md)
- [04. System Architecture](docs/04-system-architecture.md)
- [05. Modular Architecture](docs/05-modular-architecture.md)
- [06. Engineering Graph & Reasoning Engine](docs/06-engineering-graph.md)
- [07. Collaborative Multi-Agent Framework](docs/07-agent-framework.md)
- [08. Data Architecture & Logical Model](docs/08-data-architecture.md)
- [09. API Strategy & MCP Gateway](docs/09-api-strategy.md)
- [10. Security Architecture & Governance](docs/10-security-architecture.md)
- [11. Development Standards & Quality](docs/11-development-standards.md)
- [12. Feature Specification Matrix](docs/12-feature-specifications.md)
- [13. Phased Implementation Roadmap](docs/13-implementation-roadmap.md)
- [ADR Index](docs/03-architecture-decision-records.md)
