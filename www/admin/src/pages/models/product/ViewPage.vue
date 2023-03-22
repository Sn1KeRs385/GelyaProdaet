<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ProductModel, { GetByIdItemInterface } from 'src/models/product'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from 'src/enums/option-group-slug'
import ProductItemViewCard from 'src/components/models/product/ProductItemViewCard.vue'

interface Props {
  model: typeof ProductModel
}

const props = defineProps<Props>()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const listOptionsStore = useListOptionsStore()

const data = ref<GetByIdItemInterface | undefined>()
const slide = ref(1)
const fullscreenPhoto = ref(false)

const infoArray = computed(() => [
  {
    label: t('models.product.view.title.label'),
    value: data.value?.title || '-',
  },
  {
    label: listOptionsStore.getHumanSlug(OptionGroupSlug.PRODUCT_TYPE),
    value: data.value?.type.title || '-',
  },
  {
    label: listOptionsStore.getHumanSlug(OptionGroupSlug.GENDER),
    value: data.value?.gender.title || '-',
  },
  {
    label: listOptionsStore.getHumanSlug(OptionGroupSlug.BRAND),
    value: data.value?.brand?.title || '-',
  },
  {
    label: listOptionsStore.getHumanSlug(OptionGroupSlug.COUNTRY),
    value: data.value?.country?.title || '-',
  },
  {
    label: t('models.product.view.description.label'),
    value: data.value?.description || '-',
  },
])

const modelId = computed(() => {
  if (!route.params.id || Array.isArray(route.params.id)) {
    return undefined
  }
  return parseInt(route.params.id)
})

onMounted(() => {
  loadData()
})

const loadData = () => {
  if (!modelId.value) {
    router.push({ name: 'page404' })
    return
  }
  props.model.getById(modelId.value).then((response) => {
    data.value = response
  })
}

const itemsActive = computed(
  () => data.value?.items.filter(({ is_sold, is_for_sale }) => !is_sold && is_for_sale) || []
)
const itemsNotActive = computed(
  () =>
    data.value?.items
      .filter(({ is_sold, is_for_sale }) => is_sold || !is_for_sale)
      .sort((a, b) => {
        if (a.is_sold && !b.is_sold) {
          return -1
        }
        if (!a.is_sold && b.is_sold) {
          return 1
        }

        return 0
      }) || []
)
</script>

<template>
  <q-page padding>
    <div class="row justify-between">
      <h4 class="tw-my-0 tw-font-bold">
        {{ model.getTitle() }} - {{ t('models.base.viewModel') }}
      </h4>

      <q-btn color="secondary" no-caps unelevated @click="router.go(-1)">
        {{ t('models.base.back') }}
      </q-btn>
    </div>
    <div v-if="data" class="tw-mt-24px">
      <div class="tw-grid tw-w-full tw-grid-col-1 md:tw-grid-cols-3 tw-gap-40px">
        <q-carousel
          v-model="slide"
          v-model:fullscreen="fullscreenPhoto"
          swipeable
          animated
          thumbnails
          infinite
          class="tw-rounded-20px"
        >
          <q-carousel-slide
            v-for="(file, index) in data.files"
            :key="file.id"
            :name="index + 1"
            :img-src="file.url"
            class="cursor-pointer tw-rounded-20px tw-relative"
            :class="{ 'tw-rounded-20px': !fullscreenPhoto }"
            @click="fullscreenPhoto = !fullscreenPhoto"
          >
            <div class="tw-absolute tw-w-full tw-h-full tw-top-0 tw-left-0">
              <q-img :src="file.url" class="tw-w-full tw-h-full" fit="contain" />
            </div>
          </q-carousel-slide>
        </q-carousel>
        <div
          class="tw-h-full tw-w-full tw-flex tw-flex-col tw-justify-between tw-text-16px md:tw-col-span-2"
        >
          <div
            v-for="(info, index) in infoArray"
            :key="`info_${index}`"
            class="tw-grid tw-grid-cols-3 md:tw-grid-cols-5"
          >
            <div class="tw-font-bold">{{ info.label }}</div>
            <div class="tw-col-span-2 md:tw-col-span-4">{{ info.value }}</div>
          </div>
        </div>
      </div>
      <div
        class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-mt-24px tw-gap-8px md:tw-gap-12px lg:tw-gap-16px"
      >
        <ProductItemViewCard :items="itemsActive" />
      </div>
      <div
        class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 lg:tw-grid-cols-5 tw-mt-24px tw-gap-8px md:tw-gap-12px lg:tw-gap-16px"
      >
        <ProductItemViewCard :items="itemsNotActive" />
      </div>
    </div>
  </q-page>
</template>
