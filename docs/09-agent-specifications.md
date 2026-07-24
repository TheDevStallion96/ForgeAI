# 09. Agent Specification Template & Specialist Roster

## Executive Summary

This document defines the **Agent Specification Standard Template** and provides comprehensive specifications for the 10 built-in Specialist AI Agents in **ForgeAI**.

Each agent operates within strict role boundaries, utilizing dedicated tools, memory rules, permissions, and failure recovery protocols.

---

## 📋 Agent Specification Standard Template

```markdown
# AGENT-[ID]: [Agent Persona Name]

## 1. Role Purpose & Domain Scope
Core mission, bounded context responsibility, and target deliverables.

## 2. Inputs & Outputs
- **Inputs**: User prompts, Engineering Graph context, system instructions, memory buffers.
- **Outputs**: Code diffs, architecture specs, ADRs, test suites, status events.

## 3. Tool Bindings & Permissions
- Allowed tool categories and HITL approval flags.

## 4. Memory & Context Rules
Context window trimming rules and RAG knowledge retrieval scope.

## 5. Failure Handling & Circuit Breakers
Fallback actions when tool execution fails or LLM rate limits trigger.
```

---

## 🤖 Built-in Specialist Agent Specifications

### 1. Project Manager Agent (`AGENT-001`)
* **Purpose**: Orchestrates sprint planning, task decomposition, story point estimation, and milestone tracking.
* **Tools**: `BacklogManager`, `GraphQuery`, `TokenLedgerCheck`.
* **Permissions**: Read/Write backlog items; Read token budgets. Cannot execute code or mutate DB schemas.

### 2. Architect Agent (`AGENT-002`)
* **Purpose**: Enforces DDD domain boundaries, drafts ADRs, maintains C4 topology models, and reviews code changes for architectural compliance.
* **Tools**: `AdrEditor`, `GraphQuery`, `PestArchRunner`.
* **Permissions**: Read/Write `/docs`; Read codebase. Cannot issue production releases.

### 3. Backend Engineer Agent (`AGENT-003`)
* **Purpose**: Writes PHP 8.5 controllers, services, actions, Eloquent models, migrations, and seeders following Laravel standards.
* **Tools**: `PHPFileEditor`, `PintFormatter`, `LarastanChecker`, `PestRunner`.
* **Permissions**: Sandboxed file writes in `app/`, `database/`, `config/`.

### 4. Frontend Engineer Agent (`AGENT-004`)
* **Purpose**: Builds Vue 3 components, Inertia v3 pages, Tailwind v4 styling, and Reka UI accessible primitives.
* **Tools**: `VueFileEditor`, `WayfinderTypeChecker`, `ViteBuildRunner`.
* **Permissions**: Sandboxed file writes in `resources/js/`, `resources/css/`.

### 5. Mobile Engineer Agent (`AGENT-005`)
* **Purpose**: Configures mobile PWA manifest settings, responsive viewport layouts, and touch gesture interactions.
* **Tools**: `ViewportTester`, `PwaManifestEditor`.

### 6. DevOps Engineer Agent (`AGENT-006`)
* **Purpose**: Configures Docker topologies, FrankenPHP settings, Redis Horizon queues, CI/CD scripts, and monitoring health checks.
* **Tools**: `DockerComposeEditor`, `HorizonConfigurator`, `HealthCheckRunner`.
* **Permissions**: Sandboxed file writes in `.github/`, `docker/`, `config/horizon.php`.

### 7. Security Engineer Agent (`AGENT-007`)
* **Purpose**: Audits code for OWASP Top 10 vulnerabilities, validates prompt injection shields, checks passkey auth flows, and monitors secret exposure.
* **Tools**: `SecurityScanner`, `PromptInjectionTester`, `SecretAudit`.
* **Permissions**: Blocking veto on PR approvals.

### 8. QA Engineer Agent (`AGENT-008`)
* **Purpose**: Generates Pest v4 unit, feature, arch, and eval test suites; runs automated LLM evaluation harnesses.
* **Tools**: `PestTestGenerator`, `EvalBenchmarkRunner`.
* **Permissions**: Sandboxed file writes in `tests/`.

### 9. Documentation Engineer Agent (`AGENT-009`)
* **Purpose**: Maintains architecture documentation in `/docs`, updates API catalogues, and updates `AGENTS.md`.
* **Tools**: `DocsEditor`, `MarkdownLinter`.
* **Permissions**: Sandboxed file writes in `docs/`, `AGENTS.md`, `README.md`.

### 10. Release Manager Agent (`AGENT-010`)
* **Purpose**: Tags Git releases, generates changelogs, coordinates staging deployments, and monitors production health post-release.
* **Tools**: `GitReleaseTagger`, `ChangelogGenerator`, `DeploymentTrigger`.
* **Permissions**: High-consequence permissions requiring explicit human authorization.

---

## 🔗 Related Architecture Documents

- [03-ai-and-agent-framework.md](file:///home/tristan/Projects/Forge_AI/docs/03-ai-and-agent-framework.md)
- [07-agent-framework.md](file:///home/tristan/Projects/Forge_AI/docs/07-agent-framework.md)
- [10-tool-registry.md](file:///home/tristan/Projects/Forge_AI/docs/10-tool-registry.md)
