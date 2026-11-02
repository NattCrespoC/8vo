---
name: saas-refactor-130
description: Refactorizar gradualmente ANDESTRE/saas-restaurant por responsabilidades con archivos de hasta 130 líneas, contratos conservados y pruebas de regresión. Usar al dividir páginas, componentes, handlers o lógica de negocio de este SaaS; no imponer su estructura a otros proyectos.
---

# Refactor incremental de ANDESTRE

Leer AGENTS.md y sus reglas de arquitectura, SQL, seguridad y calidad en el
checkout activo. Las instrucciones del usuario y esos contratos prevalecen.

## Flujo

1. Identificar el comportamiento solicitado y una responsabilidad modificable.
2. Escribir una regresión observable; comprobar el fallo esperado antes del cambio.
3. Separar presentación, estado/interacción, HTTP y reglas de datos sin crear
   interfaces, repositorios o capas vacías que no necesita el proyecto.
4. Reutilizar componentes existentes y tokens semánticos. Mantener errores visibles
   y estados de carga locales; no anunciar éxito antes de confirmar persistencia.
5. Contar líneas físicas incluyendo blancos y comentarios. Archivos nuevos hasta
   130 líneas; reducir el archivo grande tocado extrayendo responsabilidades.
   No comprimir JSX/SQL ni quitar pruebas para cumplir el límite.
6. Mantener transacciones, idempotencia, auditoría y aislamiento de empresas.
   SQL histórico aplicado no se modifica. Leer la skill de Supabase antes de SQL.
7. Probar permisos/dinero/stock con Auth/PostgreSQL reales en copias desechables.
   UI de escritorio y móvil, unitarias, typecheck y lint según cambio.
8. Ejecutar `node scripts/ci/limites.mjs` si existe y `git diff --check`.
9. Documentar evidencia, archivos reducidos, deuda restante y pasos de despliegue.

## Límites

No reescribir todo el sistema de una vez ni introducir frameworks por preferencia.
No sustituir pruebas de RLS y concurrencia por mocks. No borrar relaciones, fotos
ni auditoría cuando una baja es reversible. Preservar cambios ajenos y no tocar
bases remotas para validar un refactor local.
