<script setup lang="ts">
import MainHeader from 'src/components/headers/MainHeader.vue'
import MainFooter from 'src/components/footers/MainFooter.vue'
</script>

<template>
  <q-layout view="hHh LpR fFf" class="tw-bg-[#FBFAFC]">
    <main-header />

    <q-page-container class="tw-pt-70px lg:tw-pt-120px tw-px-20px lg:tw-px-36px">
      <router-view />
    </q-page-container>
    <main-footer class="tw-mt-18px lg:tw-mt-40px tw-px-20px lg:tw-px-36px" />
  </q-layout>
</template>
