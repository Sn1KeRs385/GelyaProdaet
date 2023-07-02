<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import AutoFormComponent from 'src/components/models/AutoFormComponent.vue'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'
import FormField from 'src/interfaces/admin/form-field'
import { computed, ref, watch } from 'vue'
import ApiFileInterface from 'src/interfaces/Api/file-interface'
import FormData from 'src/interfaces/admin/form-data'
import draggable from 'vuedraggable'

interface ModelValueInterface {
  [key: string]: unknown
}

interface Props {
  fieldKey: string
  modelValue?: ModelValueInterface[]
  label?: string
  formFields: FormField[]
  fieldToShowInList?: string
  modelData?: BaseModelInterface
  globalFiles?: ApiFileInterface[]
}

const { t } = useI18n()

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: ModelValueInterface[] | undefined): void
}>()

const showForm = ref(false)
const showList = ref(false)
const drag = ref(false)
const rowEditData = ref<ModelValueInterface | undefined>()
const rowEditIndex = ref<number | undefined>()
const _value = ref(props.modelValue)

watch(
  () => props.modelValue,
  (newValue) => {
    _value.value = newValue
  },
  { deep: true }
)

watch(
  _value,
  (newValue) => {
    emit('update:modelValue', newValue)
  },
  { deep: true }
)

const itemListTitle = computed(() => (item: ModelValueInterface) => {
  if (props.fieldToShowInList) {
    return item?.[props.fieldToShowInList] || t('texts.unknown')
  }

  return Object.values(item)?.[0] || t('texts.unknown')
})

const itemMainField = computed<string>(() => {
  if (props.fieldToShowInList) {
    return props.fieldToShowInList
  }

  return (Object.values(props.modelValue?.[0] || {})?.[0] || 'unknown') as string
})

const onDeleteItemClick = (item: ModelValueInterface) => {
  emit(
    'update:modelValue',
    props.modelValue?.filter((valueItem) => valueItem !== item)
  )
}

const onEditItemClick = (item: ModelValueInterface) => {
  rowEditData.value = item
  rowEditIndex.value = props.modelValue?.findIndex((itemSearch) => itemSearch === item)
  showForm.value = true
}

const onAddItemClick = () => {
  rowEditData.value = undefined
  rowEditIndex.value = undefined
  showForm.value = true
}

const onFormSubmit = (formData: FormData) => {
  showForm.value = false
  if (rowEditIndex.value !== undefined) {
    const newValue = [...(props.modelValue || [])]
    const valueForChange = newValue[rowEditIndex.value]
    Object.entries(formData).forEach(([key, value]) => {
      valueForChange[key] = value.value
    })
    newValue[rowEditIndex.value] = valueForChange
    emit('update:modelValue', newValue)
    return
  }

  const newValue = [...(props.modelValue || [])]

  const data: { [key: string]: unknown } = {}

  Object.entries(formData).forEach(([key, value]) => {
    data[key] = value.value
  })

  newValue.push(data)

  emit('update:modelValue', newValue)

  showList.value = true
}
</script>

<template>
  <div>
    <q-card bordered>
      <q-item class="tw-flex tw-flex-row tw-justify-between tw-items-center">
        <div class="text-h6">
          {{ label }}
        </div>
        <div class="tw-flex tw-flex-row tw-justify-between tw-items-center">
          <q-btn
            v-if="(modelValue?.length || 0) > 0"
            color="primary"
            no-caps
            unelevated
            flat
            class="tw-px-0 md:tw-px-12px"
            @click="showList = !showList"
          >
            <span v-if="showList">
              {{ t('models.base.form.formArray.hideList') }}
            </span>
            <span v-else>{{ t('models.base.form.formArray.showList') }}</span>
          </q-btn>
          <q-btn color="primary" no-caps unelevated @click="onAddItemClick">
            {{ t('models.base.add') }}
          </q-btn>
        </div>
      </q-item>
      <q-separator v-if="showList" />
      <q-card-section v-if="showList">
        <q-list bordered separator>
          <draggable
            v-model="_value"
            :item-key="itemMainField"
            @start="drag = true"
            @end="drag = false"
          >
            <template #item="{ element }">
              <q-item
                v-ripple
                class="tw-flex tw-flex-row tw-justify-between tw-items-center tw-w-full"
                clickable
              >
                <div>{{ itemListTitle(element) }}</div>
                <div class="tw-flex tw-flex-row tw-items-center">
                  <q-btn-group>
                    <q-btn
                      flat
                      color="primary"
                      icon="delete_forever"
                      @click.stop="onDeleteItemClick(element)"
                    />
                    <q-btn color="primary" icon="edit" @click.stop="onEditItemClick(element)" />
                  </q-btn-group>
                  <q-icon class="tw-cursor-pointer tw-ml-24px" name="menu" size="30px" />
                </div>
              </q-item>
            </template>
          </draggable>
        </q-list>
      </q-card-section>
    </q-card>
    <q-dialog v-model="showForm" position="bottom">
      <q-card>
        <q-card-section>
          <div class="row justify-between">
            <h6 v-if="rowEditData" class="tw-my-0 tw-font-semibold">
              {{ label }} - {{ t('models.base.edit') }}
            </h6>
            <h6 v-else class="tw-my-0 tw-font-semibold">
              {{ label }} - {{ t('models.base.addModel') }}
            </h6>
          </div>
          <auto-form-component
            :fields="formFields"
            :model-data="rowEditData"
            :global-files="globalFiles"
            @submit-form="onFormSubmit"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>
