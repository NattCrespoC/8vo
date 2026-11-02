# 01. Formulación del Problema y Caso de Estudio

## Título del Proyecto
**Sistema web de gestión de adquisiciones e inventarios para la auditoría operativa en tiempo real mediante detección de anomalías con vectores matemáticos e inteligencia artificial.**  
*Caso de estudio:* Importadora y Distribuidora Comercial "Andina S.R.L."

---

## 1. Introducción
La gestión de compras y el control de inventarios constituyen la columna vertebral financiera de las empresas comercializadoras y distribuidoras en el mercado boliviano. La rentabilidad y viabilidad de estas organizaciones depende directamente de la eficiencia en el flujo de mercaderías, la exactitud de los costos ponderados y la transparencia en las negociaciones con proveedores. No obstante, el incremento acelerado en el volumen de operaciones comerciales ha desbordado los controles tradicionales basados en conciliaciones manuales de fin de mes. Errores de tipeo involuntarios, sobreprecios no autorizados, desvíos de inventario y fraccionamiento intencional de órdenes de compra suelen descubrirse entre 15 y 30 días después de haberse pagado los fondos, cuando la recuperación del capital ya resulta inviable.

El presente proyecto plantea el desarrollo e implementación de una plataforma web empresarial basada en el framework **Laravel 11**, integrada con una arquitectura orientada a eventos, bases de datos vectoriales en **PostgreSQL (pgvector)** e inteligencia artificial de alto rendimiento. Cada movimiento transaccional es auditado preventivamente en milisegundos mediante representaciones vectoriales multidimensionales y cálculos de desviación típica estandarizada ($Z$-Score). Este proyecto articula de forma sinérgica la rigurosidad metodológica de la **Ingeniería de Software 2**, el impacto operativo directo exigido en **Práctica Empresarial**, y la demostración de rentabilidad financiera requerida en **Preparación y Evaluación de Proyectos (PEP)**.

---

## 2. Antecedentes
En el contexto empresarial nacional, las pequeñas y medianas empresas comerciales han adoptado paulatinamente hojas de cálculo y sistemas contables tradicionales con enfoque transaccional pasivo (CRUD). Si bien estas herramientas permiten registrar entradas y salidas de inventario, carecen por completo de mecanismos analíticos autónomos capaces de fiscalizar la coherencia estadística y semántica de los datos en el momento exacto de su captura. La labor de fiscalización recae exclusivamente en auditorías humanas bimestrales o semestrales, permitiendo que anomalías recurrentes desgasten el flujo de caja.

En el caso específico de la **Importadora y Distribuidora Comercial "Andina S.R.L."**, el crecimiento transaccional sostenido de las últimas dos gestiones ha evidenciado las limitaciones estructurales de su control interno: se han registrado desfases persistentes entre el kardex físico y el valorado, compras recurrentes de emergencia a proveedores con precios alterados y una alta sobrecarga operativa en el personal administrativo.

---

## 3. Formulación del Problema (Tres Problemas Medibles y Narrados)

### 1. Problema Medible en Tiempo (Demora en la Detección y Auditoría)
En el flujo operativo vigente de la empresa, el cotejo y conciliación entre órdenes de compra, notas de remisión de almacén y facturas físicas de proveedores se ejecuta de forma manual una vez concluido el ciclo mensual. Este procedimiento manual demanda un promedio de **48 horas-hombre mensuales** por parte del personal de contabilidad y administración, y provoca que cualquier irregularidad —como el ingreso de ítems con sobreprecios no autorizados o retiros de stock sin respaldo— tarde entre **15 y 30 días calendario** en ser descubierta, momento en el que los cheques y transferencias ya fueron consolidados.

### 2. Problema Medible en Cantidad (Volumen de Registros con Inconsistencias)
Durante el último semestre de operación comercial, de un promedio mensual de **1.200 transacciones operativas** (entre compras locales, reposiciones y bajas de almacén), aproximadamente el **14% de los registros (168 transacciones por mes)** presentó inconsistencias operativas no advertidas en el momento de la carga. Entre estas irregularidades destacan facturas duplicadas con ligeras variantes en su numeración, fraccionamiento artificial de pedidos para no superar el límite de autorización gerencial, y salidas de almacén fuera del horario laboral.

### 3. Problema Medible en Costo (Impacto Económico y Pérdida Financiera)
La carencia de un filtro computacional en tiempo real que valide la coherencia de costos frente al histórico genera pérdidas financieras directas estimadas en **Bs. 8.500 mensuales** (equivalentes a **Bs. 102.000 anuales**). Estas pérdidas provienen de sobreprecios pagados frente al costo promedio ponderado, adquisiciones innecesarias de productos con sobrestock y mermas no justificadas. Esta fuga constante de capital reduce el margen neto de utilidad operativa en un **6,5%**, debilitando la liquidez del negocio.

---

## 4. Pregunta de Investigación Central
> *¿De qué manera el desarrollo de un sistema web con detección de anomalías basada en vectores matemáticos e inteligencia artificial permitirá reducir los tiempos de auditoría operativa, disminuir la cantidad de transacciones irregulares y mitigar las pérdidas financieras en el área de adquisiciones e inventarios de la empresa "Andina S.R.L."?*

---

## 5. Diagrama de Ishikawa (Causa - Efecto)

```text
[METODOLOGÍA]                 [TECNOLOGÍA]                     [PERSONAL]
Auditorías manuales          Sistema actual sin validación    Sobrecarga operativa
post-mortem (cada 30 días)   estadística ni vectorial         y errores de digitación
         \                           \                              /
          \                           \                            /
           v                           v                          v
===========================================================================> [PROBLEMA CENTRAL]
           ^                           ^                          ^          Alta incidencia de pérdidas
          /                           /                            \         financieras y demora en la
         /                           /                              \        detección de anomalías en
Sin alertas en vivo ni      Costos no indexados a la         Falta de trazabilidad   compras e inventarios
umbrales de desvío Z-Score  historia del proveedor           en tiempo real (RBAC)   de "Andina S.R.L."
[CONTROL INTERNO]           [DATOS / INSUMOS]                [PROCESOS]
```

---

## 6. Relación de Causa y Efecto (Árbol de Problemas)

| Código | Causa Raíz (Raíces del Árbol) | Problema Central (Tronco) | Efecto Directo y Medible (Ramas/Hojas) |
| :---: | :--- | :--- | :--- |
| **C1** | Registro de compras y kardex sin validación matemática ni contextual frente al historial. | Deficiente control y detección tardía de anomalías operativas en adquisiciones e inventarios. | **E1:** Pérdida financiera directa de **Bs. 8.500 mensuales** por sobreprecios y compras duplicadas. |
| **C2** | Revisión documental manual diferida al cierre mensual sin alertas en tiempo real. | Deficiente control y detección tardía de anomalías operativas en adquisiciones e inventarios. | **E2:** Inversión improductiva de **48 horas-hombre/mes** y desfase de **15 a 30 días** en detectar desvíos. |
| **C3** | Falta de trazabilidad automatizada sobre roles, horarios y patrones de comportamiento. | Deficiente control y detección tardía de anomalías operativas en adquisiciones e inventarios. | **E3:** Existencia de un **14% de transacciones mensuales (168 registros)** con inconsistencias operativas. |

---

## 7. Objetivos del Proyecto

### Objetivo General
**Desarrollar** un sistema web de gestión de adquisiciones e inventarios para la auditoría operativa en tiempo real mediante detección de anomalías con vectores matemáticos e inteligencia artificial en la Importadora y Distribuidora Comercial "Andina S.R.L.".

### Objetivos Específicos y Acciones
*(Regla de Ingeniería de Software: Ningún objetivo específico repite el verbo "Desarrollar" y todos los verbos son únicos).*

1. **Diagnosticar** los procesos actuales de compras, control de almacenes y criterios de auditoría interna de la empresa para establecer los parámetros cuantitativos y semánticos de normalidad transaccional.
   - *Acción 1.1:* Realizar el relevamiento de información mediante entrevistas estructuradas y análisis de órdenes de compra y kardex históricos.
   - *Acción 1.2:* Identificar las variables críticas de riesgo (precios unitarios, volúmenes, horarios y márgenes de tolerancia) para calibrar las reglas de negocio.
   - *Acción 1.3:* Construir un dataset base anonimizado de transacciones históricas para calibrar los umbrales de similitud de coseno y desviación estándar.

2. **Diseñar** la arquitectura de software, el esquema relacional-vectorial de base de datos y los flujos de comunicación asíncrona siguiendo estándares formales de ingeniería.
   - *Acción 2.1:* Elaborar diagramas C4, casos de uso, diagramas de secuencia y el modelo entidad-relación extendido con soporte para vectores de 768 dimensiones en PostgreSQL (pgvector).
   - *Acción 2.2:* Definir la estructura de navegación de FilamentPHP, matriz RBAC (Spatie) y componentes de interfaz para visualización de alertas.
   - *Acción 2.3:* Especificar los contratos de datos (JSON Schema) para la interacción entre Laravel Queues, Ollama y Groq API.

3. **Implementar** los módulos transaccionales del sistema, el motor matemático de detección de anomalías y el canal de notificaciones reactivas utilizando Laravel 11.
   - *Acción 3.1:* Programar los módulos CRUD de proveedores, catálogo de productos, órdenes de compra maestro-detalle y movimientos de almacén en FilamentPHP v3.
   - *Acción 3.2:* Codificar el pipeline de vectorización asíncrona con Ollama (`nomic-embed-text`), el cálculo SQL de distancia de coseno y $Z$-Score en PostgreSQL, y la generación de dictámenes con Groq API.
   - *Acción 3.3:* Integrar Laravel Reverb y Redis para transmitir alertas instantáneas en vivo a los tableros gerenciales y aplicar retenciones preventivas automáticas.

4. **Evaluar** el desempeño técnico del software mediante pruebas automatizadas y determinar la viabilidad económico-financiera del proyecto bajo escenarios de inversión y ahorro operativo.
   - *Acción 4.1:* Ejecutar pruebas unitarias y de integración con PestPHP para asegurar que la latencia total del pipeline de auditoría se mantenga por debajo de los 2.000 ms.
   - *Acción 4.2:* Medir la precisión y exhaustividad (Precision / Recall) del algoritmo inyectando transacciones de prueba con fraudes y sobreprecios simulados.
   - *Acción 4.3:* Construir el flujo de caja proyectado a 5 años y calcular VAN, TIR, Beneficio/Costo y Periodo de Recuperación en función del ahorro proyectado de Bs. 102.000 anuales.
