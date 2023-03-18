export default interface ApiPaginationResponseInterface<ItemInterface> {
  items: ItemInterface[]
  current_page: number
  per_page: number
  last_page: number
  total: number
}
