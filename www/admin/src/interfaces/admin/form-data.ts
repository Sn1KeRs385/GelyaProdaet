export default interface FormData {
  [key: string]: { value: unknown; errors: string[] }
}
