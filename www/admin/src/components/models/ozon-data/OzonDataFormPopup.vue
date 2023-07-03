<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { eventBus } from 'src/boot/event-bus'
import OzonDataForm from 'src/components/models/ozon-data/OzonDataForm.vue'

const { t } = useI18n()
const popupIsOpened = ref(false)
const productIdToForm = ref<number | undefined>(undefined)

eventBus.on('openOzonDataPopup', (productId: number) => {
  productIdToForm.value = productId
  popupIsOpened.value = true
})
</script>

<template>
  <q-dialog v-model="popupIsOpened" full-width>
    <ozon-data-form v-if="productIdToForm" :product-id="productIdToForm">
      <template #action-slot>
        <q-btn v-close-popup color="primary" no-caps size="16px" flat :label="t('texts.close')" />
      </template>
    </ozon-data-form>
    <div v-else>{{ t('texts.somethingWrong') }}</div>
  </q-dialog>
</template>
