# ADR-0003: Adoption of Event-Driven Modular Monolith Architecture

* **Status**: Accepted
* **Date**: 2026-07-24
* **Deciders**: Principal Architect, Staff Software Engineer, DevOps Architect, CTO

---

## Context and Problem Statement

ForgeAI needs an architectural design that scales with team size, maintains clear separation of domain concerns (Auth, Agents, AI Engine, Vectors, Tool Workflows, Governance), and supports asynchronous job execution (vector indexing, heavy tool calls) without prematurely incurring microservices operational complexity.

---

## Options Considered

1. **Option A: Distributed Microservices Architecture**
   - *Pros*: Independent service deployment and autonomous team ownership.
   - *Cons*: High operational cost, distributed transaction complexity, network latency, distributed tracing overhead, slow early-stage product iteration.

2. **Option B: Classic Monolith (Unstructured app/ Directory)**
   - *Pros*: Zero architectural overhead initially.
   - *Cons*: High risk of "spaghetti code", blurred domain boundaries, tight coupling between AI execution and UI controllers.

3. **Option C (Selected): Event-Driven Modular Monolith**
   - *Pros*: Clean domain modules (`Domain/AuthTenant`, `Domain/Agent`, `Domain/AIEngine`, `Domain/KnowledgeVector`, `Domain/Automation`, `Domain/Governance`), strict boundary enforcement via Larastan & Pest Arch tests, asynchronous decoupling via typed domain events and Redis Horizon queues.
   - *Cons*: Requires discipline to prevent cross-module direct database queries or illegal imports.

---

## Decision Outcome

**Chosen Option**: **Option C** (Event-Driven Modular Monolith).

### Key Rationale:
- **Optimal Balance**: Delivers microservice-like domain isolation with monolith deployment simplicity.
- **Asynchronous Scalability**: Long-running tool executions and vector indexing run asynchronously on Redis queues without blocking user HTTP threads.

---

## Consequences

- **Positive**:
  - Simplified deployment pipeline (single container image topology).
  - High developer velocity and straightforward local development environment (`composer run dev`).
  - Clear extraction path if specific modules (e.g. vector search or sandboxed execution) require dedicated microservices in the future.
- **Negative**:
  - Architecture rules must be continuously enforced via CI static analysis tools (`pest` arch tests).
