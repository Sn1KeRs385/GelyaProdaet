<script setup lang="ts">
import { computed } from 'vue'
import { useFileUploadStore } from 'file-uploader/stores/file-upload-store'
import { useI18n } from 'vue-i18n'

const fileUploadStore = useFileUploadStore()
const files = computed(() => fileUploadStore.files)
const fileCount = computed(() => files.value.length)
const fileUploadedCount = computed(() => files.value.filter((file) => file.progress === 100).length)
const fileErrorCount = computed(() => files.value.filter((file) => file.errors.length > 0).length)

const { t } = useI18n()

const uploadProgress = computed(() => {
  let progress = 0
  files.value.forEach((file) => {
    progress = progress + file.progress / 100
  })

  return (1 / fileCount.value) * progress
})

const uploadProgressPercent = computed(() => Math.round(uploadProgress.value * 100))
</script>

<template>
  <q-card
    v-show="fileCount > 0"
    flat
    bordered
    class="bg-grey-1 upload-progress-card tw-max-h-200px tw-w-500px tw-overflow-auto"
  >
    <div class="row justify-between">
      <div class="text-blue">{{ t('fileUploader.allCount', { count: fileCount }) }}</div>
      <div class="text-green">{{ t('fileUploader.endCount', { count: fileUploadedCount }) }}</div>
      <div class="text-red">{{ t('fileUploader.errorCount', { count: fileErrorCount }) }}</div>
    </div>
    <q-linear-progress
      size="18px"
      :value="uploadProgress"
      animation-speed="200"
      :color="uploadProgress < 1 ? 'blue' : 'green'"
    >
      <div class="absolute-full flex flex-center">
        <q-badge
          color="white"
          :text-color="uploadProgress < 1 ? 'blue' : 'green'"
          :label="`${uploadProgressPercent}%`"
        />
      </div>
    </q-linear-progress>
  </q-card>
</template>

<style scoped lang="scss">
.upload-progress-card {
  background: radial-gradient(circle, #35a2ff 0%, #014a88 100%);
}
</style>
