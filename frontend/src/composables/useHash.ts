import md5 from 'md5'

export const useHash = (value: string, hasher = 'md5'): string => {
  if (hasher === 'md5') {
    return md5(value.toLowerCase().trim())
  }

  return ''
}
