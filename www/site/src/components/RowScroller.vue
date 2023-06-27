<script setup lang="ts">
import { computed } from 'vue'
import SectionTitle from 'src/components/SectionTitle.vue'
import { useI18n } from 'vue-i18n'

interface Props {
  title?: string
  containerStyles?: string | string[]
}

const { t } = useI18n()

const props = withDefaults(defineProps<Props>(), {
  title: undefined,
  containerStyles: 'tw-gap-2 md:tw-gap-4 lg:tw-gap-6 tw-h-60 md:tw-h-80 lg:tw-h-96',
})
const title = computed(() => props.title)
</script>

<template>
  <div class="tw-w-full">
    <section-title
      v-if="title"
      :title="title"
      :all-text="t('texts.all')"
      all-url="/test"
      :with-divider="false"
      class="tw-mb-2"
    />
    <div class="tw-w-full tw-flex tw-flex-row tw-overflow-scroll" :class="containerStyles">
      <slot />
    </div>
  </div>
</template>
