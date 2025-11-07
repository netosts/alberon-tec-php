import { ref } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { CsvImportStatus } from '@/enums/csv-import.enum'

declare global {
  interface Window {
    Pusher: typeof Pusher
    Echo: Echo<any>
  }
}

export interface CsvImportData {
  id: number
  status: CsvImportStatus
  progress: number
  stats: CsvImportDataStats
  processed_chunks: number
  total_chunks: number
}

export interface CsvImportDataStats {
  total_rows: number
  imported: number
  duplicates: number
  errors: number
}

// Initialize Echo lazily
function initializeEcho() {
  if (!window.Echo) {
    window.Pusher = Pusher

    window.Echo = new Echo({
      broadcaster: 'reverb',
      key: import.meta.env.VITE_REVERB_APP_KEY,
      wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
      wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
      wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
      forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
      enabledTransports: ['ws', 'wss'],
    })
  }
  return window.Echo
}

export function useCsvImport() {
  const progress = ref(0)
  const stats = ref({
    total_rows: 0,
    imported: 0,
    duplicates: 0,
    errors: 0,
  })
  const status = ref<CsvImportStatus>(CsvImportStatus.IDLE)
  const processedChunks = ref(0)
  const totalChunks = ref(0)

  const subscribeToImport = (importId: number) => {
    const echo = initializeEcho()

    const channel = echo.channel(`csv-import.${importId}`)

    channel.listen('.progress.updated', (data: CsvImportData) => {
      progress.value = data.progress
      stats.value = data.stats
      status.value = data.status
      processedChunks.value = data.processed_chunks
      totalChunks.value = data.total_chunks

      if (data.status === 'completed' || data.status === 'failed') {
        echo.leave(`csv-import.${importId}`)
      }
    })
  }

  const reset = () => {
    progress.value = 0
    stats.value = {
      total_rows: 0,
      imported: 0,
      duplicates: 0,
      errors: 0,
    }
    status.value = CsvImportStatus.IDLE
  }

  return {
    progress,
    stats,
    status,
    processedChunks,
    totalChunks,
    subscribeToImport,
    reset,
  }
}
