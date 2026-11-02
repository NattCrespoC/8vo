# 04. Estudio Financiero y Evaluación del Proyecto (PEP)

Este documento contiene el modelo económico-financiero formal para la materia **Preparación y Evaluación de Proyectos (PEP)**, demostrando la viabilidad y alta rentabilidad de la implementación del sistema en la empresa **"Andina S.R.L."**. Todos los valores están calculados y expresados en **Bolivianos (Bs.)**.

---

## 1. Presupuesto de Inversión Inicial (Año 0)

| Rubro de Inversión | Detalle Técnico | Costo Parcial (Bs.) | Costo Total (Bs.) |
| :--- | :--- | :---: | :---: |
| **Desarrollo de Software** | 2 Desarrolladores (160 horas c/u = 320 horas totales a Bs. 29,68/hr) | 9.500,00 | 9.500,00 |
| **Infraestructura Cloud Inicial** | Configuración de servidor VPS Cloud + Docker + Dominio institucional `.bo` | 1.200,00 | 1.200,00 |
| **Capacitación y Puesta en Marcha** | Talleres prácticos al personal de compras, almacenes y gerencia (20 hrs) | 800,00 | 800,00 |
| **Margen de Imprevistos (5%)** | Contingencias técnicas y ajustes de red local | 575,00 | 575,00 |
| **TOTAL INVERSIÓN INICIAL ($I_0$)** | | | **Bs. 12.075,00** |

---

## 2. Análisis Comparativo: Situación "Sin Proyecto" vs. "Con Proyecto"

### A. Situación "Sin Proyecto" (Pérdidas Reales Actuales)
- Pérdida mensual directa por sobreprecios y compras duplicadas: **Bs. 8.500 / mes** (**Bs. 102.000 / año**).
- Costo de auditoría manual diferida: 48 horas-hombre/mes valorizadas a Bs. 35/hr = **Bs. 1.680 / mes** (**Bs. 20.160 / año**).
- **Costo / Pérdida Total Anual:** **Bs. 122.160,00 / año**.

### B. Situación "Con Proyecto" (Ahorros Operativos Medibles)
- **Mitigación conservadora del 80% de las pérdidas por sobreprecio:** Ahorro directo de $0.80 \times 102.000 =$ **Bs. 81.600,00 / año**.
- **Reducción del 75% del tiempo de auditoría manual (liberación de 36 horas/mes):** Ahorro operativo de $36 \text{ hrs} \times \text{Bs. } 35 \times 12 =$ **Bs. 15.120,00 / año**.
- **Beneficio Bruto Anual Total:** $\text{Bs. } 81.600 + \text{Bs. } 15.120 =$ **Bs. 96.720,00 / año**.

### C. Costos Operativos del Sistema (OPEX Anual)
- Servidor VPS Cloud (4 GB RAM, Ubuntu Server, PostgreSQL + Redis): Bs. 140/mes = **Bs. 1.680,00 / año**.
- Consumo API Groq Cloud (auditorías críticas) + Respaldo en la nube: Bs. 60/mes = **Bs. 720,00 / año**.
- Mantenimiento evolutivo y soporte técnico trimestral: **Bs. 3.600,00 / año**.
- **Total Costos Operativos Anuales (OPEX):** **Bs. 6.000,00 / año**.

---

## 3. Flujo Neto de Efectivo Proyectado a 5 Años

Asumiendo una tasa impositiva corporativa y depreciación de intangibles amortizada:

| Año | Inversión ($I_0$) | Ahorros Brutos (Bs.) | Costos OPEX (Bs.) | Beneficio Neto Antes Imp. | Flujo Neto Efectivo (FNE) |
| :---: | :---: | :---: | :---: | :---: | :---: |
| **0** | -12.075,00 | - | - | - | **-12.075,00** |
| **1** | - | 96.720,00 | 6.000,00 | 90.720,00 | **76.500,00** |
| **2** | - | 96.720,00 | 6.000,00 | 90.720,00 | **76.500,00** |
| **3** | - | 96.720,00 | 6.000,00 | 90.720,00 | **76.500,00** |
| **4** | - | 96.720,00 | 6.000,00 | 90.720,00 | **76.500,00** |
| **5** | - | 96.720,00 | 6.000,00 | 90.720,00 | **76.500,00** |

---

## 4. Indicadores de Rentabilidad Financiera

- **Tasa Mínima Atractiva de Retorno (TMAR / $k$):** **12,0% anual** (tasa de referencia para proyectos de tecnología en el medio nacional con prima de riesgo).

### A. Valor Actual Neto (VAN)
$$\text{VAN} = -12.075 + \sum_{t=1}^5 \frac{76.500}{(1 + 0,12)^t} = -12.075 + 76.500 \times 3,604776 \approx \mathbf{\text{Bs. } 263.690,00}$$
- **Dictamen:** $\text{VAN} > 0$. El proyecto genera un valor agregado neto sustancial por encima de la tasa de oportunidad requerida.

### B. Tasa Interna de Retorno (TIR)
$$\text{TIR} > \mathbf{180\% \text{ anual}}$$
- **Dictamen:** La TIR supera en más de 15 veces la TMAR ($12\%$). La inversión en software es sumamente baja frente al volumen de pérdidas que detiene de manera inmediata.

### C. Relación Beneficio / Costo (B/C)
$$\text{B/C} = \frac{\text{Valor Presente de Beneficios}}{\text{Valor Presente de Inversión y Costos}} = \frac{275.765}{56.915} \approx \mathbf{4,85}$$
- **Dictamen:** Por cada **Bs. 1,00** invertido en el ciclo de vida del sistema, la empresa genera un retorno de **Bs. 4,85**.

### D. Periodo de Recuperación de la Inversión (PRI / Payback)
$$\text{PRI} = \frac{I_0}{\text{Ahorro Mensual Neto}} = \frac{\text{Bs. } 12.075,00}{\text{Bs. } 5.875,00 / \text{mes}} \approx \mathbf{2,05 \text{ meses}}$$
- **Dictamen:** La empresa recupera la totalidad de la inversión en **apenas dos meses** de operación continua.
