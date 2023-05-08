<script setup lang="ts">
import { Bar } from 'vue-chartjs'
import { SalesReportChart } from 'src/interfaces/charts/sales-report-chart'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import { DateTime } from 'luxon'

interface Props {
  data: SalesReportChart
}

const { t, locale } = useI18n()

const props = defineProps<Props>()

const isShowChart = computed(() => labels.value.length > 0)

const labels = computed(
  () => props.data?.labels?.map((label) => formatTimestamp(Number(label))) || []
)

const chartData = computed(() => {
  return {
    labels: labels.value,
    datasets: [
      {
        label: t('charts.salesReportChart.sold.title'),
        backgroundColor: '#0015FF',
        data: props.data.sold.dataset,
        totalText: props.data.sold.total,
        // totalText: t('charts.salesReportChart.sold.withTotal', { total: props.data.sold.total }),
      },
      {
        label: t('charts.salesReportChart.earn.title'),
        backgroundColor: '#2FFF00',
        data: props.data.earn.dataset,
        totalText: props.data.earn.total,
        // totalText: t('charts.salesReportChart.earn.withTotal', { total: props.data.earn.total }),
      },
    ],
  }
})

const options = computed(() => {
  return {
    responsive: true,
    maintainAspectRatio: false,
  }
})

const formatTimestamp = (timestamp: number) => {
  const datetime = DateTime.fromSeconds(timestamp)
  switch (props.data.group) {
    case 'day':
      return datetime.setLocale(locale.value).toFormat('d MMMM yyyy')
    case 'month':
      const string = datetime.setLocale(locale.value).toFormat('LLLL yyyy')
      return string.charAt(0).toUpperCase() + string.slice(1)
    case 'quarter':
      return t('texts.quarterWithNumberAndYear', {
        quarter: datetime.setLocale(locale.value).toFormat('q'),
        year: datetime.setLocale(locale.value).toFormat('yyyy'),
      })
    case 'year':
      return datetime.setLocale(locale.value).toFormat('yyyy')
    default:
  }
}
</script>

<template>
  <div class="tw-flex-col">
    <Bar v-if="isShowChart" :data="chartData" :options="options" />
    <div class="tw-flex tw-flex-row tw-items-center tw-w-full tw-justify-between">
      <div>
        <b>{{ t('charts.salesReportChart.total.title') }}</b>
      </div>
      <div class="tw-flex tw-flex-row tw-items-center tw-gap-4">
        <div
          v-for="dataset in chartData.datasets"
          :key="dataset.label"
          class="tw-flex tw-flex-row tw-items-center tw-gap-2"
        >
          <div class="tw-w-40px tw-h-10px" :style="{ backgroundColor: dataset.backgroundColor }" />
          <div>{{ dataset.totalText }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
