<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import models from 'src/models/'
import { computed } from 'vue'
import BaseModel from 'src/models/base-model'

interface Link {
  title: string
  href: string
  icon?: string
  caption?: string
  children?: Link[]
}
interface ModelToLink {
  model: BaseModel<unknown>
  icon?: string
  caption?: string
  children?: ModelToLink[]
}

const { t } = useI18n()

const linksStatic: Link[] = [
  {
    title: 'Dashboard',
    href: '/',
    icon: 'dashboard',
    caption: '123',
  },
]

const modelsToLink: ModelToLink[] = [
  {
    icon: 'checkroom',
    model: models.Product,
  },
]

const links = computed(() => {
  const processModelToLink = (modelToLink: ModelToLink): Link => {
    const link: Link = {
      title: t(modelToLink.model.getTitle()),
      href: modelToLink.model.getUrl(),
      icon: modelToLink.icon,
      caption: modelToLink.caption,
    }

    const children: Link[] = []
    modelToLink.children?.forEach((childModelToLink) => {
      children.push(processModelToLink(childModelToLink))
    })

    if (children.length > 0) {
      link.children = children
    }

    return link
  }
  const linksComputed: Link[] = [...linksStatic]
  modelsToLink.forEach((modelToLink) => {
    linksComputed.push(processModelToLink(modelToLink))
  })
  return linksComputed
})
</script>

<template>
  <q-list>
    <q-item-label header>Меню админки</q-item-label>

    <q-item
      v-for="link in links"
      :key="link.href"
      clickable
      :to="link.href"
      exact
      class="tw-text-slate-800"
    >
      <q-item-section v-if="link.icon" avatar>
        <q-icon :name="link.icon" />
      </q-item-section>

      <q-item-section>
        <q-item-label>{{ link.title }}</q-item-label>
        <q-item-label v-if="link.caption" caption>{{ link.caption }}</q-item-label>
      </q-item-section>
    </q-item>
  </q-list>
</template>
