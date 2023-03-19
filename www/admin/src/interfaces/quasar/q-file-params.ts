export default interface QFileParams {
  name?: string | undefined
  multiple?: boolean | undefined
  accept?: string | undefined
  capture?: 'user' | 'environment' | undefined
  maxFileSize?: number | string | undefined
  maxTotalSize?: number | string | undefined
  maxFiles?: number | string | undefined
}
