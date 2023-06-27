import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import ProductInterface from 'src/interfaces/models/product-interface'
import CompilationInterface from 'src/interfaces/models/compilation-interface'
import HeaderInterface from 'src/interfaces/header-interface'
import FooterInterface from 'src/interfaces/footer-interface'

export default interface IndexPageDataResponseInterface {
  product_types: ListOptionInterface[]
  last_products: ProductInterface[]
  compilations: CompilationInterface[]
  header: HeaderInterface
  footer: FooterInterface
}
