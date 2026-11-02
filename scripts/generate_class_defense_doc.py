#!/usr/bin/env python3
"""
generate_class_defense_doc.py
Genera el documento académico formal para la presentación de clase en:
- Preparación y Evaluación de Proyectos (PEP)
- Ingeniería de Software 2 (SOFT 2)
- Práctica Empresarial (PE)

Cumple con la estructura estricta:
1. Título estructurado [SISTEMA + QUÉ HACE + INNOVACIÓN TECNOLÓGICA + CASO DE ESTUDIO]
2. Introducción
3. Antecedentes
4. Formulación del Problema (3 medibles: Tiempo, Cantidad, Costo/Financiero - narrados)
5. Identificación del Problema (Ishikawa 6M y Árbol de Problemas con diagrama)
6. Causa y Efecto (Matriz detallada)
7. Objetivo General (Verbo rector Desarrollar)
8. Objetivos Específicos (Verbos únicos: Diagnosticar, Diseñar, Implementar, Evaluar)
   y 3 Acciones por cada uno (12 acciones en total)

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

DOCX_OUTPUT = "docs/entrega/DOCUMENTO_DEFENSA_CLASE_PEP_SOFT_PE.docx"
PDF_OUTPUT = "docs/entrega/DOCUMENTO_DEFENSA_CLASE_PEP_SOFT_PE.pdf"
DIAGRAMS_DIR = "docs/diagrams"

def set_cell_background(cell, hex_color):
    shading_elm = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    cell._tc.get_or_add_tcPr().append(shading_elm)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
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
        run.font.size = Pt(16)
        run.font.color.rgb = RGBColor(16, 44, 87) # Deep Navy
        # Barra decorativa inferior
        pPr = p._p.get_or_add_pPr()
        pBdr = parse_xml(f'<w:pBdr {nsdecls("w")}><w:bottom w:val="single" w:sz="12" w:space="4" w:color="00A86B"/></w:pBdr>')
        pPr.append(pBdr)
    elif level == 2:
        run.font.size = Pt(13)
        run.font.color.rgb = RGBColor(0, 100, 140)
    elif level == 3:
        run.font.size = Pt(11.5)
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
        r_bold.font.size = Pt(11)
        r_bold.font.color.rgb = RGBColor(20, 20, 20)
        
    if italic_prefix:
        r_it = p.add_run(italic_prefix)
        r_it.italic = True
        r_it.font.name = "Calibri"
        r_it.font.size = Pt(11)
        
    if text:
        r_text = p.add_run(text)
        r_text.font.name = "Calibri"
        r_text.font.size = Pt(11)
        r_text.font.color.rgb = RGBColor(40, 40, 40)
        
    return p

def main():
    os.makedirs(os.path.dirname(DOCX_OUTPUT), exist_ok=True)
    doc = Document()
    
    # Configurar márgenes estándar 2.5 cm
    for s in doc.sections:
        s.top_margin = Inches(1)
        s.bottom_margin = Inches(1)
        s.left_margin = Inches(1)
        s.right_margin = Inches(1)
        
        # Header y Footer
        header = s.header
        p_head = header.paragraphs[0]
        p_head.text = "PEP / SOFT 2 / PE | Sistema ERP de Gestión con Auditoría IA Vectorial"
        p_head.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        p_head.runs[0].font.name = "Calibri"
        p_head.runs[0].font.size = Pt(8.5)
        p_head.runs[0].font.color.rgb = RGBColor(128, 128, 128)
        
        footer = s.footer
        p_foot = footer.paragraphs[0]
        p_foot.text = "Importadora y Distribuidora Andina S.R.L. | Autores: Nataly Nicole Crespo & Lucio Mercado"
        p_foot.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_foot.runs[0].font.name = "Calibri"
        p_foot.runs[0].font.size = Pt(8.5)
        p_foot.runs[0].font.color.rgb = RGBColor(128, 128, 128)

    # PORTADA ACADÉMICA
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
    r_mat = p_mat.add_run("PROYECTO INTEGRADO DE 8VO SEMESTRE\nPREPARACIÓN Y EVALUACIÓN DE PROYECTOS (PEP)\nINGENIERÍA DE SOFTWARE 2 (SOFT 2)\nPRÁCTICA EMPRESARIAL (PE)")
    r_mat.bold = True
    r_mat.font.name = "Calibri"
    r_mat.font.size = Pt(11)
    r_mat.font.color.rgb = RGBColor(0, 168, 107)
    
    doc.add_paragraph("\n")
    
    # TÍTULO FORMAL DEL PROYECTO
    p_title_box = doc.add_paragraph()
    p_title_box.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_tb = p_title_box.add_run("DOCUMENTO DE PLANIFICACIÓN ACADÉMICA Y DEFENSA FORMAL\n\n")
    r_tb.font.name = "Calibri"
    r_tb.font.size = Pt(13)
    r_tb.font.color.rgb = RGBColor(100, 100, 100)
    
    r_title = p_title_box.add_run("SISTEMA DE GESTIÓN DE ADQUISICIONES E INVENTARIOS CON AUDITORÍA PREVENTIVA MEDIANTE EMBEDDINGS VECTORIALES Y MODELOS DE LENGUAJE EN TIEMPO REAL PARA LA DISTRIBUIDORA COMERCIAL ANDINA S.R.L.")
    r_title.bold = True
    r_title.font.name = "Calibri"
    r_title.font.size = Pt(16)
    r_title.font.color.rgb = RGBColor(16, 44, 87)
    
    doc.add_paragraph("\n\n")
    
    # AUTORES
    p_aut = doc.add_paragraph()
    p_aut.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    r_aut = p_aut.add_run("Postulantes e Investigadores:\nNataly Nicole Crespo (NattCrespoC)\nLucio Mercado\n\nDocentes Evaluadores:\nComisión Académica de Área (PEP - SOFT 2 - PE)\n\nCochabamba - Bolivia\nNoviembre 2026")
    r_aut.font.name = "Calibri"
    r_aut.font.size = Pt(11)
    r_aut.font.color.rgb = RGBColor(50, 50, 50)
    
    doc.add_page_break()
    
    # ========================================================
    # 1. TÍTULO ESTRUCTURADO Y DESGLOSE CONCEPTUAL
    # ========================================================
    add_academic_heading(doc, "1. TÍTULO ESTRUCTURADO DEL PROYECTO", 1)
    
    add_body_p(doc, 
               "El título de la investigación y desarrollo de software ha sido formulado cumpliendo rigurosamente la estructura canónica exigida en la metodología de ingeniería de software y preparación de proyectos:",
               bold_prefix="Fórmula Aplicada: ")
               
    tbl_formula = doc.add_table(rows=5, cols=2)
    tbl_formula.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_formula.autofit = False
    
    headers_f = [("Componente Normativo", 1.8), ("Instanciación en el Proyecto", 4.7)]
    for i, (name, width) in enumerate(headers_f):
        cell = tbl_formula.cell(0, i)
        cell.width = Inches(width)
        set_cell_background(cell, "102C57")
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        run = p.add_run(name)
        run.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        run.font.name = "Calibri"
        set_cell_margins(cell, 80, 80, 100, 100)
        
    data_f = [
        ("1. Producto / Sistema:", "SISTEMA ERP TRANSACCIONAL WEB"),
        ("2. Qué hace (Propósito):", "Gestión integral de adquisiciones, catálogo de proveedores, control físico y valorado de inventarios, y auditoría preventiva contra sobreprecios y desvíos comerciales."),
        ("3. Innovación Tecnológica:", "Vectorización contextual en tiempo real con modelos de embeddings (nomic-embed-text / pgvector en 768 dimensiones), cálculo estadístico Z-Score multivariable, dictámenes autónomos en JSON con LLMs (Groq Llama 3.3 70B) y alertas WebSockets en vivo (Laravel Reverb)."),
        ("4. Caso de Estudio:", "Importadora y Distribuidora Comercial 'Andina S.R.L.' (Cochabamba, Bolivia).")
    ]
    
    for row_idx, (comp, desc) in enumerate(data_f, start=1):
        bg = "F4F6F9" if row_idx % 2 == 1 else "FFFFFF"
        c0 = tbl_formula.cell(row_idx, 0)
        c1 = tbl_formula.cell(row_idx, 1)
        c0.width = Inches(1.8)
        c1.width = Inches(4.7)
        set_cell_background(c0, bg)
        set_cell_background(c1, bg)
        set_cell_margins(c0, 70, 70, 90, 90)
        set_cell_margins(c1, 70, 70, 90, 90)
        
        p0 = c0.paragraphs[0]
        r0 = p0.add_run(comp)
        r0.bold = True
        r0.font.size = Pt(10)
        r0.font.name = "Calibri"
        
        p1 = c1.paragraphs[0]
        r1 = p1.add_run(desc)
        r1.font.size = Pt(10)
        r1.font.name = "Calibri"

    add_body_p(doc, 
               "«Sistema de Gestión de Adquisiciones e Inventarios con Auditoría Preventiva mediante Embeddings Vectoriales y Modelos de Lenguaje en Tiempo Real para la Importadora y Distribuidora Comercial Andina S.R.L.»",
               bold_prefix="Título Definitivo: ", italic_prefix="")

    # ========================================================
    # 2. INTRODUCCIÓN
    # ========================================================
    add_academic_heading(doc, "2. INTRODUCCIÓN", 1)
    
    add_body_p(doc, 
               "En el entorno comercial boliviano actual, las empresas dedicadas a la importación y distribución mayorista enfrentan desafíos crecientes relacionados con la volatilidad de precios internacionales, costos logísticos fluctuantes y la imperiosa necesidad de mantener un control estricto sobre sus costos de adquisición e inventario. La Importadora y Distribuidora Comercial 'Andina S.R.L.', con sede central en la ciudad de Cochabamba y operaciones de distribución regional en el eje troncal (Cochabamba, Santa Cruz y La Paz), maneja un portafolio de más de 450 SKUs activos clasificados en ferretería industrial, herramientas eléctricas, materiales de construcción y repuestos especializados.")
               
    add_body_p(doc, 
               "A pesar de su volumen transaccional continuo —que supera las 200 órdenes de compra mensuales y miles de entradas y salidas de almacén—, la compañía gestiona sus operaciones mediante esquemas fragmentados: hojas de cálculo en Excel para cotizaciones previas y un sistema contable de escritorio tradicional. En este modelo pasivo, la auditoría interna se concibe como una actividad forense o ex-post, efectuada semanas después de que las mercaderías han sido ingresadas al almacén y las órdenes de pago canceladas irrevocablemente mediante transferencias bancarias.")
               
    add_body_p(doc, 
               "El presente proyecto propone un cambio de paradigma tecnológico y operativo: la evolución desde sistemas CRUD tradicionales hacia un ERP con Auditoría Predictiva y Reactiva Integrada. Mediante la sinergia entre una base de datos relacional robusta (PostgreSQL 16) potenciada con extensiones vectoriales (pgvector), algoritmos estadísticos de desvío cuadrático (Z-Score), modelos de lenguaje de última generación para análisis semántico (Llama 3.3 70B vía Groq) y comunicación bidireccional reactiva (Laravel Reverb), el sistema actúa como un auditor autónomo que inspecciona el 100% de las transacciones comerciales en menos de 250 milisegundos, bloqueando compras fraudulentas o sobreprecios antes de que se desembolse un solo boliviano.")

    # ========================================================
    # 3. ANTECEDENTES
    # ========================================================
    add_academic_heading(doc, "3. ANTECEDENTES", 1)
    
    add_body_p(doc, 
               "Andina S.R.L. fue constituida formalmente hace más de 12 años como una empresa familiar orientada a abastecer ferreterías medianas y talleres de manufactura en Cochabamba. Su crecimiento progresivo la llevó a importar directamente contenedores de herramientas y suministros industriales desde mercados como Brasil, China y Argentina, lo que incrementó significativamente la complejidad de su cadena de suministro.")
               
    add_body_p(doc, 
               "Durante el último trienio, la gerencia general identificó recurrentes fricciones operativas entre el Departamento de Compras y la Jefatura de Almacén. Al analizar auditorías de cierre de gestión correspondientes a los años 2023, 2024 y 2025, se evidenció que la empresa sufrió compras con sobreprecios no autorizados de entre el 15% y el 42% respecto a las cotizaciones históricas promedio del mercado, compras canalizadas a proveedores recién creados que no contaban con certificación de calidad, y diferencias sistemáticas entre la cantidad física recibida en almacén y lo facturado.")
               
    add_body_p(doc, 
               "Actualmente, la empresa cuenta con 22 colaboradores en planta, de los cuales 6 intervienen directamente en el ciclo comercial: el Gerente General, un Auditor Interno, dos Oficiales de Adquisiciones y dos Encargados de Almacén Central. No existe integración automatizada entre la recepción del bien (remisión física) y la orden de compra aprobada, realizándose cotejos manuales en papel. Esta desconexión tecnológica ha colocado a la empresa en una situación de alta vulnerabilidad frente a errores humanos involuntarios, colusión interna y falta de visibilidad analítica para la toma de decisiones estratégicas.")

    # ========================================================
    # 4. FORMULACIÓN DEL PROBLEMA
    # ========================================================
    add_academic_heading(doc, "4. FORMULACIÓN DEL PROBLEMA", 1)
    
    add_body_p(doc, 
               "El problema central que aqueja a la Importadora y Distribuidora Comercial Andina S.R.L. se sintetiza en la ineficiencia operativa, falta de trazabilidad y vulnerabilidad financiera en la gestión de compras e inventarios originada por el uso de sistemas transaccionales pasivos y la ejecución extemporánea del control interno de auditoría. Para que este planteamiento posea rigor científico y sea rigurosamente evaluable en el marco de la Ingeniería de Software y la Preparación y Evaluación de Proyectos, el problema se desglosa en tres dimensiones empíricas y cuantitativamente medibles:",
               bold_prefix="Planteamiento Central: ")

    # Tres problemas medibles y narrados
    add_body_p(doc, 
               "En el esquema operativo vigente, el proceso de revisión de facturas, cotejo de precios unitarios con cotizaciones previas y verificación de saldos de inventario se lleva a cabo de manera manual y ex-post al cierre de cada mes. Esto genera una latencia o retraso temporal promedio de 35 a 45 días calendario entre el momento en que se genera una orden de compra irregular y el momento en que auditoría interna detecta la inconsistencia. Debido a que las políticas comerciales y bancarias exigen el pago a proveedores a 15 días plazo o contra entrega, cuando la auditoría concluye su informe el desembolso de fondos ya fue consumado, imposibilitando cualquier anulación, retención preventiva o recuperación económica.",
               bold_prefix="Dimensión 1: Tiempo de Detección y Retención Ex-Post (Medible en Días Calendario). ")
               
    add_body_p(doc, 
               "Debido a la ausencia de un software integrado con validaciones en tiempo real, de las más de 200 órdenes de compra y 650 movimientos de almacén registrados mensualmente, el área de auditoría interna únicamente tiene capacidad física para auditar una muestra selectiva que no supera el 12% del volumen total transaccionado. Esto implica que el 88% de las transacciones comerciales ingresan al inventario y se liquidan financieramente a ciegas, acumulando una merma no justificada promedio del 14.5% mensual sobre los ítems de alta rotación en almacén central, atribuible a despachos no conciliados, errores de digitación de SKUs y recepciones deficientes.",
               bold_prefix="Dimensión 2: Cantidad y Cobertura de Control Operacional (Medible en Porcentaje y Volumen Transaccional). ")
               
    add_body_p(doc, 
               "La concurrencia de sobreprecios no advertidos, mermas físicas no justificadas y penalizaciones por quiebres de stock representó para Andina S.R.L. una pérdida económica directa acumulada de 142.500,00 Bolivianos (Bs.) durante la gestión fiscal 2025. Los sobreprecios detectados aleatoriamente superaron hasta en un 42% la media móvil del ítem, afectando el margen bruto de comercialización en 5.8 puntos porcentuales y reduciendo el flujo de caja operativo disponible para nuevas importaciones de capital de trabajo.",
               bold_prefix="Dimensión 3: Costo e Impacto Financiero Directo (Medible en Moneda Nacional - Bolivianos). ")

    # ========================================================
    # 5. IDENTIFICACIÓN DEL PROBLEMA (ISHIKAWA Y ÁRBOL)
    # ========================================================
    add_academic_heading(doc, "5. IDENTIFICACIÓN DEL PROBLEMA: CAUSA RAÍZ Y ÁRBOL DE PROBLEMAS", 1)
    
    add_body_p(doc, 
               "Para desentrañar la etiología multifactorial del problema y evitar atacar únicamente los síntomas superficiales, se procedió a una doble identificación analítica: el Diagrama de Causa-Efecto de Ishikawa bajo el estándar de las 6M (adaptado a la industria comercial de software) y el Diagrama del Árbol de Problemas.")
               
    add_academic_heading(doc, "5.1. Diagrama de Causa y Efecto de Ishikawa (Las 6M)", 2)
    
    # Incrustar imagen Ishikawa
    ishikawa_path = os.path.join(DIAGRAMS_DIR, "ishikawa_andina.png")
    if os.path.exists(ishikawa_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(ishikawa_path, width=Inches(6.2))
        p_caption = doc.add_paragraph()
        p_caption.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r_cap = p_caption.add_run("Figura 1: Diagrama de Causa-Efecto de Ishikawa (6M) para la Gestión de Compras e Inventarios en Andina S.R.L.")
        r_cap.italic = True
        r_cap.font.size = Pt(9.5)
        r_cap.font.name = "Calibri"

    add_body_p(doc, 
               "El análisis de las 6 categorías del diagrama de Ishikawa revela las siguientes fuentes de ineficiencia:",
               bold_prefix="Interpretación de las 6 Categorías: ")
               
    ishikawa_bullets = [
        ("Métodos de Trabajo: ", "Inexistencia de un protocolo formal de autorización escalonada. Las órdenes de compra se autorizan por mensajería informal o firmas rápidas en papel sin consulta previa al kardex valorado."),
        ("Mano de Obra / Personal: ", "Falta de capacitación en técnicas de compras estratégicas, rotación de auxiliares de almacén y colusión o permisividad entre compradores y proveedores habituales."),
        ("Maquinaria / Sistemas: ", "Uso de sistemas de escritorio obsoletos, bases de datos desconectadas y hojas de cálculo no relacionales que carecen de validaciones lógicas, auditoría automática o encriptación."),
        ("Materiales / Datos: ", "Dispersión de cotizaciones en correos personales, catálogos desactualizados de proveedores, inconsistencia en códigos SKU y ausencia de digitalización de facturas de entrega."),
        ("Medición y Control: ", "Ausencia de indicadores estadísticos en tiempo real (Z-Score, medias móviles de costos) y nula correlación entre el precio cotizado y el precio de mercado al momento del registro."),
        ("Medio Ambiente / Entorno: ", "Mercado altamente informal en Bolivia, presión por compras de emergencia para no detener entregas y volatilidad en los tipos de cambio para insumos importados.")
    ]
    for b_title, b_desc in ishikawa_bullets:
        p_b = doc.add_paragraph(style='List Bullet')
        p_b.paragraph_format.space_after = Pt(3)
        r_bt = p_b.add_run(b_title)
        r_bt.bold = True
        r_bt.font.name = "Calibri"
        r_bd = p_b.add_run(b_desc)
        r_bd.font.name = "Calibri"

    add_academic_heading(doc, "5.2. Estructura del Árbol de Problemas", 2)
    
    add_body_p(doc, 
               "En la estructura jerárquica del Árbol de Problemas, se establece una clara diferenciación entre las causas estructurales (raíces), el problema central (tronco) y las consecuencias o efectos directos e indirectos (ramas y copa):")
               
    tbl_tree = doc.add_table(rows=4, cols=2)
    tbl_tree.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_tree.autofit = False
    
    headers_t = [("Nivel del Árbol", 1.8), ("Descripción y Componentes Identificados", 4.7)]
    for i, (name, width) in enumerate(headers_t):
        cell = tbl_tree.cell(0, i)
        cell.width = Inches(width)
        set_cell_background(cell, "102C57")
        p = cell.paragraphs[0]
        run = p.add_run(name)
        run.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        run.font.name = "Calibri"
        set_cell_margins(cell, 80, 80, 100, 100)
        
    data_t = [
        ("COPA / RAMAS\n(Efectos Finales)", "E1: Pérdida económica de 142.500 Bs./año por compras con sobreprecio y desvíos no detectados.\nE2: Mermas no justificadas del 14.5% y quiebres de stock en almacén por recepción no conciliada.\nE3: Riesgo legal e impositivo ante el SIN por transacciones con proveedores no validados."),
        ("TRONCO\n(Problema Central)", "Vulnerabilidad en el control interno, desfasaje temporal de 35 a 45 días en auditoría y falta de trazabilidad automatizada en el ciclo de compras e inventarios de Andina S.R.L."),
        ("RAÍCES\n(Causas Principales)", "C1: Empleo de sistemas CRUD pasivos que registran datos sin validar desvíos estadísticos al instante.\nC2: Inexistencia de mecanismos asíncronos y vectoriales para cotejar compras contra el histórico del proveedor.\nC3: Ausencia de canales de alerta en tiempo real (WebSockets) que impidan el pago antes del desembolso.")
    ]
    
    for row_idx, (level_name, desc_text) in enumerate(data_t, start=1):
        bg = "F4F6F9" if row_idx % 2 == 1 else "FFFFFF"
        c0 = tbl_tree.cell(row_idx, 0)
        c1 = tbl_tree.cell(row_idx, 1)
        c0.width = Inches(1.8)
        c1.width = Inches(4.7)
        set_cell_background(c0, bg)
        set_cell_background(c1, bg)
        set_cell_margins(c0, 70, 70, 90, 90)
        set_cell_margins(c1, 70, 70, 90, 90)
        
        p0 = c0.paragraphs[0]
        r0 = p0.add_run(level_name)
        r0.bold = True
        r0.font.size = Pt(10)
        r0.font.name = "Calibri"
        
        p1 = c1.paragraphs[0]
        r1 = p1.add_run(desc_text)
        r1.font.size = Pt(9.5)
        r1.font.name = "Calibri"

    # ========================================================
    # 6. CAUSA Y EFECTO
    # ========================================================
    add_academic_heading(doc, "6. MATRIZ DE CAUSA Y EFECTO", 1)
    
    add_body_p(doc, 
               "La interrelación directa entre cada causa identificada en el diagnóstico operativo y su repercusión directa en los estados de resultados y operaciones de la distribuidora se detalla a continuación:")

    tbl_ce = doc.add_table(rows=4, cols=3)
    tbl_ce.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_ce.autofit = False
    
    headers_ce = [("Causa Raíz Operacional (Origen)", 2.2), ("Mecanismo de Falla / Brecha", 2.0), ("Efecto e Impacto Negativo (Resultado)", 2.3)]
    for i, (name, width) in enumerate(headers_ce):
        cell = tbl_ce.cell(0, i)
        cell.width = Inches(width)
        set_cell_background(cell, "00A86B") # Green emerald
        p = cell.paragraphs[0]
        run = p.add_run(name)
        run.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        run.font.name = "Calibri"
        set_cell_margins(cell, 80, 80, 100, 100)
        
    data_ce = [
        ("C1: Proceso de compras manual y registro en hojas de cálculo sin validación algorítmica.",
         "Los operadores de compras digitan precios pactados discrecionalmente sin cotejo automático de cotizaciones históricas ni alertas de desvío.",
         "E1: Pago consolidado de sobreprecios de hasta el 42% (impacto de 142.500 Bs./año) no recuperable por detectarse 45 días tarde."),
         
        ("C2: Recepción física de almacén desconectada de la orden de compra autorizada.",
         "Los encargados de almacén firman notas de remisión en papel sin verificar en línea si los SKUs, marcas y cantidades coinciden con la orden aprobada.",
         "E2: Pérdidas por merma ficticia del 14.5% mensual, recepción de mercadería de menor especificación técnica y quiebres de stock imprevistos."),
         
        ("C3: Dependencia exclusiva de auditoría manual ex-post sobre muestras menores al 12%.",
         "La sobrecarga de transacciones impide la inspección universal; el 88% de las compras nunca es analizado por el auditor interno.",
         "E3: Asimetría de información en gerencia general, imposibilidad de auditar en tiempo real y vulnerabilidad ante colusión comercial.")
    ]
    
    for row_idx, (c, m, e) in enumerate(data_ce, start=1):
        bg = "F9FAF7" if row_idx % 2 == 1 else "FFFFFF"
        for col_idx, text_val in enumerate([c, m, e]):
            cell = tbl_ce.cell(row_idx, col_idx)
            cell.width = Inches(headers_ce[col_idx][1])
            set_cell_background(cell, bg)
            set_cell_margins(cell, 70, 70, 80, 80)
            p = cell.paragraphs[0]
            r = p.add_run(text_val)
            r.font.size = Pt(9.5)
            r.font.name = "Calibri"

    # ========================================================
    # 7. OBJETIVO GENERAL
    # ========================================================
    add_academic_heading(doc, "7. OBJETIVO GENERAL", 1)
    
    add_body_p(doc, 
               "«Desarrollar un sistema de gestión de adquisiciones e inventarios con un motor de auditoría preventiva en tiempo real basado en embeddings vectoriales y modelos de lenguaje, que permita detectar y retener transacciones comerciales anómalas antes de la emisión de pagos, optimizando el control interno y la rentabilidad financiera en la Importadora y Distribuidora Comercial Andina S.R.L.»",
               bold_prefix="Formulación Canónica: ", italic_prefix="")
               
    add_body_p(doc, 
               "El verbo rector seleccionado es «Desarrollar», el cual engloba de manera integral todas las fases del ciclo de vida del software (análisis, diseño arquitectónico, programación, integración de inteligencia artificial y validación experimental), respondiendo directamente a la solución definitiva de las causas del problema formulado.")

    # ========================================================
    # 8. OBJETIVOS ESPECÍFICOS Y ACCIONES
    # ========================================================
    add_academic_heading(doc, "8. OBJETIVOS ESPECÍFICOS Y ACCIONES OPERATIVAS", 1)
    
    add_body_p(doc, 
               "En estricto apego a la metodología académica de Ingeniería de Software y Preparación de Proyectos, ninguno de los objetivos específicos repite el verbo rector del objetivo general ('Desarrollar'), ni existe duplicidad de verbos entre los propios objetivos específicos. Cada objetivo específico cuenta con tres acciones directas y operativas que materializan su cumplimiento:")

    objectives_data = [
        {
            "num": "8.1",
            "verbo": "Diagnosticar",
            "obj": "Diagnosticar los procesos actuales de compras, recepción física en almacén y mecanismos de auditoría interna de Andina S.R.L. para establecer la línea base de tiempos, costos y tolerancias de desvío.",
            "acciones": [
                ("Acción 1.1: ", "Levantar los requerimientos funcionales y no funcionales mediante entrevistas estructuradas al Gerente General, Oficiales de Compras y Almaceneros."),
                ("Acción 1.2: ", "Recopilar y depurar el histórico transaccional de los últimos 24 meses (500 compras y movimientos) para modelar la media de precios y desviación estándar por SKU."),
                ("Acción 1.3: ", "Elaborar la matriz de riesgos operacionales y el diagrama de procesos As-Is / To-Be formalizando los umbrales de alerta (Z >= 2.2 y similitud coseno < 0.80).")
            ]
        },
        {
            "num": "8.2",
            "verbo": "Diseñar",
            "obj": "Diseñar la arquitectura de software en capas, los modelos de datos relacionales-vectoriales y las interfaces interactivas para garantizar alta disponibilidad y respuesta en submilisegundos.",
            "acciones": [
                ("Acción 2.1: ", "Modelar la base de datos relacional en PostgreSQL 16 incorporando la extensión pgvector con columnas de 768 dimensiones e índices HNSW (vector_cosine_ops)."),
                ("Acción 2.2: ", "Definir los contratos canónicos de datos JSON para la normalización de compras y comunicación estricta con la API de inferencia LLM (Groq Llama 3.3 70B)."),
                ("Acción 2.3: ", "Diseñar las interfaces de usuario reactivas en FilamentPHP v3 y Livewire 3 adaptadas a la matriz de roles y permisos (Gerente, Auditor, Compras, Almacén).")
            ]
        },
        {
            "num": "8.3",
            "verbo": "Implementar",
            "obj": "Implementar los módulos transaccionales del ERP y el motor asíncrono de auditoría inteligente integrando colas Redis, vectorización local y difusión reactiva por WebSockets.",
            "acciones": [
                ("Acción 3.1: ", "Codificar los módulos transaccionales de Catálogo de Proveedores, Órdenes de Compra con maestro-detalle y Control de Almacén bajo tipado estricto PHP 8.3."),
                ("Acción 3.2: ", "Programar el pipeline asíncrono con Laravel Observers y Jobs en Redis para la vectorización local (nomic-embed-text) y dictamen analítico en Groq."),
                ("Acción 3.3: ", "Desplegar el servidor de WebSockets con Laravel Reverb para la transmisión instantánea del evento AnomalyDetectedEvent hacia el panel de auditoría.")
            ]
        },
        {
            "num": "8.4",
            "verbo": "Evaluar",
            "obj": "Evaluar el comportamiento funcional, la precisión del motor de auditoría predictiva y la viabilidad socioeconómica del proyecto mediante pruebas empíricas e indicadores de evaluación financiera.",
            "acciones": [
                ("Acción 4.1: ", "Ejecutar pruebas unitarias, de integración y de mutación automatizadas bajo el framework PestPHP garantizando una cobertura de código superior al 85%."),
                ("Acción 4.2: ", "Calcular la precisión, exhaustividad (recall) y tasa de falsos positivos del motor Z-Score y Distancia de Coseno sobre el dataset de 530 transacciones de prueba."),
                ("Acción 4.3: ", "Computar los indicadores de viabilidad financiera (VAN de 213.450 Bs., TIR del 48.2%, Relación B/C de 2.45 y Payback de 1.4 años) certificando la rentabilidad de la inversión.")
            ]
        }
    ]

    for item in objectives_data:
        add_academic_heading(doc, f"{item['num']}. Objetivo Específico: {item['verbo']}", 2)
        add_body_p(doc, item['obj'], bold_prefix="Enunciado: ")
        
        p_act_lbl = doc.add_paragraph()
        p_act_lbl.paragraph_format.space_before = Pt(4)
        p_act_lbl.paragraph_format.space_after = Pt(2)
        r_lbl = p_act_lbl.add_run("Acciones Operativas Requeridas:")
        r_lbl.bold = True
        r_lbl.font.size = Pt(10.5)
        r_lbl.font.name = "Calibri"
        
        for act_tag, act_desc in item['acciones']:
            p_a = doc.add_paragraph(style='List Bullet')
            p_a.paragraph_format.space_after = Pt(3)
            r_at = p_a.add_run(act_tag)
            r_at.bold = True
            r_at.font.name = "Calibri"
            r_ad = p_a.add_run(act_desc)
            r_ad.font.name = "Calibri"

    # ========================================================
    # 9. COMPONENTES Y VINCULACIÓN CON LA CLASE DE MAÑANA
    # ========================================================
    add_academic_heading(doc, "9. SÍNTESIS DE ARQUITECTURA TÉCNICA Y AVANCE ITERACIÓN 1", 1)
    
    comp_path = os.path.join(DIAGRAMS_DIR, "diagrama_componentes.png")
    if os.path.exists(comp_path):
        p_img_c = doc.add_paragraph()
        p_img_c.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img_c.paragraph_format.space_before = Pt(8)
        p_img_c.paragraph_format.space_after = Pt(4)
        run_img_c = p_img_c.add_run()
        run_img_c.add_picture(comp_path, width=Inches(6.2))
        p_caption_c = doc.add_paragraph()
        p_caption_c.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r_cap_c = p_caption_c.add_run("Figura 2: Arquitectura del Sistema ERP y Motor de Auditoría Vectorial en Tiempo Real")
        r_cap_c.italic = True
        r_cap_c.font.size = Pt(9.5)
        r_cap_c.font.name = "Calibri"

    add_body_p(doc, 
               "El presente documento y el código fuente correspondiente al Sprint 1 (modelos relacionales tipados, migraciones PostgreSQL 16 con extensión vectorial pgvector, seeders con histórico de 530 transacciones y suite de pruebas PestPHP) constituyen la evidencia formal para la defensa de clase. Los entregables han sido verificados para garantizar compatibilidad nativa multiplataforma (Linux Podman / Windows PowerShell & Podman Desktop) y tipado estricto.",
               bold_prefix="Conclusión de Entrega: ")

    doc.save(DOCX_OUTPUT)
    print(f"✅ Documento Word de clase generado: {DOCX_OUTPUT}")

    # Compilar a PDF mediante LibreOffice
    print("⏳ Compilando a PDF con LibreOffice...")
    cmd = ["libreoffice", "--headless", "--convert-to", "pdf", DOCX_OUTPUT, "--outdir", os.path.dirname(PDF_OUTPUT)]
    subprocess.run(cmd, check=True)
    print(f"✅ Documento PDF de clase compilado: {PDF_OUTPUT}")

if __name__ == "__main__":
    main()
