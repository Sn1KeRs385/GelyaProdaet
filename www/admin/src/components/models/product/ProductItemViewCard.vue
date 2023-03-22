<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import ProductItemInterface from 'src/interfaces/models/product-item-interface'
import ProductItemModel from 'src/models/product-item'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from '../../../enums/option-group-slug'
import ProductItemWithSizeInterface from 'src/interfaces/models/product-item-with-size-interface'
import ProductItemWithColorInterface from 'src/interfaces/models/product-item-with-color-interface'

interface Props {
  items: (ProductItemInterface & ProductItemWithSizeInterface & ProductItemWithColorInterface)[]
}

defineProps<Props>()

const { t } = useI18n()
const listOptionsStore = useListOptionsStore()

const getItemCardClasses = computed(() => (item: ProductItemInterface) => {
  if (!item.is_for_sale) {
    return ['bg-black', 'text-white']
  }

  if (item.is_sold) {
    return ['bg-grey-4']
  }

  return ['bg-primary', 'text-white']
})

const getItemCardStatus = computed(() => (item: ProductItemInterface) => {
  if (!item.is_for_sale) {
    return t('models.product.view.item.statuses.isNotForSale')
  }

  if (item.is_sold) {
    return t('models.product.view.item.statuses.isSold')
  }

  return t('models.product.view.item.statuses.isForSale')
})

const syncItemFromResponse = (item: ProductItemInterface, response: ProductItemInterface) => {
  item.is_sold = response.is_sold
  item.is_for_sale = response.is_for_sale
}

const markSold = (item: ProductItemInterface) => {
  ProductItemModel.markSold(item.id).then((response) => syncItemFromResponse(item, response))
}

const markNotForSale = (item: ProductItemInterface) => {
  ProductItemModel.markNotForSale(item.id).then((response) => syncItemFromResponse(item, response))
}

const rollbackForSaleStatus = (item: ProductItemInterface) => {
  ProductItemModel.rollbackForSaleStatus(item.id).then((response) =>
    syncItemFromResponse(item, response)
  )
}
</script>

<template>
  <q-card v-for="item in items" :key="`item_${item.id}`">
    <q-card-section :class="getItemCardClasses(item)">
      <div class="text-h6">
        <span class="tw-font-bold">
          {{ listOptionsStore.getHumanSlug(OptionGroupSlug.SIZE) }}
        </span>
        {{ item.size.title }}
      </div>
      <div class="text-subtitle2 tw-font-bold">{{ getItemCardStatus(item) }}</div>
      <div v-if="item.color" class="text-subtitle2">
        <span>
          {{ listOptionsStore.getHumanSlug(OptionGroupSlug.COLOR) }}
        </span>
        {{ item.color.title }}
      </div>
    </q-card-section>

    <q-card-actions align="around" vertical>
      <q-btn
        v-if="item.is_for_sale && !item.is_sold"
        no-caps
        :label="t('models.product.view.item.statusButton.toNotSale')"
        icon="delete"
        class="tw-w-full"
        @click="markNotForSale(item)"
      />
      <q-btn
        v-if="item.is_for_sale && !item.is_sold"
        color="primary"
        :label="t('models.product.view.item.statuses.isSold')"
        icon="done"
        no-caps
        unelevated
        class="tw-w-full"
        @click="markSold(item)"
      />

      <q-btn
        v-if="!item.is_for_sale || item.is_sold"
        no-caps
        :label="t('models.product.view.item.statusButton.toSale')"
        icon="undo"
        class="tw-w-full"
        @click="rollbackForSaleStatus(item)"
      />
    </q-card-actions>
  </q-card>
</template>
