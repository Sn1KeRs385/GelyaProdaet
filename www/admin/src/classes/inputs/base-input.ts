import { Component } from 'vue'

export abstract class BaseInput {
  public readonly component: Component
  protected constructor(component: Component) {
    this.component = component
  }

  abstract getParams(): unknown
}
