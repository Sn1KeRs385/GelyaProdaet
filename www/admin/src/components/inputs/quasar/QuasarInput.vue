<script setup lang="ts">
interface Props {
  tooltip?: string
  label?: string
  required: boolean
}

defineProps<Props>()
</script>

<template>
  <q-input v-bind="$attrs" :label="label">
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
  </q-input>
</template>
