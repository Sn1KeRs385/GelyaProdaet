<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  title: string
  fontSize?: string
  allText?: string
  allUrl?: string
  withDivider?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  fontSize: '16px',
  allText: undefined,
  allUrl: undefined,
  withDivider: true,
})

const showAllSection = computed(() => props.allText && props.allUrl)
</script>

<template>
  <div
    class="tw-w-full row tw-gap-2 tw-items-end tw-text-dark tw-font-semibold"
    :style="{ fontSize }"
  >
    <div>
      {{ title.toUpperCase() }}
    </div>
    <div class="tw-grow" :class="{ 'tw-bg-black tw-h-2px tw-mb-2': withDivider }"></div>
    <router-link v-if="showAllSection" :to="allUrl" class="tw-text-dark">
      {{ allText.toUpperCase() }}
    </router-link>
  </div>
</template>
