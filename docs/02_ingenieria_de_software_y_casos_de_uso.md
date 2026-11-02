# 02. Ingeniería de Software y Especificación de Casos de Uso

---

## 1. Requisitos del Sistema

### Requisitos Funcionales (RF)
- **RF-01 (Gestión de Órdenes de Compra):** El sistema debe permitir registrar, listar, editar y auditar órdenes de compra compuestas por maestro (proveedor, fecha, forma de pago) y detalle (productos, cantidades, precios unitarios).
- **RF-02 (Ingesta Multimodal de Almacén):** El sistema debe recibir imágenes de comprobantes físicos y notas de voz, procesándolas mediante OCR y Whisper para precompletar el formulario de ingreso físico.
- **RF-03 (Vectorización Automática Asíncrona):** El sistema debe interceptar cada orden de compra o movimiento de inventario para normalizar su contenido y generar su vector de 768 dimensiones vía Ollama en segundo plano.
- **RF-04 (Cálculo Estadístico y Vectorial):** El sistema debe computar en PostgreSQL la distancia de coseno frente al histórico del proveedor/categoría y el $Z$-Score por cada producto, determinando un nivel de riesgo (`BAJO`, `MEDIO`, `ALTO`, `CRITICO`).
- **RF-05 (Bloqueo Preventivo y Dictamen IA):** Si el riesgo es `ALTO` o `CRITICO`, el sistema debe cambiar el estado de la transacción a `BLOQUEADO_POR_AUDITORIA` y solicitar un dictamen en JSON estructurado a Groq API.
- **RF-06 (Alertas en Tiempo Real por WebSockets):** El sistema debe emitir eventos mediante Laravel Reverb para que las pantallas de los directivos se actualicen en menos de 1 segundo sin recargar el navegador.
- **RF-07 (Copiloto Text-to-SQL de Solo Lectura):** El sistema debe permitir a la gerencia realizar preguntas analíticas en lenguaje natural y ejecutar sentencias SQL seguras tipo `SELECT`.
- **RF-08 (Acciones Administrativas con Function Calling):** El sistema debe permitir al auditor aprobar excepciones justificadas o anular compras bloqueadas con generación automática de actas PDF firmadas con QR.

### Requisitos No Funcionales (RNF)
- **RNF-01 (Latencia del Pipeline):** El tiempo total del ciclo de auditoría (despacho + vectorización en Ollama + consulta SQL + Groq + Reverb) no debe superar los 2.000 ms.
- **RNF-02 (Tiempo de Respuesta HTTP):** La interfaz de usuario debe responder en menos de 250 ms al persistir una transacción, delegando el cómputo de IA a Redis Queues.
- **RNF-03 (Seguridad de Consultas):** El motor Text-to-SQL debe conectarse exclusivamente a través de un usuario de PostgreSQL con permisos `GRANT SELECT`, denegando cualquier comando DDL/DML de modificación.
- **RNF-04 (Trazabilidad e Inmutabilidad):** Todo cambio de estado de una transacción retenida debe quedar registrado en una bitácora inmutable con usuario, fecha, hora, IP y justificación.

---

## 2. Diagrama de Casos de Uso General (Mermaid)

```mermaid
flowchart LR
    subgraph Actores
        COM[Encargado de Compras]
        ALM[Jefe de Almacén]
        AUD[Auditor Interno]
        GER[Gerente General]
        IA((Motor IA Híbrido:\nOllama + Groq))
    end

    subgraph Sistema_ERP [Sistema ERP con Auditoría IA Vectorial]
        CU01(CU-01: Registrar Orden de Compra)
        CU02(CU-02: Recepcionar Mercadería Multimodal)
        CU03(CU-03: Auditar Transacción Vectorialmente)
        CU04(CU-04: Monitorear y Resolver Alertas en Vivo)
        CU05(CU-05: Consultar Métricas con Text-to-SQL)
        CU06(CU-06: Ejecutar Acciones con Function Calling)
    end

    COM --> CU01
    ALM --> CU02
    CU01 -.->|<<include>>| CU03
    CU02 -.->|<<include>>| CU03
    CU03 --> IA
    CU03 -.->|<<extend>> Alerta Crítica| CU04
    AUD --> CU04
    GER --> CU04
    GER --> CU05
    GER --> CU06
    AUD --> CU06
    CU05 --> IA
    CU06 --> IA
```

---

## 3. Especificación Expandida de Casos de Uso Críticos

### CU-01 & CU-03: Registro y Auditoría Vectorial de Orden de Compra
- **Actores:** Encargado de Compras (Primario), Motor IA (Secundario).
- **Precondiciones:** Sesión iniciada con rol `compras`; proveedor y productos vigentes en el catálogo.
- **Flujo Principal:**
  1. El usuario ingresa a *Órdenes de Compra*, selecciona el proveedor y agrega los ítems mediante el repeater maestro-detalle.
  2. Presiona *Guardar Orden*.
  3. El sistema guarda el registro con estado `EN_REVISION_IA` y libera la pantalla en menos de 200 ms.
  4. `PurchaseOrderObserver` despacha el trabajo `AnalyzeTransactionAnomalyJob` a la cola Redis.
  5. El Job concatena la cadena canónica y solicita el vector de 768 floats a Ollama local (`nomic-embed-text`).
  6. Se ejecuta la consulta en PostgreSQL con `pgvector` calculando la similitud de coseno y el $Z$-Score de cada producto.
  7. Si la similitud $\ge 0.80$ y $|Z| \le 2.0$, la orden pasa a estado `APROBADA` con riesgo `BAJO`.
- **Flujo Alternativo (Anomalía Crítica):**
  - 7a. Si la similitud $< 0.80$ o $|Z| > 2.0$, se marca riesgo `CRITICO` y estado `BLOQUEADO_POR_AUDITORIA`.
  - 7b. El Job invoca la API de Groq (`llama-3.3-70b-versatile`) con salida JSON estructurada.
  - 7c. Se guarda el dictamen en la columna `ai_audit_report` y se emite `AnomalyDetectedEvent` vía Laravel Reverb.
  - 7d. La pantalla de los directivos despliega la notificación en vivo con sonido y detalles del sobreprecio.

---

## 4. Diagrama de Secuencia del Proceso de Auditoría

```mermaid
sequenceDiagram
    autonumber
    actor Operador as Encargado de Compras
    participant Panel as FilamentPHP v3
    participant BD as PostgreSQL 16 (pgvector)
    participant Cola as Redis Queue (Job Asíncrono)
    participant Ollama as Ollama Local (nomic-embed-text)
    participant Groq as Groq Cloud (Llama 3.3 70B)
    participant Reverb as Laravel Reverb (WebSocket)
    actor Gerente as Dashboard Gerencial

    Operador->>Panel: Guarda Orden de Compra (Bs. 18.500)
    Panel->>BD: INSERT purchase_orders (status: EN_REVISION_IA)
    BD-->>Panel: ID generado (< 150ms)
    Panel->>Cola: Dispatch AnalyzeTransactionAnomalyJob
    Panel-->>Operador: Notificación: "Orden enviada a auditoría IA"
    
    Cola->>Ollama: POST /api/embeddings (Cadena Canónica)
    Ollama-->>Cola: Vector [768 floats] (~25ms)
    Cola->>BD: SELECT Coseno (<=>) & Z-Score de Precios
    BD-->>Cola: Similitud: 0.63 | Z-Score: +3.6 (Sobreprecio detectado)
    
    Cola->>Groq: Prompt estructurado solicitando Dictamen JSON
    Groq-->>Cola: JSON Dictamen (Riesgo CRITICO, Impacto: Bs. 3.450)
    Cola->>BD: UPDATE status=BLOQUEADO_POR_AUDITORIA, guarda vector y JSON
    Cola->>Reverb: Broadcast AnomalyDetectedEvent (audit-channel)
    Reverb-->>Gerente: Push WebSocket en vivo (Alerta roja + sonido)
```

---

## 5. Diagrama de Arquitectura de Base de Datos (ERD)

```mermaid
erDiagram
    USERS ||--o{ PURCHASE_ORDERS : registra
    USERS ||--o{ INVENTORY_MOVEMENTS : ejecuta
    USERS ||--o{ AUDIT_RESOLUTIONS : resuelve
    SUPPLIERS ||--o{ PURCHASE_ORDERS : provee
    CATEGORIES ||--o{ PRODUCTS : clasifica
    PRODUCTS ||--o{ PURCHASE_ORDER_ITEMS : contiene
    PRODUCTS ||--o{ INVENTORY_MOVEMENTS : afecta
    PURCHASE_ORDERS ||--|{ PURCHASE_ORDER_ITEMS : detalla
    PURCHASE_ORDERS ||--o{ AUDIT_RESOLUTIONS : genera

    SUPPLIERS {
        bigint id PK
        string nit UK
        string business_name
        string category_rubro
        decimal risk_score
        boolean is_active
    }

    PRODUCTS {
        bigint id PK
        bigint category_id FK
        string sku UK
        string name
        decimal current_stock
        decimal min_stock
        decimal weighted_avg_cost
        decimal std_dev_cost
    }

    PURCHASE_ORDERS {
        bigint id PK
        bigint supplier_id FK
        bigint user_id FK
        string order_number UK
        decimal total_amount
        string status
        string risk_level
        decimal cosine_similarity
        decimal max_z_score
        vector_768 embedding
        jsonb ai_audit_report
        timestamps created_at
    }

    PURCHASE_ORDER_ITEMS {
        bigint id PK
        bigint purchase_order_id FK
        bigint product_id FK
        decimal quantity
        decimal unit_price
        decimal historical_avg_price
        decimal item_z_score
        decimal subtotal
    }

    INVENTORY_MOVEMENTS {
        bigint id PK
        bigint product_id FK
        bigint user_id FK
        string movement_type
        decimal quantity
        string multimodal_transcript
        string invoice_photo_path
        string risk_level
        vector_768 embedding
        timestamps created_at
    }

    AUDIT_RESOLUTIONS {
        bigint id PK
        bigint purchase_order_id FK
        bigint auditor_user_id FK
        string decision
        text justification
        string signed_pdf_path
        timestamps created_at
    }
```
