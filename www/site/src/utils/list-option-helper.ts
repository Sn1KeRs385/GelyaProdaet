import ListOptionInterface from 'src/interfaces/models/list-option-interface'

export const sort = (itemA: ListOptionInterface, itemB: ListOptionInterface) => {
  const weightA = itemA.weight || itemA.weight || 0
  const weightB = itemB.weight || itemB.weight || 0

  if (weightA !== weightB) {
    return weightA - weightB
  }

  const titleA = itemA.title.toLowerCase() || itemA.title.toLowerCase() || 'zzzzzzzzzzzzzzzzzz'
  const titleB = itemB.title.toLowerCase() || itemB.title.toLowerCase() || 'zzzzzzzzzzzzzzzzzz'

  if (titleA < titleB) {
    return -1
  } else if (titleA > titleB) {
    return 1
  }

  const idA = itemA.id
  const idB = itemB.id

  return idA - idB
}
