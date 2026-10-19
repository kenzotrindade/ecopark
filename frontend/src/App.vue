<script setup lang="ts">
import { onMounted, ref } from 'vue'
import EcoBadge from './components/EcoBadge.vue'
import SessionForm from './components/SessionForm.vue'
import StationList from './components/StationList.vue'
import { useEcoPark } from './composables/useEcoPark'
import type { ChargeSessionPayload } from './types/ecopark'

const {
  stations,
  errorTitle,
  errorMessage,
  ecoMetrics,
  fetchStations,
  startSession,
} = useEcoPark()

const submitting = ref(false)
const successMessage = ref('')

async function handleSession(payload: ChargeSessionPayload) {
  submitting.value = true
  successMessage.value = ''

  try {
    const success = await startSession(payload)

    if (success) {
      successMessage.value = 'La session de recharge a été créée.'
    }
  } finally {
    submitting.value = false
  }
}

onMounted(fetchStations)
</script>

<template>
  <main class="app-shell">
    <header class="topbar">
      <a class="brand" href="/" aria-label="EcoPark, accueil">
        <span class="brand-mark">E</span>
        <span>ecopark<span class="brand-dot">.</span></span>
      </a>
      <span class="topbar-label">
        <span class="live-dot"></span>
        API REST · V1
      </span>
    </header>

    <section class="hero">
      <p class="eyebrow">MOBILITÉ ÉLECTRIQUE · SIMPLIFIÉE</p>
      <h1>Rechargez mieux.<br /><span>Respirez plus.</span></h1>
      <p class="hero-description">
        Gérez les stations de recharge et planifiez vos sessions depuis une
        interface connectée à l'API EcoPark.
      </p>
    </section>

    <section class="dashboard">
      <div class="dashboard-main">
        <StationList :stations="stations" />
      </div>

      <aside class="dashboard-side">
        <SessionForm :stations="stations" :loading="submitting" @submit-session="handleSession" />
      </aside>
    </section>

    <div v-if="successMessage" class="notice notice--success" role="status">
      <span aria-hidden="true">✓</span>
      {{ successMessage }}
    </div>

    <div v-if="errorTitle" class="notice notice--error" role="alert">
      <strong>{{ errorTitle }}</strong>
      <span>{{ errorMessage }}</span>
    </div>

    <section class="metrics-section">
      <div class="metrics-heading">
        <div>
          <p class="eyebrow">OBSERVABILITÉ</p>
          <h2>Empreinte de la requête</h2>
        </div>
        <span class="metrics-caption">Dernière réponse API</span>
      </div>
      <EcoBadge :metrics="ecoMetrics" />
    </section>

    <footer class="footer">
      <span>EcoPark <span class="brand-dot">·</span> Projet pédagogique RESTful</span>
      <span>PHP 8 · Slim 4 · Vue 3</span>
    </footer>
  </main>
</template>

<style>
:root {
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont,
    "Segoe UI", sans-serif;
  color: #e8eee9;
  background: #0d1311;
  font-synthesis: none;
  text-rendering: optimizeLegibility;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;

  --background: #0d1311;
  --surface: #141c18;
  --border: #29352e;
  --text: #e8eee9;
  --muted: #8d9a91;
  --accent: #5bdf9b;
  --accent-soft: #1b3b2b;
  --success: #5bdf9b;
  --danger: #ff8f8f;
}

* {
  box-sizing: border-box;
}

body {
  min-width: 320px;
  min-height: 100vh;
  margin: 0;
}

button,
input,
select {
  font: inherit;
}

button:focus-visible,
a:focus-visible,
input:focus-visible,
select:focus-visible {
  outline: 2px solid var(--accent);
  outline-offset: 3px;
}

.app-shell {
  width: min(1120px, calc(100% - 48px));
  margin: 0 auto;
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 76px;
  border-bottom: 1px solid var(--border);
}

.brand {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: var(--text);
  font-size: 1.15rem;
  font-weight: 800;
  letter-spacing: -0.06em;
  text-decoration: none;
}

.brand-mark {
  display: grid;
  width: 31px;
  height: 31px;
  place-items: center;
  border-radius: 9px;
  background: var(--accent);
  color: #0d1911;
  font-size: 1rem;
}

.brand-dot {
  color: var(--accent);
}

.topbar-label {
  display: flex;
  align-items: center;
  gap: 9px;
  color: var(--muted);
  font-size: 0.75rem;
  font-weight: 650;
  letter-spacing: 0.08em;
}

.live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--accent);
  box-shadow: 0 0 12px #5bdf9b66;
}

.hero {
  padding: 70px 0 48px;
}

.eyebrow {
  color: var(--accent);
  font-size: 0.7rem;
  font-weight: 750;
  letter-spacing: 0.15em;
}

.hero h1 {
  margin: 18px 0;
  font-size: clamp(2.7rem, 7vw, 5.1rem);
  font-weight: 760;
  letter-spacing: -0.075em;
  line-height: 1.02;
}

.hero h1 span {
  color: var(--accent);
}

.hero-description {
  max-width: 560px;
  color: var(--muted);
  font-size: 1rem;
  line-height: 1.8;
}

.dashboard {
  display: grid;
  grid-template-columns: minmax(0, 1.25fr) minmax(300px, 0.75fr);
  align-items: start;
  gap: 20px;
}

.dashboard-main,
.dashboard-side {
  min-width: 0;
}

.notice {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin-top: 18px;
  padding: 15px 18px;
  border: 1px solid var(--border);
  border-radius: 12px;
  font-size: 0.88rem;
  line-height: 1.5;
}

.notice--success {
  border-color: #326849;
  background: #14271c;
  color: var(--success);
}

.notice--error {
  border-color: #713c3c;
  background: #2b1919;
  color: var(--danger);
}

.notice--error span {
  color: #e6baba;
}

.metrics-section {
  margin-top: 48px;
}

.metrics-heading {
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 17px;
}

.metrics-heading .eyebrow {
  margin-bottom: 7px;
}

.metrics-heading h2 {
  margin: 0;
  font-size: 1.25rem;
  letter-spacing: -0.035em;
}

.metrics-caption {
  color: var(--muted);
  font-size: 0.76rem;
}

.footer {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  margin-top: 48px;
  padding: 22px 0 30px;
  border-top: 1px solid var(--border);
  color: var(--muted);
  font-size: 0.75rem;
}

@media (max-width: 760px) {
  .app-shell {
    width: min(100% - 32px, 560px);
  }

  .hero {
    padding: 48px 0 32px;
  }

  .dashboard {
    grid-template-columns: 1fr;
  }

  .metrics-section {
    margin-top: 34px;
  }

  .footer {
    flex-direction: column;
  }
}

@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    scroll-behavior: auto !important;
    transition-duration: 0.01ms !important;
    animation-duration: 0.01ms !important;
  }
}
</style>
