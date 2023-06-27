<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { useHeaderStore } from 'src/stores/header-store'
import { computed } from 'vue'

const { t } = useI18n()

interface RouterLinkInterface {
  title: string
  to: {
    name?: string
    path?: string
  }
}

const headerStore = useHeaderStore()

const links = [
  {
    title: t('header.links.home'),
    to: 'index',
  },
  {
    title: t('header.links.catalog'),
    to: 'index',
  },
]

const linksComputed = computed(() => {
  const linksTemp: RouterLinkInterface[] = []

  links.forEach((link) => {
    linksTemp.push({
      title: link.title,
      to: {
        name: link.to,
      },
    })
  })

  headerStore.links.forEach((link) => {
    linksTemp.push({
      title: link.title,
      to: {
        path: link.url,
      },
    })
  })

  return linksTemp
})
</script>

<template>
  <q-header reveal class="main-header" :reveal-offset="10">
    <q-toolbar
      class="tw-h-full tw-grid tw-grid-cols-[24px_calc(100%-48px)_24px] lg:tw-grid-cols-[15%_70%_15%]"
    >
      <div class="tw-block lg:tw-hidden"></div>
      <div class="row tw-justify-center lg:tw-justify-start tw-items-center">
        <img src="src/assets/logo-blue.svg" alt="logo" class="main-header__logo" />
      </div>
      <div class="row items-center justify-center tw-gap-40px tw-hidden lg:tw-flex">
        <router-link
          v-for="link in linksComputed"
          :key="`routeTo_${link.title}`"
          :to="link.to"
          class="main-header__link"
        >
          {{ link.title }}
        </router-link>
      </div>
      <q-icon name="menu" color="primary" class="lg:tw-hidden" size="24px"></q-icon>
    </q-toolbar>
  </q-header>
</template>

<style lang="scss">
.q-header {
  &--hidden {
    @media (min-width: 1024px) {
      transform: translateY(-135%);
    }
  }
}
</style>
<style scoped lang="scss">
.main-header {
  box-shadow: 0 4px 8px rgb(0 0 0 / 20%);
  @apply tw-bg-light tw-rounded-4px tw-text-dark;
  @apply tw-my-5px tw-mx-8px lg:tw-m-24px;
  @apply tw-px-10px lg:tw-px-30px;
  @apply tw-h-48px lg:tw-h-74px;
  &__logo {
    @apply tw-h-48px tw-text-primary;
  }
  &__link {
    @apply tw-font-semibold tw-text-16px tw-leading-100 tw-text-black tw-no-underline;
  }
}
</style>
