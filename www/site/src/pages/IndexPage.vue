<script lang="ts">
import { useListOptionsStore } from 'src/stores/list-options-store'
import { api } from 'src/boot/axios'
import { AxiosResponse } from 'axios'
import IndexPageDataResponseInterface from 'src/interfaces/api/responses/page-data/index-page-data-response-interface'
import { useProductsStore } from 'src/stores/products-store'
import { useCompilationsStore } from 'src/stores/compilations-store'
import { useHeaderStore } from 'src/stores/header-store'
import { useFooterStore } from 'src/stores/footer-store'

export default {
  async preFetch({ store }) {
    await api
      .get('v1/site/index')
      .then((response: AxiosResponse<IndexPageDataResponseInterface>) => {
        const listOptionsStore = useListOptionsStore(store)
        const productsStore = useProductsStore(store)
        const compilationsStore = useCompilationsStore(store)
        const headerStore = useHeaderStore(store)
        const footerStore = useFooterStore(store)

        listOptionsStore.setOptionsForPage(response.data.product_types)
        productsStore.setProducts(response.data.last_products)
        compilationsStore.setCompilations(response.data.compilations)
        headerStore.set(response.data.header)
        footerStore.set(response.data.footer)
      })
  },
}
</script>

<script setup lang="ts">
import { useListOptionsStore } from 'src/stores/list-options-store'
import { useProductsStore } from 'src/stores/products-store'
import { computed } from 'vue'
import ProductComponent from 'src/components/product/ProductComponent.vue'
import { useI18n } from 'vue-i18n'
import ImageWithBadge from 'src/components/ImageWithBadge.vue'
import RowScroller from 'src/components/RowScroller.vue'

const { t } = useI18n()

const listOptionsStore = useListOptionsStore()
const productsStore = useProductsStore()
const compilationsStore = useCompilationsStore()

const listOptions = computed(() => listOptionsStore.optionsForPage)
const products = computed(() => productsStore.products)
const compilations = computed(() => compilationsStore.compilations)
</script>

<template>
  <q-page class="tw-flex tw-flex-col tw-gap-12">
    <row-scroller :title="t('products.categorySection')">
      <image-with-badge
        v-for="option in listOptions"
        :key="option.id"
        :label="option.title"
        :image-url="option.logo?.[0]?.url"
        :image-alt="option.title"
        class="tw-h-full tw-rounded-20px tw-w-130px md:tw-w-190px lg:tw-w-250px tw-flex-none"
        image-class="tw-rounded-20px"
      />
    </row-scroller>
    <row-scroller
      :title="t('products.newProductsSection')"
      container-styles="tw-gap-2 md:tw-gap-4 lg:tw-gap-6 tw-h-450px"
    >
      <product-component
        v-for="product in products"
        :key="`product-${product.id}`"
        :product="product"
        class="tw-h-full tw-w-250px tw-rounded-20px tw-overflow-hidden tw-flex-none"
      />
    </row-scroller>
    <row-scroller
      v-for="compilation in compilations"
      :key="`compilation_${compilation.id}`"
      :title="t('products.compilationWithName', { compilationName: compilation.name })"
      container-styles="tw-gap-2 md:tw-gap-4 lg:tw-gap-6 tw-h-450px"
    >
      <product-component
        v-for="product in compilation.products"
        :key="`compilation-${compilation.id}-product-${product.id}`"
        :product="product"
        class="tw-h-full tw-w-250px tw-rounded-20px tw-overflow-hidden tw-flex-none"
      />
    </row-scroller>
  </q-page>
</template>
