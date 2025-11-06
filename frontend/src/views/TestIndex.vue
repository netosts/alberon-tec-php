<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Main Content -->
    <main class="mx-auto max-w-6xl px-6 py-12">
      <div class="space-y-8">
        <!-- Step 3: Contacts List -->
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import crypto from 'crypto'

const selectedFile = ref(null)
const dragging = ref(false)
const processing = ref(false)
const imported = ref(false)
const contacts = ref([])
const summary = ref({
  total: 0,
  imported: 0,
  duplicates: 0,
  errors: 0,
})
const currentPage = ref(1)
const itemsPerPage = 10

const fileInput = ref(null)

const handleFileSelect = (event) => {
  const file = event.target.files?.[0]
  if (file && file.type === 'text/csv') {
    selectedFile.value = file
  }
}

const handleDrop = (event) => {
  dragging.value = false
  const file = event.dataTransfer.files?.[0]
  if (file && file.type === 'text/csv') {
    selectedFile.value = file
  }
}

const hashEmail = (email) => {
  return crypto.createHash('md5').update(email.toLowerCase().trim()).digest('hex')
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const processFile = async () => {
  if (!selectedFile.value) return

  processing.value = true

  try {
    const text = await selectedFile.value.text()
    const lines = text.trim().split('\n')

    if (lines.length < 2) throw new Error('CSV file is empty')

    // Parse header
    const headers = lines[0].split(',').map((h) => h.trim().toLowerCase())
    const nameIdx = headers.indexOf('name')
    const emailIdx = headers.indexOf('email')
    const phoneIdx = headers.indexOf('phone')
    const birthdateIdx = headers.indexOf('birthdate')

    if (nameIdx === -1 || emailIdx === -1) {
      throw new Error('CSV must contain "name" and "email" columns')
    }

    // Process rows
    const seen = new Set()
    const importedContacts = []
    let duplicates = 0
    let errors = 0

    for (let i = 1; i < lines.length; i++) {
      const values = lines[i].split(',').map((v) => v.trim())

      if (values.length === 1 && values[0] === '') continue

      const name = values[nameIdx]?.trim()
      const email = values[emailIdx]?.toLowerCase().trim()
      const phone = values[phoneIdx]?.trim()
      const birthdate = values[birthdateIdx]?.trim()

      // Validation
      if (!name || !email || !email.includes('@')) {
        errors++
        continue
      }

      // Deduplication
      if (seen.has(email)) {
        duplicates++
        continue
      }

      seen.add(email)
      importedContacts.push({ name, email, phone, birthdate })
    }

    contacts.value = importedContacts
    summary.value = {
      total: lines.length - 1,
      imported: importedContacts.length,
      duplicates,
      errors,
    }

    imported.value = true
  } catch (error) {
    alert(`Error processing file: ${error.message}`)
  } finally {
    processing.value = false
  }
}

const reset = () => {
  selectedFile.value = null
  imported.value = false
  contacts.value = []
  currentPage.value = 1
  summary.value = { total: 0, imported: 0, duplicates: 0, errors: 0 }
}

const paginatedContacts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return contacts.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => {
  return Math.ceil(contacts.value.length / itemsPerPage)
})
</script>

<style scoped>
/* Smooth transitions */
button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
