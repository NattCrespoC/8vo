#!/usr/bin/env python3
import os
import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np

os.makedirs("docs/diagrams", exist_ok=True)

# Configurar estilo general
plt.style.use('seaborn-v0_8-whitegrid' if 'seaborn-v0_8-whitegrid' in plt.style.available else 'default')
plt.rcParams['font.sans-serif'] = 'DejaVu Sans'
plt.rcParams['font.family'] = 'sans-serif'

# ==========================================
# 1. CRONOGRAMA GANTT DE SPRINTS (02 al 25 Nov)
# ==========================================
fig, ax = plt.subplots(figsize=(12, 6), dpi=300)

tasks = [
    "Sprint 1: Cimientos, Podman, RBAC y Catálogos",
    "Sprint 2: Vectorización Ollama, pgvector y Groq",
    "Sprint 3: WebSockets Reverb, Multimodal y Copiloto",
    "Sprint 4: Calidad TDD (Pest), Estudio PEP y Defensa",
]
starts = [2, 7, 14, 20]
durations = [5, 7, 6, 6]
colors = ['#2563eb', '#0891b2', '#7c3aed', '#059669']

y_pos = np.arange(len(tasks))

bars = ax.barh(y_pos, durations, left=starts, height=0.55, align='center', color=colors, edgecolor='#1e293b', linewidth=1.5)

for bar, start, dur in zip(bars, starts, durations):
    ax.text(start + dur / 2, bar.get_y() + bar.get_height() / 2, f"{dur} días ({start} - {start + dur - 1} Nov)",
            ha='center', va='center', color='white', fontweight='bold', fontsize=11)

ax.set_yticks(y_pos)
ax.set_yticklabels(tasks, fontweight='bold', fontsize=11)
ax.invert_yaxis()
ax.set_xlabel("Días de Noviembre 2026", fontweight='bold', fontsize=12)
ax.set_xlim(1, 26)
ax.set_xticks(range(2, 26, 2))
ax.set_title("Cronograma de Sprints e Iteraciones del Proyecto ERP (02 al 25 de Noviembre)", fontsize=14, fontweight='bold', pad=15)
plt.tight_layout()
plt.savefig("docs/diagrams/01_cronograma_gantt_sprints.png", dpi=300)
plt.close()
print("✅ Gráfico 1: 01_cronograma_gantt_sprints.png generado.")

# ==========================================
# 2. DIAGRAMA DE ISHIKAWA (Causa - Efecto)
# ==========================================
fig, ax = plt.subplots(figsize=(14, 7), dpi=300)
ax.set_xlim(0, 100)
ax.set_ylim(0, 50)
ax.axis('off')

# Espina dorsal central
ax.annotate('', xy=(88, 25), xytext=(10, 25),
            arrowprops=dict(arrowstyle="->", color='#1e293b', lw=4))

# Caja del problema central (Cabeza del pez)
problem_box = patches.FancyBboxPatch((88, 16), 11, 18, boxstyle="round,pad=0.5",
                                     edgecolor='#dc2626', facecolor='#fee2e2', lw=2)
ax.add_patch(problem_box)
ax.text(93.5, 25, "PROBLEMA CENTRAL:\nPérdidas Financieras y\nDemora en Detección\nde Desvíos en Compras\ne Inventarios Andina",
        ha='center', va='center', color='#991b1b', fontweight='bold', fontsize=8.5)

# Espinas superiores
# Categoria 1: Metodología
ax.annotate('', xy=(30, 25), xytext=(20, 42), arrowprops=dict(arrowstyle="->", color='#2563eb', lw=2.5))
ax.text(20, 44, "METODOLOGÍA\nAuditorías manuales post-mortem (30 días)", ha='center', va='bottom', fontsize=9, fontweight='bold', color='#1d4ed8')

# Categoria 2: Tecnología
ax.annotate('', xy=(55, 25), xytext=(45, 42), arrowprops=dict(arrowstyle="->", color='#0891b2', lw=2.5))
ax.text(45, 44, "TECNOLOGÍA\nSistema actual sin validación matemática", ha='center', va='bottom', fontsize=9, fontweight='bold', color='#0e7490')

# Categoria 3: Personal
ax.annotate('', xy=(80, 25), xytext=(70, 42), arrowprops=dict(arrowstyle="->", color='#7c3aed', lw=2.5))
ax.text(70, 44, "PERSONAL\nSobrecarga operativa y digitación errónea", ha='center', va='bottom', fontsize=9, fontweight='bold', color='#6d28d9')

# Espinas inferiores
# Categoria 4: Control Interno
ax.annotate('', xy=(30, 25), xytext=(20, 8), arrowprops=dict(arrowstyle="->", color='#dc2626', lw=2.5))
ax.text(20, 6, "CONTROL INTERNO\nSin alertas en vivo ni umbrales Z-Score", ha='center', va='top', fontsize=9, fontweight='bold', color='#b91c1c')

# Categoria 5: Datos / Insumos
ax.annotate('', xy=(55, 25), xytext=(45, 8), arrowprops=dict(arrowstyle="->", color='#ea580c', lw=2.5))
ax.text(45, 6, "DATOS / INSUMOS\nPrecios sin indexar a historia de compras", ha='center', va='top', fontsize=9, fontweight='bold', color='#c2410c')

# Categoria 6: Procesos
ax.annotate('', xy=(80, 25), xytext=(70, 8), arrowprops=dict(arrowstyle="->", color='#059669', lw=2.5))
ax.text(70, 6, "PROCESOS\nCarencia de trazabilidad en tiempo real (RBAC)", ha='center', va='top', fontsize=9, fontweight='bold', color='#047857')

ax.set_title("Diagrama de Causa y Efecto (Ishikawa) - Caso de Estudio: Andina S.R.L.", fontsize=14, fontweight='bold', pad=20)
plt.tight_layout()
plt.savefig("docs/diagrams/02_ishikawa_causa_efecto.png", dpi=300)
plt.close()
print("✅ Gráfico 2: 02_ishikawa_causa_efecto.png generado.")

# ==========================================
# 3. ARQUITECTURA DE COMPONENTES DEL ERP
# ==========================================
fig, ax = plt.subplots(figsize=(13, 8), dpi=300)
ax.set_xlim(0, 100)
ax.set_ylim(0, 100)
ax.axis('off')

# Capas
layers = [
    {"name": "CAPA PRESENTACIÓN REACTIVA (FilamentPHP v3 + Livewire 3)", "y": 80, "color": "#dbeafe", "edge": "#2563eb",
     "items": ["Dashboard Reactivo\n(KPIs & Ahorro Bs.)", "Módulo Compras\n(Maestro-Detalle)", "Kardex Valorado &\nRecepción Multimodal", "Centro Auditoría IA\n(Dictámenes JSON)"]},
    {"name": "CAPA DE LÓGICA Y COMPONENTES DESACOPLADOS (Laravel 11)", "y": 52, "color": "#fef3c7", "edge": "#d97706",
     "items": ["Procurement Component\n(Supplier & Orders)", "Warehouse Component\n(Kardex & Stocks)", "AuditEngine Component\n(Embedding & Z-Score)", "Realtime Component\n(Reverb WebSockets)"]},
    {"name": "CAPA DE PERSISTENCIA Y CONTENEDORES (Podman)", "y": 25, "color": "#e0e7ff", "edge": "#4f46e5",
     "items": ["PostgreSQL 16\n(pgvector 768d + HNSW)", "Redis Cache & Queue\n(Jobs Asíncronos)", "Storage Local / S3\n(Facturas & Audios)", "Conexión ReadOnly\n(Text-to-SQL Seguro)"]},
    {"name": "MOTORES DE INTELIGENCIA ARTIFICIAL (Costo $0)", "y": 2, "color": "#dcfce7", "edge": "#16a34a",
     "items": ["Ollama Local\n(nomic-embed-text 768d)", "Groq Cloud LLM\n(Llama 3.3 70B JSON)", "Whisper Neural\n(Voz a Texto Almacén)", "Vision AI\n(OCR de Facturas)"]}
]

for layer in layers:
    y = layer["y"]
    rect = patches.FancyBboxPatch((4, y), 92, 19, boxstyle="round,pad=0.5",
                                  edgecolor=layer["edge"], facecolor=layer["color"], lw=2)
    ax.add_patch(rect)
    ax.text(6, y + 16, layer["name"], fontsize=10.5, fontweight='bold', color=layer["edge"])
    
    # Cajas de ítems dentro de la capa
    x_positions = [6, 29, 52, 75]
    for x, item in zip(x_positions, layer["items"]):
        item_box = patches.FancyBboxPatch((x, y + 2), 20, 11, boxstyle="round,pad=0.3",
                                          edgecolor='#94a3b8', facecolor='white', lw=1)
        ax.add_patch(item_box)
        ax.text(x + 10, y + 7.5, item, ha='center', va='center', fontsize=8.5, fontweight='bold', color='#1e293b')

ax.set_title("Arquitectura de Componentes Modulares Desacoplados (ERP con Auditoría IA)", fontsize=14, fontweight='bold', pad=15)
plt.tight_layout()
plt.savefig("docs/diagrams/03_arquitectura_componentes.png", dpi=300)
plt.close()
print("✅ Gráfico 3: 03_arquitectura_componentes.png generado.")

# ==========================================
# 4. DISTRIBUCIÓN DE ANOMALÍAS Y Z-SCORE
# ==========================================
fig, ax = plt.subplots(figsize=(10, 5.5), dpi=300)

x = np.linspace(-4, 4, 1000)
y = (1 / np.sqrt(2 * np.pi)) * np.exp(-0.5 * x**2)

ax.plot(x, y, color='#1e293b', lw=2.5, label='Distribución Histórica de Precios Unitarios')

# Zona Normal (|Z| < 1.5)
ax.fill_between(x, y, where=(x >= -1.5) & (x <= 1.5), color='#22c55e', alpha=0.3, label='Riesgo BAJO: Operación Normal (|Z| ≤ 1.5)')

# Zona Alerta Media (1.5 < Z < 2.2)
ax.fill_between(x, y, where=(x > 1.5) & (x < 2.2), color='#eab308', alpha=0.4, label='Riesgo MEDIO: Desvío Aceptable (1.5 < Z < 2.2)')

# Zona Crítica (Z >= 2.2)
ax.fill_between(x, y, where=(x >= 2.2), color='#ef4444', alpha=0.6, label='Riesgo CRÍTICO / ALTO: Bloqueo Inmediato (Z ≥ 2.2)')

# Línea del umbral crítico
ax.axvline(2.2, color='#b91c1c', linestyle='--', lw=2, label='Umbral Crítico de Bloqueo (Z = +2.2)')

# Anotación
ax.annotate('Transacción Anómala Detectada\n(Ej. Sobreprecio +45%, Z = +3.4)',
            xy=(3.4, 0.01), xytext=(2.3, 0.22),
            arrowprops=dict(facecolor='#b91c1c', shrink=0.05, width=1.5, headwidth=8),
            fontweight='bold', fontsize=9, color='#991b1b',
            bbox=dict(boxstyle="round,pad=0.4", facecolor='#fee2e2', edgecolor='#b91c1c', lw=1.5))

ax.set_title("Modelo Estadístico de Detección de Sobreprecios por Z-Score", fontsize=13, fontweight='bold', pad=12)
ax.set_xlabel("Desviaciones Estándar respecto a la Media Histórica (Z-Score)", fontweight='bold', fontsize=10)
ax.set_ylabel("Densidad de Probabilidad", fontweight='bold', fontsize=10)
ax.legend(loc='upper left', frameon=True, fontsize=8.5)
plt.tight_layout()
plt.savefig("docs/diagrams/04_distribucion_anomalias_zscore.png", dpi=300)
plt.close()
print("✅ Gráfico 4: 04_distribucion_anomalias_zscore.png generado.")
