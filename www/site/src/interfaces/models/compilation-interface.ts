import CompilationType from 'src/enums/compilation-type'
import ProductInterface from 'src/interfaces/models/product-interface'

export default interface CompilationInterface {
  id: number
  type?: CompilationType
  name?: string
  products?: ProductInterface[]
}
