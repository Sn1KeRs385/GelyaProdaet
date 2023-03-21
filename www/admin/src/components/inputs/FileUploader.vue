<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed, ref, watch } from 'vue'
import { QFile } from 'quasar'
import CollectionName from 'file-uploader/enums/collection-name'
import { useFileUploadStore } from 'file-uploader/stores/file-upload-store'
import QFileParams from 'src/interfaces/quasar/q-file-params'
import FileUploadInfoInterface from 'file-uploader/interfaces/file-upload-info-interface'

interface Props {
  modelValue?: number[]
  isReady: boolean
  label: string
  fileUploaderOptions?: QFileParams
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number[]): void
  (e: 'update:isReady', value: boolean): void
}>()

const fileUploadStore = useFileUploadStore()

const fileInput = ref<InstanceType<typeof QFile>>()

const uploadFiles = ref<FileUploadInfoInterface[]>([])
watch(
  uploadFiles,
  () => {
    const ids: number[] = []

    let ready = true
    uploadFiles.value.forEach((uploadFile) => {
      if (!uploadFile.isFinishedUpload) {
        ready = false
      }
      if (!uploadFile.id) {
        return
      }
      if (props.modelValue?.includes(uploadFile.id)) {
        return
      }
      ids.push(uploadFile.id)
    })

    if (ids.length > 0) {
      const newValue = [...(props.modelValue || [])]
      newValue.push(...ids)
      emit('update:modelValue', newValue)
    }

    emit('update:isReady', ready)
  },
  { deep: true }
)

const hasFiles = computed(() => props.modelValue?.length || 0 > 0 || uploadFiles.value.length > 0)
const onChooseFiles = (files: File[]) => {
  files.forEach((file) => {
    let collectionName
    if (/^video/gi.test(file.type)) {
      collectionName = CollectionName.VIDEO
    } else if (/^image/gi.test(file.type)) {
      collectionName = CollectionName.IMAGE
    } else {
      collectionName = CollectionName.FILE
    }
    if (collectionName) {
      const uploadFile = fileUploadStore.addFileToUploadQueue(file, collectionName)
      uploadFiles.value.push(uploadFile)
    }
  })
  fileUploadStore.startUpload()
}
const openChooseFileDialog = () => {
  ;(fileInput.value?.$el as HTMLInputElement).click()
}

const { t } = useI18n()
</script>

<template>
  <q-card>
    <q-card-section class="row justify-between">
      <div class="text-h6">{{ label }}</div>
      <q-btn color="primary" no-caps unelevated @click="openChooseFileDialog">
        {{ t('models.base.add') }}
      </q-btn>
    </q-card-section>
    <q-separator v-if="hasFiles" />

    <q-card-section v-if="hasFiles">
      <q-linear-progress
        v-for="uploadFile in uploadFiles"
        :key="uploadFile.file.name"
        :value="uploadFile.progress"
        color="primary"
        class="q-mt-sm"
        size="25px"
      >
        <div class="absolute-full flex flex-center">
          <q-badge color="primary" :label="`${uploadFile.progress}% - ${uploadFile.file.name}`" />
        </div>
      </q-linear-progress>
      <!--      <q-img src="https://cdn.quasar.dev/img/parallax2.jpg" class="tw-w-250px tw-h-150px" />-->
    </q-card-section>

    <q-file
      ref="fileInput"
      v-bind="fileUploaderOptions"
      class="hidden"
      @update:model-value="onChooseFiles"
    />
  </q-card>
</template>
