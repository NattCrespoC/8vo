# Arquitectura por Componentes Modulares Desacoplados

Este directorio estructura la lógica de negocio del ERP en componentes independientes de alta cohesión y bajo acoplamiento, siguiendo las directrices de **Ingeniería de Software 2** y buenas prácticas de Laravel 11.

---

## 1. Módulos y Responsabilidades

```
app/Components/
├── Procurement/          # Adquisiciones, Gestión de Proveedores y Órdenes de Compra
├── Warehouse/            # Almacenes, Kardex Valorado e Ingesta Multimodal (Vision/Whisper)
├── AuditEngine/          # Motor de Auditoría Vectorial, pgvector (768d), Z-Score e Inferencia Groq
├── RealtimeAlerts/       # Notificaciones reactivas y WebSockets con Laravel Reverb (Puerto 8080)
├── SecurityRBAC/         # Control de acceso basado en roles con Spatie (4 Roles) y Trazabilidad
└── AnalyticsCopilot/     # Consultas Text-to-SQL seguras (Solo lectura) y Function Calling
```

---

## 2. Reglas de Interacción entre Componentes

1. **Aislamiento de Lógica:** Ningún componente accede a controladores HTTP ni vistas directamente. Toda interacción se realiza mediante interfaces tipadas (`Contracts`), objetos de transferencia de datos (`DTOs`) o eventos de dominio.
2. **Tipado Estricto:** Cada archivo PHP dentro de un componente inicia con `declare(strict_types=1);`.
3. **Cero Consultas N+1:** Las consultas complejas o de similitud matemática deben encapsularse dentro de clases de consulta (`Queries`) o Servicios especializados.
4. **Desacoplamiento Asíncrono:** Toda comunicación pesada (vectorización de texto en Ollama, dictámenes de Groq, despacho de WebSockets) se realiza mediante eventos y trabajos en colas Redis (`ShouldQueue`).
