# GEMINI.md - Reglas Maestras de Desarrollo y Arquitectura del Proyecto

Este archivo guía el comportamiento de los modelos Gemini y del asistente Antigravity en este espacio de trabajo.

## Principios Centrales
1. **Dominio:** Sistema ERP de Gestión de Compras e Inventarios con Auditoría IA Vectorial en Tiempo Real ("Andina S.R.L.").
2. **Stack Oficial:**
   - Backend: PHP 8.3+, Laravel 11, Livewire 3, FilamentPHP v3.
   - Base de Datos: PostgreSQL 16 con extensión `pgvector` e índices HNSW (768 dimensiones).
   - Colas y Cache: Redis.
   - WebSockets: Laravel Reverb.
   - IA Embeddings: Ollama local (`nomic-embed-text`).
   - IA Generativa / Multimodal: Groq API (`llama-3.3-70b-versatile`, `whisper-large-v3-turbo`, `llama-3.2-11b-vision-preview`).
   - Testing: PestPHP.
   - Roles y Permisos: Spatie Laravel Permission.
   - Reportes: DomPDF con códigos QR.

## Restricciones y Buenas Prácticas
- Strict Types en cada archivo PHP: `declare(strict_types=1);`.
- La lógica de negocio SIEMPRE debe residir en `app/Services/` o `app/Actions/`.
- No realizar llamadas bloqueantes a APIs externas en el hilo HTTP: usar Jobs despachados por Observers.
- Normalizar siempre la cadena canónica antes de solicitar el embedding.
- Proteger las consultas Text-to-SQL ejecutándolas con usuario de PostgreSQL con permisos `SELECT ONLY`.
- Documentar y justificar todo cambio frente a los estándares de Ingeniería de Software 2, Práctica Empresarial y Preparación y Evaluación de Proyectos.
