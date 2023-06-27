<script setup lang="ts">
import ProductInterface from 'src/interfaces/models/product-interface'
import { computed } from 'vue'
import XImg from 'src/components/default/XImg.vue'
import ImagesUrl from 'src/enums/images-url'
import ListOptionInterface from 'src/interfaces/models/list-option-interface'
import { sort as listOptionsSort } from 'src/utils/list-option-helper'
import { useI18n } from 'vue-i18n'

interface Props {
  product: ProductInterface
  imageClass?: string[] | string
}

const { t } = useI18n()

const props = defineProps<Props>()

const product = computed(() => props.product)

const description = computed(() => product.value.description)

const images = computed(() => product.value.files || [])
const type = computed(() => product.value.type.title)
const brand = computed(() => product.value.brand?.title)
const country = computed(() => product.value.country?.title)
const price = computed(() => product.value.price_normalize)
const priceFinal = computed(() => product.value.price_final_normalize)

const items = computed(() => product.value.items || [])
const notSoldItems = computed(() => items.value.filter((item) => !item.is_sold))

const sizes = computed(() => {
  let sizes: ListOptionInterface[] = []

  notSoldItems.value.forEach((item) => {
    if (item.size && !sizes.includes(item.size)) {
      sizes.push(item.size)
    }
  })

  return sizes
    .sort(listOptionsSort)
    .filter((size, index, self) => self.findIndex(({ title }) => title === size.title) === index)
})

const sizeYears = computed(() => {
  let sizes: ListOptionInterface[] = []

  notSoldItems.value.forEach((item) => {
    if (item.size_year && !sizes.includes(item.size_year)) {
      sizes.push(item.size_year)
    }
  })

  return sizes
    .sort(listOptionsSort)
    .filter((size, index, self) => self.findIndex(({ title }) => title === size.title) === index)
})

const colors = computed(() => {
  let colors: ListOptionInterface[] = []

  notSoldItems.value.forEach((item) => {
    if (item.color && !colors.includes(item.color)) {
      colors.push(item.color)
    }
  })

  return colors
    .sort(listOptionsSort)
    .filter((color, index, self) => self.findIndex(({ title }) => title === color.title) === index)
})

const imageUrl = computed(() => images?.value?.[0]?.url || ImagesUrl.EMPTY_IMAGE)
const imageAlt = computed(
  () => `${type.value} ${brand.value} ${country.value} - ${product.value.id}`
)
const imageClass = computed(() => props.imageClass)
</script>

<template>
  <div class="tw-border-1 tw-border-solid tw-border-[#F4F2F3] tw-flex tw-flex-col">
    <div class="image-inside-container-hover-scale tw-w-full tw-h-[60%] tw-flex-none">
      <x-img
        :src="imageUrl"
        :alt="imageAlt"
        class="tw-w-full tw-h-full"
        :class="imageClass"
        fit="cover"
      />
    </div>
    <div
      class="tw-grow tw-flex tw-flex-col tw-p-12px md:tw-p-14px lg:tw-p-16px tw-bg-[#F4F2F3] tw-flex-none"
    >
      <div class="tw-flex tw-flex-row tw-gap-x-8px tw-items-center">
        <div v-if="priceFinal" class="tw-color-grey tw-font-semibold tw-text-18px">
          <s>{{ price }} ₽</s>
        </div>
        <div class="text-primary tw-font-semibold tw-text-24px">{{ priceFinal || price }} ₽</div>
      </div>
      <div class="tw-font-semibold tw-text-18px tw-flex-none">{{ type }}</div>
      <div class="tw-font-semibold tw-text-12px tw-flex-none">{{ brand }} {{ country }}</div>
      <div v-if="sizes.length > 0" class="tw-flex tw-flex-row tw-gap-4px tw-flex-wrap tw-flex-none">
        <div class="tw-text-14px">{{ t('products.sizes') }}:</div>
        <div v-for="(size, index) in sizes" :key="`size_${index}`">
          {{ size.title }}
        </div>
      </div>
      <div
        v-if="sizeYears.length > 0"
        class="tw-flex tw-flex-row tw-gap-4px tw-flex-wrap tw-flex-none"
      >
        <div class="tw-text-14px">{{ t('products.sizeYears') }}:</div>
        <div v-for="(size, index) in sizeYears" :key="`sizeYear_${index}`">
          {{ size.title }}
        </div>
      </div>
      <div
        v-if="colors.length > 0"
        class="tw-flex tw-flex-row tw-gap-4px tw-flex-wrap tw-flex-none"
      >
        <div class="tw-text-14px">{{ t('products.colors') }}:</div>
        <div v-for="(color, index) in colors" :key="`color_${index}`">
          {{ color.title }}
        </div>
      </div>
      <q-space />
      <div v-if="description" class="tw-line-clamp-1">
        {{ description }}
      </div>
    </div>
  </div>
</template>
