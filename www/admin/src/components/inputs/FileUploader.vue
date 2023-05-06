<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed, onMounted, ref, watch } from 'vue'
import { QFile } from 'quasar'
import CollectionName from 'file-uploader/enums/collection-name'
import { useFileUploadStore } from 'file-uploader/stores/file-upload-store'
import QFileParams from 'src/interfaces/quasar/q-file-params'
import FileUploadInfoInterface from 'file-uploader/interfaces/file-upload-info-interface'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import ModelWithFilesInterface from 'src/interfaces/models/model-with-files-interface'
import FileStatus from 'file-uploader/enums/file-status'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import ImagesUrl from 'src/enums/images-url'

interface Props {
  modelValue?: number[]
  modelData?: BaseModelInterface & ModelWithFilesInterface
  isReady: boolean
  label: string
  fileUploaderOptions?: QFileParams
  collectionName?: CollectionName
  filesField?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number[]): void
  (e: 'update:isReady', value: boolean): void
}>()

const fileUploadStore = useFileUploadStore()

const fileInput = ref<InstanceType<typeof QFile>>()

const filesFromModelData = computed<ApiFileInterface[]>(
  () => props.modelData?.[props.filesField || 'files'] || []
)

onMounted(() => {
  const fileIds = filesFromModelData.value.map(({ id }) => id)
  emit('update:modelValue', fileIds)
})

const uploadFiles = ref<FileUploadInfoInterface[]>([])
const _isShowReadyFiles = ref(false)

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

const hasUploadFiles = computed(() => uploadFiles.value.length > 0)
const hasReadyFiles = computed(() => readyFiles.value.length > 0)
const isShowReadyFiles = computed(() => _isShowReadyFiles.value && hasReadyFiles.value)

const readyFiles = computed(() => {
  return [...filesFromModelData.value].sort((a, b) => {
    if (a.type?.indexOf('image/') === 0 && b.type?.indexOf('image/') !== 0) {
      return -1
    } else if (a.type?.indexOf('image/') !== 0 && b.type?.indexOf('image/') === 0) {
      return 1
    } else {
      return 0
    }
  })
})

const readyFileIsDeleted = computed(
  () => (file: ApiFileInterface) => !props.modelValue?.includes(file.id)
)

const onChooseFiles = (files: File[] | File) => {
  const handleFile = (file: File) => {
    let collectionName = props.collectionName

    if (!collectionName) {
      if (/^video/gi.test(file.type)) {
        collectionName = CollectionName.VIDEO
      } else if (/^image/gi.test(file.type)) {
        collectionName = CollectionName.IMAGE
      } else {
        collectionName = CollectionName.FILE
      }
    }

    if (collectionName) {
      const uploadFile = fileUploadStore.addFileToUploadQueue(file, collectionName)
      uploadFiles.value.push(uploadFile)
    }
  }
  if (Array.isArray(files)) {
    files.forEach(handleFile)
  } else {
    handleFile(files)
  }
  fileUploadStore.startUpload()
}

const getReadyFileImageSrc = computed(() => (file: ApiFileInterface) => {
  let url: string | undefined
  if (file.type?.indexOf('image/') === 0 && file.status === FileStatus.FINISHED) {
    url = file.url
  }

  return url || ImagesUrl.EMPTY_IMAGE
})

const deleteFile = (file: ApiFileInterface) => {
  const newValue = (props.modelValue || []).filter((id) => id !== file.id)
  emit('update:modelValue', newValue)
}
const rollbackFile = (file: ApiFileInterface) => {
  const newValue = props.modelValue || []
  if (newValue.includes(file.id)) {
    return
  }
  newValue.push(file.id)
  emit('update:modelValue', newValue)
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
      <div class="row items-center justify-between tw-gap-8px tw-w-full md:tw-w-max">
        <q-btn
          v-if="readyFiles.length > 0"
          color="primary"
          no-caps
          unelevated
          flat
          class="tw-px-0 md: tw-px-12px"
          @click="_isShowReadyFiles = !_isShowReadyFiles"
        >
          <span v-if="isShowReadyFiles">
            {{ t('models.base.form.fileUploader.hideReadyFiles') }}
          </span>
          <span v-else>{{ t('models.base.form.fileUploader.showReadyFiles') }}</span>
        </q-btn>
        <q-btn color="primary" no-caps unelevated @click="openChooseFileDialog">
          {{ t('models.base.add') }}
        </q-btn>
      </div>
    </q-card-section>
    <q-separator v-if="isShowReadyFiles || hasUploadFiles" />

    <q-card-section v-if="hasUploadFiles">
      <q-linear-progress
        v-for="uploadFile in uploadFiles"
        :key="uploadFile.file.name"
        :value="uploadFile.progress / 100"
        color="primary"
        class="q-mt-sm"
        size="25px"
      >
        <div class="absolute-full flex flex-center">
          <q-badge color="primary" :label="`${uploadFile.progress}% - ${uploadFile.file.name}`" />
        </div>
      </q-linear-progress>
    </q-card-section>

    <q-card-section v-if="isShowReadyFiles" class="tw-grid tw-gap-12px files-container">
      <q-card
        v-for="readyFile in readyFiles"
        :key="`file_${readyFile.id}`"
        class="tw-w-full tw-max-h-250 md:tw-max-h-300px tw-flex tw-flex-col"
      >
        <q-img :src="getReadyFileImageSrc(readyFile)" class="tw-w-full tw-flex-shrink-1">
          <div v-if="readyFileIsDeleted(readyFile)" class="absolute-top">
            <div class="text-h6 text-red text-center">
              {{ t('models.base.form.fileUploader.fileOnDeleting') }}
            </div>
          </div>
          <div class="absolute-bottom">
            <div v-if="readyFile.status !== FileStatus.FINISHED" class="text-h6">
              {{ t(`fileUploaderModule.enums.fileStatuses.${readyFile.status}`) }}
            </div>
            <div class="text-subtitle1 tw-line-clamp-1">{{ readyFile.original_filename }}</div>
          </div>
        </q-img>

        <q-card-actions class="tw-grow">
          <q-btn
            v-if="!readyFileIsDeleted(readyFile)"
            flat
            class="tw-w-full"
            @click="deleteFile(readyFile)"
          >
            {{ t('models.base.form.fileUploader.delete') }}
          </q-btn>
          <q-btn v-else flat class="tw-w-full" @click="rollbackFile(readyFile)">
            {{ t('models.base.form.fileUploader.rollback') }}
          </q-btn>
        </q-card-actions>
      </q-card>
    </q-card-section>

    <q-file
      ref="fileInput"
      v-bind="fileUploaderOptions"
      class="hidden"
      @update:model-value="onChooseFiles"
    />
  </q-card>
</template>

<style lang="scss" scoped>
.files-container {
  grid-template-columns: repeat(1, minmax(0, 1fr));
  @media (min-width: 768px) {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  @media (min-width: 1024px) {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }
}
</style>
