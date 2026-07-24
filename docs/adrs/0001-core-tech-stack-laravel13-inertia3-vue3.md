# ADR-0001: Core Tech Stack - Laravel 13, Inertia v3, Vue 3, Tailwind v4

* **Status**: Accepted
* **Date**: 2026-07-24
* **Deciders**: Principal Architect, CTO, Staff Software Engineer, UX Architect

---

## Context and Problem Statement

ForgeAI requires a web platform stack capable of delivering:
1. Enterprise-grade server infrastructure with rich database ORM, background queues, and security primitives.
2. A single-page application (SPA) reactive user experience with sub-second token streaming.
3. Rapid developer velocity, type-safe route handling, and unified maintainability.

---

## Options Considered

1. **Option A: Python FastAPI Backend + React SPA Frontend**
   - *Pros*: Direct access to Python AI/ML ecosystem.
   - *Cons*: High API glue overhead, duplicate type definitions, complex authentication management, dual deployment pipelines.

2. **Option B: Node.js (NestJS) + Next.js SSR**
   - *Pros*: Full-stack TypeScript.
   - *Cons*: Heavy framework fragmentation, complex serverless cold starts, immature queueing compared to Laravel Horizon.

3. **Option C (Selected): Laravel 13 (PHP 8.5) + Inertia.js v3 + Vue 3 + Tailwind v4**
   - *Pros*: Seamless SPA development without client-side API boilerplate, robust first-party authentication (Fortify + Passkeys), superior queue management (Horizon/Redis), automated typed route generation via `@laravel/vite-plugin-wayfinder`, unified testing with Pest v4.
   - *Cons*: Custom code tool runners require subprocess/Docker sandboxing rather than native in-process Python execution.

---

## Decision Outcome

**Chosen Option**: **Option C** (Laravel 13 + Inertia v3 + Vue 3 + Tailwind v4).

### Key Rationale:
- **Developer Velocity**: Inertia v3 removes REST/GraphQL API boilerplate, rendering Vue 3 pages directly from server controllers.
- **Wayfinder Route Safety**: `laravel/wayfinder` generates typed TypeScript route helpers directly from Laravel controllers.
- **Enterprise Security**: `laravel/fortify` and `@laravel/passkeys` provide out-of-the-box WebAuthn and passkey auth.

---

## Consequences

- **Positive**:
  - Accelerated feature development with single-repository focus.
  - Sub-second SSE streaming for AI token generation using Inertia v3 hooks.
  - Robust background queue orchestration using Laravel Horizon.
- **Negative**:
  - Python-specific ML libraries must be executed via sandboxed subprocesses or HTTP sidecars when needed.
