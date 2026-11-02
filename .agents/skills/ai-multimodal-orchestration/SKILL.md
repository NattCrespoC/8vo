---
name: ai-multimodal-orchestration
description: "Integration patterns for hybrid AI: Ollama local embeddings (nomic-embed-text), Groq Cloud inference (Llama 3.3 70B JSON schema), Whisper voice notes transcription, Vision invoice parsing, and safe read-only Text-to-SQL. Use when interacting with Ollama, Groq, building LLM services, or designing conversational agents."
---

# AI Multimodal Orchestration Skill

This skill documents how to integrate and orchestrate local and cloud AI models within Laravel 11 without incurring API costs while maintaining enterprise-grade response times ($< 400\text{ ms}$).

## 1. Local Embeddings: Ollama Client (`EmbeddingService.php`)

- **Modelo:** `nomic-embed-text`
- **Dimensiones:** `768`
- **Ubicación:** `http://127.0.0.1:11434/api/embeddings`
- **Latencia promedio:** 15 a 30 ms en CPU/iGPU.

```php
namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class EmbeddingService
{
    public function generate(string $canonicalText): array
    {
        $response = Http::timeout(5)
            ->retry(2, 100)
            ->post(config('services.ollama.url') . '/api/embeddings', [
                'model' => 'nomic-embed-text',
                'prompt' => $canonicalText,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException("Error conectando con Ollama: " . $response->body());
        }

        return $response->json('embedding');
    }
}
```

---

## 2. Cloud Audit Opinion: Groq API (`GroqAuditService.php`)

- **Endpoint:** `https://api.groq.com/openai/v1/chat/completions`
- **Modelo:** `llama-3.3-70b-versatile`
- **Salida requerida:** Estricta en JSON (`response_format: { type: "json_object" }`).

```php
namespace App\Services\AI;

use Illuminate\Support\Facades\Http;

class GroqAuditService
{
    public function generateDictamen(array $transactionDetails, float $cosineSimilarity, float $maxZScore, array $anomalousItems): array
    {
        $systemPrompt = <<<SYS
Eres el auditor financiero principal de la empresa "Andina S.R.L.". Tu tarea es analizar discrepancias en órdenes de compra y emitir un dictamen técnico estricto.
Debes responder ÚNICAMENTE con un objeto JSON con las siguientes claves:
{
  "is_anomalous": boolean,
  "risk_level": "BAJO" | "MEDIO" | "ALTO" | "CRITICO",
  "confidence_percentage": number (0-100),
  "primary_reason": string,
  "financial_impact_bs": number,
  "recommended_action": string
}
SYS;

        $userPrompt = "Datos de la transacción:\n" . json_encode([
            'detalles' => $transactionDetails,
            'similitud_coseno' => $cosineSimilarity,
            'z_score_maximo' => $maxZScore,
            'items_con_desvio' => $anomalousItems,
        ], JSON_PRETTY_PRINT);

        $response = Http::withToken(config('services.groq.api_key'))
            ->timeout(10)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

        return json_decode($response->json('choices.0.message.content'), true);
    }
}
```

---

## 3. Multimodal Warehouse Ingestion (Whisper + Vision)

### A. Transcripción de Audio de Almacén (`WhisperService.php`)
- **Modelo Groq:** `whisper-large-v3-turbo`
- **Uso:** El operario en almacén envía una nota de voz ("Llegaron 50 cajas pero 4 tienen el sello roto...").

```php
$response = Http::withToken(config('services.groq.api_key'))
    ->attach('file', file_get_contents($audioPath), 'audio.m4a')
    ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
        'model' => 'whisper-large-v3-turbo',
        'language' => 'es',
    ]);
$transcript = $response->json('text');
```

### B. Extracción OCR de Facturas / Remisiones (`VisionInvoiceService.php`)
- **Modelo Groq:** `llama-3.2-11b-vision-preview`
- **Entrada:** Imagen en Base64 o URL.
- **Salida:** JSON estructurado con NIT del emisor, número de factura, ítems, cantidades y subtotales para autollenar el formulario.

---

## 4. Text-to-SQL Seguro de Solo Lectura

- **Principio de Seguridad:** Las consultas generadas por el modelo NUNCA se ejecutan sobre la conexión `pgsql` principal con permisos de escritura.
- **Conexión:** `pgsql_readonly` configurada con un usuario PostgreSQL que únicamente posee privilegios `GRANT SELECT`.
- **Validación previa:** Regex estricto que rechaza sentencias que contengan `INSERT`, `UPDATE`, `DELETE`, `DROP`, `TRUNCATE`, `ALTER` o `;`.
