#!/usr/bin/env python3
"""
generate_technical_study_doc.py
Genera el documento formal del Estudio Técnico, Tablas Comparativas de Selección
Tecnológica, Presupuesto Detallado por Módulo y Estrategia de Despliegue para Andina S.R.L.

Materias evaluadas:
- Preparación y Evaluación de Proyectos (PEP)
- Ingeniería de Software 2 (SOFT 2)
- Práctica Empresarial (PE)

Autores oficiales:
- Nataly Nicole Crespo (NattCrespoC)
- Lucio Mercado
"""

import os
import subprocess
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

DOCX_OUTPUT = "docs/entrega/ESTUDIO_TECNICO_Y_COMPARATIVAS_ERP_IA.docx"
PDF_OUTPUT = "docs/entrega/ESTUDIO_TECNICO_Y_COMPARATIVAS_ERP_IA.pdf"
DIAGRAMS_DIR = "docs/diagrams"

def set_cell_background(cell, hex_color):
    shading_elm = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    cell._tc.get_or_add_tcPr().append(shading_elm)

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def add_academic_heading(doc, text, level):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Calibri"
    if level == 1:
        run.font.size = Pt(15)
        run.font.color.rgb = RGBColor(16, 44, 87) # Deep Navy
        pPr = p._p.get_or_add_pPr()
        pBdr = parse_xml(f'<w:pBdr {nsdecls("w")}><w:bottom w:val="single" w:sz="12" w:space="4" w:color="00A86B"/></w:pBdr>')
        pPr.append(pBdr)
    elif level == 2:
        run.font.size = Pt(12.5)
        run.font.color.rgb = RGBColor(0, 120, 150)
    elif level == 3:
        run.font.size = Pt(11)
        run.font.color.rgb = RGBColor(50, 50, 50)
    return p

def add_body_p(doc, text="", bold_prefix="", italic_prefix=""):
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(6)
    p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    
    if bold_prefix:
        r_bold = p.add_run(bold_prefix)
        r_bold.bold = True
        r_bold.font.name = "Calibri"
        r_bold.font.size = Pt(10.5)
        r_bold.font.color.rgb = RGBColor(20, 20, 20)
        
    if italic_prefix:
        r_it = p.add_run(italic_prefix)
        r_it.italic = True
        r_it.font.name = "Calibri"
        r_it.font.size = Pt(10.5)
        
    if text:
        r_text = p.add_run(text)
        r_text.font.name = "Calibri"
        r_text.font.size = Pt(10.5)
        r_text.font.color.rgb = RGBColor(40, 40, 40)
        
    return p

def create_styled_table(doc, headers, data):
    table = doc.add_table(rows=len(data) + 1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False

    # Cabecera
    for i, (name, width) in enumerate(headers):
        cell = table.cell(0, i)
        cell.width = Inches(width)
        set_cell_background(cell, "102C57") # Deep Navy
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        run = p.add_run(name)
        run.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        run.font.name = "Calibri"
        run.font.size = Pt(9.5)
        set_cell_margins(cell, 80, 80, 100, 100)

    # Filas de datos
    for row_idx, row_values in enumerate(data, start=1):
        bg = "F4F6F9" if row_idx % 2 == 1 else "FFFFFF"
        for col_idx, text_val in enumerate(row_values):
            cell = table.cell(row_idx, col_idx)
            cell.width = Inches(headers[col_idx][1])
            set_cell_background(cell, bg)
            set_cell_margins(cell, 70, 70, 90, 90)
            p = cell.paragraphs[0]
            r = p.add_run(str(text_val))
            r.font.size = Pt(9)
            r.font.name = "Calibri"

    doc.add_paragraph("")
    return table

def main():
    os.makedirs(os.path.dirname(DOCX_OUTPUT), exist_ok=True)
    doc = Document()
    
    # Márgenes estándar
    for s in doc.sections:
        s.top_margin = Inches(1)
        s.bottom_margin = Inches(1)
        s.left_margin = Inches(1)
        s.right_margin = Inches(1)
        
        header = s.header
        p_head = header.paragraphs[0]
        p_head.text = "ESTUDIO TÉCNICO Y COMPARATIVO DE SELECCIÓN TECNOLÓGICA | ERP ANDINA S.R.L."
        p_head.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        p_head.runs[0].font.name = "Calibri"
        p_head.runs[0].font.size = Pt(8.5)
        p_head.runs[0].font.color.rgb = RGBColor(128, 128, 128)
        
        footer = s.footer
        p_foot = footer.paragraphs[0]
        p_foot.text = "PEP / SOFT 2 / PE | Autores: Nataly Nicole Crespo & Lucio Mercado"
        p_foot.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_foot.runs[0].font.name = "Calibri"
        p_foot.runs[0].font.size = Pt(8.5)
        p_foot.runs[0].font.color.rgb = RGBColor(128, 128, 128)

    # PORTADA
    p_inst = doc.add_paragraph()
    p_inst.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_inst = p_inst.add_run("UNIVERSIDAD MAYOR DE SAN SIMÓN\nFACULTAD DE CIENCIAS Y TECNOLOGÍA\nCARRERA DE INGENIERÍA DE SISTEMAS / INFORMÁTICA")
    r_inst.bold = True
    r_inst.font.name = "Calibri"
    r_inst.font.size = Pt(12)
    r_inst.font.color.rgb = RGBColor(16, 44, 87)
    
    doc.add_paragraph("\n")
    
    p_mat = doc.add_paragraph()
    p_mat.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_mat = p_mat.add_run("PREPARACIÓN Y EVALUACIÓN DE PROYECTOS (PEP)\nINGENIERÍA DE SOFTWARE 2 (SOFT 2)\nPRÁCTICA EMPRESARIAL (PE)")
    r_mat.bold = True
    r_mat.font.name = "Calibri"
    r_mat.font.size = Pt(11)
    r_mat.font.color.rgb = RGBColor(0, 168, 107)
    
    doc.add_paragraph("\n")
    
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_t0 = p_title.add_run("ESTUDIO TÉCNICO Y EVALUACIÓN COMPARATIVA MULTICRITERIO PARA LA SELECCIÓN DEL STACK TECNOLÓGICO, PRESUPUESTO POR MÓDULOS Y PLAN DE DESPLIEGUE EN PRODUCCIÓN\n\n")
    r_t0.font.name = "Calibri"
    r_t0.font.size = Pt(12)
    r_t0.font.color.rgb = RGBColor(100, 100, 100)
    
    r_t1 = p_title.add_run("SISTEMA ERP DE GESTIÓN DE ADQUISICIONES E INVENTARIOS CON AUDITORÍA IA VECTORIAL EN TIEMPO REAL\n(CASO DE ESTUDIO: DISTRIBUIDORA COMERCIAL ANDINA S.R.L.)")
    r_t1.bold = True
    r_t1.font.name = "Calibri"
    r_t1.font.size = Pt(15)
    r_t1.font.color.rgb = RGBColor(16, 44, 87)
    
    doc.add_paragraph("\n\n")
    
    p_aut = doc.add_paragraph()
    p_aut.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    r_aut = p_aut.add_run("Equipo de Desarrollo e Investigación:\nNataly Nicole Crespo (NattCrespoC)\nLucio Mercado\n\nCochabamba - Bolivia\nNoviembre 2026")
    r_aut.font.name = "Calibri"
    r_aut.font.size = Pt(11)
    r_aut.font.color.rgb = RGBColor(50, 50, 50)
    
    doc.add_page_break()

    # ========================================================
    # 1. INTRODUCCIÓN AL ESTUDIO TÉCNICO
    # ========================================================
    add_academic_heading(doc, "1. INTRODUCCIÓN AL ESTUDIO TÉCNICO Y FUNDAMENTACIÓN DE SELECCIÓN", 1)
    
    add_body_p(doc, 
               "El presente estudio técnico constituye una pieza fundamental tanto para la Ingeniería de Software 2 como para la Preparación y Evaluación de Proyectos (PEP). En el marco del desarrollo del Sistema ERP para la Importadora y Distribuidora Comercial 'Andina S.R.L.', la selección de cada tecnología no obedece a preferencias arbitrarias o modas de la industria, sino a un análisis riguroso de factibilidad técnica, compatibilidad arquitectónica, sostenibilidad financiera y soberanía de los datos empresariales.")
               
    add_body_p(doc, 
               "Los criterios analizados en cada comparativa son: (1) Tiempo de salida a producción (Time-to-Market); (2) Desempeño en latencia transaccional y concurrencia; (3) Costo total de propiedad (TCO) y costos recurrentes de licenciamiento/APIs; (4) Madurez del ecosistema para auditoría y procesos transaccionales relacionales; y (5) Mantenibilidad y disponibilidad de talento profesional local en el contexto boliviano.")

    # ========================================================
    # 2. TABLAS COMPARATIVAS DE SELECCIÓN TECNOLÓGICA
    # ========================================================
    add_academic_heading(doc, "2. TABLAS COMPARATIVAS DE SELECCIÓN TECNOLÓGICA", 1)

    # Comparativa 1: Lenguajes Backend
    add_academic_heading(doc, "2.1. Comparativa de Lenguajes de Programación Backend", 2)
    add_body_p(doc, "Se contrastaron las cuatro alternativas principales para el backend transaccional:")
    
    headers_l = [("Criterio Evaluado", 1.8), ("PHP 8.3 / 8.5 (Elegido)", 1.7), ("Python 3.12 (FastAPI/Django)", 1.5), ("Node.js / TS (NestJS)", 1.5)]
    data_l = [
        ["Tipado y Seguridad", "Estricto (declare(strict_types=1);), motor tipado nativo.", "Tipado opcional (mypy/pydantic).", "Tipado transpilado con TypeScript."],
        ["Ecosistema ERP Empresarial", "Líder global absoluto en ERPs y CRMs administrativos.", "Fuerte en ciencia de datos, menor en ERPs web.", "Fuerte en microservicios, fragmentado en ORM."],
        ["Velocidad de Scaffold", "Excepcional; frameworks integrados de alta velocidad.", "Media; requiere ensamblar componentes dispersos.", "Media; alta verbosidad de configuración."],
        ["Consumo de Memoria", "Modelo Shared-Nothing (liberación inmediata de memoria).", "Procesos persistentes con riesgo de memory leaks.", "Loop asíncrono con sobrecarga en CPU blocking."],
        ["Costo de Hosting / Servidor", "Bajo; compatible con cualquier VPS Linux o contenedor.", "Medio; requiere workers WSGI/ASGI dedicados.", "Medio; gestión intensiva de procesos Node."],
        ["Conclusión Técnica", "SELECCIONADO: Equilibrio perfecto de velocidad, madurez y tipado.", "Descartado: Mayor tiempo de desarrollo de interfaces.", "Descartado: Mayor complejidad en lógica transaccional."]
    ]
    create_styled_table(doc, headers_l, data_l)

    # Comparativa 2: Frameworks Backend
    add_academic_heading(doc, "2.2. Comparativa de Frameworks Web Backend", 2)
    add_body_p(doc, "Evaluación entre frameworks web de nivel empresarial:")
    
    headers_f = [("Dimensión Arquitectónica", 1.8), ("Laravel 11 (Elegido)", 1.7), ("Django 5.0 (Python)", 1.5), ("NestJS (Node/TS)", 1.5)]
    data_f = [
        ["ORM y Persistencia", "Eloquent ORM con soporte nativo de scopes y pgvector.", "Django ORM potente pero rígido con extensiones.", "TypeORM/Prisma con migraciones a menudo frágiles."],
        ["Colas y Asincronía", "Sistema de Queues nativo con Redis y Workers automáticos.", "Celery (complejo de configurar y mantener).", "BullMQ (requiere amplia programación manual)."],
        ["WebSockets Nativos", "Laravel Reverb (servidor WebSocket nativo sin Pusher).", "Django Channels (requiere Redis + ASGI adicional).", "Socket.IO nativo."],
        ["Control de Acceso (RBAC)", "Spatie Permission nativo, maduro y granular.", "Sistema de permisos base limitado.", "CASL o módulos ad-hoc que requieren desarrollo."],
        ["Testing Integrado", "PestPHP nativo (elegante, expresivo y rápido).", "Pytest / TestCase estándar.", "Jest / Vitest."],
        ["Dictamen de Selección", "SELECCIONADO: La suite más completa y robusta para ERP.", "Descartado: Mayor costo de desarrollo en tiempo real.", "Descartado: Ecosistema atomizado para CRUD relacional."]
    ]
    create_styled_table(doc, headers_f, data_f)

    # Comparativa 3: Panel Administrativo y UI
    add_academic_heading(doc, "2.3. Comparativa de Frameworks de Interfaz Administrativa y Frontend", 2)
    add_body_p(doc, "Evaluación de tecnologías para la capa visual del ERP:")
    
    headers_u = [("Parámetro", 1.7), ("FilamentPHP v3 + Tailwind (Elegido)", 1.8), ("React Custom Dashboard (SPA)", 1.6), ("Django Admin", 1.4)]
    data_u = [
        ["Productividad / Tiempo Scaffold", "Menos de 2 días para módulos CRUD completos.", "2 a 3 semanas por módulo transaccional.", "Rápido pero con diseño anticuado y rígido."],
        ["Reactividad y Livewire", "Reactividad total en servidor sin escribir APIs REST.", "Requiere programar endpoints REST para cada acción.", "Nula; requiere recargar la página completa."],
        ["Diseño Estético y Tailwind", "Nativo con Tailwind CSS y componentes Heroicons.", "Requiere Tailwind + Shadcn o librerías de terceros.", "CSS plano tradicional con baja personalización."],
        ["Coste de Licencia", "100% Gratis y Open Source (Licencia MIT).", "Open source pero altísimo costo de horas-hombre.", "Open source (BSD)."],
        ["Veredicto", "SELECCIONADO: Máxima productividad con estética de primer nivel.", "Descartado: Dispara los costos de desarrollo en 300%.", "Descartado: No cumple el estándar visual corporativo."]
    ]
    create_styled_table(doc, headers_u, data_u)

    # Comparativa 4: Motor de Base de Datos y Búsqueda Vectorial
    add_academic_heading(doc, "2.4. Comparativa de Motores de Base de Datos y Almacenamiento Vectorial", 2)
    add_body_p(doc, "Evaluación para el almacenamiento transaccional y la auditoría vectorial:")
    
    headers_db = [("Característica", 1.7), ("PostgreSQL 16 + pgvector (Elegido)", 1.8), ("Pinecone (SaaS Vectorial)", 1.6), ("MySQL 8 + Milvus", 1.4)]
    data_db = [
        ["Arquitectura de Almacenamiento", "Unificada: Datos relacionales ACID y vectores en 1 BD.", "Dividida: BD relacional + Pinecone externo.", "Dividida: MySQL para tablas + Milvus para vectores."],
        ["Latencia de Consulta", "Submilisegundos con índice HNSW en la misma máquina.", "150 a 300 ms por latencia de red hacia nube de Pinecone.", "Media: requiere sincronizar dos bases de datos."],
        ["Costo Operacional Mensual", "0,00 Bs. (Self-hosted en Podman/Docker sin costos).", "Desde $70 USD/mes (~490 Bs./mes) para 1M vectores.", "Consumo doble de memoria RAM por dos motores."],
        ["Privacidad y Soberanía", "100% Soberanía interna: datos nunca salen de la empresa.", "Riesgo legal: datos comerciales en nube pública de terceros.", "Local pero con alta complejidad operativa."],
        ["Veredicto de Selección", "SELECCIONADO: La solución arquitectónica más limpia y económica.", "Descartado: Costo recurrente inaceptable y riesgo de fuga.", "Descartado: Complejidad excesiva de mantenimiento."]
    ]
    create_styled_table(doc, headers_db, data_db)

    # Comparativa 5: Motores de Embeddings e Inferencia IA
    add_academic_heading(doc, "2.5. Comparativa de Modelos de Lenguaje e Inferencia IA", 2)
    add_body_p(doc, "Evaluación para la generación de vectores y dictámenes de auditoría:")
    
    headers_ai = [("Criterio IA", 1.7), ("Ollama (Local) + Groq Cloud (Elegido)", 1.8), ("OpenAI API (GPT-4o)", 1.6), ("AWS Bedrock (Claude 3)", 1.4)]
    data_ai = [
        ["Costo de Embeddings (Vectores)", "0,00 Bs. (nomic-embed-text en GPU/CPU local).", "0.02 USD por 1M tokens recurrentes.", "0.02 USD por 1M tokens recurrentes."],
        ["Velocidad de Inferencia LLM", "Ultra rápida: Groq LPU a 300 tokens/segundo.", "Media: 40 a 60 tokens/segundo.", "Media: 50 a 70 tokens/segundo."],
        ["Dependencia de Internet", "Híbrida: Vectorización y bloqueo local sin internet.", "100% dependiente de conectividad internacional.", "100% dependiente de conectividad."],
        ["Formato Estricto de Auditoría", "JSON Schema forzado nativamente en Groq Cloud.", "JSON Mode disponible.", "Tool calling disponible."],
        ["Veredicto", "SELECCIONADO: Costo operativo mínimo y velocidad imbatible.", "Descartado: Costos impredecibles en moneda extranjera.", "Descartado: Mayor complejidad de integración."]
    ]
    create_styled_table(doc, headers_ai, data_ai)

    # ========================================================
    # 3. PRESUPUESTO DETALLADO Y COSTO POR MÓDULO
    # ========================================================
    add_academic_heading(doc, "3. PRESUPUESTO DEL PROYECTO Y VALORACIÓN POR MÓDULOS (PEP)", 1)
    
    add_body_p(doc, 
               "Bajo los estándares de Preparación y Evaluación de Proyectos (PEP) y estimación de costos en Ingeniería de Software mediante puntos de función y horas-hombre valoradas en el mercado boliviano (Bs. 75/hora promedio de desarrollo especializado), se detalla el presupuesto económico de la solución:")

    headers_cost = [("Módulo / Componente Técnico", 2.2), ("Horas Estimadas", 1.1), ("Costo Unitario (Bs.)", 1.4), ("Subtotal (Bs.)", 1.8)]
    data_cost = [
        ["M01: Catálogo de Proveedores y Scoring de Riesgo", "56 hrs", "Bs. 75,00", "Bs. 4.200,00"],
        ["M02: Órdenes de Compra y Maestro-Detalle", "90 hrs", "Bs. 75,00", "Bs. 6.750,00"],
        ["M03: Almacenes y Kardex Valorado (Cálculo CPP)", "75 hrs", "Bs. 75,00", "Bs. 5.625,00"],
        ["M04: Motor de Auditoría Vectorial e Índices HNSW", "110 hrs", "Bs. 80,00", "Bs. 8.800,00"],
        ["M05: Centro de Auditoría IA y Dictámenes Groq LLM", "105 hrs", "Bs. 80,00", "Bs. 8.400,00"],
        ["M06: Notificaciones Reactivas en Vivo (Laravel Reverb)", "65 hrs", "Bs. 75,00", "Bs. 4.875,00"],
        ["M07: Control de Acceso RBAC (Spatie Roles & Permisos)", "40 hrs", "Bs. 75,00", "Bs. 3.000,00"],
        ["M08: Aseguramiento de Calidad y Pruebas TDD (PestPHP)", "50 hrs", "Bs. 75,00", "Bs. 3.750,00"],
        ["Infraestructura, Dockerización y Puesta en Marcha", "45 hrs", "Bs. 75,00", "Bs. 3.375,00"],
        ["TOTAL INVERSIÓN INICIAL DE SOFTWARE (I0)", "636 hrs", "-", "Bs. 48.775,00"]
    ]
    create_styled_table(doc, headers_cost, data_cost)

    add_body_p(doc, 
               "La inversión inicial calculada de Bs. 48.775,00 se recupera en tan solo 4,1 meses frente a las pérdidas documentadas en la empresa (Bs. 142.500,00 anuales = Bs. 11.875,00 mensuales por sobreprecios y mermas no detectadas). Esto ratifica una Tasa Interna de Retorno (TIR) superior al 120% y un VAN ampliamente positivo, certificando la viabilidad económica del software.",
               bold_prefix="Conclusión de Viabilidad Financiera: ")

    # ========================================================
    # 4. ESTRATEGIA Y PLAN DE DESPLIEGUE (DEPLOY)
    # ========================================================
    add_academic_heading(doc, "4. PLAN Y ESTRATEGIA DE DESPLIEGUE EN PRODUCCIÓN (DEPLOYMENT)", 1)
    
    add_body_p(doc, 
               "El despliegue del sistema ERP para Andina S.R.L. se basa en una arquitectura contenerizada y modular que garantiza alta disponibilidad, aislamiento de procesos pesados de IA y facilidad de replicación entre entornos de desarrollo y producción:")

    deploy_steps = [
        ("1. Servidor de Producción Recomendado: ", "VPS Linux Ubuntu 24.04 LTS con 8 vCPUs, 16 GB de RAM y 100 GB NVMe SSD. Esta capacidad permite ejecutar simultáneamente PostgreSQL 16 con extensión vectorial, Redis en memoria, el servidor de inferencia Ollama local con nomic-embed-text, y los demonios de Laravel."),
        ("2. Contenedores Podman / Docker: ", "La base de datos PostgreSQL 16 pgvector y Redis operan en contenedores aislados mediante podman-compose, con volúmenes persistentes montados en /var/lib/postgresql/data y políticas de reinicio automático unless-stopped."),
        ("3. Proxy Inverso y Certificados SSL: ", "Nginx actúa como proxy inverso frente a la aplicación PHP-FPM (puerto 8000) y como terminador de WebSocket para Laravel Reverb (puerto 8080), con certificados TLS/SSL gratuitos y renovados automáticamente mediante Let's Encrypt Certbot."),
        ("4. Gestión de Procesos en Segundo Plano (Supervisor): ", "Para garantizar que la vectorización asíncrona y los WebSockets no se interrumpan ante reinicios, se configuran dos demonios con supervisor / systemd: (a) 'php artisan queue:work redis --queue=default,audit' con 4 workers concurrentes; y (b) 'php artisan reverb:start --host=0.0.0.0 --port=8080'."),
        ("5. Política de Respaldo Automatizado (Backup): ", "Un cron job diario ejecuta pg_dump comprimido a las 02:00 AM sobre la base de datos auditoria_ia, almacenando copias cifradas con retención rotativa de 30 días.")
    ]
    for st_title, st_desc in deploy_steps:
        p_s = doc.add_paragraph(style='List Bullet')
        p_s.paragraph_format.space_after = Pt(4)
        r_st = p_s.add_run(st_title)
        r_st.bold = True
        r_st.font.name = "Calibri"
        r_sd = p_s.add_run(st_desc)
        r_sd.font.name = "Calibri"

    # Incrustar diagrama de arquitectura si existe
    comp_path = os.path.join(DIAGRAMS_DIR, "diagrama_componentes.png")
    if os.path.exists(comp_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(comp_path, width=Inches(6.2))
        p_caption = doc.add_paragraph()
        p_caption.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r_cap = p_caption.add_run("Figura 1: Topología de Despliegue y Arquitectura Desacoplada del ERP con Auditoría IA")
        r_cap.italic = True
        r_cap.font.size = Pt(9.5)
        r_cap.font.name = "Calibri"

    doc.save(DOCX_OUTPUT)
    print(f"✅ Documento de Estudio Técnico en Word generado: {DOCX_OUTPUT}")

    # Compilar a PDF mediante LibreOffice
    print("⏳ Compilando a PDF con LibreOffice...")
    cmd = ["libreoffice", "--headless", "--convert-to", "pdf", DOCX_OUTPUT, "--outdir", os.path.dirname(PDF_OUTPUT)]
    subprocess.run(cmd, check=True)
    print(f"✅ Documento de Estudio Técnico en PDF compilado: {PDF_OUTPUT}")

if __name__ == "__main__":
    main()
