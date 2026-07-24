# 02. Domain-Driven Design (DDD) Specification

## Executive Summary

ForgeAI is architected using **Domain-Driven Design (DDD)**.

The domain is partitioned into 10 Bounded Contexts with explicit aggregate boundaries, domain events, ubiquitous language definitions, and an anti-corruption layer governing inter-domain interactions.

---

## 🗺️ Context Map & Bounded Context Relationships

```mermaid
graph TD
    subgraph AuthTenantContext[1. Auth & Tenant Context]
        OrgAggregate[Organization Aggregate]
        UserAggregate[User Aggregate]
    end

    subgraph AgentContext[2. Agent Core Context]
        AgentAggregate[Agent Definition Aggregate]
        SessionAggregate[Execution Session Aggregate]
    end

    subgraph AIEngineContext[3. AI Engine Context (laravel/ai)]
        ProviderPolicy[Provider Policy]
        StreamPipeline[Streaming Pipeline]
    end

    subgraph KnowledgeContext[4. Knowledge & Vector Context]
        KnowledgeBaseAggregate[KnowledgeBase Aggregate]
        VectorChunk[DocumentChunk Entity]
    end

    subgraph AutomationContext[5. Automation & Tool Context]
        ToolAggregate[ToolDefinition Aggregate]
        SandboxRunner[Sandbox Runner]
    end

    subgraph GraphContext[6. Engineering Graph Context]
        NodeAggregate[GraphNode Aggregate]
        EdgeAggregate[GraphEdge Aggregate]
    end

    subgraph GovernanceContext[7. Token & Governance Context]
        LedgerAggregate[TokenLedger Aggregate]
        QuotaPolicy[QuotaPolicy]
    end

    AuthTenantContext -->|Upstream / TenantScope| AgentContext
    AuthTenantContext -->|Upstream / TenantScope| KnowledgeContext
    AgentContext -->|Uses| AIEngineContext
    AgentContext -->|Queries| KnowledgeContext
    AgentContext -->|Dispatches Tools| AutomationContext
    AgentContext -->|Traverses| GraphContext
    AIEngineContext -->|Emits Events to| GovernanceContext
```

---

## 🏛️ Detailed Bounded Contexts & Aggregates

### 1. `AuthTenantContext` (`app/Domain/AuthTenant`)
- **Ubiquitous Language**: Organization, Workspace, TenantScope, PasskeyCredential, RolePolicy.
- **Aggregates**:
  - `OrganizationAggregate`: Root entity for multi-tenant isolation, seat licensing, and default token quotas.
  - `UserAggregate`: User identity, authentication credentials (Fortify/Passkeys), and organization role assignments.

### 2. `AgentContext` (`app/Domain/Agent`)
- **Ubiquitous Language**: AgentPersona, SystemInstruction, ExecutionSession, ContextWindow, MemoryBuffer, ThoughtTrace.
- **Aggregates**:
  - `AgentAggregate`: Agent definition, system instructions, primary/fallback LLM models, and tool bindings.
  - `ExecutionSessionAggregate`: Stateful conversation thread, message history, current execution state, and accumulated context token count.

### 3. `AIEngineContext` (`app/Domain/AIEngine`)
- **Ubiquitous Language**: ProviderDriver, PromptPayload, CompletionResponse, TokenStream, ProviderFailoverCascade.
- **Responsibilities**: Wraps `Laravel\Ai\*` SDK. Manages driver invocations (OpenAI, Anthropic, Gemini, Ollama), structured output parsing, and real-time SSE chunk streaming.

### 4. `KnowledgeContext` (`app/Domain/KnowledgeVector`)
- **Ubiquitous Language**: KnowledgeBase, VectorEmbedding, DocumentChunk, CosineSimilarity, SemanticSearch.
- **Aggregates**:
  - `KnowledgeBaseAggregate`: Collection of indexed documentation, codebase files, or PDFs bound to a tenant organization.
  - `DocumentChunk`: Text segment with embedded float vector (`vector(1536)`) and metadata filters.

### 5. `AutomationContext` (`app/Domain/Automation`)
- **Ubiquitous Language**: ToolDefinition, SandboxProcess, HumanInTheLoopGate, ExecutionResult, ParameterSchema.
- **Aggregates**:
  - `ToolAggregate`: Executable capability (e.g. `RunPestTests`, `QueryDatabase`, `CreateGitCommit`) with JSON Schema inputs and permission constraints.

### 6. `EngineeringGraphContext` (`app/Domain/EngineeringGraph`)
- **Ubiquitous Language**: GraphNode, GraphEdge, NodeRelation, AdjacencyMatrix, ContextTraversal.
- **Aggregates**:
  - `GraphNodeAggregate`: Node representing an artifact (Feature, ADR, Commit, Issue, Test, Incident).
  - `GraphEdgeAggregate`: Directed edge representing a relationship (e.g., `IMPLEMNETS`, `TESTS`, `CAUSED_BY`).

### 7. `GovernanceContext` (`app/Domain/Governance`)
- **Ubiquitous Language**: TokenLedger, CostEstimate, QuotaCircuitBreaker, UsageAudit.
- **Aggregates**:
  - `TokenLedgerAggregate`: Append-only ledger recording input/output token counts, model provider, and USD cost per execution session.

---

## ⚡ Key Domain Events Catalogue

```php
// Core Domain Event Signatures
namespace App\Domain\Agent\Events;

final class AgentExecutionRequested {
    public function __construct(
        public readonly string $sessionId,
        public readonly string $agentId,
        public readonly string $prompt,
        public readonly string $tenantId,
    ) {}
}

namespace App\Domain\Automation\Events;

final class ToolExecutionRequired {
    public function __construct(
        public readonly string $sessionId,
        public readonly string $toolId,
        public readonly array $parameters,
        public readonly bool $requiresHumanApproval,
    ) {}
}

namespace App\Domain\Governance\Events;

final class TokenUsageRecorded {
    public function __construct(
        public readonly string $tenantId,
        public readonly string $sessionId,
        public readonly string $provider,
        public readonly string $model,
        public readonly int $promptTokens,
        public readonly int $completionTokens,
        public readonly float $estimatedCostUsd,
    ) {}
}
```

---

## 🔗 Related Architecture Documents

- [00-overview.md](file:///home/tristan/Projects/Forge_AI/docs/00-overview.md)
- [04-system-architecture.md](file:///home/tristan/Projects/Forge_AI/docs/04-system-architecture.md)
- [05-modular-architecture.md](file:///home/tristan/Projects/Forge_AI/docs/05-modular-architecture.md)
- [08-data-architecture.md](file:///home/tristan/Projects/Forge_AI/docs/08-data-architecture.md)
