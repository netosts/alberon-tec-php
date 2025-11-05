import api from '../api'

export const uploadCsvContactService = (data: FormData) => {
  return api.post('/contacts/upload-csv', data, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })
}

export const getContactsService = () => {
  return api.get('/contacts')
}
