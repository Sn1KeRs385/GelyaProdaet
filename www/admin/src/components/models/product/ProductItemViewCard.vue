<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import ProductItemInterface from 'src/interfaces/models/product-item-interface'
import ProductItemModel, {
  AfterStatusManipulateInterface as ProductItemAfterStatusManipulateInterface,
} from 'src/models/product-item'
import { useListOptionsStore } from 'src/stores/list-options-store'
import OptionGroupSlug from '../../../enums/option-group-slug'
import ProductItemWithSizeInterface from 'src/interfaces/models/product-item-with-size-interface'
import ProductItemWithColorInterface from 'src/interfaces/models/product-item-with-color-interface'
import ProductItemWithNormalizePricesInterface from 'src/interfaces/models/product-item-with-normalize-prices-interface'
import ProductItemWithSizeYearInterface from 'src/interfaces/models/product-item-with-size-year-interface'

type ProductItemInterfaceUnited = ProductItemInterface &
  ProductItemWithSizeInterface &
  ProductItemWithSizeYearInterface &
  ProductItemWithColorInterface &
  ProductItemWithNormalizePricesInterface

interface Props {
  items: ProductItemInterfaceUnited[]
}

const props = defineProps<Props>()

const { t } = useI18n()
const listOptionsStore = useListOptionsStore()

const itemsSorted = computed(() =>
  [...props.items].sort((itemA, itemB) => {
    const forSaleA = itemA.is_for_sale ? 1 : 0
    const forSaleB = itemB.is_for_sale ? 1 : 0
    if (forSaleA !== forSaleB) {
      return forSaleB - forSaleA
    }

    const isReservedA = itemA.is_reserved ? 1 : 0
    const isReservedB = itemB.is_reserved ? 1 : 0
    if (isReservedA !== isReservedB) {
      return isReservedB - isReservedA
    }

    const weightA = itemA.size_year?.weight || itemA.size?.weight || 0
    const weightB = itemB.size_year?.weight || itemB.size?.weight || 0

    if (weightA !== weightB) {
      return weightA - weightB
    }

    const titleA =
      itemA.size_year?.title.toLowerCase() ||
      itemA.size?.title.toLowerCase() ||
      'zzzzzzzzzzzzzzzzzz'
    const titleB =
      itemB.size_year?.title.toLowerCase() ||
      itemB.size?.title.toLowerCase() ||
      'zzzzzzzzzzzzzzzzzz'

    if (titleA < titleB) {
      return -1
    } else if (titleA > titleB) {
      return 1
    }

    const idA = itemA.id
    const idB = itemB.id

    return idA - idB
  })
)

const getItemCardClasses = computed(() => (item: ProductItemInterfaceUnited) => {
  if (!item.is_for_sale) {
    return ['bg-black', 'text-white']
  }

  if (item.is_sold) {
    return ['bg-grey-4']
  }

  if (item.is_reserved) {
    return ['text-primary']
  }

  return ['bg-primary', 'text-white']
})

const getItemCardStatus = computed(() => (item: ProductItemInterfaceUnited) => {
  if (!item.is_for_sale) {
    return t('models.product.view.item.statuses.isNotForSale')
  }

  if (item.is_sold) {
    return t('models.product.view.item.statuses.isSold')
  }

  if (item.is_reserved) {
    return t('models.product.view.item.statuses.isReserved')
  }

  return t('models.product.view.item.statuses.isForSale')
})

const getItemCardTitle = computed(() => (item: ProductItemInterfaceUnited) => {
  if (item.size_year) {
    let title = item.size_year.title
    if (item.size) {
      title += ` (${item.size.title})`
    }
    return title
  } else if (item.size) {
    return item.size.title
  }

  return t('texts.unknown')
})

const getSubtitleTextForItem = computed(() => (item: ProductItemInterfaceUnited) => {
  const values: { label: string; value: string }[] = []

  if (item.color) {
    values.push({
      label: listOptionsStore.getHumanSlug(OptionGroupSlug.COLOR),
      value: item.color.title,
    })
  }

  if (item.count > 1) {
    values.push({
      label: t('models.product.view.item.count.label'),
      value: item.count.toString(),
    })
  }

  return values
})

const getPriceInfoForItem = computed(() => (item: ProductItemInterfaceUnited) => {
  const values: { label: string; value: string; isPriceSell?: boolean }[] = []

  values.push({
    label: t('models.product.view.item.price_buy.label'),
    value: t('models.product.view.item.price_buy.amount', { amount: item.price_buy_normalize }),
  })

  if (item.price_sell_normalize) {
    values.push({
      label: t('models.product.view.item.price_sell.label'),
      value: t('models.product.view.item.price_sell.amount', { amount: item.price_sell_normalize }),
      isPriceSell: true,
    })
  }

  values.push({
    label: t('models.product.view.item.price.label'),
    value: t('models.product.view.item.price.amount', { amount: item.price_normalize }),
  })

  return values
})

const onPriceSellSave = (value: number, initialValue: number, item: ProductItemInterfaceUnited) => {
  changePriceSell(item, value)
}

const syncItemFromResponse = (
  item: ProductItemInterfaceUnited,
  response: ProductItemAfterStatusManipulateInterface
) => {
  item.is_sold = response.is_sold
  item.is_for_sale = response.is_for_sale
  item.is_reserved = response.is_reserved
  item.price_sell = response.price_sell
  item.price_sell_normalize = response.price_sell_normalize
}

const markSold = (item: ProductItemInterfaceUnited) => {
  ProductItemModel.markSold(item.id).then((response) => syncItemFromResponse(item, response))
}

const changePriceSell = (item: ProductItemInterfaceUnited, price: number) => {
  ProductItemModel.changePriceSell(item.id, price).then((response) =>
    syncItemFromResponse(item, response)
  )
}

const markNotForSale = (item: ProductItemInterfaceUnited) => {
  ProductItemModel.markNotForSale(item.id).then((response) => syncItemFromResponse(item, response))
}

const rollbackForSaleStatus = (item: ProductItemInterfaceUnited) => {
  ProductItemModel.rollbackForSaleStatus(item.id).then((response) =>
    syncItemFromResponse(item, response)
  )
}

const switchReserve = (item: ProductItemInterfaceUnited) => {
  ProductItemModel.switchReserve(item.id).then((response) => syncItemFromResponse(item, response))
}
</script>

<template>
  <q-card v-for="item in itemsSorted" :key="`item_${item.id}`">
    <q-card-section :class="getItemCardClasses(item)">
      <div class="text-h6">
        <span class="tw-font-bold">
          {{ listOptionsStore.getHumanSlug(OptionGroupSlug.SIZE) }}
        </span>
        {{ getItemCardTitle(item) }}
      </div>
      <div class="text-subtitle2 tw-font-bold">{{ getItemCardStatus(item) }}</div>
      <div v-if="getSubtitleTextForItem(item).length > 0" class="text-subtitle2">
        <template
          v-for="(subtitle, index) in getSubtitleTextForItem(item)"
          :key="`subtitle_${subtitle.label}`"
        >
          <span v-if="index > 0">;</span>
          <span class="tw-font-medium">{{ subtitle.label }}:</span>
          {{ subtitle.value }}
        </template>
      </div>

      <div class="row tw-justify-between tw-w-full tw-mt-6px">
        <div
          v-for="(priceInfo, index) in getPriceInfoForItem(item)"
          :key="`priceInfo_${index}`"
          class="col"
          :class="{
            'tw-text-12px': !priceInfo.isPriceSell,
            'tw-text-13px': priceInfo.isPriceSell,
            'tw-text-gray-400': !priceInfo.isPriceSell,
            'tw-text-gray-600': priceInfo.isPriceSell,
            'tw-text-center': index > 0 && index < getPriceInfoForItem(item).length - 1,
            'tw-text-right': index === getPriceInfoForItem(item).length - 1,
          }"
        >
          <div
            :class="{
              'tw-font-bold': !priceInfo.isPriceSell,
              'tw-font-extrabold': priceInfo.isPriceSell,
            }"
          >
            {{ priceInfo.label }}
          </div>
          <div
            :class="{
              'tw-font-medium': !priceInfo.isPriceSell,
              'tw-font-bold': priceInfo.isPriceSell,
            }"
          >
            {{ priceInfo.value }}
            <q-popup-edit
              v-if="priceInfo.isPriceSell"
              v-slot="scope"
              v-model.number="item.price_sell_normalize"
              buttons
              :label-set="t('models.base.form.save')"
              :label-cancel="t('models.base.form.cancel')"
              @save="(value, initialValue) => onPriceSellSave(value, initialValue, item)"
            >
              <q-input v-model="scope.value" dense autofocus @keyup.enter="scope.set" />
            </q-popup-edit>
          </div>
        </div>
      </div>
    </q-card-section>

    <q-card-actions align="around" vertical>
      <q-btn
        v-if="item.is_for_sale && !item.is_sold"
        :label="
          item.is_reserved
            ? t('models.product.view.item.statusButton.rollbackReserve')
            : t('models.product.view.item.statusButton.reserve')
        "
        color="primary"
        :icon="item.is_reserved ? 'remove_shopping_cart' : 'shopping_cart'"
        no-caps
        outline
        class="tw-w-full"
        @click="switchReserve(item)"
      />
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
        :label="t('models.product.view.item.statusButton.sold')"
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
