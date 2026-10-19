import { ref } from 'vue'
import type {
  ApiProblemDetails,
  ChargeSessionPayload,
  EcoMetrics,
  Station,
} from '../types/ecopark'

const API_BASE_URL = 'http://localhost:8000/api'

export function useEcoPark() {
  const stations = ref<Station[]>([])
  const errorTitle = ref('')
  const errorMessage = ref('')

  const ecoMetrics = ref<EcoMetrics>({
    responseTime: null,
    payloadSize: null,
    co2: null,
  })

  function captureMetrics(response: Response) {
    ecoMetrics.value = {
      responseTime: response.headers.get('X-Response-Time-Ms'),
      payloadSize: response.headers.get('X-Payload-Size-Bytes'),
      co2: response.headers.get('X-Estimated-CO2-Grams'),
    }
  }

  function clearError() {
    errorTitle.value = ''
    errorMessage.value = ''
  }

  async function handleError(response: Response) {
    let problem: Partial<ApiProblemDetails> = {}

    try {
      problem = await response.json() as Partial<ApiProblemDetails>
    } catch {
      // La réponse n'est pas un document JSON exploitable.
    }

    errorTitle.value = problem.title || `Erreur HTTP ${response.status}`
    errorMessage.value = problem.detail || 'Une erreur est survenue lors de la requête.'
  }

  async function fetchStations() {
    clearError()

    try {
      const response = await fetch(`${API_BASE_URL}/stations`)
      captureMetrics(response)

      if (!response.ok) {
        await handleError(response)
        return
      }

      stations.value = await response.json() as Station[]
    } catch {
      errorTitle.value = 'API inaccessible'
      errorMessage.value =
        'Impossible de contacter le serveur EcoPark. Vérifiez que le backend est démarré.'
    }
  }

  async function startSession(payload: ChargeSessionPayload) {
    clearError()

    try {
      const response = await fetch(`${API_BASE_URL}/sessions`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json, application/problem+json',
        },
        body: JSON.stringify(payload),
      })

      captureMetrics(response)

      if (!response.ok) {
        await handleError(response)
        return false
      }

      await fetchStations()
      return true
    } catch {
      errorTitle.value = 'API inaccessible'
      errorMessage.value =
        'Impossible de contacter le serveur EcoPark. Vérifiez que le backend est démarré.'
      return false
    }
  }

  return {
    stations,
    errorTitle,
    errorMessage,
    ecoMetrics,
    fetchStations,
    startSession,
  }
}
