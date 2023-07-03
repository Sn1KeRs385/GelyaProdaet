<script setup lang="ts">
import { CreateCallback, OptionsCallback } from 'src/classes/inputs/quasar/quasar-select'
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { QSelect } from 'quasar'

interface Props {
  optionsCallback: OptionsCallback
  createCallback?: CreateCallback
  label?: string
  tooltip?: string
  multiple: boolean
  required: boolean
}

const { t } = useI18n()

const props = defineProps<Props>()

const emit = defineEmits<{ (e: 'update:modelValue', value: number | string): void }>()

const options = ref(props.optionsCallback())

const filteredOptions = ref(options.value)
const filterText = ref<string | undefined>()

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const filterFn = (val: string, update: any) => {
  update(() => {
    if (val === '') {
      filterText.value = undefined
      filteredOptions.value = options.value
    } else {
      filterText.value = val
      const needle = val.toLowerCase()
      filteredOptions.value = options.value.filter(
        (v) => v.label.toLowerCase().indexOf(needle) > -1
      )
    }
  })
}

const createElement = (label: string) => {
  const createCallback = props.createCallback
  if (!createCallback) {
    return
  }
  createCallback(label).then((value) => {
    options.value.push({ label, value })
    filteredOptions.value.push({ label, value })
    emit('update:modelValue', value)
  })
}
</script>

<template>
  <q-select
    v-bind="$attrs"
    :options="filteredOptions"
    :label="label"
    use-input
    input-debounce="0"
    :multiple="multiple"
    :use-chips="multiple"
    @filter="filterFn"
    @update:model-value="(value) => emit('update:modelValue', value)"
  >
    <template #label>
      {{ label }}
      <span v-if="required" class="tw-text-red-600">*</span>
    </template>
    <template v-if="tooltip" #prepend>
      <q-icon v-if="tooltip" name="info">
        <q-tooltip transition-show="scale" transition-hide="scale" class="tw-text-16px">
          {{ tooltip }}
        </q-tooltip>
      </q-icon>
    </template>
    <template #no-option>
      <q-item>
        <q-item-section class="text-grey" side>
          {{ t('models.base.form.select.noResult') }}
        </q-item-section>
        <q-item-section v-if="filterText && createCallback" class="text-grey">
          <q-btn
            color="primary"
            no-caps
            unelevated
            class="tw-w-max tw-self-end"
            @click="createElement(filterText)"
          >
            {{ t('models.base.form.select.createButton', { name: `${label} - ${filterText}` }) }}
          </q-btn>
        </q-item-section>
      </q-item>
    </template>
  </q-select>
</template>
