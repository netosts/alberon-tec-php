<template>
  <div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold text-gray-900">Imported Contacts</h2>
    </div>

    <div v-if="loading" class="p-8 text-center">
      <p class="text-gray-500">Loading contacts...</p>
    </div>

    <div v-else-if="error" class="p-8 text-center">
      <p class="text-red-600">{{ error }}</p>
    </div>

    <div v-else-if="contacts.length === 0" class="p-8 text-center">
      <p class="text-gray-500">No contacts yet. Upload a CSV file to get started.</p>
    </div>

    <div v-else>
      <ul class="divide-y divide-gray-200">
        <li
          v-for="contact in contacts"
          :key="contact.id"
          class="p-4 hover:bg-gray-50 transition-colors"
        >
          <div class="flex items-center space-x-4">
            <img
              :src="getGravatarUrl(contact.email)"
              :alt="contact.name"
              class="w-12 h-12 rounded-full"
            />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">
                {{ contact.name }}
              </p>
              <p class="text-sm text-gray-500 truncate">
                {{ contact.email }}
              </p>
            </div>
            <div class="text-sm text-gray-500">
              <p v-if="contact.phone">{{ contact.phone }}</p>
              <p v-if="contact.birthdate">{{ formatDate(contact.birthdate) }}</p>
            </div>
          </div>
        </li>
      </ul>

      <!-- Pagination -->
      <div
        v-if="pagination.last_page > 1"
        class="px-4 py-3 border-t flex items-center justify-between"
      >
        <div class="text-sm text-gray-700">
          Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} contacts
        </div>
        <div class="flex space-x-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1 text-sm bg-white border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1 text-sm bg-white border rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { md5 } from '@/utils/crypto'

interface Contact {
  id: number
  name: string
  email: string
  phone: string | null
  birthdate: string | null
}

interface PaginationData {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
  data: Contact[]
}

const contacts = ref<Contact[]>([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0,
})
const loading = ref(false)
const error = ref<string | null>(null)

const fetchContacts = async (page = 1) => {
  loading.value = true
  error.value = null

  try {
    const response = await fetch(`http://localhost:8000/api/contacts?page=${page}`)

    if (!response.ok) {
      throw new Error('Failed to fetch contacts')
    }

    const data: PaginationData = await response.json()
    contacts.value = data.data
    pagination.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total,
      from: data.from,
      to: data.to,
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'An error occurred'
  } finally {
    loading.value = false
  }
}

const goToPage = (page: number) => {
  fetchContacts(page)
}

const getGravatarUrl = (email: string): string => {
  const hash = md5(email.trim().toLowerCase())
  return `https://www.gravatar.com/avatar/${hash}?d=identicon&s=48`
}

const formatDate = (dateString: string | null): string => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(() => {
  fetchContacts()
})
</script>
