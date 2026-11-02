#!/usr/bin/env python3
import os
import subprocess
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

os.makedirs("docs/entrega", exist_ok=True)

doc = Document()

# Configuración de márgenes estándar (2.5 cm)
sections = doc.sections
for section in sections:
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    tcPr.append(parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>'))

# ==========================================
# PORTADA FORMAL
# ==========================================
p_uni = doc.add_paragraph()
p_uni.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_uni = p_uni.add_run("UNIVERSIDAD MAYOR DE SAN SIMÓN\nFACULTAD DE CIENCIAS Y TECNOLOGÍA\nCARRERA DE INGENIERÍA DE SISTEMAS / INFORMÁTICA\n")
run_uni.font.name = "Calibri"
run_uni.font.size = Pt(14)
run_uni.font.bold = True
run_uni.font.color.rgb = RGBColor(0x1e, 0x29, 0x3b)

doc.add_paragraph("\n")

p_title = doc.add_paragraph()
p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_title = p_title.add_run("SISTEMA WEB DE GESTIÓN DE ADQUISICIONES E INVENTARIOS PARA LA AUDITORÍA OPERATIVA EN TIEMPO REAL MEDIANTE DETECCIÓN DE ANOMALÍAS CON VECTORES MATEMÁTICOS E INTELIGENCIA ARTIFICIAL\n")
run_title.font.name = "Calibri"
run_title.font.size = Pt(16)
run_title.font.bold = True
run_title.font.color.rgb = RGBColor(0x25, 0x63, 0xeb)

p_case = doc.add_paragraph()
p_case.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_case = p_case.add_run("CASO DE ESTUDIO: IMPORTADORA Y DISTRIBUIDORA COMERCIAL \"ANDINA S.R.L.\"\n")
run_case.font.name = "Calibri"
run_case.font.size = Pt(12)
run_case.font.italic = True
run_case.font.color.rgb = RGBColor(0x47, 0x55, 0x69)

doc.add_paragraph("\n")

p_sub = doc.add_paragraph()
p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
run_sub = p_sub.add_run("PROYECTO ACADÉMICO INTEGRADO DE 8VO SEMESTRE:\nIngeniería de Software 2 | Práctica Empresarial | Preparación y Evaluación de Proyectos (PEP)\n")
run_sub.font.name = "Calibri"
run_sub.font.size = Pt(11)
run_sub.font.bold = True

doc.add_paragraph("\n\n")

p_team = doc.add_paragraph()
p_team.alignment = WD_ALIGN_PARAGRAPH.RIGHT
run_team = p_team.add_run("Equipo de Desarrollo e Investigación:\nNataly Nicole Crespo (NattCrespoC)\nLucio Mercado\n\nCochabamba - Bolivia\nNoviembre 2026")
run_team.font.name = "Calibri"
run_team.font.size = Pt(11)

doc.add_page_break()

# ==========================================
# CAPÍTULO 1: FORMULACIÓN Y OBJETIVOS
# ==========================================
h1 = doc.add_heading("1. Formulación del Problema y Justificación", level=1)
h1.style.font.color.rgb = RGBColor(0x1e, 0x40, 0xaf)

doc.add_paragraph(
    "La gestión de compras y el control de inventarios representan el núcleo financiero de las empresas comerciales en Bolivia. "
    "En la actualidad, los controles tradicionales se ejecutan mediante auditorías manuales posteriores al cierre mensual (post-mortem), "
    "provocando que sobreprecios, compras duplicadas y mermas pasen desapercibidos hasta que el capital ya fue desembolsado."
)

doc.add_heading("1.1. Tres Problemas Medibles y Narrados", level=2)

p_prob1 = doc.add_paragraph()
p_prob1.add_run("1. Problema de Tiempo (Demora en la Detección): ").bold = True
p_prob1.add_run(
    "El proceso de conciliación manual entre órdenes de compra, notas de almacén y facturas físicas demanda 48 horas-hombre mensuales. "
    "Cualquier desvío en precios o faltante de mercadería tarda entre 15 y 30 días calendario en ser descubierto, imposibilitando la recuperación de fondos."
)

p_prob2 = doc.add_paragraph()
p_prob2.add_run("2. Problema de Cantidad (Volumen de Inconsistencias): ").bold = True
p_prob2.add_run(
    "De un promedio mensual de 1.200 transacciones operativas, el 14% (168 transacciones por mes) presenta irregularidades no advertidas al momento de su carga: "
    "facturas duplicadas, compras fraccionadas para evadir límites de autorización y salidas fuera de horario laboral."
)

p_prob3 = doc.add_paragraph()
p_prob3.add_run("3. Problema de Costo (Impacto Económico Directo): ").bold = True
p_prob3.add_run(
    "La ausencia de validación matemática en tiempo real genera pérdidas directas estimadas en Bs. 8.500 mensuales (Bs. 102.000 anuales) por sobreprecios y compras con sobrestock, "
    "reduciendo el margen neto de utilidad operativa en un 6,5%."
)

doc.add_heading("1.2. Diagrama de Causa y Efecto (Ishikawa)", level=2)
if os.path.exists("docs/diagrams/02_ishikawa_causa_efecto.png"):
    doc.add_picture("docs/diagrams/02_ishikawa_causa_efecto.png", width=Inches(6.2))
    p_cap = doc.add_paragraph("Figura 1: Diagrama de Ishikawa de las 6 categorías de causa del problema central.")
    p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_cap.style.font.size = Pt(9)
    p_cap.style.font.italic = True

doc.add_heading("1.3. Objetivos del Proyecto", level=2)
p_objg = doc.add_paragraph()
p_objg.add_run("Objetivo General: ").bold = True
p_objg.add_run("Desarrollar un sistema web de gestión de adquisiciones e inventarios para la auditoría operativa en tiempo real mediante detección de anomalías con vectores matemáticos e inteligencia artificial en la Importadora y Distribuidora Comercial \"Andina S.R.L.\".")

doc.add_paragraph("Objetivos Específicos y Acciones:").bold = True
doc.add_paragraph("1. Diagnosticar los procesos actuales de compras y almacenes para calibrar las variables de riesgo histórico.")
doc.add_paragraph("2. Diseñar la arquitectura por componentes desacoplados, modelo entidad-relación y contratos JSON en PostgreSQL.")
doc.add_paragraph("3. Implementar el motor vectorial con Ollama (nomic-embed-text), índices HNSW en pgvector, dictámenes con Groq y alertas WebSockets con Laravel Reverb.")
doc.add_paragraph("4. Evaluar la calidad mediante 20 pruebas TDD con PestPHP y determinar la rentabilidad financiera (VAN, TIR) bajo estándares de PEP.")

doc.add_page_break()

# ==========================================
# CAPÍTULO 2: ARQUITECTURA Y METODOLOGÍA TDD
# ==========================================
doc.add_heading("2. Arquitectura de Software y Metodología TDD", level=1)

doc.add_paragraph(
    "El desarrollo se rige bajo la combinación de Scrum y eXtreme Programming (XP), implementando Desarrollo Guiado por Pruebas (TDD) en PestPHP. "
    "El repositorio se estructura modularmente en componentes desacoplados:"
)

if os.path.exists("docs/diagrams/03_arquitectura_componentes.png"):
    doc.add_picture("docs/diagrams/03_arquitectura_componentes.png", width=Inches(6.2))
    p_cap2 = doc.add_paragraph("Figura 2: Arquitectura en Capas y Componentes Modulares Desacoplados.")
    p_cap2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_cap2.style.font.size = Pt(9)
    p_cap2.style.font.italic = True

doc.add_heading("2.1. Desglose de Componentes Principales", level=2)
table_comp = doc.add_table(rows=1, cols=3)
table_comp.alignment = WD_TABLE_ALIGNMENT.CENTER
table_comp.style = 'Table Grid'
hdr_cells = table_comp.rows[0].cells
hdr_cells[0].text = "Componente"
hdr_cells[1].text = "Ubicación en Código"
hdr_cells[2].text = "Responsabilidad Técnica"
for cell in hdr_cells:
    set_cell_background(cell, "2563EB")
    for p in cell.paragraphs:
        for r in p.runs:
            r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            r.font.bold = True

components_data = [
    ("Procurement", "app/Components/Procurement/", "Catálogo de proveedores, validación de NIT y órdenes maestro-detalle."),
    ("Warehouse", "app/Components/Warehouse/", "Kardex físico/valorado, recepción multimodal de facturas con Vision y Whisper."),
    ("AuditEngine", "app/Components/AuditEngine/", "Cadena canónica, embeddings 768d, cálculo Coseno (<=>) y Z-Score en PostgreSQL."),
    ("RealtimeAlerts", "app/Components/RealtimeAlerts/", "Laravel Reverb (puerto 8080), eventos ShouldBroadcastNow y alertas reactivas."),
    ("SecurityRBAC", "app/Components/SecurityRBAC/", "Roles Spatie (Gerente, Auditor, Compras, Almacén) y bitácora inmutable."),
    ("AnalyticsCopilot", "app/Components/AnalyticsCopilot/", "Consultas Text-to-SQL seguras en pgsql_readonly y Function Calling."),
]

for name, path, desc in components_data:
    row_cells = table_comp.add_row().cells
    row_cells[0].text = name
    row_cells[1].text = path
    row_cells[2].text = desc

doc.add_page_break()

# ==========================================
# CAPÍTULO 3: MODELADO MATEMÁTICO DE ANOMALÍAS
# ==========================================
doc.add_heading("3. Algoritmo Matemático de Detección de Anomalías", level=1)

doc.add_paragraph(
    "La evaluación de anomalías opera en milisegundos mediante la combinación de geometría analítica multidimensional y estadística inferencial:"
)

doc.add_heading("3.1. Distancia de Coseno y Similitud Vectorial", level=2)
doc.add_paragraph(
    "Cada compra se transforma en un vector unitario de 768 dimensiones con el modelo local nomic-embed-text. "
    "En PostgreSQL 16 con extensión pgvector, se evalúa la distancia de coseno frente al histórico mediante el operador <=> con índice HNSW:\n"
    "Similitud(A, B) = cos(θ) = (A · B) / (||A|| ||B||)"
)

doc.add_heading("3.2. Desviación Típica Estandarizada (Z-Score)", level=2)
doc.add_paragraph(
    "El índice Z mide cuántas desviaciones estándar (σ) se aleja el precio unitario pactado (x) respecto al Costo Promedio Ponderado histórico (μ):\n"
    "Z = (x - μ) / σ\n"
    "Si |Z| ≥ 2.2 o Similitud < 0.80, la transacción se clasifica como CRÍTICA y se bloquea preventivamente."
)

if os.path.exists("docs/diagrams/04_distribucion_anomalias_zscore.png"):
    doc.add_picture("docs/diagrams/04_distribucion_anomalias_zscore.png", width=Inches(5.8))
    p_cap3 = doc.add_paragraph("Figura 3: Modelo Estadístico de Clasificación de Riesgo por Desviación Z-Score.")
    p_cap3.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_cap3.style.font.size = Pt(9)
    p_cap3.style.font.italic = True

doc.add_page_break()

# ==========================================
# CAPÍTULO 4: EVALUACIÓN FINANCIERA (PEP)
# ==========================================
doc.add_heading("4. Estudio Económico y Financiero (PEP)", level=1)

doc.add_paragraph(
    "El análisis financiero evalúa la viabilidad del proyecto para la empresa Andina S.R.L. a un horizonte de 5 años, con valores expresados en Bolivianos (Bs.):"
)

table_fin = doc.add_table(rows=1, cols=3)
table_fin.alignment = WD_TABLE_ALIGNMENT.CENTER
table_fin.style = 'Table Grid'
hdr_fin = table_fin.rows[0].cells
hdr_fin[0].text = "Indicador Financiero"
hdr_fin[1].text = "Valor Cuantificado"
hdr_fin[2].text = "Interpretación y Dictamen"
for cell in hdr_fin:
    set_cell_background(cell, "059669")
    for p in cell.paragraphs:
        for r in p.runs:
            r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            r.font.bold = True

fin_data = [
    ("Inversión Inicial (I₀)", "Bs. 12.075,00", "Desarrollo (320 hrs), servidor VPS, dominio y capacitación."),
    ("Ahorro Bruto Anual", "Bs. 96.720,00", "80% de pérdidas evitadas (Bs. 81.600) + 36 hrs/mes de auditoría manual (Bs. 15.120)."),
    ("Costos Operativos (OPEX)", "Bs. 6.000,00 / año", "Hosting VPS Cloud, cuota Groq Cloud y soporte trimestral."),
    ("Flujo Neto Anual (FNE)", "Bs. 76.500,00 / año", "Beneficio neto continuo después de costos e impuestos."),
    ("Valor Actual Neto (VAN)", "Bs. 263.690,00", "VAN > 0 calculado a una TMAR del 12%. Proyecto altamente rentable."),
    ("Tasa Interna de Retorno (TIR)", "> 180% anual", "Supera ampliamente el costo de oportunidad del capital (12%)."),
    ("Relación Beneficio / Costo (B/C)", "4,85", "Por cada Bs. 1 invertido y operado, la empresa recupera Bs. 4,85."),
    ("Periodo de Recuperación (PRI)", "2,05 meses", "La inversión total se amortiza en los dos primeros meses de operación."),
]

for ind, val, dictam in fin_data:
    row_cells = table_fin.add_row().cells
    row_cells[0].text = ind
    row_cells[1].text = val
    row_cells[2].text = dictam

doc.add_page_break()

# ==========================================
# CAPÍTULO 5: CRONOGRAMA DE SPRINTS (02 al 25 Nov)
# ==========================================
doc.add_heading("5. Cronograma Diario de Sprints e Hitos de Entrega", level=1)

doc.add_paragraph(
    "El proyecto se descompone en 4 iteraciones continuas cubriendo exactamente los 23 días calendario hasta la defensa final del 25 de noviembre:"
)

if os.path.exists("docs/diagrams/01_cronograma_gantt_sprints.png"):
    doc.add_picture("docs/diagrams/01_cronograma_gantt_sprints.png", width=Inches(6.2))
    p_cap4 = doc.add_paragraph("Figura 4: Cronograma Gantt de las 4 Iteraciones de Desarrollo.")
    p_cap4.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_cap4.style.font.size = Pt(9)
    p_cap4.style.font.italic = True

table_sprints = doc.add_table(rows=1, cols=4)
table_sprints.alignment = WD_TABLE_ALIGNMENT.CENTER
table_sprints.style = 'Table Grid'
hdr_sp = table_sprints.rows[0].cells
hdr_sp[0].text = "Sprint"
hdr_sp[1].text = "Fechas"
hdr_sp[2].text = "Objetivo Central"
hdr_sp[3].text = "Entregable Verificable (DoD)"
for cell in hdr_sp:
    set_cell_background(cell, "7C3AED")
    for p in cell.paragraphs:
        for r in p.runs:
            r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            r.font.bold = True

sprints_data = [
    ("Sprint 1", "02 - 06 Nov", "Cimientos, Podman, RBAC y Catálogos", "PostgreSQL pgvector activo, roles Spatie y seeder de 500 compras."),
    ("Sprint 2", "07 - 13 Nov", "Vectorización Ollama, pgvector y Groq", "Cálculo de Coseno y Z-Score con bloqueo automático de compras anómalas."),
    ("Sprint 3", "14 - 19 Nov", "WebSockets Reverb, Multimodal y Copiloto", "Alertas push en vivo en pantalla gerencial, OCR facturas y Text-to-SQL."),
    ("Sprint 4", "20 - 25 Nov", "Calidad QA (Pest), Estudio PEP y Defensa", "20 tests en verde, estudio financiero final y simulacro en dos pantallas."),
]

for sp, dates, goal, dod in sprints_data:
    row_cells = table_sprints.add_row().cells
    row_cells[0].text = sp
    row_cells[1].text = dates
    row_cells[2].text = goal
    row_cells[3].text = dod

# Guardar documento Word
docx_path = "docs/entrega/Proyecto_ERP_Auditoria_IA.docx"
doc.save(docx_path)
print(f"✅ Documento Word generado exitosamente: {docx_path}")

# Conversión a PDF con LibreOffice
print("⏳ Convirtiendo documento Word a PDF mediante LibreOffice...")
try:
    res = subprocess.run([
        "libreoffice", "--headless", "--convert-to", "pdf",
        "--outdir", "docs/entrega", docx_path
    ], capture_output=True, text=True, check=True)
    pdf_path = "docs/entrega/Proyecto_ERP_Auditoria_IA.pdf"
    print(f"✅ Documento PDF compilado exitosamente: {pdf_path}")
except Exception as e:
    print(f"⚠️ Error durante la conversión a PDF: {e}")
