# ADR-0002: Standardizing AI Engine on First-Party `laravel/ai` SDK

* **Status**: Accepted
* **Date**: 2026-07-24
* **Deciders**: AI Systems Architect, Principal Architect, CTO, Staff Software Engineer

---

## Context and Problem Statement

ForgeAI must integrate with multiple LLM providers (OpenAI, Anthropic, Google Gemini, Ollama, DeepSeek) for text generation, structured output parsing, tool calling, vector embeddings, and multi-provider failover.

We need to decide whether to write custom vendor wrappers, rely on unmaintained third-party PHP packages, or standardize on Laravel's official first-party AI SDK (`laravel/ai`).

---

## Options Considered

1. **Option A: Custom HTTP Vendor Clients (Guzzle / OpenAI-PHP / Anthropic-PHP)**
   - *Pros*: Complete low-level control over raw HTTP payloads.
   - *Cons*: High maintenance burden, disparate error structures, manual tool-calling schema transformations for each vendor.

2. **Option B: LangChain / LlamaIndex Python Microservice Sidecar**
   - *Pros*: Access to Python AI ecosystem.
   - *Cons*: Network latency overhead, complex IPC serialization, additional microservice infrastructure to deploy and monitor.

3. **Option C (Selected): Standardize on `laravel/ai` SDK (`Laravel\Ai\*`)**
   - *Pros*: First-party Laravel package, unified fluent interface across OpenAI, Anthropic, Gemini, DeepSeek, and Ollama; built-in structured output parsing, tool definitions, embedding generation, streaming support, and seamless testing mocks.
   - *Cons*: Tied to Laravel ecosystem release cycles (mitigated by Laravel core backing).

---

## Decision Outcome

**Chosen Option**: **Option C** (`laravel/ai` SDK).

### Key Rationale:
- **Provider Agnostic Abstraction**: Switch or cascade providers (e.g. `anthropic:claude-3-5-sonnet` -> `openai:gpt-4o`) without refactoring agent logic.
- **Native Laravel Integration**: Built-in integration with Laravel caching, queues, events, and testing mocks (`Ai::fake()`).

---

## Consequences

- **Positive**:
  - Dramatic reduction in AI integration code size and maintenance cost.
  - Uniform testing using `Ai::fake()` for fast, deterministic unit test assertions without burning live LLM tokens.
- **Negative**:
  - Bleeding-edge provider features must be contributed to `laravel/ai` or extended via custom driver extensions.
