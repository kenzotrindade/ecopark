<script setup lang="ts">
import type { Station } from '../types/ecopark'

defineProps<{
  stations: Station[]
}>()
</script>

<template>
  <section class="panel">
    <div class="section-heading">
      <div>
        <p class="eyebrow">RÉSEAU</p>
        <h2>Stations de recharge</h2>
      </div>
      <span class="count">{{ stations.length }} stations</span>
    </div>

    <div v-if="stations.length === 0" class="empty-state">
      Aucune station disponible.
    </div>

    <div v-else class="station-list">
      <article
        v-for="station in stations"
        :key="station.id"
        class="station"
      >
        <div class="station-icon" aria-hidden="true">⚡</div>

        <div class="station-info">
          <h3>{{ station.locationName }}</h3>
          <p>{{ station.powerKw }} kW de puissance</p>
        </div>

        <span
          class="status"
          :class="station.isAvailable ? 'status--available' : 'status--unavailable'"
        >
          <span class="status-dot"></span>
          {{ station.isAvailable ? 'Disponible' : 'Occupée' }}
        </span>
      </article>
    </div>
  </section>
</template>

<style scoped>
.panel {
  padding: 26px;
  border: 1px solid var(--border);
  border-radius: 18px;
  background: var(--surface);
}
.section-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 22px;
}
.eyebrow {
  margin: 0 0 6px;
  color: var(--muted);
  font-size: 0.7rem;
  font-weight: 750;
  letter-spacing: 0.14em;
}
h2 {
  margin: 0;
  font-size: 1.25rem;
  letter-spacing: -0.035em;
}
.count {
  color: var(--muted);
  font-size: 0.8rem;
}
.station-list {
  display: grid;
  gap: 10px;
}
.station {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 15px;
  border: 1px solid var(--border);
  border-radius: 12px;
}
.station-icon {
  display: grid;
  width: 42px;
  height: 42px;
  flex: 0 0 42px;
  place-items: center;
  border-radius: 11px;
  background: var(--accent-soft);
  font-size: 1.25rem;
}
.station-info {
  min-width: 0;
  flex: 1;
}
.station-info h3 {
  margin: 0;
  font-size: 0.93rem;
  font-weight: 650;
}
.station-info p {
  margin: 5px 0 0;
  color: var(--muted);
  font-size: 0.82rem;
}
.status {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  white-space: nowrap;
  font-size: 0.75rem;
  font-weight: 600;
}
.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: currentColor;
}
.status--available {
  color: var(--success);
}
.status--unavailable {
  color: var(--muted);
}
.empty-state {
  padding: 28px 0;
  color: var(--muted);
  text-align: center;
}
@media (max-width: 480px) {
  .panel {
    padding: 18px;
  }
  .station {
    flex-wrap: wrap;
  }
  .status {
    margin-left: 56px;
  }
}
</style>
