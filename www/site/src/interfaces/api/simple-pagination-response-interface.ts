export default interface SimplePaginationResponseInterface<ItemInterface> {
  items: ItemInterface[]
  current_page: number
  per_page: number
  from: number
  to: number
}
