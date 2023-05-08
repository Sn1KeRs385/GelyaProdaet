<script setup lang="ts">
import SalesReportChart from 'src/components/charts/SalesReportChart.vue'
import { computed, onMounted, ref, watch } from 'vue'
import { useMainDashboardStore } from 'src/stores/main-dashboard-store'
import DatePickerInsideInput from 'src/components/dashboard/DatePickerInsideInput.vue'
import { useI18n } from 'vue-i18n'
import { DateTime } from 'luxon'
import PieChart from 'src/components/charts/PieChart.vue'

const { t } = useI18n()

const mainDashboardStore = useMainDashboardStore()

const from = ref<string | null>(null)
const to = ref<string | null>(null)

watch(from, () => {
  loadDashboardData()
})
watch(to, () => {
  loadDashboardData()
})

onMounted(() => {
  from.value = DateTime.now().startOf('month').toFormat('yyyy/LL/dd')
  to.value = DateTime.now().endOf('month').toFormat('yyyy/LL/dd')

  loadDashboardData()
})

const loadDashboardData = () => {
  mainDashboardStore.loadMainDashboard({
    from: from.value ? DateTime.fromFormat(from.value, 'yyyy/LL/dd') : undefined,
    to: to.value ? DateTime.fromFormat(to.value, 'yyyy/LL/dd') : undefined,
  })
}

const salesData = computed(() => mainDashboardStore.sales)
const typeData = computed(() => mainDashboardStore.type)
const genderData = computed(() => mainDashboardStore.gender)
const brandData = computed(() => mainDashboardStore.brand)
const countryData = computed(() => mainDashboardStore.country)

const chips = [
  {
    text: t('dashboards.main.fastFilters.allTime'),
    callback: () => {
      from.value = null
      to.value = null
    },
  },
  {
    text: t('dashboards.main.fastFilters.currentMonth'),
    callback: () => {
      from.value = DateTime.now().startOf('month').toFormat('yyyy/LL/dd')
      to.value = DateTime.now().endOf('month').toFormat('yyyy/LL/dd')
    },
  },
  {
    text: t('dashboards.main.fastFilters.previousMonth'),
    callback: () => {
      from.value = DateTime.now().minus({ month: 1 }).startOf('month').toFormat('yyyy/LL/dd')
      to.value = DateTime.now().minus({ month: 1 }).endOf('month').toFormat('yyyy/LL/dd')
    },
  },
  {
    text: t('dashboards.main.fastFilters.currentYear'),
    callback: () => {
      from.value = DateTime.now().startOf('year').toFormat('yyyy/LL/dd')
      to.value = DateTime.now().endOf('year').toFormat('yyyy/LL/dd')
    },
  },
  {
    text: t('dashboards.main.fastFilters.previousYear'),
    callback: () => {
      from.value = DateTime.now().minus({ year: 1 }).startOf('year').toFormat('yyyy/LL/dd')
      to.value = DateTime.now().minus({ year: 1 }).endOf('year').toFormat('yyyy/LL/dd')
    },
  },
]
</script>

<template>
  <div class="tw-px-10px tw-mt-10px">
    <div
      class="tw-w-full tw-flex tw-flex-col lg:tw-flex-row tw-gap-6 md:tw-items-center tw-mb-4 lg:tw-justify-between"
    >
      <div class="tw-f-full tw-flex tw-flex-row tw-gap-4 lg:tw-gap-6">
        <date-picker-inside-input v-model="from" :label="t('dashboards.main.from.title')" />
        <date-picker-inside-input v-model="to" :label="t('dashboards.main.to.title')" />
      </div>
      <div class="tw-flex tw-flex-row tw-gap-2 tw-flex-wrap">
        <q-chip
          v-for="chip in chips"
          :key="chip.text"
          rounded
          clickable
          color="primary"
          text-color="white"
          class="tw-h-28px"
          @click="chip.callback"
        >
          {{ chip.text }}
        </q-chip>
      </div>
    </div>
    <div class="text-h4">{{ t('dashboards.main.salesReport.title') }}</div>
    <sales-report-chart :data="salesData" class="tw-w-full tw-h-200px" />
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-mt-50px">
      <div v-if="typeData.dataset.length > 0">
        <div class="text-h5">{{ t('dashboards.main.typeReport.title') }}</div>
        <pie-chart :data="typeData" class="tw-h-200px" />
      </div>
      <div v-if="genderData.dataset.length > 0">
        <div class="text-h5">{{ t('dashboards.main.genderReport.title') }}</div>
        <pie-chart :data="genderData" class="tw-h-200px" />
      </div>
      <div v-if="brandData.dataset.length > 0">
        <div class="text-h5">{{ t('dashboards.main.brandReport.title') }}</div>
        <pie-chart :data="brandData" class="tw-h-200px" />
      </div>
      <div v-if="countryData.dataset.length > 0">
        <div class="text-h5">{{ t('dashboards.main.countryReport.title') }}</div>
        <pie-chart :data="countryData" class="tw-h-200px" />
      </div>
    </div>
  </div>
</template>
