<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'

interface Props {
  modelValue?: string
  label: string
}

const { t, tm } = useI18n()

defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string | undefined): void
}>()

const calendarLocale = computed<{
  days: string[]
  daysShort: string[]
  months: string[]
  monthsShort: string[]
}>(() => tm('calendar'))
</script>

<template>
  <q-input
    :model-value="modelValue"
    mask="date"
    :label="label"
    dense
    @update:model-value="(value) => emit('update:modelValue', value)"
  >
    <template #append>
      <q-icon name="event" class="cursor-pointer">
        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
          <q-date
            :model-value="modelValue"
            :locale="calendarLocale"
            @update:model-value="(value) => emit('update:modelValue', value)"
          >
            <div class="row items-center justify-end">
              <q-btn v-close-popup :label="t('texts.close')" color="primary" flat />
            </div>
          </q-date>
        </q-popup-proxy>
      </q-icon>
    </template>
  </q-input>
</template>
