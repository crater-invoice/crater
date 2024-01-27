<template>
  <header
    class="
      fixed
      top-0
      left-0
      z-20
      flex
      items-center
      justify-between
      w-full
      px-4
      py-3
      md:h-16 md:px-8
      bg-gradient-to-r
      from-primary-500
      to-primary-400
      dark:from-gray-700/70 dark:to-gray-800/70
      bg-primary-500
      dark:bg-transparent
      dark:backdrop-blur-xl
      dark:shadow-glass
      dark:border
      dark:border-white/10
    "
  >
    <BaseDarkHighlight />

    <router-link
      to="/admin/dashboard"
      class="
        float-none
        text-lg
        not-italic
        font-black
        tracking-wider
        text-white
        brand-main
        md:float-left
        font-base
        hidden
        md:block
      "
    >
      <img v-if="adminLogo" :src="adminLogo" class="h-6" />
      <MainLogo v-else class="h-6" light-color="white" dark-color="white" />
    </router-link>

    <!-- toggle button-->
    <div
      :class="{ 'is-active': globalStore.isSidebarOpen }"
      class="
        flex
        float-left
        p-1
        overflow-visible
        text-sm
        ease-linear
        bg-white
        border-0
        rounded
        cursor-pointer
        md:hidden md:ml-0
        hover:bg-gray-100
        dark:bg-gray-800 dark:border-gray-500 dark:border
      "
      @click.prevent="onToggle"
    >
      <BaseIcon name="MenuIcon" class="!w-6 !h-6 text-gray-500" />
    </div>

    <ul class="flex float-right h-8 m-0 list-none md:h-9">
      <li
        v-if="hasCreateAbilities"
        class="relative hidden float-left m-0 md:block"
      >
        <BaseDropdown width-class="w-48" >
          <template #activator>
            <div
              class="
                flex
                items-center
                justify-center
                w-8
                h-8
                ml-2
                text-sm text-black
                bg-white
                rounded
                md:h-9 md:w-9
                dark:bg-gray-700 dark:border-gray-500 dark:border
              "
            >
              <BaseIcon name="PlusIcon" class="w-5 h-5 text-gray-600 dark:text-white" />
            </div>
          </template>

          <router-link to="/admin/invoices/create">
            <BaseDropdownItem
              v-if="userStore.hasAbilities(abilities.CREATE_INVOICE)"
              v-slot="slotProps"
            >
              <BaseIcon
                name="DocumentTextIcon"
                :class="slotProps.class"
                aria-hidden="true"
              />
              {{ $t('invoices.new_invoice') }}
            </BaseDropdownItem>
          </router-link>
          <router-link to="/admin/estimates/create">
            <BaseDropdownItem
              v-if="userStore.hasAbilities(abilities.CREATE_ESTIMATE)"
              v-slot="slotProps"
            >
              <BaseIcon
                name="DocumentIcon"
                :class="slotProps.class"
                aria-hidden="true"
              />
              {{ $t('estimates.new_estimate') }}
            </BaseDropdownItem>
          </router-link>

          <router-link to="/admin/customers/create">
            <BaseDropdownItem
              v-if="userStore.hasAbilities(abilities.CREATE_CUSTOMER)"
              v-slot="slotProps"
            >
              <BaseIcon
                name="UserIcon"
                :class="slotProps.class"
                aria-hidden="true"
              />
              {{ $t('customers.new_customer') }}
            </BaseDropdownItem>
          </router-link>
        </BaseDropdown>
      </li>

      <li class="ml-2">
        <GlobalSearchBar
          v-if="
            userStore.currentUser.is_owner ||
            userStore.hasAbilities(abilities.VIEW_CUSTOMER)
          "
        />
      </li>

      <li>
        <CompanySwitcher />
      </li>

      <!-- User Dropdown-->
      <li class="relative block float-left ml-2">
        <BaseDropdown width-class="w-48">
          <template #activator>
            <img
              :src="previewAvatar"
              class="block w-8 h-8 rounded md:h-9 md:w-9 object-cover"
            />
          </template>

          <router-link to="/admin/settings/account-settings">
            <BaseDropdownItem v-slot="slotProps">
              <BaseIcon
                name="CogIcon"
                :class="slotProps.class"
                aria-hidden="true"
              />
              {{ $t('navigation.settings') }}
            </BaseDropdownItem>
          </router-link>

          <BaseDropdownItem v-slot="slotProps" @click="logout">
            <BaseIcon
              name="LogoutIcon"
              :class="slotProps.class"
              aria-hidden="true"
            />
            {{ $t('navigation.logout') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </li>
    </ul>
  </header>
</template>

<script setup>
import { useAuthStore } from '@/scripts/admin/stores/auth'
import { useRouter } from 'vue-router'
import { computed } from 'vue'
import { useUserStore } from '@/scripts/admin/stores/user'
import { useGlobalStore } from '@/scripts/admin/stores/global'

import CompanySwitcher from '@/scripts/components/CompanySwitcher.vue'
import GlobalSearchBar from '@/scripts/components/GlobalSearchBar.vue'
import MainLogo from '@/scripts/components/icons/MainLogo.vue'

import abilities from '@/scripts/admin/stub/abilities'

const authStore = useAuthStore()
const userStore = useUserStore()
const globalStore = useGlobalStore()
const router = useRouter()

const previewAvatar = computed(() => {
  return userStore.currentUser && userStore.currentUser.avatar !== 0
    ? userStore.currentUser.avatar
    : getDefaultAvatar()
})

const adminLogo = computed(() => {
  if (globalStore.globalSettings.admin_portal_logo) {
    return '/storage/' + globalStore.globalSettings.admin_portal_logo
  }

  return false
})

function getDefaultAvatar() {
  const imgUrl = new URL('/img/default-avatar.jpg', import.meta.url)
  return imgUrl
}

function hasCreateAbilities() {
  return userStore.hasAbilities([
    abilities.CREATE_INVOICE,
    abilities.CREATE_ESTIMATE,
    abilities.CREATE_CUSTOMER,
  ])
}

async function logout() {
  await authStore.logout()
  router.push('/login')
}

function onToggle() {
  globalStore.setSidebarVisibility(true)
}
</script>
