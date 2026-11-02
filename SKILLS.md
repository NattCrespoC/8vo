# SKILLS.md - Catálogo Maestro y Reglas de Desarrollo del Proyecto

**Proyecto:** Sistema de Gestión de Adquisiciones e Inventarios con Auditoría IA Vectorial  
**Stack Oficial:** PHP 8.3+, Laravel 11, FilamentPHP v3.2, Livewire 3, PostgreSQL 16 (`pgvector`), Redis, Laravel Reverb, Ollama (`nomic-embed-text`), Groq API (`llama-3.3-70b-versatile`).

---

## 1. Principios de Arquitectura Inviolables (Ingeniería de Software 2)
- **Cero Lógica en Controladores o Recursos de Filament:** Todo cálculo matemático, llamada a IA o regla de negocio debe vivir en clases dentro de `app/Services/` o `app/Actions/`.
- **Tipado Estricto:** Todos los archivos PHP deben iniciar con `declare(strict_types=1);` y usar tipos de retorno explícitos en cada método.
- **Procesamiento Asíncrono Obligatorio:** Ninguna llamada a Ollama o Groq puede bloquear el hilo HTTP principal del usuario. Toda auditoría debe despacharse mediante un `Observer` hacia un `Job` en cola (`ShouldQueue`).

---

## 2. Índice Maestro de Skills Instaladas (`.agents/skills/`)

### A. Seguridad & Auditoría de Vulnerabilidades (OWASP / DevSecOps)
- **`laravel-security`:** Audita vulnerabilidades específicas de Laravel (Mass assignment, SQL injection en `DB::raw`/`whereRaw`, Blade XSS `{!! !!}`, exclusiones CSRF, `APP_KEY`, `APP_DEBUG`).
- **`flow-security`:** Audita flujos de negocio multi-paso: máquinas de estado en órdenes de compra $\to$ pagos $\to$ facturas, replay attacks, manipulación de montos y precios.
- **`llm-security`:** Audita seguridad en pipelines IA: prevención de prompt injection, control de permisos en Function Calling, sanitización de credenciales de Groq/Ollama y protección de datos sensibles (PII).
- **`injection-flaws`:** Detección transversal de SQL Injection, OS Command Injection, SSRF y Path Traversal.
- **`secrets-detection`:** Escaneo preventivo de llaves API, credenciales de PostgreSQL, tokens y contraseñas.
- **`security-audit`:** Generación de reportes de auditoría de seguridad `SECURITY-AUDIT.md`.
- **`security-reviewer`:** Revisión experta con severidad CWE y remediación de código vulnerable.
- **`design-threat-review`:** Modelado metodológico de amenazas (STRIDE) sobre diagramas y esquemas antes de codificar.
- **`vibesec-skill` & `secure-code-guardian`:** Protección en caliente y hardening de endpoints web.

### B. Framework, UI & Tiempo Real
- **`filament-development` & `filament-pro`:** Creación de recursos administrativos, repeaters maestro-detalle, badges semánticos, modales para dictámenes IA, widgets con métricas financieras y listeners en vivo de Laravel Echo.
- **`laravel-best-practices` & `laravel-specialist`:** Domain-Driven Design (DDD), patrones Service/Action, optimización de Eloquent, manejo de excepciones y diseño de APIs robustas.
- **`laravel-permission-development`:** Roles y permisos RBAC con `spatie/laravel-permission` (Gerente, Auditor, Compras, Almacén).
- **`reverb-websockets` & `websocket-engineer`:** Laravel Reverb en puerto 8080, eventos `ShouldBroadcastNow`, presencia y notificaciones push sin recargar el navegador.
- **`tailwindcss-development`:** Estilizado moderno para alertas visuales y componentes web.
- **`infer-conventions`:** Detección y mantenimiento continuo de convenciones de código.

### C. Base de Datos, pgvector y Rendimiento
- **`pgvector-anomaly-detection`:** Modelado matemático, cálculo de distancia de coseno ($<=>$) y algoritmo $Z$-Score.
- **`db-specialized-fit`:** Auditoría de motores vectoriales (dimensiones de vector 768d para `nomic-embed-text`, métrica de coseno, índice HNSW).
- **`db-indexing`:** Auditoría de índices de alto rendimiento en PostgreSQL 16.
- **`db-types-precision`:** Garantía de tipos numéricos monetarios exactos (`decimal(12,2)`) y marcas de tiempo UTC con zona horaria (`timestamptz`).
- **`db-security-access`:** Seguridad de base de datos, Row-Level Security y configuración del usuario `pgsql_readonly` para Text-to-SQL.
- **`sql-pro`:** Optimización de consultas SQL complejas y análisis de planes de ejecución (`EXPLAIN ANALYZE`).

### D. Inteligencia Artificial & Evaluación Académica
- **`ai-multimodal-orchestration`:** Conexión cliente con Ollama (`nomic-embed-text`), Groq API JSON Schema (`llama-3.3-70b-versatile`), Whisper y Vision.
- **`academic-project-evaluation`:** Fórmulas financieras PEP (VAN, TIR, B/C, Payback en Bolivianos) y especificaciones formales para SOFT 2 y PE.
- **`pest-testing`:** Pruebas unitarias y de integración para validar la detección de anomalías y la estabilidad del ERP.

---

## 3. Contratos de Datos Canónicos para IA

### Cadena Canónica para Embeddings (Ollama `nomic-embed-text`)
```text
TIPO:{TIPO_TRANSACCION}|PROVEEDOR:{NIT_O_NOMBRE}|RUBRO:{CATEGORIA}|TOTAL:{MONTO}|HORA:{HH}|DIA:{1-7}|ROL:{ROL_OPERADOR}|ITEMS:{SKU1}:cant={Q1},pu={P1};{SKU2}:cant={Q2},pu={P2}
```

### Contrato JSON Obligatorio para Groq API (`llama-3.3-70b-versatile`)
```json
{
  "is_anomalous": true,
  "risk_level": "CRITICO",
  "confidence_percentage": 94,
  "primary_reason": "Sobreprecio del 42% respecto al promedio histórico del ítem y registro en horario no laboral.",
  "financial_impact_bs": 3450.00,
  "recommended_action": "Bloquear orden de pago y solicitar cotización de respaldo al área de compras."
}
```

---

## 4. Convenciones de Git para el Equipo
- Todo commit debe seguir Conventional Commits (`feat:`, `fix:`, `refactor:`, `test:`, `docs:`, `chore:`).
- Se opera con la llave SSH sin contraseña `~/.ssh/id_ed25519_proyecto`.
