---
name: opennextjs-cloudflare
description: Best practices, build workflows, caching, bindings access, and runtime optimization for running Next.js applications on Cloudflare Workers with OpenNext (@opennextjs/cloudflare). Use when writing Next.js App Router code for Cloudflare, configuring open-next.config.ts, accessing Cloudflare bindings (KV, D1, R2, Hyperdrive, AI), troubleshooting workerd bundle errors, or deploying with Wrangler.
license: MIT
metadata:
  author: soft-paws-solutions
  version: "1.0.0"
---

# OpenNext on Cloudflare Workers

Guide for building, optimizing, and deploying Next.js applications on Cloudflare Workers using `@opennextjs/cloudflare`.

## Core Architecture

OpenNext transforms standard Next.js build output into a Cloudflare Worker powered by `workerd`.

1. **Build Step**:
   - `opennextjs-cloudflare build` compiles Next.js with `next build`, then bundles server chunks into `.open-next/worker.js` and assets into `.open-next/assets/`.
2. **Configuration (`open-next.config.ts`)**:
   - Uses `defineCloudflareConfig({ ... })` to specify caching layers, placement, and bundle behavior.
3. **Runtime Context**:
   - Access Cloudflare bindings in Route Handlers and Server Components using:
     ```ts
     import { getCloudflareContext } from '@opennextjs/cloudflare';
     
     const { env, cf, ctx } = await getCloudflareContext({ async: true });
     ```

---

## Best Practices & Runtime Rules

### 1. Cloudflare Bindings & Environment Variables
- In local development (`next dev`), `process.env` holds `.env.local` values. In Cloudflare Workers (`opennextjs-cloudflare`), bindings live on `env` inside the Cloudflare context.
- Always use fallback resolution:
  ```ts
  const cf = await getCloudflareContext({ async: true }).catch(() => null);
  const secretKey = (cf?.env as Record<string, string>)?.MY_SECRET || process.env.MY_SECRET;
  ```
- Never rely on top-level global evaluation for bindings; always resolve them inside request handlers or middleware.

### 2. Edge & Node.js Runtime Compatibility
- In `wrangler.jsonc`, ensure `compatibility_flags` includes `["nodejs_compat"]` to enable Node.js API polyfills.
- Keep `compatibility_date` current.
- Avoid native binaries (`.node` C++ addons) or synchronous filesystem writes (`fs.writeFileSync`). Use Cloudflare KV, R2, or D1 for persistence.

### 3. Incremental Static Regeneration (ISR) & Caching
- To enable persistent ISR across distributed worker isolates, configure an R2 or KV incremental cache in `open-next.config.ts`:
  ```ts
  import { defineCloudflareConfig } from '@opennextjs/cloudflare';
  import r2IncrementalCache from '@opennextjs/cloudflare/overrides/incremental-cache/r2-incremental-cache';

  export default defineCloudflareConfig({
    incrementalCache: r2IncrementalCache,
  });
  ```
- Ensure the R2 bucket binding is configured in `wrangler.jsonc` (e.g. `NEXT_CACHE_WORKERS_KV` or `NEXT_CACHE_R2_BUCKET`).

### 4. Streaming & Large Responses
- When returning binary data (PDFs, Excel spreadsheets, audio), use `new NextResponse(bytes, { headers })` and avoid buffering infinite streams into memory to stay within Worker memory limits (128MB default).
- For large background operations, dispatch to Cloudflare Queues or background worker cron jobs rather than blocking synchronous HTTP request threads.

### 5. Troubleshooting Workerd & Deployments
- **503 Service Unavailable / Script Startup Error**: Check for unhandled exceptions in top-level module code or missing environment variables that crash during worker initialization.
- **Bundle Size Exceeded**: Cloudflare Workers free plan limit is 3MB compressed; paid plan allows up to 500MB. Keep server dependencies lean; use dynamic `import()` for heavy libraries like `pdf-lib` or parsers.
- **Wrangler Preview vs Deploy**:
  - Build & Preview: `opennextjs-cloudflare build && wrangler dev`
  - Deploy: `opennextjs-cloudflare build && wrangler deploy`
