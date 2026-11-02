---
name: academic-project-evaluation
description: "Guidelines, templates, and mathematical formulas for academic project defense in 8th semester: Preparación y Evaluación de Proyectos (PEP), Ingeniería de Software 2 (SOFT 2), and Práctica Empresarial (PE). Use when drafting thesis chapters, calculating financial indicators (NPV/VAN, IRR/TIR, B/C, Payback), designing Ishikawa/problem trees, or preparing defense presentations."
---

# Academic Project Evaluation Skill (PEP, SOFT 2, PE)

This skill provides the academic frameworks, calculation models, and documentation templates required for an 8th-semester engineering project defense.

---

## 1. Mapeo de Entregables por Materia

| Materia | Criterio Central de Evaluación | Entregables del Proyecto |
| :--- | :--- | :--- |
| **Práctica Empresarial (PE)** | Solución real y medible en una empresa del medio. | - Caso de estudio: Importadora "Andina S.R.L."<br>- Módulo de compras, almacén y kardex.<br>- Reportes ejecutivos de auditoría en PDF con firma y QR. |
| **Ingeniería de Software 2 (SOFT 2)** | Arquitectura limpia, pruebas automatizadas, patrones y trazabilidad. | - Diagramas C4, UML (Casos de uso, Secuencia, ERD).<br>- Arquitectura en capas (Services, Actions, Observers, Jobs).<br>- Suite de pruebas con PestPHP.<br>- Control de versiones colaborativo en Git. |
| **Prep. y Eval. de Proyectos (PEP)** | Estudio de mercado, técnico, legal, organizacional y evaluación financiera rigurosa. | - Presupuesto de inversión inicial ($I_0$).<br>- Flujo de caja proyectado a 5 años.<br>- Indicadores: VAN, TIR, Beneficio/Costo, Periodo de Recuperación. |

---

## 2. Fórmulas Financieras de PEP (Expresadas en Bolivianos - Bs.)

### A. Valor Actual Neto (VAN / NPV)
$$\text{VAN} = -I_0 + \sum_{t=1}^n \frac{\text{FNE}_t}{(1 + k)^t}$$
- $I_0 = \text{Bs. } 12.075,00$ (Inversión inicial)
- $k = 12\%$ (Tasa de Descuento / TMAR bancaria y de riesgo de mercado en Bolivia)
- $\text{FNE}_t \approx \text{Bs. } 76.500,00$ anual (Flujo Neto de Efectivo por ahorros operativos)
- **Resultado:** $\text{VAN} \approx \text{Bs. } 263.680,00 > 0 \implies \text{Proyecto Altamente Viable y Rentable}$.

### B. Tasa Interna de Retorno (TIR / IRR)
Tasa $r$ para la cual $\text{VAN} = 0$:
$$0 = -I_0 + \sum_{t=1}^n \frac{\text{FNE}_t}{(1 + \text{TIR})^t}$$
- **Resultado:** $\text{TIR} > 180\%$, superando ampliamente el costo de oportunidad del capital (12%).

### C. Relación Beneficio / Costo (B/C)
$$\text{B/C} = \frac{\text{Valor Presente de Ingresos / Ahorros}}{\text{Valor Presente de Costos e Inversión}} \approx 4,85$$
- Por cada 1 Bs. invertido y operado, la empresa recupera y ahorra Bs. 4,85.

### D. Periodo de Recuperación de la Inversión (PRI / Payback)
$$\text{PRI} = \frac{I_0}{\text{Ahorro Mensual Neto}} = \frac{12.075}{5.750} \approx 2,1 \text{ meses}$$

---

## 3. Matriz de Formulación del Problema (Regla de Oro: 3 Medibles y Narrados)
- **Problema de Tiempo:** 48 horas-hombre/mes invertidas en revisiones manuales diferidas; desvíos detectados con 15 a 30 días de desfase, imposibilitando la recuperación del dinero.
- **Problema de Cantidad:** 14% de las 1.200 transacciones mensuales (168 documentos/mes) presentan inconsistencias operativas (sobreprecios, duplicados o fraccionamiento de órdenes).
- **Problema de Costo:** Pérdida financiera directa acumulada de Bs. 8.500 mensuales (Bs. 102.000 anuales) por falta de control automatizado de costos ponderados y compras irregulares.
