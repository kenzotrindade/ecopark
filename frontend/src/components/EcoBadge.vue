<script setup lang="ts">
import type { EcoMetrics } from '../types/ecopark'

defineProps<{
  metrics: EcoMetrics
}>()
</script>

<template>
  <section class="metrics" aria-label="Métriques de la réponse API">
    <div class="metric">
      <span class="metric-icon" aria-hidden="true">◷</span>
      <div>
        <p class="metric-label">Temps de réponse</p>
        <p class="metric-value">
          {{ metrics.responseTime !== null ? `${metrics.responseTime} ms` : '—' }}
        </p>
      </div>
    </div>

    <div class="metric">
      <span class="metric-icon" aria-hidden="true">↗</span>
      <div>
        <p class="metric-label">Taille du payload</p>
        <p class="metric-value">
          {{ metrics.payloadSize !== null ? `${metrics.payloadSize} octets` : '—' }}
        </p>
      </div>
    </div>

    <div class="metric">
      <span class="metric-icon" aria-hidden="true">♧</span>
      <div>
        <p class="metric-label">CO₂ estimé</p>
        <p class="metric-value">
          {{ metrics.co2 !== null ? `${metrics.co2} g` : '—' }}
        </p>
      </div>
    </div>
  </section>
</template>

<style scoped>
.metrics {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}
.metric {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  padding: 17px;
  border: 1px solid var(--border);
  border-radius: 14px;
  background: var(--surface);
}
.metric-icon {
  display: grid;
  width: 38px;
  height: 38px;
  flex: 0 0 38px;
  place-items: center;
  border-radius: 10px;
  background: var(--accent-soft);
  color: var(--accent);
  font-size: 1.25rem;
}
.metric-label {
  margin: 0 0 5px;
  color: var(--muted);
  font-size: 0.75rem;
}
.metric-value {
  margin: 0;
  overflow-wrap: anywhere;
  font-size: 0.95rem;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
}
@media (max-width: 640px) {
  .metrics {
    grid-template-columns: 1fr;
  }
}
</style>
