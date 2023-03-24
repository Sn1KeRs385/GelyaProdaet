<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ProductModel, { GetByIdItemInterface } from 'src/models/product'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from 'src/enums/option-group-slug'
import ProductItemViewCard from 'src/components/models/product/ProductItemViewCard.vue'
import imagesUrl from 'src/enums/images-url'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import FileStatus from 'file-uploader/enums/file-status'
import ImagesUrl from 'src/enums/images-url'

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

const getFileImageSrc = computed(() => (file: ApiFileInterface) => {
  let url: string | undefined
  if (file.type?.indexOf('image/') === 0 && file.status === FileStatus.FINISHED) {
    url = file.url
  }

  return url || ImagesUrl.EMPTY_IMAGE
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

      <div
        class="row tw-gap-12px row tw-justify-between md:tw-justify-start tw-w-full md:tw-w-auto tw-mt-8px md:tw-mt-0"
      >
        <q-btn
          color="primary"
          no-caps
          unelevated
          flat
          :to="{ name: `edit_${model.constructor.name}`, params: { id: modelId } }"
        >
          {{ t('models.base.edit') }}
        </q-btn>

        <q-btn color="primary" no-caps unelevated @click="router.go(-1)">
          {{ t('models.base.back') }}
        </q-btn>
      </div>
    </div>
    <div v-if="data" class="tw-mt-24px">
      <div class="tw-grid tw-w-full tw-grid-col-1 md:tw-grid-cols-3 tw-gap-40px">
        <q-img
          v-if="data.files.length === 0"
          :src="imagesUrl.EMPTY_IMAGE"
          class="tw-h-200px md:tw-h-max"
        >
          <div class="absolute-bottom text-center text-subtitle2">
            {{ t('models.product.view.images.imageNotFound') }}
          </div>
        </q-img>
        <q-carousel
          v-else
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
            :img-src="getFileImageSrc(file)"
            class="cursor-pointer tw-rounded-20px tw-relative"
            :class="{ 'tw-rounded-20px': !fullscreenPhoto }"
            @click="fullscreenPhoto = !fullscreenPhoto"
          >
            <div class="tw-absolute tw-w-full tw-h-full tw-top-0 tw-left-0">
              <q-img :src="getFileImageSrc(file)" class="tw-w-full tw-h-full" fit="contain">
                <div v-if="file.status !== FileStatus.FINISHED" class="absolute-bottom">
                  <div v-if="file.status !== FileStatus.FINISHED" class="text-h6">
                    {{ t(`fileUploaderModule.enums.fileStatuses.${file.status}`) }}
                  </div>
                </div>
              </q-img>
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
