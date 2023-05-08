import { ChartItemInterface } from 'src/interfaces/chart-item-interface'

export interface SalesReportChart {
  labels: (number | string)[]
  sold: ChartItemInterface
  earn: ChartItemInterface
  group: 'day' | 'month' | 'quarter' | 'year'
}
