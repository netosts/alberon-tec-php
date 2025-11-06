<template>
  <div class="rounded-xl border border-slate-700 bg-slate-800/50 p-8 backdrop-blur-sm">
    <h2 class="mb-6 text-xl font-bold text-white">Imported Contacts</h2>

    <div v-if="pagination.data?.length === 0" class="py-12 text-center">
      <p class="text-slate-400">No contacts found</p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="contact in pagination.data!!"
        :key="contact.id"
        class="flex items-center gap-4 rounded-lg border border-slate-700/50 bg-slate-700/20 p-4 transition-colors hover:bg-slate-700/40"
      >
        <img
          :src="`https://www.gravatar.com/avatar/${hashEmail(contact.email!!)}?d=identicon&s=48`"
          :alt="contact.name ?? 'Contact Avatar'"
          class="h-12 w-12 rounded-full"
        />
        <div class="min-w-0 flex-1">
          <p class="truncate font-semibold text-white">{{ contact.name }}</p>
          <p class="truncate text-sm text-slate-400">{{ contact.email }}</p>
          <p class="text-sm text-slate-500">{{ contact.phone }}</p>
        </div>
        <div class="shrink-0 text-right">
          <p class="text-sm text-slate-400">{{ useFormatDate(contact.birthdate) }}</p>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.last_page > 1" class="mt-6 flex items-center justify-between">
      <p class="text-sm text-slate-400">
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }}
      </p>
      <div class="flex gap-2">
        <button
          @click="$emit('page-change', pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="rounded-lg bg-slate-700 px-4 py-2 text-white transition-colors hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
        >
          Previous
        </button>
        <div class="flex items-center gap-1">
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="$emit('page-change', page)"
            :class="[
              'rounded-lg px-3 py-2 font-semibold transition-colors',
              page === pagination.current_page
                ? 'bg-blue-600 text-white'
                : 'bg-slate-700 text-slate-300 hover:bg-slate-600',
            ]"
          >
            {{ page }}
          </button>
        </div>
        <button
          @click="$emit('page-change', pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="rounded-lg bg-slate-700 px-4 py-2 text-white transition-colors hover:bg-slate-600 disabled:cursor-not-allowed disabled:opacity-50"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useFormatDate } from '@/composables/useDate'
import md5 from 'md5'
import type { IContact } from '@/types/contact'
import type { IPagination } from '@/types/pagination'
import { getContactsService } from '@/services/home/homeApi'

defineEmits<{
  'page-change': [page: number]
}>()

const pagination = ref<IPagination<IContact[]>>({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
  from: 0,
  to: 0,
  data: [],
})

const hashEmail = (email: string): string => {
  return md5(email.toLowerCase().trim())
}

// Show max 7 page buttons
const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const total = pagination.value.last_page
  const maxVisible = 7
  const pages: number[] = []

  if (total <= maxVisible) {
    // Show all pages if total is small
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    // Show first, last, current and nearby pages
    const start = Math.max(1, current - 2)
    const end = Math.min(total, current + 2)

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }

    // Add first page if not included
    if (start > 1) {
      pages.unshift(1)
    }

    // Add last page if not included
    if (end < total) {
      pages.push(total)
    }
  }

  return pages
})

const getPagination = () => {
  getContactsService().then(({ data: { data } }) => {
    pagination.value = data
  })
}
</script>

<style scoped></style>
