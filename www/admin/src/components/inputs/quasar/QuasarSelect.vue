<script setup lang="ts">
import { OptionsCallback } from 'src/classes/inputs/quasar/quasar-select'

interface Props {
  optionsCallback: OptionsCallback
}

const props = defineProps<Props>()

const options = props.optionsCallback()
</script>

<template>
  <q-select v-bind="$attrs" :options="options" />
</template>
