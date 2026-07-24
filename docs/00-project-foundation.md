# 00. Project Foundation & Repository Standards

## Executive Summary

This document establishes the official repository structure, documentation organization, naming conventions, Markdown formatting standards, versioning rules, and cross-referencing strategies for **ForgeAI**.

It serves as the foundation for both human software engineers and autonomous AI agents contributing to the codebase.

---

## 📁 Repository Directory Structure

```
/
├── .agents/                  # Agent skills, instructions, and workspace customizations
│   └── skills/               # Domain-specific skill modules
├── app/                      # Application source code
│   ├── Domain/               # Modular Monolith DDD bounded contexts
│   │   ├── AuthTenant/       # Identity, Tenants, Fortify passkey auth
│   │   ├── Agent/            # Agent definitions, session execution, memory
│   │   ├── AIEngine/         # laravel/ai SDK wrappers & drivers
│   │   ├── KnowledgeVector/  # Document chunking, embeddings, pgvector
│   │   ├── Automation/       # Sandboxed tool runners, HITL gates
│   │   ├── EngineeringGraph/ # Graph nodes, edges, recursive CTE engine
│   │   └── Governance/       # Token ledgers, budgets, rate limiters
│   └── Http/                 # Controllers, middleware, Inertia routes
├── config/                   # Laravel configuration files
├── database/                 # Migrations, factories, seeders
├── docs/                     # Authoritative Product, Architecture & Governance Documentation
├── resources/                # Frontend source code
│   └── js/                   # Vue 3 SPA components, Inertia pages, layout frames
├── routes/                   # Web, API, and Wayfinder route declarations
├── storage/                  # Application logs, temporary caches, file uploads
└── tests/                    # Pest v4 testing matrix (Unit, Feature, Arch, Evals)
```

---

## 📄 Documentation Hierarchy (`/docs`)

All project documentation resides inside `/docs` and strictly follows the numbered index naming convention:

```
docs/
├── INDEX.md                                  # Master Architecture & Governance Navigation Index
├── 00-project-foundation.md                  # Repository & Documentation Layout, Versioning & Cross-Referencing
├── 01-forgeai-constitution.md                # Supreme Project Constitution, Core Values & Engineering Principles
├── 02-engineering-standards.md               # Coding, API, DB, Pest v4, Security & AI Prompt Standards
├── 03-development-workflow.md                # 12-Stage Lifecycle Workflow, Approval Gates & Quality Checks
├── 04-git-workflow.md                        # Branching Strategy, Commit Conventions, SemVer & Release Pipeline
├── 05-definition-of-done.md                  # Comprehensive Definition of Done (Functional, Test, Security, AI)
├── 06-information-architecture.md            # UI Navigation Hierarchy, Workspace Views & Information Flow
├── 07-design-system-foundation.md            # Design Tokens, Tailwind v4 Palette, Typography & Accessibility
├── 08-technical-backlog-framework.md         # Backlog Hierarchy (Vision -> Epic -> Feature -> Story -> Task)
├── 09-agent-specifications.md                # Agent Definition Template & 10 Specialist Agent Specifications
├── 10-tool-registry.md                       # Standard Interface Specifications for 13 Tool Categories
├── 11-prompt-library.md                      # Version-Controlled Prompt Library Structure & Specialist Prompts
├── 12-implementation-blueprint.md           # Canonical Idea-to-Production Implementation Lifecycle
└── adrs/                                     # Architectural Decision Records directory
    ├── 0001-core-tech-stack-laravel13-inertia3-vue3.md
    ├── 0002-ai-framework-laravel-ai-sdk.md
    └── ...
```

---

## 🏷️ Naming Conventions & File Standards

| Asset Type | Convention | Example |
|---|---|---|
| **PHP Domain Classes** | PascalCase | `App\Domain\Agent\Models\AgentSession` |
| **Vue Components** | PascalCase | `resources/js/components/AgentChatWindow.vue` |
| **Inertia Pages** | PascalCase | `resources/js/pages/Agents/Index.vue` |
| **Database Migrations** | `YYYY_MM_DD_HHMMSS_snake_case` | `2026_08_01_000000_create_agents_table.php` |
| **Pest Test Files** | `PascalCaseTest.php` | `tests/Feature/AgentExecutionTest.php` |
| **Documentation Files** | `numeric-kebab-case.md` | `docs/01-forgeai-constitution.md` |

---

## 📝 Markdown & Diagram Standards

1. **GitHub Flavored Markdown (GFM)**: Standard GFM headers, tables, bullet points, and syntax-highlighted code fences.
2. **Alert Fences**: Use standard GitHub alert blocks (`> [!NOTE]`, `> [!IMPORTANT]`, `> [!WARNING]`, `> [!CAUTION]`).
3. **Diagrams**: All architecture, sequence, C4, state machine, and entity-relationship diagrams MUST be specified in **Mermaid** code blocks.
4. **Clickable Links**: All file references MUST use Markdown links using relative paths or full absolute file URIs (`file:///...`).

---

## 🔗 Related Architecture Documents

- [01-forgeai-constitution.md](file:///home/tristan/Projects/Forge_AI/docs/01-forgeai-constitution.md)
- [02-engineering-standards.md](file:///home/tristan/Projects/Forge_AI/docs/02-engineering-standards.md)
- [04-git-workflow.md](file:///home/tristan/Projects/Forge_AI/docs/04-git-workflow.md)
