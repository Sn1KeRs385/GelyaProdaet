export default interface QTableColParams {
  name: string
  label: string
  // eslint-disable-next-line @typescript-eslint/no-explicit-any,
  field: string | ((row: any) => any)
  required?: boolean
  align?: 'left' | 'right' | 'center'
  sortable?: boolean
  // eslint-disable-next-line @typescript-eslint/no-explicit-any,
  sort?: (a: any, b: any, rowA: any, rowB: any) => number
  sortOrder?: 'ad' | 'da'
  // eslint-disable-next-line @typescript-eslint/no-explicit-any,
  format?: (val: any, row: any) => any
  // eslint-disable-next-line @typescript-eslint/no-explicit-any,
  style?: string | ((row: any) => string)
  // eslint-disable-next-line @typescript-eslint/no-explicit-any,
  classes?: string | ((row: any) => string)
  headerStyle?: string
  headerClasses?: string
}
