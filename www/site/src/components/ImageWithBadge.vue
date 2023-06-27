<script setup lang="ts">
import { computed } from 'vue'
import ImagesUrl from 'src/enums/images-url'
import XImg from 'src/components/default/XImg.vue'

interface Props {
  label: string
  imageUrl?: string
  imageAlt?: string
  imageClass?: string[] | string
}

const props = defineProps<Props>()

const label = computed(() => props.label)
const imageUrl = computed(() => props.imageUrl || ImagesUrl.EMPTY_IMAGE)
const imageAlt = computed(() => props.imageAlt)
const imageClass = computed(() => props.imageClass)
</script>

<template>
  <div class="tw-relative image-inside-container-hover-scale">
    <x-img :src="imageUrl" :alt="imageAlt" class="tw-w-full tw-h-full" :class="imageClass" />
    <div class="tw-absolute tw-bottom-8 tw-left-0 tw-w-full row tw-justify-center">
      <q-chip
        color="white"
        text-color="primary"
        size="md"
        class="tw-px-6 tw-py-4 tw-text-lg tw-font-medium"
      >
        {{ label }}
      </q-chip>
    </div>
  </div>
</template>
