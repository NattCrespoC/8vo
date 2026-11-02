---
name: pgvector-anomaly-detection
description: "Mathematical modeling, PostgreSQL pgvector configuration, HNSW indexing, cosine distance calculations, and Z-Score anomaly detection algorithms for transaction auditing. Use when writing migrations with vector columns, optimizing vector queries, calculating statistical deviations, or tuning similarity thresholds."
---

# pgvector Anomaly Detection Skill

This skill explains how to implement, optimize, and test vector-based mathematical anomaly detection in PostgreSQL 16 using `pgvector` and standard statistical metrics.

## 1. Mathematical Foundation

### A. Distancia de Coseno y Similitud Vectorial
Compara el ángulo entre el vector de la transacción actual ($A$) y los vectores históricos de compras del mismo proveedor o categoría ($B$):

$$\text{Similitud de Coseno} = \cos(\theta) = \frac{A \cdot B}{\|A\| \|B\|} = \frac{\sum_{i=1}^n A_i B_i}{\sqrt{\sum_{i=1}^n A_i^2} \sqrt{\sum_{i=1}^n B_i^2}}$$

En `pgvector`, el operador `<=>` calcula la **distancia de coseno** ($1 - \text{similitud}$):
- Distancia $\approx 0.0 \implies$ Transacción idéntica al patrón histórico habitual.
- Distancia $> 0.20 \implies$ Similitud $< 0.80$, posible desvío o anomalía semántica.

### B. Índice Z-Score (Desviación Típica Estandarizada)
Mide a cuántas desviaciones estándar ($\sigma$) se encuentra el precio unitario ($x$) respecto a la media histórica ponderada ($\mu$):

$$Z = \frac{x - \mu}{\sigma}$$

- $|Z| \le 1.5$: Precio normal dentro de la fluctuación aceptable.
- $1.5 < |Z| \le 2.2$: Desvío medio (alerta amarilla).
- $|Z| > 2.2$: Sobreprecio anómalo significativo (alerta naranja).
- $|Z| \ge 3.0$: Desvío extremo o fraude evidente (bloqueo automático).

---

## 2. Configuración en PostgreSQL 16 y Migraciones Laravel

### Habilitar la Extensión e Índices HNSW
El algoritmo **HNSW** (Hierarchical Navigable Small World) permite búsquedas vectoriales aproximadas en tiempo logarítmico ($< 5\text{ ms}$):

```php
// En la migración de purchase_orders:
DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

Schema::table('purchase_orders', function (Blueprint $table) {
    $table->decimal('cosine_similarity', 5, 4)->nullable();
    $table->decimal('max_z_score', 6, 2)->nullable();
    $table->string('risk_level', 15)->default('BAJO')->index();
    $table->jsonb('ai_audit_report')->nullable();
});

// Columna de 768 dimensiones para nomic-embed-text
DB::statement('ALTER TABLE purchase_orders ADD COLUMN embedding vector(768)');

// Índice HNSW optimizado para coseno
DB::statement('CREATE INDEX purchase_orders_embedding_idx ON purchase_orders USING hnsw (embedding vector_cosine_ops) WITH (m = 16, ef_construction = 64)');
```

---

## 3. Consulta de Similitud Vectorial en Eloquent / Query Builder

```php
public function calculateHistoricalSimilarity(int $supplierId, array $embeddingVector, int $excludeOrderId): float
{
    $vectorString = '[' . implode(',', $embeddingVector) . ']';

    $result = DB::selectOne("
        SELECT AVG(1 - (embedding <=> ?::vector)) as avg_similarity
        FROM (
            SELECT embedding
            FROM purchase_orders
            WHERE supplier_id = ?
              AND status = 'APROBADA'
              AND id != ?
              AND embedding IS NOT NULL
            ORDER BY created_at DESC
            LIMIT 50
        ) as historical_sample
    ", [$vectorString, $supplierId, $excludeOrderId]);

    return $result->avg_similarity !== null ? (float) $result->avg_similarity : 0.95;
}
```

---

## 4. Matriz de Clasificación Híbrida de Riesgo

| Condición Z-Score | Similitud de Coseno | Nivel de Riesgo | Acción del Sistema |
| :--- | :--- | :--- | :--- |
| $\|Z\| < 1.5$ | $\ge 0.85$ | `BAJO` | Aprobación automática silenciosa |
| $1.5 \le \|Z\| < 2.2$ | $0.80 \le \text{Sim} < 0.85$ | `MEDIO` | Aprobado con advertencia visual |
| $2.2 \le \|Z\| < 3.0$ | $0.72 \le \text{Sim} < 0.80$ | `ALTO` | Retención preventiva + Dictamen Groq |
| $\|Z\| \ge 3.0$ | $< 0.72$ | `CRITICO` | Bloqueo estricto + Alerta WebSocket Reverb |
