# 03. Algoritmo Matemático y Pipeline de Inteligencia Artificial

---

## 1. Fundamentos Matemáticos de la Detección de Anomalías

Para que el proyecto mantenga una sólida base científica y aplicabilidad real en auditoría financiera, el sistema combina **geometría analítica vectorial multidimensional** con **estadística inferencial paramétrica**.

### A. Similitud y Distancia de Coseno en Espacios Vectoriales ($\mathbb{R}^{768}$)
Cada orden de compra o movimiento de almacén es mapeado a un vector de características en 768 dimensiones mediante el modelo `nomic-embed-text`:

$$\vec{A} = [a_1, a_2, \dots, a_{768}]$$

La **similitud de coseno** entre la transacción entrante ($\vec{A}$) y la transacción histórica promedio del proveedor ($\vec{B}$) se define como el coseno del ángulo formado por ambos vectores:

$$\text{Similitud}(\vec{A}, \vec{B}) = \cos(\theta) = \frac{\vec{A} \cdot \vec{B}}{\|\vec{A}\| \|\vec{B}\|} = \frac{\sum_{i=1}^{768} a_i b_i}{\sqrt{\sum_{i=1}^{768} a_i^2} \sqrt{\sum_{i=1}^{768} b_i^2}}$$

En **PostgreSQL (pgvector)**, la distancia de coseno se calcula nativamente mediante el operador `<=>`:

$$\text{Distancia}(\vec{A}, \vec{B}) = 1 - \text{Similitud}(\vec{A}, \vec{B})$$

- $\text{Distancia} \to 0$: Comportamiento transaccional coherente con el patrón histórico.
- $\text{Distancia} > 0.20 \implies \text{Similitud} < 0.80$: Anomalía contextual severa (combinación atípica de productos, proveedor inusual, rol no autorizado o volumen desproporcionado).

---

### B. Desviación Típica Estandarizada ($Z$-Score)
El $Z$-Score evalúa la probabilidad de que un precio unitario observado ($x$) pertenezca a la distribución de precios históricos del producto ($\mu$ con desviación típica $\sigma$):

$$Z = \frac{x - \mu}{\sigma}$$

Donde:
- $\mu = \text{Costo Promedio Ponderado (CPP)}$ registrado en el catálogo de productos.
- $\sigma = \text{Desviación Estándar Histórica}$ de las compras aprobadas de ese producto. Si $\sigma = 0$ (primeras compras), se asume una tolerancia base de $\sigma = 0.05 \times \mu$.

---

### C. Función de Puntuación de Anomalía Compuesta (Anomaly Score)
El sistema computa un índice de riesgo normalizado $\mathcal{R} \in [0, 1]$ ponderando la distancia geométrica y la dispersión estadística:

$$\mathcal{R} = w_1 \cdot \min\left(1.0, \frac{|Z|}{3.5}\right) + w_2 \cdot (1 - \text{Similitud})$$

Con pesos calibrados empíricamente:
- $w_1 = 0.65$ (Ponderación al desvío monetario directo del precio).
- $w_2 = 0.35$ (Ponderación al contexto semántico y relacional de la operación).

---

## 2. Plantilla Canónica de Normalización de Texto

Para que dos compras con datos similares produzcan vectores cercanos en el hiperespacio, se eliminan variaciones tipográficas mediante esta cadena canónica:

```text
TIPO:ORDEN_COMPRA|PROVEEDOR:8492019014|RUBRO:FERRETERIA_INDUSTRIAL|TOTAL:18500.00|HORA:14|DIA:2|ROL:compras|ITEMS:GUANT-IND-01:cant=200,pu=92.50;BOT-SEG-42:cant=30,pu=180.00
```

---

## 3. Optimización con Índices HNSW en PostgreSQL 16

Para realizar búsquedas de similitud en menos de **5 milisegundos**, la columna `embedding` se indexa con el algoritmo **HNSW (Hierarchical Navigable Small World)**:

```sql
CREATE EXTENSION IF NOT EXISTS vector;

-- Creación del índice HNSW para métrica de distancia de coseno
CREATE INDEX purchase_orders_embedding_hnsw_idx 
ON purchase_orders 
USING hnsw (embedding vector_cosine_ops) 
WITH (m = 16, ef_construction = 64);
```

- `m = 16`: Número máximo de conexiones bidireccionales por nodo en cada capa del grafo.
- `ef_construction = 64`: Tamaño dinámico de la lista de candidatos durante la construcción del índice.

---

## 4. Contrato JSON Schema Estricto para Groq API (`llama-3.3-70b-versatile`)

Cuando se dispara una anomalía crítica, Laravel invoca la API de Groq imponiendo validación estricta de salida:

```json
{
  "$schema": "http://json-schema.org/draft-07/schema#",
  "title": "DictamenAuditoriaIA",
  "type": "object",
  "properties": {
    "is_anomalous": {
      "type": "boolean"
    },
    "risk_level": {
      "type": "string",
      "enum": ["BAJO", "MEDIO", "ALTO", "CRITICO"]
    },
    "confidence_percentage": {
      "type": "number",
      "minimum": 0,
      "maximum": 100
    },
    "primary_reason": {
      "type": "string"
    },
    "financial_impact_bs": {
      "type": "number",
      "minimum": 0
    },
    "recommended_action": {
      "type": "string"
    }
  },
  "required": [
    "is_anomalous",
    "risk_level",
    "confidence_percentage",
    "primary_reason",
    "financial_impact_bs",
    "recommended_action"
  ]
}
```
