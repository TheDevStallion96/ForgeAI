# 11. Version-Controlled Prompt Library & System Prompts

## Executive Summary

The **ForgeAI Prompt Library** establishes the storage conventions, version control strategy, prompt framing structures, and system prompt specifications for all AI agent personas.

System prompts are managed as code—stored inside `.prompts/` as version-controlled Markdown files with YAML frontmatter specifying model drivers, temperature settings, and tool bindings.

---

## 📁 Prompt Storage Directory Structure

```
.prompts/
├── planner.md                # AGENT-001: Project Manager Agent Prompt
├── architect.md              # AGENT-002: Architect Agent Prompt
├── backend-engineer.md       # AGENT-003: Backend Engineer Agent Prompt
├── frontend-engineer.md      # AGENT-004: Frontend Engineer Agent Prompt
├── devops-engineer.md        # AGENT-006: DevOps Engineer Agent Prompt
├── security-engineer.md      # AGENT-007: Security Engineer Agent Prompt
├── qa-engineer.md            # AGENT-008: QA Engineer Agent Prompt
├── reviewer.md               # Code & Architecture Reviewer Prompt
└── docs-engineer.md          # AGENT-009: Documentation Engineer Agent Prompt
```

---

## 📄 Prompt Specification File Format (YAML + Markdown)

```markdown
---
name: "Architect Agent System Prompt"
agent_id: "AGENT-002"
version: "1.2.0"
model: "anthropic:claude-3-5-sonnet"
fallback_model: "openai:gpt-4o"
temperature: 0.20
tools:
  - "AdrEditor"
  - "GraphQuery"
  - "PestArchRunner"
---

# Role Definition
You are the **Architect Agent** for ForgeAI. Your primary responsibility is to enforce Domain-Driven Design (DDD) boundaries under `app/Domain/`, draft Architectural Decision Records (ADRs) in `docs/adrs/`, and ensure zero technical debt.

# Non-Negotiable Rules
1. Never generate production code without verifying architectural alignment.
2. Enforce strict layer separation between Domain, Infrastructure, and HTTP Controllers.
3. Wrap all untrusted context in `<untrusted_rag_context>` delimiter tags.
```

---

## 🤖 Specialist System Prompt Specifications

### 1. Architect Agent System Prompt (`.prompts/architect.md`)
* **Focus**: DDD Bounded Context isolation, ADR generation, C4 diagram creation, static analysis compliance.

### 2. Backend Engineer System Prompt (`.prompts/backend-engineer.md`)
* **Focus**: PHP 8.5 strict typing, Laravel 13 conventions, constructor property promotion, Pint formatting, Larastan Level 8 compliance.

### 3. Frontend Engineer System Prompt (`.prompts/frontend-engineer.md`)
* **Focus**: Vue 3 Composition API (`<script setup lang="ts">`), Inertia.js v3, Tailwind CSS v4 styling, Reka UI accessible primitives, Wayfinder typed route imports.

### 4. Security Engineer System Prompt (`.prompts/security-engineer.md`)
* **Focus**: STRIDE threat mitigation, OWASP Top 10 auditing, prompt injection shield validation, secret masking, passkey auth verification.

### 5. QA Engineer System Prompt (`.prompts/qa-engineer.md`)
* **Focus**: Pest v4 unit, feature, arch, and eval test generation (`tests/Unit/`, `tests/Feature/`, `tests/Arch/`, `tests/Evals/`).

---

## 🔗 Related Architecture Documents

- [02-engineering-standards.md](file:///home/tristan/Projects/Forge_AI/docs/02-engineering-standards.md)
- [07-agent-framework.md](file:///home/tristan/Projects/Forge_AI/docs/07-agent-framework.md)
- [09-agent-specifications.md](file:///home/tristan/Projects/Forge_AI/docs/09-agent-specifications.md)
