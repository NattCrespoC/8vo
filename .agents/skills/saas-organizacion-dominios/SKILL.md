---
name: saas-organizacion-dominios
description: Organizar carpetas de ANDESTRE/saas-restaurant por responsabilidad al crear, mover o refactorizar componentes. Aplicar antes de añadir archivos a un dominio para evitar carpetas saturadas, duplicados y fragmentación artificial; no reorganizar proyectos ajenos sin su propio contrato.
---

# Organización por responsabilidad

Leer AGENTS.md y docs/REGLAS_IA/01_ARQUITECTURA.md del checkout. Esta guía es
obligatoria para cambios de estructura del SaaS; las instrucciones del usuario
prevalecen. No generar carpetas ni código hasta inspeccionar el árbol y sus imports.

## Regla para cada cambio

- Buscar primero una responsabilidad y un componente existente reutilizable.
- Agrupar por tarea del dominio: listado, edición, categorías, movimientos.
  Evitar carpetas universales `utils`, `misc`, `helpers` o una carpeta por archivo.
- Máximo 15 archivos fuente directos en una carpeta nueva de componentes. Si una
  carpeta heredada ya supera 15, no aumentar su número: mover un grupo coherente
  antes de añadir código. El límite cuenta .ts/.tsx/.js/.jsx, incluidos tests.
- Antes de extraer, explicar qué responsabilidad se separa y quién la consume.
  El límite de 130 líneas no autoriza decenas de wrappers ni duplicar estructuras.
- Mover archivos existentes, actualizar consumidores y borrar la ruta anterior;
  no dejar copias, reexports de compatibilidad ni `index.ts` por costumbre.
- Mantener las rutas públicas de Next. Los movimientos internos no son cambios
  de URL, permisos, esquema SQL ni autorización para limpiar datos.
- Conservar tests cerca de su responsabilidad. Ejecutar typecheck, regresiones
  afectadas y `node scripts/ci/limites.mjs` después de actualizar imports.
- Un traslado conserva la deuda de líneas del origen; no ampliarla. Documentar
  archivos grandes pendientes sin reescribirlos sólo para una reorganización.

## Evidencia de cierre

Mostrar árbol breve antes/después, número de archivos trasladados y checks reales.
No afirmar que todo el repositorio está ordenado al corregir sólo un dominio.
La comprobación automática de cantidad se aplica a src/components; el criterio
semántico de agrupación requiere revisión del cambio, no sólo contar archivos.
