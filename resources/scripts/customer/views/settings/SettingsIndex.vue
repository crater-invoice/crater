<template>
  <BasePage>
    <BasePageHeader :title="$tc('settings.setting', 2)" class="pb-6">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem
          :title="$t('general.home')"
          :to="`/${companySlug}/customer/dashboard`"
        />
        <BaseBreadcrumbItem
          :title="$tc('settings.setting', 2)"
          :to="`/${companySlug}/customer/settings/customer-profile`"
          active
        />
      </BaseBreadcrumb>
    </BasePageHeader>

    <div class="w-full mb-6 select-wrapper xl:hidden">
      <aside class="pb-3 lg:col-span-3">
        <nav class="space-y-1">
          <BaseList>
            <BaseListItem
              v-for="(menuItem, index) in menuItems"
              :key="index"
              :title="menuItem.title"
              :to="menuItem.link"
              :active="hasActiveUrl(menuItem.link)"
              :index="index"
              class="py-3"
            >
              <template #icon>
                <component :is="menuItem.icon" class="h-5 w-6" />
              </template>
            </BaseListItem>
          </BaseList>
        </nav>
      </aside>
    </div>

    <div class="flex">
      <div class="hidden mt-1 xl:block min-w-[240px]">
        <BaseList>
          <BaseListItem
            v-for="(menuItem, index) in menuItems"
            :key="index"
            :title="menuItem.title"
            :to="menuItem.link"
            :active="hasActiveUrl(menuItem.link)"
            :index="index"
            class="py-3"
          >
            <template #icon>
              <component :is="menuItem.icon" class="h-5 w-6" />
            </template>
          </BaseListItem>
        </BaseList>
      </div>
      <div class="w-full overflow-hidden">
        <RouterView />
      </div>
    </div>
  </BasePage>
</template>

<script setup>
import { ref, reactive, watchEffect, computed } from 'vue'
import BaseList from '@/scripts/components/list/BaseList.vue'
import BaseListItem from '@/scripts/components/list/BaseListItem.vue'
import { OfficeBuildingIcon, UserIcon } from '@heroicons/vue/outline'
import { useGlobalStore } from '@/scripts/customer/stores/global'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

//route
const { useRoute, useRouter } = window.VueRouter

const route = useRoute()
const router = useRouter()
const globalStore = useGlobalStore()

const companySlug = computed(() => {
  return globalStore.companySlug
})

// local state
const { global } = window.i18n
let currentSetting = ref({})
let activeIndex = ref()
const menuItems = reactive([
  {
    link: `/${globalStore.companySlug}/customer/settings/customer-profile`,
    title: t('settings.account_settings.account_settings'),
    icon: UserIcon,
  },
  {
    link: `/${globalStore.companySlug}/customer/settings/address-info`,
    title: t('settings.menu_title.address_information'),
    icon: OfficeBuildingIcon,
  },
])

// watch

watchEffect(() => {
  if (route.path === `/${globalStore.companySlug}/customer/settings`) {
    router.push({ name: 'customer.profile' })
  }

  const menu = menuItems.find((item) => {
    return item.link === route.path
  })

  currentSetting.value = { ...menu }
})

// computed props

const dropdownMenuItems = computed(() => {
  return menuItems
})

// methods

function hasActiveUrl(url) {
  return route.path.indexOf(url) > -1
}

function navigateToSetting(setting) {
  return router.push(setting.link)
}
</script>
