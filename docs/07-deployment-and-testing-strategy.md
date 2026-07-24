# 07. Deployment, Scaling & Testing Strategy

## Executive Summary

ForgeAI's infrastructure blueprint balances low operational overhead with high scalability.

The application deploys as a containerized stack (using **Docker**, **FrankenPHP / PHP 8.5**, **PostgreSQL + pgvector**, and **Redis**).

Testing follows a rigid **Pest v4** test pyramid, complemented by automated LLM evaluation harnesses to test prompt consistency and tool calling determinism.

---

## 🏗️ Production Container Topology

```mermaid
graph TD
    LB[Load Balancer / Cloudflare Edge] --> EdgeRouter[FrankenPHP Application Cluster]

    subgraph Application Cluster
        App1[FrankenPHP Container 1: App + Inertia]
        App2[FrankenPHP Container 2: App + Inertia]
    end

    subgraph Background Queue Cluster
        Worker1[Horizon Queue Worker: Critical/AI Queues]
        Worker2[Horizon Queue Worker: Tool Execution Sandbox]
        Worker3[Horizon Queue Worker: Vector Embeddings]
    end

    subgraph Persistence Layer
        Postgres[(PostgreSQL 16 + pgvector Primary)]
        RedisCluster[(Redis Cluster: Cache + Session + Horizon)]
    end

    EdgeRouter --> App1
    EdgeRouter --> App2
    App1 --> Postgres
    App2 --> Postgres
    App1 --> RedisCluster
    App2 --> RedisCluster
    Worker1 --> Postgres
    Worker1 --> RedisCluster
    Worker2 --> RedisCluster
    Worker3 --> Postgres
```

---

## 🧪 Testing Strategy Pyramid (Pest v4)

ForgeAI enforces automated quality control at every layer of the application using **Pest v4**:

```
        / \
       /   \        LLM Evals Harness (Prompt Accuracy & Determinism)
      / E2E \       Inertia E2E / Browser Smoke Tests
     /-------\
    / Feature \     Inertia Controller Actions, Auth & Tool Sandboxes
   /-----------\
  / Unit & Arch \   Domain Models, Services, PHP Architectural Rules
 /---------------\
```

### 1. Architectural Enforcement (`tests/ArchTest.php`)
```php
// Enforce strict layer separation
arch('domain modules do not leak into each other directly')
    ->expect('App\Domain\Agent')
    ->not->toUse('App\Domain\KnowledgeVector');

arch('controllers must extend BaseController')
    ->expect('App\Http\Controllers')
    ->toExtend('App\Http\Controllers\Controller');
```

### 2. Feature & Controller Tests (`tests/Feature/AgentExecutionTest.php`)
- Tests Inertia page renders, Wayfinder route dispatching, Fortify passkey authentication flows, and authorization gates.

### 3. LLM Quality Evaluation Harness (`tests/Evals/AgentEvalTest.php`)
- Runs automated evaluations on fixed prompt datasets to measure token accuracy, tool invocation precision, and system instruction compliance.

---

## 🚀 CI/CD Pipeline Workflow

```mermaid
graph LR
    Push[Git Push / PR] --> Lint[1. Pint Code Format Check]
    Lint --> Stan[2. Larastan Level 8 Static Analysis]
    Stan --> TypeCheck[3. Wayfinder & Vue TypeScript Check]
    TypeCheck --> PestTests[4. Pest v4 Unit & Feature Tests]
    PestTests --> ArchTests[5. Pest Architecture Tests]
    ArchTests --> Build[6. Vite Asset & Container Build]
    Build --> Deploy[7. Zero-Downtime Deployment via Docker / Cloud]
```

### Continuous Integration Commands:
```bash
# Executed automatically in CI pipeline prior to deployment
composer run ci:check
```
This single script executes Pint checks, Larastan static analysis, Vue type checks, and the full Pest test suite.

---

## 📈 Observability & Log Telemetry

- **Real-time Tail Debugging**: Powered by `laravel/pail` for zero-overhead tailing of production log streams.
- **Queue Health**: Monitored via Laravel Horizon dashboards, alerting on job failure rates or queue latency spikes.
- **System Health Endpoints**: `/up` endpoint monitored for database connectivity, vector store health, and Redis availability.

---

## 🔗 Related Architecture Documents

- [02. Software Architecture Blueprint](file:///home/tristan/Projects/Forge_AI/docs/02-software-architecture-blueprint.md)
- [03. AI & Agent Framework Architecture](file:///home/tristan/Projects/Forge_AI/docs/03-ai-and-agent-framework.md)
- [05. API Strategy & Event Catalogue](file:///home/tristan/Projects/Forge_AI/docs/05-api-and-event-catalogue.md)
- [ADR-0003: Event-Driven Modular Monolith Architecture](file:///home/tristan/Projects/Forge_AI/docs/adrs/0003-event-driven-modular-monolith-architecture.md)
