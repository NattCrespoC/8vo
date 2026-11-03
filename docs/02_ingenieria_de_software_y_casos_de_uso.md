# 02. Ingeniería de Software, Casos de Uso y Modelo de Datos - PETIDENTITY

---

## 1. Especificación de Requisitos del Sistema

### Requisitos Funcionales (RF)
- **RF-01 (Gestión de Cuentas y Contacto Directo):** El sistema debe permitir a los ciudadanos registrarse e iniciar sesión de manera segura, solicitando de forma obligatoria su número de teléfono o WhatsApp para facilitar el contacto inmediato en caso de coincidencias.
- **RF-02 (Registro de Mascota Perdida):** El sistema debe proveer un formulario para que los propietarios registren a su mascota extraviada, incluyendo: nombre, especie (perro, gato, otro), raza, tamaño (pequeño, mediano, grande), color principal, color secundario, señas particulares o collar, fecha del extravío y ubicación geográfica.
- **RF-03 (Registro de Mascota Encontrada):** El sistema debe ofrecer un formulario simplificado y rápido para que los transeúntes o rescatistas que encuentren un animal en la vía pública puedan reportarlo de inmediato, omitiendo el nombre y enfocándose en los rasgos físicos visibles y la ubicación exacta del hallazgo.
- **RF-04 (Carga y Almacenamiento de Fotografías):** El sistema debe admitir la subida de una o varias fotografías de la mascota en formatos estándar (JPG, PNG, WebP) con validación de peso y enlace público accesible (`storage:link`).
- **RF-05 (Geolocalización Interactiva con Leaflet.js):** El sistema debe integrar un mapa interactivo basado en OpenStreetMap y Leaflet.js sin requerir claves de API de pago, permitiendo al usuario seleccionar con un solo clic o arrastre las coordenadas exactas (`latitude` y `longitude`) y la zona/barrio textual.
- **RF-06 (Muro Público y Filtros de Búsqueda):** El sistema debe proveer una galería pública interactiva que permita filtrar los reportes activos por tipo (`Perdidas`, `Encontradas`, `Todas`), especie, tamaño y color, además de un mapa general con marcadores codificados por color (rojo/naranja para perdidas, verde/azul para encontradas).
- **RF-07 (Motor de Coincidencias Inteligentes - `PetMatcherService`):** Cada vez que se registre o consulte un reporte, el sistema debe cruzar automáticamente los datos contra los reportes activos del tipo opuesto, calculando un porcentaje de afinidad (`match_score` de 0% a 100%) combinando distancia geográfica (Haversine) y compatibilidad de rasgos físicos.
- **RF-08 (Ficha de Detalle y Enlace de Contacto WhatsApp):** En la vista de detalle de cada mascota, el sistema debe mostrar su ficha completa, su ubicación en el mapa, un botón de contacto directo hacia WhatsApp con mensaje predeterminado y una sección destacada con las **"Posibles Coincidencias Detectadas por IA"** ordenadas por porcentaje de compatibilidad.

### Requisitos No Funcionales (RNF)
- **RNF-01 (Rendimiento y Latencia):** El cálculo de coincidencias y renderizado de la ficha de detalle no debe superar los **300 ms** para listas de hasta 1.000 reportes activos.
- **RNF-02 (Cero Costes de Licenciamiento en Mapas):** La solución de cartografía debe operar 100% sobre capas libres de OpenStreetMap y bibliotecas Open Source (Leaflet.js), sin depender de cuotas ni cobros de Google Maps API.
- **RNF-03 (Diseño Adaptativo / Mobile-First):** La interfaz construida en Blade y Tailwind CSS debe ser completamente responsiva, permitiendo una experiencia fluida desde smartphones tanto para rescatistas en la calle como para usuarios de escritorio.
- **RNF-04 (Seguridad e Integridad de Datos):** Las contraseñas deben estar cifradas mediante `bcrypt`, las operaciones con archivos deben sanitizar los nombres de imagen para evitar Path Traversal, y las consultas a base de datos deben utilizar sentencias preparadas mediante Eloquent ORM.

---

## 2. Catálogo Detallado de Casos de Uso

| Código | Caso de Uso | Actor Principal | Propósito / Descripción Breve |
| :--- | :--- | :--- | :--- |
| **CU-01** | Autenticación y Perfil | Ciudadano / Rescatista | Registro, inicio de sesión y gestión de número de WhatsApp para contacto. |
| **CU-02** | Reportar Mascota Perdida | Dueño de Mascota | Publicar reporte con foto, rasgos detallados y ubicación del extravío. |
| **CU-03** | Reportar Mascota Encontrada | Transeúnte / Albergue | Reporte ágil de animal avistado o rescatado en vía pública. |
| **CU-04** | Carga de Fotografías | Usuario Autenticado | Subir y asociar imágenes a los reportes para su reconocimiento visual. |
| **CU-05** | Marcado en Mapa Leaflet | Usuario Autenticado | Ubicar geográficamente el suceso mediante un pin interactivo arrastrable. |
| **CU-06** | Galería Pública y Filtros | Visitante / Ciudadano | Explorar reportes con filtros por estado, especie, color y mapa general. |
| **CU-07** | Detección de Coincidencias | Motor del Sistema | Evaluación automática de compatibilidad multicriterio entre reportes opuestos. |

---

## 3. Diagrama General de Casos de Uso (Mermaid)

```mermaid
flowchart LR
    subgraph Actores
        DUENO[Dueño de Mascota Perdida]
        RESC[Rescatista / Transeúnte]
        VIS[Visitante Público]
        SYS((Motor IA / PetMatcher))
    end

    subgraph PETIDENTITY_Platform [Plataforma PETIDENTITY]
        CU01(CU-01: Autenticación y Contacto)
        CU02(CU-02: Publicar Mascota Perdida)
        CU03(CU-03: Publicar Mascota Encontrada)
        CU04(CU-04: Cargar Fotografías)
        CU05(CU-05: Seleccionar Coordenadas en Mapa)
        CU06(CU-06: Explorar Muro y Filtros)
        CU07(CU-07: Evaluar Coincidencias Inteligentes)
    end

    DUENO --> CU01
    DUENO --> CU02
    RESC --> CU01
    RESC --> CU03
    VIS --> CU06

    CU02 -.->|<<include>>| CU04
    CU02 -.->|<<include>>| CU05
    CU03 -.->|<<include>>| CU04
    CU03 -.->|<<include>>| CU05

    CU02 -.->|<<trigger>>| CU07
    CU03 -.->|<<trigger>>| CU07
    CU07 --> SYS
```

---

## 4. Diagrama de Arquitectura de Contenedores (Podman)

```mermaid
flowchart TD
    subgraph Cliente ["Capa de Usuario (Frontend Responsivo)"]
        BrowserMobile["Navegador Móvil / Desktop (Tailwind CSS)"]
        LeafletMap["Leaflet.js + OpenStreetMap (Pines Interactivos)"]
        WhatsAppContact["Enlace Directo WhatsApp API"]
    end

    subgraph Podman_Stack ["Stack Contenedorizado de PETIDENTITY (Podman)"]
        subgraph AppContainer ["petidentity_app (Puerto 8081)"]
            LaravelApp["Laravel 11 Core (PHP 8.4)"]
            AuthMod["Módulo de Autenticación"]
            ReportController["PetReportController"]
            PetMatcher["PetMatcherService (Haversine + Fenotípico)"]
        end

        subgraph DBContainer ["petidentity_postgres (Puerto 5433)"]
            PostgresDB[("Base de Datos Relacional")]
        end

        subgraph RedisContainer ["petidentity_redis (Puerto 6380)"]
            RedisCache[("Cache y Colas de Notificación")]
        end
    end

    BrowserMobile -->|HTTP 8081| LaravelApp
    LeafletMap <-->|Coordenadas Lat/Lng| ReportController
    ReportController --> PostgresDB
    ReportController --> PetMatcher
    PetMatcher --> RedisCache
    BrowserMobile --> WhatsAppContact
```

---

## 5. Modelo Entidad - Relación (MER en Mermaid)

```mermaid
erDiagram
    USERS ||--o{ PET_REPORTS : "publica"
    PET_REPORTS ||--o{ PET_PHOTOS : "contiene"
    PET_REPORTS ||--o{ PET_MATCHES : "participa como perdido"
    PET_REPORTS ||--o{ PET_MATCHES : "participa como encontrado"

    USERS {
        bigint id PK
        string name
        string email UK
        string phone
        string whatsapp
        string password
        timestamp created_at
        timestamp updated_at
    }

    PET_REPORTS {
        bigint id PK
        bigint user_id FK
        string type "lost | found"
        string name "nullable en found"
        string species "dog | cat | other"
        string breed
        string size "small | medium | large"
        string primary_color
        string secondary_color
        text special_features
        string address_description
        decimal latitude "10, 7"
        decimal longitude "10, 7"
        date incident_date
        string contact_phone
        string contact_whatsapp
        string status "active | matched | resolved"
        timestamp created_at
        timestamp updated_at
    }

    PET_PHOTOS {
        bigint id PK
        bigint pet_report_id FK
        string photo_path
        boolean is_primary
        timestamp created_at
        timestamp updated_at
    }

    PET_MATCHES {
        bigint id PK
        bigint lost_report_id FK
        bigint found_report_id FK
        decimal match_score "5, 2 (0 - 100%)"
        decimal geo_distance_km "8, 2"
        string status "potential | confirmed | discarded"
        timestamp created_at
        timestamp updated_at
    }
```
