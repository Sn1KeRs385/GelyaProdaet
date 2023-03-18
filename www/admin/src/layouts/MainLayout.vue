<script setup lang="ts">
import { ref } from 'vue'
import { useUserStore } from 'src/stores/user-store'
import MainMenu from 'src/components/MainMenu.vue'

const userStore = useUserStore()
const { logout } = userStore

const leftDrawerOpen = ref(true)

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}
</script>

<template>
  <q-layout view="hHh LpR fFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="toggleLeftDrawer" />

        <q-toolbar-title>Геля продаёт</q-toolbar-title>

        <q-icon name="logout" size="sm" class="cursor-pointer" @click="logout"></q-icon>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" side="left" elevated>
      <main-menu />
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>
