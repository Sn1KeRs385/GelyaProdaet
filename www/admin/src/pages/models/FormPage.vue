<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BaseModel from 'src/models/base-model'
import { onMounted, ref } from 'vue'
import { QTableProps } from 'quasar'
import { useRoute, useRouter } from 'vue-router'

interface Props {
  model: BaseModel<never>
}

const props = defineProps<Props>()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
console.log(route.query)
const tableRef = ref()
const rows = ref([])
const loading = ref(false)
const pagination = ref({
  sortBy: route.query.sortBy || 'id',
  descending: route.query.desc || true,
  page: route.query.page || 1,
  rowsPerPage: route.query.perPage || 25,
  rowsNumber: 1,
})

// const data = props.model.index()
onMounted(() => {
  tableRef.value.requestServerInteraction()
})

const onRequest = async (tableProps: QTableProps) => {
  loading.value = true

  const tempData = await props.model.index(
    tableProps.pagination?.page || 1,
    tableProps.pagination?.rowsPerPage || 25
  )

  rows.value.splice(0, rows.value.length, ...tempData.items)

  pagination.value.page = tempData.current_page
  pagination.value.rowsPerPage = tempData.per_page
  pagination.value.rowsNumber = tempData.total
  pagination.value.sortBy = tableProps.pagination?.sortBy || 'id'
  pagination.value.descending =
    tableProps.pagination?.descending !== undefined ? tableProps.pagination?.descending : true

  loading.value = false

  saveParamsToRoute()
}

const saveParamsToRoute = () => {
  const params = {
    page: pagination.value.page,
    perPage: pagination.value.rowsPerPage,
    sortBy: pagination.value.sortBy,
    desc: pagination.value.descending.toString(),
  }

  router.push({ query: params })
}
</script>

<template>
  <q-page padding>
    <div class="row justify-between">
      <h4 class="tw-my-0 tw-font-bold">{{ t(model.getTitle()) }}</h4>
      <q-btn color="primary" no-caps unelevated>{{ t('models.base.create') }}</q-btn>
    </div>
    <q-table
      ref="tableRef"
      v-model:pagination="pagination"
      class="tw-mt-4"
      color="primary"
      :rows-per-page-options="[25, 50, 75, 100, 200, 500, 1000]"
      :rows="rows"
      :columns="model.getTableSettings()"
      row-key="id"
      :loading="loading"
      binary-state-sort
      @request="onRequest"
    />
  </q-page>
</template>
