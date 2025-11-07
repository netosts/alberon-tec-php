import type { IPagination } from '@/types/pagination'
import api from '../api'

export const uploadCsvContactService = (data: FormData) => {
  return api.post('/contacts/upload-csv', data, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })
}

export const getContactsService = (pagination: IPagination) => {
  return api.get('/contacts', {
    params: {
      page: pagination.current_page,
      per_page: pagination.per_page,
    },
  })
}
