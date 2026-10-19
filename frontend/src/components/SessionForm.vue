<script setup lang="ts">
import { ref } from 'vue'
import type { Station, ChargeSessionPayload } from '../types/ecopark'

const props = defineProps<{
  stations: Station[]
  loading?: boolean
}>()

const emit = defineEmits<{
  'submit-session': [payload: ChargeSessionPayload]
}>()

const userName = ref('')
const stationId = ref<number | ''>('')
const kwhRequested = ref(20)

const availableStations = () =>
  props.stations.filter((station) => station.isAvailable)

function submitForm() {
  if (
    !userName.value.trim() ||
    stationId.value === '' ||
    !Number.isInteger(kwhRequested.value) ||
    kwhRequested.value < 1
  ) {
    return
  }

  emit('submit-session', {
    stationId: stationId.value,
    userName: userName.value.trim(),
    kwhRequested: kwhRequested.value,
  })

  stationId.value = ''
}
</script>

<template>
  <section class="panel">
    <p class="eyebrow">NOUVELLE RÉSERVATION</p>
    <h2>Planifier une recharge</h2>
    <p class="intro">Choisissez une station et indiquez votre besoin énergétique.</p>

    <form class="session-form" @submit.prevent="submitForm">
      <label for="userName">Nom de l'utilisateur</label>
      <input
        id="userName"
        v-model="userName"
        name="userName"
        type="text"
        placeholder="Ex. Alex Martin"
        maxlength="100"
        autocomplete="name"
        required
      />

      <label for="stationId">Station de recharge</label>
      <select id="stationId" v-model.number="stationId" required>
        <option value="" disabled>Choisir une station</option>
        <option
          v-for="station in availableStations()"
          :key="station.id"
          :value="station.id"
        >
          {{ station.locationName }} — {{ station.powerKw }} kW
        </option>
      </select>
      <p v-if="availableStations().length === 0" class="field-hint">
        Aucune station n'est actuellement disponible.
      </p>

      <label for="kwhRequested">Énergie demandée (kWh)</label>
      <input
        id="kwhRequested"
        v-model.number="kwhRequested"
        name="kwhRequested"
        type="number"
        min="1"
        step="1"
        required
      />
      <p class="field-hint">
        Le cours compare cette valeur à la puissance maximale de la station.
      </p>

      <button
        type="submit"
        :disabled="loading || availableStations().length === 0"
      >
        {{ loading ? 'Envoi en cours…' : 'Créer la session' }}
        <span aria-hidden="true">↗</span>
      </button>
    </form>
  </section>
</template>

<style scoped>
.panel {
  padding: 26px;
  border: 1px solid var(--border);
  border-radius: 18px;
  background: var(--surface);
}
.eyebrow {
  margin: 0 0 8px;
  color: var(--accent);
  font-size: 0.7rem;
  font-weight: 750;
  letter-spacing: 0.14em;
}
h2 {
  margin: 0;
  font-size: 1.35rem;
  letter-spacing: -0.04em;
}
.intro {
  margin: 9px 0 24px;
  color: var(--muted);
  font-size: 0.88rem;
  line-height: 1.6;
}
.session-form {
  display: grid;
  gap: 10px;
}
label {
  margin-top: 7px;
  font-size: 0.82rem;
  font-weight: 650;
}
input,
select {
  width: 100%;
  min-width: 0;
  padding: 12px 13px;
  border: 1px solid var(--border);
  border-radius: 10px;
  outline: none;
  background: var(--background);
  color: var(--text);
  font: inherit;
  font-size: 0.88rem;
}
input:focus,
select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-soft);
}
.field-hint {
  margin: 0;
  color: var(--muted);
  font-size: 0.76rem;
  line-height: 1.5;
}
button {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-top: 15px;
  padding: 13px 15px;
  border: 0;
  border-radius: 10px;
  background: var(--accent);
  color: white;
  font: inherit;
  font-size: 0.88rem;
  font-weight: 650;
  cursor: pointer;
  transition: opacity 160ms ease, transform 160ms ease;
}
button:hover:not(:disabled) {
  transform: translateY(-1px);
}
button:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}
</style>
