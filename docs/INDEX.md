# ForgeAI Master Architecture, Governance & Technical Documentation Suite

Welcome to the **ForgeAI Master Documentation & Governance Suite**.

ForgeAI is an **Operating System for Software Engineering** that enables human engineers and autonomous AI agents to collaboratively plan, design, build, test, review, deploy, monitor, and continuously evolve software systems.

---

## 🏛️ Engineering Governance & Foundation Suite (`/docs`)

| Section | Document Title | Primary Roles | Key Focus Areas |
|---|---|---|---|
| 00 | [Project Foundation & Layout](file:///home/tristan/Projects/Forge_AI/docs/00-project-foundation.md) | Platform Architect, Tech Writer | Repository layout, docs directory structure, naming conventions & markdown standards |
| 01 | [ForgeAI Constitution](file:///home/tristan/Projects/Forge_AI/docs/01-forgeai-constitution.md) | CTO, Principal Architect, All Roles | Supreme vision, mission, core values, engineering philosophy & non-negotiables |
| 02 | [Comprehensive Engineering Standards](file:///home/tristan/Projects/Forge_AI/docs/02-engineering-standards.md) | Staff Engineer, Security Lead | PHP 8.5/Pint/Larastan L8, Vue 3/Wayfinder/Tailwind v4, DB, Pest v4 & AI prompt standards |
| 03 | [End-to-End Development Workflow](file:///home/tristan/Projects/Forge_AI/docs/03-development-workflow.md) | Engineering Manager, Product Lead | 12-Stage development lifecycle, inputs, outputs, approval gates & quality checks |
| 04 | [Git Workflow & Release Strategy](file:///home/tristan/Projects/Forge_AI/docs/04-git-workflow.md) | DevOps Architect, Staff Engineer | Branching model, Conventional Commits, PR gates (`ci:check`), SemVer 2.0 & releases |
| 05 | [Definition of Done (DoD)](file:///home/tristan/Projects/Forge_AI/docs/05-definition-of-done.md) | QA Lead, Security Lead, Staff Engineer | 10 Verification categories: Functional, Pest tests, Docs, Security, Performance, Accessibility, Evals |
| 06 | [Information Architecture & UX](file:///home/tristan/Projects/Forge_AI/docs/06-information-architecture.md) | UX Architect, Product Lead | UI navigation hierarchy, 10 primary workspace screens & split-pane agent workspaces |
| 07 | [Design System Standards](file:///home/tristan/Projects/Forge_AI/docs/07-design-system-foundation.md) | UX Architect, Frontend Engineer | Aesthetic pillars, Inter/JetBrains Mono typography, Tailwind v4 HSL palette & Reka UI |
| 08 | [Technical Backlog Framework](file:///home/tristan/Projects/Forge_AI/docs/08-technical-backlog-framework.md) | Product Manager, PM Agent | 7-Tier Work Breakdown Structure (Vision -> Initiative -> Epic -> Feature -> Story -> Task -> Subtask) |
| 09 | [Agent Specifications Roster](file:///home/tristan/Projects/Forge_AI/docs/09-agent-specifications.md) | AI Systems Architect, Staff Engineer | Agent specification template & 10 Specialist Agent personas (PM, Architect, Devs, SecOps, QA) |
| 10 | [Standard Tool Registry](file:///home/tristan/Projects/Forge_AI/docs/10-tool-registry.md) | AI Systems Architect, Platform Engineer | Standard `ToolDefinition` PHP interface & specifications across 13 tool categories |
| 11 | [Prompt Library Standards](file:///home/tristan/Projects/Forge_AI/docs/11-prompt-library.md) | AI Systems Architect, Tech Writer | Version-controlled `.prompts/` YAML+Markdown structure & specialist system prompts |
| 12 | [Canonical Implementation Blueprint](file:///home/tristan/Projects/Forge_AI/docs/12-implementation-blueprint.md) | Principal Architect, CTO | Step-by-step idea-to-production lifecycle blueprint, sequence flows & approval gates |

---

## 📚 Master Product & Architecture Blueprint Suite

| Section | Document Title | Primary Focus Areas |
|---|---|---|
| 00-Overview | [Overview & Strategic Foundation](file:///home/tristan/Projects/Forge_AI/docs/00-overview.md) | Vision, mission, product goals, design philosophy & ubiquitous glossary |
| 01-PRD | [Product Requirements Document (PRD)](file:///home/tristan/Projects/Forge_AI/docs/01-product-requirements.md) | Problem statement, personas, user journeys, functional/NFRs, scope boundaries |
| 02-DDD | [Domain-Driven Design Specification](file:///home/tristan/Projects/Forge_AI/docs/02-domain-driven-design.md) | 10 Bounded Contexts, Aggregates, Entities, Value Objects, Domain Events & Context Map |
| 03-ADRs | [Architecture Decision Records Index](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md) | Master ADR index and 10 detailed decision records (ADR-0001 through ADR-0010) |
| 04-System | [System Architecture Specification](file:///home/tristan/Projects/Forge_AI/docs/04-system-architecture.md) | C4 system topology, Inertia v3 SSE streaming, Horizon queues & hybrid vector search |
| 05-Modules | [Modular Architecture Specification](file:///home/tristan/Projects/Forge_AI/docs/05-modular-architecture.md) | 14 Core Domain Modules: Core, AI, Agent, Workspace, Projects, Planning, Graph, etc. |
| 06-Graph | [Engineering Graph & Reasoning Engine](file:///home/tristan/Projects/Forge_AI/docs/06-engineering-graph.md) | Connected property graph schema, recursive CTE query engine & agent context reasoning |
| 07-AgentFramework | [Collaborative Multi-Agent Framework](file:///home/tristan/Projects/Forge_AI/docs/07-agent-framework.md) | Multi-agent state machine, context compaction, tool sandboxing & 10 Specialist Agent personas |
| 08-DataArch | [Data Architecture & Logical Model](file:///home/tristan/Projects/Forge_AI/docs/08-data-architecture.md) | Logical entity models, aggregate boundaries, multi-tenant query scopes & token ledgers |
| 09-APIStrategy | [API Strategy & MCP Gateway](file:///home/tristan/Projects/Forge_AI/docs/09-api-strategy.md) | Wayfinder typed actions, REST/SSE APIs, Webhook engine & Bi-directional MCP Gateway |
| 10-SecurityArch | [Security Architecture & Governance](file:///home/tristan/Projects/Forge_AI/docs/10-security-architecture.md) | Zero-Trust security, Passkeys/Fortify, RBAC/ABAC matrix, Prompt Injection shields & Secrets |
| 11-Standards | [Development Standards & Quality](file:///home/tristan/Projects/Forge_AI/docs/11-development-standards.md) | Directory conventions, PHP 8.5/Pint/Larastan level 8 standards & Pest v4 test matrix |
| 12-Specs | [Feature Specification Matrix](file:///home/tristan/Projects/Forge_AI/docs/12-feature-specifications.md) | Feature specification template & per-module functional specification matrices |
| 13-Roadmap | [Phased Implementation Roadmap](file:///home/tristan/Projects/Forge_AI/docs/13-implementation-roadmap.md) | 6-Phase execution timeline, milestones, dependencies, risks & exit criteria |

---

## 🏛️ Architectural Decision Records (ADRs)

| ADR ID | Title | Status | Date |
|---|---|---|---|
| [ADR-0001](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0001-core-tech-stack---laravel-13-inertia-v3-vue-3-tailwind-v4) | Core Tech Stack: Laravel 13, Inertia v3, Vue 3, Tailwind v4 | **Accepted** | 2026-07-24 |
| [ADR-0002](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0002-standardizing-ai-engine-on-first-party-laravelai-sdk) | Standardizing AI Engine on First-Party `laravel/ai` SDK | **Accepted** | 2026-07-24 |
| [ADR-0003](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0003-adoption-of-event-driven-modular-monolith-architecture) | Adoption of Event-Driven Modular Monolith Architecture | **Accepted** | 2026-07-24 |
| [ADR-0004](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0004-relational--vector-storage-engine-postgresql--pgvector) | PostgreSQL 16 + `pgvector` as Primary Data & Vector Engine | **Accepted** | 2026-07-24 |
| [ADR-0005](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0005-engineering-graph-storage-strategy) | Engineering Graph Storage via PostgreSQL Recursive Adjacency Models | **Accepted** | 2026-07-24 |
| [ADR-0006](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0006-asynchronous-queue-topology-via-redis-horizon) | Queue Topology & Priority Levels via Redis Horizon | **Accepted** | 2026-07-24 |
| [ADR-0007](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0007-bi-directional-model-context-protocol-mcp-gateway) | Bi-Directional Model Context Protocol (MCP) Gateway Integration | **Accepted** | 2026-07-24 |
| [ADR-0008](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0008-authentication--passwordless-security-fortify--passkeys) | Authentication Engine: Laravel Fortify + WebAuthn Passkeys | **Accepted** | 2026-07-24 |
| [ADR-0009](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0009-sandboxed-tool-execution--process-isolation) | Subprocess & Isolated Container Sandboxing for Agent Tools | **Accepted** | 2026-07-24 |
| [ADR-0010](file:///home/tristan/Projects/Forge_AI/docs/03-architecture-decision-records.md#adr-0010-real-time-streaming-via-inertia-v3-sse-streams) | Server-Sent Events (SSE) Streaming for Agent Outputs & Graphs | **Accepted** | 2026-07-24 |

---

## 🛠️ Technology Stack Reference

- **Backend Framework**: Laravel 13 (PHP 8.5)
- **AI Infrastructure**: `laravel/ai` (v0.10+ SDK)
- **Frontend Architecture**: Inertia.js v3 + Vue 3 (Composition API, `<script setup>`)
- **Route Resolution**: `@laravel/vite-plugin-wayfinder` / `laravel/wayfinder`
- **Authentication**: `laravel/fortify` + `@laravel/passkeys` (WebAuthn / Passkeys)
- **Database & Search**: PostgreSQL 16 + `pgvector` HNSW indexes
- **Asynchronous Execution**: Redis Horizon Queues (`critical`, `ai-inference`, `tool-execution`)
- **Testing & Evals**: Pest v4 (Unit, Feature, Arch, Evals)
- **Code Standards**: Laravel Pint, Larastan Level 8+
