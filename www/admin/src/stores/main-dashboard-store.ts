import { defineStore } from 'pinia'
import { api } from 'src/boot/axios'
import { DateTime } from 'luxon'
import { AxiosResponse } from 'axios'
import { SalesReportChart } from 'src/interfaces/charts/sales-report-chart'
import { ChartItemInterface } from 'src/interfaces/chart-item-interface'

export interface MainDashboardStore {
  sales: SalesReportChart
  type: ChartItemInterface
  gender: ChartItemInterface
  brand: ChartItemInterface
  country: ChartItemInterface
}

export interface loadMainDashboardParams {
  from?: DateTime
  to?: DateTime
}

export const useMainDashboardStore = defineStore('main-dashboard', {
  state: (): MainDashboardStore => ({
    sales: {
      labels: [],
      sold: {
        dataset: [],
        total: 0,
      },
      earn: {
        dataset: [],
        total: 0,
      },
      group: 'day',
    },
    type: {
      labels: [],
      dataset: [],
      backgroundColor: [],
      total: 0,
    },
    gender: {
      labels: [],
      dataset: [],
      backgroundColor: [],
      total: 0,
    },
    brand: {
      labels: [],
      dataset: [],
      backgroundColor: [],
      total: 0,
    },
    country: {
      labels: [],
      dataset: [],
      backgroundColor: [],
      total: 0,
    },
  }),
  getters: {},
  actions: {
    loadMainDashboard({ from, to }: loadMainDashboardParams = {}): void {
      api
        .get('admin/v1/dashboard/main', {
          params: {
            from: from?.startOf('day').toUnixInteger(),
            to: to?.endOf('day').toUnixInteger(),
          },
        })
        .then((response: AxiosResponse<MainDashboardStore>) => {
          this.$patch({
            sales: response.data.sales,
            type: response.data.type,
            gender: response.data.gender,
            brand: response.data.brand,
            country: response.data.country,
          })
        })
    },
  },
})
