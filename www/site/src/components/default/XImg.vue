<script setup lang="ts">
import { computed, ref } from 'vue'
import ImagesUrl from 'src/enums/images-url'
import { QImgProps } from 'quasar'

interface Props extends QImgProps {
  fallbackImage?: string
}

const props = defineProps<Props>()

const hasError = ref(false)

const fallbackImageComputed = computed(() => props.fallbackImage || ImagesUrl.EMPTY_IMAGE)

const onError = () => {
  hasError.value = true
}
</script>

<template>
  <q-img v-bind="$attrs" @error="onError">
    <template #error>
      <img class="tw-w-full tw-h-full" :src="fallbackImageComputed" />
    </template>
  </q-img>
</template>
