# PETIDENTITY — Plataforma Web de Reencuentro de Mascotas con IA Geodésica y Multicriterio

> **Propuesta de Grado 2:** Sistema web colaborativo para el registro, geolocalización y reencuentro de mascotas perdidas y encontradas mediante algoritmos de coincidencia inteligente y visión artificial.  
> **Caso de Estudio:** Red Ciudadana y Albergues de Rescate Animal de Cochabamba / Bolivia.  
> **Materias Vinculadas:** Ingeniería de Software 2, Práctica Empresarial, Preparación y Evaluación de Proyectos (PEP).

---

## 🐾 Descripción del Proyecto

**PETIDENTITY** es una solución integral orientada a erradicar la dispersión de avisos de mascotas perdidas en redes sociales. La plataforma unifica en un solo mapa interactivo en tiempo real todos los reportes de animales extraviados y rescatados, aplicando un motor algorítmico multicriterio (`PetMatcherService`) que combina:

1. **Distancia Ortodrómica de Haversine:** Cálculo geodésico exacto sobre la superficie terrestre con función de decaimiento lineal ($0$ a $30\text{ puntos}$ según cercanía dentro de un radio urbano de $15\text{ km}$).
2. **Scoring Fenotípico Estricto:** Evaluación taxonómica de especie (filtro excluyente), tamaño, afinidad de raza y coincidencia cromática de colores primario y secundario ($0$ a $70\text{ puntos}$).
3. **Puntaje Total Ponderado:** Determina compatibilidad de $0\%$ a $100\%$, categorizando en insignias automáticas de **ALTA COINCIDENCIA** ($\ge 80\%$), **MEDIA** ($50-79\%$) o **BAJA**.
4. **Geolocalización Libre y Abierta:** Integración de mapas con **Leaflet.js + OpenStreetMap**, prescindiendo de los altos costos y cuotas de Google Maps API.
5. **Canal de Rescate Inmediato:** Conexión directa uno a uno entre quien perdió y quien encontró a la mascota mediante botón preconfigurado de **WhatsApp**.

---

## 🏛️ Arquitectura y Tecnologías

- **Framework Web:** Laravel 11.x (PHP 8.3+ / 8.4) con tipado estricto `declare(strict_types=1);`.
- **Frontend Responsivo:** Blade Templates + Tailwind CSS + Alpine.js + Leaflet.js 1.9.4.
- **Base de Datos:** PostgreSQL 16 / 17 (o SQLite en memoria para tests).
- **Contenedores:** Podman / Podman Compose (sin colisión de puertos: App `8081`, DB `5433`, Redis `6380`).
- **Pruebas Automatizadas:** PestPHP 2.x (12 pruebas unitarias y de integración pasando al 100%).

---

## 🚀 Puesta en Marcha Rápida (Comandos Listos para Copiar y Pegar)

### Opción A: Ejecución Local en Host (Recomendada para Demo Instantánea)

```powershell
# 1. Navegar al directorio de PETIDENTITY
cd "..\petidentity"

# 2. Configurar entorno y base de datos
php artisan migrate:fresh --seed

# 3. Vincular almacenamiento público de fotos
php artisan storage:link

# 4. Levantar servidor en el puerto 8081 (evita colisión con Propuesta 1)
php artisan serve --port=8081
```

Acceder en el navegador a: **[http://localhost:8081](http://localhost:8081)**

---

### Opción B: Ejecución Contenedorizada con Podman Compose

```powershell
# 1. Navegar al directorio
cd "..\petidentity"

# 2. Levantar servicios aislados (PostgreSQL en puerto 5433, Redis en puerto 6380)
podman-compose up -d

# 3. Verificar salud de los contenedores
podman ps
```

---

## 🧪 Ejecución de Pruebas Automatizadas (PestPHP)

PETIDENTITY cuenta con una suite completa de pruebas unitarias y de integración:

```powershell
cd "..\petidentity"
php artisan test
```

### Resultados de la Suite de Pruebas:
```text
   PASS  Tests\Unit\PetMatcherServiceTest
  ✓ it calculates exact Haversine distance between two coordinates in Cochabamba
  ✓ it calculates distance score with linear decay up to 15km
  ✓ it returns 0 phenotype score and 0 total score for different species
  ✓ it calculates max phenotype score for identical characteristics
  ✓ it evaluates match yielding high confidence for identical breed and nearby location

   PASS  Tests\Feature\PetReportTest
  ✓ it displays the public gallery with reports and active counters
  ✓ it filters pet reports by species and status
  ✓ it validates required fields when publishing a pet report
  ✓ it creates a pet report with photo and executes automated matching
  ✓ it displays pet report detail page with map and matching recommendations
  ✓ it allows marking a report as resolved/recuperado
  ✓ it returns geo-pins json for leaflet map rendering

  Tests:    12 passed (47 assertions)
  Duration: 0.84s
```

---

## 🌐 URLs Principales de Demostración

| Vista / Módulo | URL Directa | Descripción |
| :--- | :--- | :--- |
| **Galería y Mapa General** | [http://localhost:8081](http://localhost:8081) | Dashboard público, filtros reactivos y mapa con pines rojos (perdidas) y azules (encontradas). |
| **Reportar Mascota Perdida** | [http://localhost:8081/reportar?type=PERDIDA](http://localhost:8081/reportar?type=PERDIDA) | Formulario con selector interactivo de coordenadas en mapa Leaflet y subida de foto. |
| **Reportar Mascota Encontrada** | [http://localhost:8081/reportar?type=ENCONTRADA](http://localhost:8081/reportar?type=ENCONTRADA) | Registro de animales rescatados o avistados en la vía pública. |
| **Ficha de Rocky (Demostración IA)** | [http://localhost:8081/reportes/1](http://localhost:8081/reportes/1) | **Caso de prueba estelar:** Perro Cocker perdido en Plaza Colón con coincidencia del **94.38%** detectada a 310 metros. |
| **Ficha de Mishka (Gata Siamesa)** | [http://localhost:8081/reportes/3](http://localhost:8081/reportes/3) | Coincidencia felina del **93.92%** a 540 metros entre Parque Lincoln y Av. América. |
| **Mis Reportes** | [http://localhost:8081/mis-reportes](http://localhost:8081/mis-reportes) | Panel de administración y marcado de caso como `RECUPERADO`. |
| **API Geo-Pins Leaflet (JSON)** | [http://localhost:8081/api/pet-reports/geo-pins](http://localhost:8081/api/pet-reports/geo-pins) | Endpoint JSON consumido por el mapa para renderizar marcadores interactivos. |

---

## 👥 Credenciales de Prueba (Sembradas en Base de Datos)

| Rol / Tipo de Usuario | Correo Electrónico | Contraseña | Propósito |
| :--- | :--- | :--- | :--- |
| **Administrador General** | `admin@petidentity.org` | `secret` | Gestión global y métricas del sistema. |
| **Albergue San Roque** | `rescate@petidentity.org` | `secret` | Registro masivo de animales rescatados. |
| **Ciudadano / Tutor** | `ciudadano@petidentity.org` | `secret` | Publicación de reportes de pérdida. |

---

## 📚 Documentación Académica Detallada

La carpeta `docs/` contiene el marco conceptual y metodológico completo:
- [`01_formulacion_y_caso_de_estudio.md`](docs/01_formulacion_y_caso_de_estudio.md): Planteamiento, 3 problemas medibles (tiempo, cantidad, costo) y diagrama de Ishikawa.
- [`02_ingenieria_de_software_y_casos_de_uso.md`](docs/02_ingenieria_de_software_y_casos_de_uso.md): Requerimientos RF/RNF, catálogo CU-01 a CU-07, diagrama C4 de Podman y modelo entidad-relación.
- [`03_algoritmo_matematico_y_pipeline_ia.md`](docs/03_algoritmo_matematico_y_pipeline_ia.md): Fórmulas de Haversine, función de decaimiento geodésico y pipeline de visión artificial.
- [`04_estudio_financiero_y_evaluacion_pep.md`](docs/04_estudio_financiero_y_evaluacion_pep.md): Evaluación financiera PEP: VAN al 12% = **+Bs. 45.235,25**, TIR = **84,3%**, B/C = **2,37**, Payback = **13 meses**.
- [`05_metodologia_y_plan_iteraciones.md`](docs/05_metodologia_y_plan_iteraciones.md): Metodología Scrum Híbrido + XP, protocolo TDD y desglose diario de tareas.

---

## ⚖️ Comparativa y Selección entre Ambas Propuestas

Ambos proyectos están configurados para correr **en simultáneo** sin conflictos:

| Criterio | Propuesta 1 (Andina ERP) | Propuesta 2 (PETIDENTITY) |
| :--- | :--- | :--- |
| **Directorio de Trabajo** | `PROYECTOS FINALES\8vo` | `PROYECTOS FINALES\petidentity` |
| **Rama Git** | `main` | `feat/propuesta-petidentity` |
| **Puerto Web** | `http://localhost:8000/admin` | `http://localhost:8081` |
| **Puerto Base de Datos** | `5432` (`auditoria_ia`) | `5433` o `5432` (`petidentity_db`) |
| **Tecnología Diferencial** | pgvector 768d + Ollama + Reverb | Haversine + Scoring Multicriterio + Leaflet OSM |
| **Impacto Principal** | Detección de fraude y sobreprecio | Reencuentro comunitario y bienestar animal |

### Guía para Congelar o Adoptar una Propuesta:

- **Si se aprueba la Propuesta 1 (Andina ERP):**
  Continuar trabajando en el directorio original `8vo` en la rama `main`. La rama `feat/propuesta-petidentity` y el directorio `petidentity` quedan congelados como respaldo.
- **Si se aprueba la Propuesta 2 (PETIDENTITY):**
  Fusionar `feat/propuesta-petidentity` hacia `main` (`git checkout main && git merge feat/propuesta-petidentity`) o continuar el desarrollo en el directorio `petidentity`.
