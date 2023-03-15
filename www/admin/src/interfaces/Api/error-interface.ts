export default interface ApiErrorInterface {
  code?: string
  message: string
  errors?: { [key: string]: string[] }
}
