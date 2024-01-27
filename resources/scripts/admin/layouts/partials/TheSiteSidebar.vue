<template>
  <!-- MOBILE MENU -->
  <TransitionRoot as="template" :show="globalStore.isSidebarOpen">
    <Dialog
      as="div"
      class="fixed inset-0 z-40 flex md:hidden"
      @close="globalStore.setSidebarVisibility(false)"
    >
      <TransitionChild
        as="template"
        enter="transition-opacity ease-linear duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="transition-opacity ease-linear duration-300"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <DialogOverlay
          class="fixed inset-0 bg-gray-600 bg-opacity-75 dark:bg-gray-900/90"
        />
      </TransitionChild>

      <TransitionChild
        as="template"
        enter="transition ease-in-out duration-300"
        enter-from="-translate-x-full"
        enter-to="translate-x-0"
        leave="transition ease-in-out duration-300"
        leave-from="translate-x-0"
        leave-to="-translate-x-full"
      >
        <div
          class="relative flex flex-col flex-1 w-full max-w-xs bg-white dark:bg-gray-800"
        >
          <TransitionChild
            as="template"
            enter="ease-in-out duration-300"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="ease-in-out duration-300"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="absolute top-0 right-0 pt-2 -mr-12">
              <button
                class="
                flex
                items-center
                justify-center
                w-10
                h-10
                ml-1
                rounded-full
                focus:outline-none
                focus:ring-2
                focus:ring-inset
                focus:ring-white"
                @click="globalStore.setSidebarVisibility(false)"
              >
                <span class="sr-only">Close sidebar</span>
                <BaseIcon
                  name="XIcon"
                  class="w-6 h-6 text-white"
                  aria-hidden="true"
                />
              </button>
            </div>
          </TransitionChild>
          <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center shrink-0 px-4 mb-10">
              <img class="h-auto max-w-full w-36 hidden dark:block" :src="getDarkLogo"/>
              <img class="h-auto max-w-full w-36 block dark:hidden" :src="getLightLogo"/>
            </div>

            <nav
              v-for="menu in globalStore.menuGroups"
              :key="menu"
              class="mt-5 space-y-1"
            >
              <router-link
                v-for="item in menu"
                :key="item.name"
                :to="item.link"
                :class="[
                  hasActiveUrl(item.link)
                    ? 'text-primary-500 border-primary-500 bg-gray-100 dark:shadow-glass dark:backdrop-blur-xl dark:hover:bg-gray-700  dark:bg-gray-700/50 dark:text-primary-400 dark:font-medium'
                    : 'text-black dark:text-gray-300',
                  'cursor-pointer px-0 pl-4 py-3 border-transparent flex items-center border-l-4 border-solid text-sm not-italic font-medium',
                ]"
                @click="globalStore.setSidebarVisibility(false)"
              >
                <BaseIcon
                  :name="item.icon"
                  :class="[
                    hasActiveUrl(item.link)
                      ? 'text-primary-500 dark:text-primary-400'
                      : 'text-gray-400',
                    'mr-4 shrink-0 h-5 w-5',
                  ]"
                  @click="globalStore.setSidebarVisibility(false)"
                />
                {{ $t(item.title) }}
              </router-link>
              <LightDarkSwitch
                :show-label="false"
                class="absolute right-6 top-6 !w-auto"
              />
            </nav>
          </div>
        </div>
      </TransitionChild>
      <div class="shrink-0 w-14">
        <!-- Force sidebar to shrink to fit close icon -->
      </div>
    </Dialog>
  </TransitionRoot>

  <!-- DESKTOP MENU -->
  <div
    class="
    hidden
    w-56
    h-screen
    bg-white
    border-r border-gray-200 border-solid
    xl:w-64
    md:fixed md:flex md:flex-col md:inset-y-0
    pt-16
    dark:border-gray-800
    dark:bg-gray-800/80"
  >
    <div
      v-for="menu in globalStore.menuGroups"
      :key="menu"
      class="p-0 m-0 mt-6 list-none"
    >
      <router-link
        v-for="item in menu"
        :key="item"
        :to="item.link"
        :class="[
          hasActiveUrl(item.link)
            ? 'text-primary-500 border-primary-500 bg-gray-100 dark:border-primary-400 dark:shadow-glass dark:backdrop-blur-xl dark:hover:bg-gray-700 dark:bg-gray-700/50 dark:text-primary-400 dark:font-medium'
            : 'text-black dark:hover:bg-transparent dark:hover:text-white dark:text-gray-300',
          'cursor-pointer px-0 pl-6 hover:bg-gray-50 py-3 group flex items-center border-l-4 border-solid border-transparent text-sm not-italic font-medium',
        ]"
      >
        <BaseIcon
          :name="item.icon"
          :class="[
            hasActiveUrl(item.link)
              ? 'text-primary-500 dark:text-primary-400'
              : 'text-gray-400 group-hover:text-black dark:text-gray-400 dark:group-hover:text-white',
            'mr-4 shrink-0 h-5 w-5 ',
          ]"
        />

        {{ $t(item.title) }}
      </router-link>
    </div>
    <LightDarkSwitch
      class="absolute bottom-0 py-4 border-t border-gray-200 dark:border-gray-700"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  Dialog,
  DialogOverlay,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'

import { useRoute } from 'vue-router'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import LightDarkSwitch from '@/scripts/components/LightDarkSwitcher.vue'

const route = useRoute()
const globalStore = useGlobalStore()

const getDarkLogo = computed(() => new URL('/img/logo-white.png', import.meta.url))
const getLightLogo = computed(() => new URL('/img/crater-logo.png', import.meta.url))

function hasActiveUrl(url) {
  return route.path.indexOf(url) > -1
}
</script>
