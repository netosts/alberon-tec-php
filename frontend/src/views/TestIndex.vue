<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Main Content -->
    <main class="mx-auto max-w-6xl px-6 py-12">
      <div class="space-y-8">
        <!-- Step 1: Upload -->

        <!-- Step 2: Summary -->
        <div
          v-if="imported"
          class="rounded-xl border border-slate-700 bg-slate-800/50 p-8 backdrop-blur-sm"
        >
          <h2 class="mb-6 text-xl font-bold text-white">
            <span
              class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-green-600 text-sm font-semibold text-white mr-3"
              >✓</span
            >
            Import Summary
          </h2>

          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg bg-slate-700/50 p-6">
              <p class="text-sm text-slate-400 mb-2">Total Rows</p>
              <p class="text-3xl font-bold text-white">{{ summary.total }}</p>
            </div>
            <div class="rounded-lg bg-green-900/20 border border-green-700/50 p-6">
              <p class="text-sm text-green-300 mb-2">Successfully Imported</p>
              <p class="text-3xl font-bold text-green-400">{{ summary.imported }}</p>
            </div>
            <div class="rounded-lg bg-orange-900/20 border border-orange-700/50 p-6">
              <p class="text-sm text-orange-300 mb-2">Duplicates</p>
              <p class="text-3xl font-bold text-orange-400">{{ summary.duplicates }}</p>
            </div>
            <div class="rounded-lg bg-red-900/20 border border-red-700/50 p-6">
              <p class="text-sm text-red-300 mb-2">Errors</p>
              <p class="text-3xl font-bold text-red-400">{{ summary.errors }}</p>
            </div>
          </div>

          <button
            @click="reset"
            class="mt-6 rounded-lg bg-slate-700 px-6 py-2 font-semibold text-white hover:bg-slate-600 transition-colors"
          >
            Import Another File
          </button>
        </div>

        <!-- Step 3: Contacts List -->
        <div
          v-if="imported"
          class="rounded-xl border border-slate-700 bg-slate-800/50 p-8 backdrop-blur-sm"
        >
          <h2 class="mb-6 text-xl font-bold text-white">Imported Contacts</h2>

          <div class="space-y-3">
            <div
              v-for="(contact, idx) in paginatedContacts"
              :key="idx"
              class="flex items-center gap-4 rounded-lg border border-slate-700/50 bg-slate-700/20 p-4 hover:bg-slate-700/40 transition-colors"
            >
              <img
                :src="`https://www.gravatar.com/avatar/${hashEmail(contact.email)}?d=identicon&s=48`"
                :alt="contact.name"
                class="h-12 w-12 rounded-full"
              />
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-white truncate">{{ contact.name }}</p>
                <p class="text-sm text-slate-400 truncate">{{ contact.email }}</p>
                <p class="text-sm text-slate-500">{{ contact.phone }}</p>
              </div>
              <div class="text-right flex-shrink-0">
                <p class="text-sm text-slate-400">{{ formatDate(contact.birthdate) }}</p>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-slate-400">
              Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
              {{ Math.min(currentPage * itemsPerPage, contacts.length) }} of {{ contacts.length }}
            </p>
            <div class="flex gap-2">
              <button
                @click="currentPage = Math.max(1, currentPage - 1)"
                :disabled="currentPage === 1"
                class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Previous
              </button>
              <div class="flex items-center gap-1">
                <button
                  v-for="page in totalPages"
                  :key="page"
                  @click="currentPage = page"
                  :class="[
                    'rounded-lg px-3 py-2 font-semibold transition-colors',
                    page === currentPage
                      ? 'bg-blue-600 text-white'
                      : 'bg-slate-700 text-slate-300 hover:bg-slate-600',
                  ]"
                >
                  {{ page }}
                </button>
              </div>
              <button
                @click="currentPage = Math.min(totalPages, currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Next
              </button>
            </div>
          </div>
        </div>
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
