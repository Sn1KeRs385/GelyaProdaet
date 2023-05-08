<script setup lang="ts">
import { Pie } from 'vue-chartjs'
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import { ChartItemInterface } from 'src/interfaces/chart-item-interface'

interface Props {
  data: ChartItemInterface
}

const { t } = useI18n()

const props = defineProps<Props>()

const labels = computed(() => props.data.labels || [])

const isShowChart = computed(() => labels.value.length > 0)

const chartData = computed(() => {
  return {
    labels: labels.value,
    datasets: [
      {
        label: t('charts.pieChart.sold.title'),
        backgroundColor: props.data.backgroundColor,
        data: props.data.dataset,
        totalText: props.data.total,
        // totalText: t('charts.salesReportChart.sold.withTotal', { total: props.data.sold.total }),
      },
    ],
  }
})

const options = computed(() => {
  return {
    responsive: true,
    // maintainAspectRatio: false,
  }
})
</script>

<template>
  <div class="tw-flex-col">
    <Pie v-if="isShowChart" :data="chartData" :options="options" />
    <div class="tw-flex tw-flex-row tw-items-center tw-w-full tw-justify-between">
      <div>
        <b>{{ t('charts.pieChart.total.title') }}</b>
        {{ data.total }}
      </div>
    </div>
  </div>
</template>
