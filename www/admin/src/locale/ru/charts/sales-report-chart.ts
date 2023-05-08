const sold = 'Продажи'
const earn = 'Прибыль'
export default {
  total: {
    title: 'Итого:',
  },
  sold: {
    title: sold,
    withTotal: `${sold}: {total}`,
  },
  earn: {
    title: earn,
    withTotal: `${earn}: {total}`,
  },
}
