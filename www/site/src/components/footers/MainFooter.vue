<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { useFooterStore } from 'src/stores/footer-store'
import { computed } from 'vue'

const { t } = useI18n()

const footerStore = useFooterStore()

const links = [
  {
    title: t('footer.links.about'),
    to: 'index',
  },
  {
    title: t('footer.links.delivery'),
    to: 'index',
  },
  {
    title: t('footer.links.promotions'),
    to: 'index',
  },
  {
    title: t('footer.links.returnInfo'),
    to: 'index',
  },
]

const linksFromStore = computed(() => footerStore.links)
</script>

<template>
  <div class="tw-bg-primary tw-text-white tw-py-100px tw-flex tw-flex-col">
    <div class="tw-w-full tw-flex tw-flex-row tw-justify-between tw-gap-x-16px">
      <div class="tw-flex tw-flex-row tw-justify-between tw-grow">
        <div class="tw-flex tw-flex-col tw-gap-y-8px tw-grow">
          <div class="tw-font-semibold tw-text-22px">{{ t('footer.catalog') }}</div>
          <div class="tw-grid tw-grid-cols-3 tw-gap-8px tw-w-full">
            <router-link
              v-for="link in linksFromStore"
              :key="`catalogLink_${link.title}`"
              :to="{ path: link.url }"
              class="tw-text-white"
            >
              {{ link.title }}
            </router-link>
          </div>
        </div>
        <div class="tw-flex tw-flex-col tw-gap-y-8px tw-grow">
          <div class="tw-font-semibold tw-text-22px">{{ t('footer.forBuyer') }}</div>
          <div class="tw-grid tw-grid-rows-3 tw-grid-flow-col tw-gap-8px tw-w-full">
            <router-link
              v-for="link in links"
              :key="`buyerLink_${link.title}`"
              :to="{ to: link.to }"
              class="tw-text-white"
            >
              {{ link.title }}
            </router-link>
          </div>
        </div>
      </div>
      <div class="tw-flex tw-flex-col tw-flex-none tw-gap-y-8px">
        <div class="tw-font-semibold tw-text-22px">{{ t('footer.social') }}</div>
        <div class="tw-flex tw-flex-row tw-items-center tw-w-full tw-justify-center tw-gap-12px">
          <a href="https://t.me/+v9pX22EJaqBjOTQy" target="_blank">
            <i
              class="fa-brands fa-telegram tw-text-50px tw-text-white hover:tw-text-black tw-transition-colors"
            />
          </a>
          <a
            href="https://invite.viber.com/?g2=AQA60LEq%2F%2FIJhVDDtvKEZIcTbtS2J8OJEcahxTCTwrGJZB5Tb4PkrPfrjzeX1rhY"
            target="_blank"
          >
            <i
              class="fa-brands fa-viber tw-text-50px tw-text-white hover:tw-text-black tw-transition-colors"
            />
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
