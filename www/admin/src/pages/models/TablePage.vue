<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import BaseModel from 'src/models/base-model'
import { computed, onMounted, ref, watch } from 'vue'
import { QTableProps } from 'quasar'
import { useRoute, useRouter } from 'vue-router'
import QTableColParams from 'src/interfaces/quasar/q-table-params'
import BaseModelInterface from 'src/interfaces/models/base-model-interface'

interface Props {
  model: BaseModel<BaseModelInterface, BaseModelInterface, BaseModelInterface>
}

const props = defineProps<Props>()

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const tableRef = ref()
const rows = ref<BaseModelInterface[]>([])
const loading = ref(false)
const pagination = ref({
  sortBy: route.query.sortBy || 'id',
  descending: route.query.desc || true,
  page: route.query.page || 1,
  rowsPerPage: route.query.perPage || 25,
  rowsNumber: 1,
})

const tableSettings = computed(() => props.model.getTableSettings())

watch(
  () => props.model,
  () => {
    loading.value = true
    rows.value.splice(0, rows.value.length)
    pagination.value.page = 1
    pagination.value.rowsPerPage = 25
    pagination.value.rowsNumber = 0
    pagination.value.sortBy = 'id'
    pagination.value.descending = true
    tableRef.value.requestServerInteraction()
  }
)

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

const onRowClick = (row: BaseModelInterface) => {
  router.push({ name: `view_${props.model.constructor.name}`, params: { id: row.id } })
}

const getRowValue = (colSetting: QTableColParams, tableRow: { [key: string]: unknown }) => {
  const value = tableRow[colSetting.field as string]
  if (colSetting.format) {
    return colSetting.format(value, tableRow)
  }

  return value
}
</script>

<template>
  <q-page padding>
    <div class="row justify-between">
      <h4 class="tw-my-0 tw-font-bold">{{ model.getTitle() }}</h4>
      <q-btn
        color="primary"
        no-caps
        unelevated
        :to="{ name: `create_form_${model.constructor.name}` }"
      >
        {{ t('models.base.create') }}
      </q-btn>
    </div>
    <q-table
      ref="tableRef"
      v-model:pagination="pagination"
      class="tw-mt-4"
      color="primary"
      :rows-per-page-options="[25, 50, 75, 100, 200, 500, 1000]"
      :rows="rows"
      :columns="tableSettings"
      row-key="id"
      :loading="loading"
      binary-state-sort
      @request="onRequest"
    >
      <template #body="tableBodyProps">
        <q-tr :props="tableBodyProps" @click="onRowClick(tableBodyProps.row)">
          <q-td v-for="colSetting in tableSettings" :key="colSetting.name" :props="tableBodyProps">
            {{ getRowValue(colSetting, tableBodyProps.row) }}
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </q-page>
</template>
