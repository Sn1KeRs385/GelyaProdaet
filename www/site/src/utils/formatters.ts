export const formatPluralNumber = (number: number, titles: string[]): string => {
  while (titles.length < 3) {
    titles.push('')
  }
  const cases = [2, 0, 1, 1, 1, 2]
  return titles[
    number % 100 > 4 && number % 100 < 20 ? 2 : cases[number % 10 < 5 ? number % 10 : 5]
  ]
}

export const formatMoney = (value: number | string, sign: string | undefined): string => {
  value = formatNumber(value, ' ')
  if (sign) {
    value = value + ` ${sign}`
  }
  return value
}

export const formatNumber = (value: number | string, separator = ' ') => {
  let mainString = value.toString().replace(/[^0-9.]/g, '')
  mainString = mainString.split('').reverse().join('')
  mainString = (mainString.match(/.{1,3}/g) || []).join(separator)
  mainString = mainString.split('').reverse().join('')

  return mainString
  // return value
  //   .toString()
  //   .replace(/[^0-9.]/, '')
  //   .replace(/\B(?=(\d{3})+(?!\d))/g, separator)
}

export const formatShortNumber = (value: number | string, shortener: string[]) => {
  const numberArray =
    value
      .toString()
      .replace(/[^0-9.]/g, '')
      .split('')
      .reverse()
      .join('')
      .match(/.{1,3}/g) || []

  if (numberArray.length >= 2 && shortener?.[numberArray.length - 2]) {
    let number: number | string = parseInt(
      numberArray[numberArray.length - 1].split('').reverse().join('')
    )

    let afterDot: string | number = numberArray[numberArray.length - 2].split('').reverse().join('')
    afterDot = parseInt(afterDot) / 1000

    if (afterDot > 0.0) {
      number = (number + afterDot).toFixed(1)
    }

    return `${number} ${shortener[numberArray.length - 2]}`
  }

  return numberArray.join('').split('').reverse().join('')
}

export const camelToKebab = (camel: string): string => {
  let kebab = camel.replace(/[A-Z]/g, (letter) => '-' + letter.toLowerCase())
  if (kebab[0] === '-') {
    kebab = kebab.slice(1)
  }
  return kebab
}

/**
 * Обрезает число если оно больше чем `max`
 * ```
 * 123 => '+99'
 * ```
 * Если число меньше чем `min` то результат пустой
 */
export const formatCutNumber = (value: number | string, min: number, max: number): string => {
  if (typeof value === 'string') {
    value = Number(value)
  }
  if (value > max) {
    return '+' + max
  } else if (value > min) {
    return value.toString()
  }
  return ''
}
