import BaseModel from 'src/models/base-model'
import QTableColParams from 'src/interfaces/quasar/q-table-params'

interface IndexStruct {
  id: number
}

class ProductModel extends BaseModel<IndexStruct> {
  protected readonly title = 'models.product.title'
  protected readonly url = 'products'

  getTableSettings(): QTableColParams[] {
    return [
      { name: 'id', label: 'Id', field: 'id', sortable: true, align: 'left' },
      { name: 'brand', label: 'Бренд', field: 'brand', format: (val) => val.title, align: 'left' },
      {
        name: 'country',
        label: 'Страна',
        field: 'country',
        format: (val) => val.title,
        align: 'left',
      },
    ]
  }
}

const modelClass = new ProductModel()
export default modelClass
