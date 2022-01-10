<template>
  <BasePage>
    <!-- Page Header Section -->
    <BasePageHeader :title="$t('users.title')">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />
        <BaseBreadcrumbItem :title="$tc('users.title', 2)" to="#" active />
      </BaseBreadcrumb>

      <template #actions>
        <div class="flex items-center justify-end space-x-5">
          <BaseButton
            v-show="usersStore.totalUsers"
            variant="primary-outline"
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
            <template #right="slotProps">
              <BaseIcon
                v-if="!showFilters"
                name="FilterIcon"
                :class="slotProps.class"
              />
              <BaseIcon v-else name="XIcon" :class="slotProps.class" />
            </template>
          </BaseButton>

          <BaseButton
            v-if="userStore.currentUser.is_owner"
            @click="$router.push('users/create')"
          >
            <template #left="slotProps">
              <BaseIcon
                name="PlusIcon"
                :class="slotProps.class"
                aria-hidden="true"
              />
            </template>
            {{ $t('users.add_user') }}
          </BaseButton>
        </div>
      </template>
    </BasePageHeader>

    <BaseFilterWrapper :show="showFilters" class="mt-3" @clear="clearFilter">
      <BaseInputGroup :label="$tc('users.name')" class="flex-1 mt-2 mr-4">
        <BaseInput
          v-model="filters.name"
          type="text"
          name="name"
          autocomplete="off"
        />
      </BaseInputGroup>

      <BaseInputGroup :label="$tc('users.email')" class="flex-1 mt-2 mr-4">
        <BaseInput
          v-model="filters.email"
          type="text"
          name="email"
          autocomplete="off"
        />
      </BaseInputGroup>

      <BaseInputGroup class="flex-1 mt-2" :label="$tc('users.phone')">
        <BaseInput
          v-model="filters.phone"
          type="text"
          name="phone"
          autocomplete="off"
        />
      </BaseInputGroup>
    </BaseFilterWrapper>

    <!-- User Empty Placeholder -->

    <BaseEmptyPlaceholder
      v-show="showEmptyScreen"
      :title="$t('users.no_users')"
      :description="$t('users.list_of_users')"
    >
      <AstronautIcon class="mt-5 mb-4" />

      <template #actions>
        <BaseButton
          v-if="userStore.currentUser.is_owner"
          variant="primary-outline"
          @click="$router.push('/admin/users/create')"
        >
          <template #left="slotProps">
            <BaseIcon name="PlusIcon" :class="slotProps.class" />
          </template>
          {{ $t('users.add_user') }}
        </BaseButton>
      </template>
    </BaseEmptyPlaceholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="
          relative
          flex
          items-center
          justify-end
          h-5
          border-gray-200 border-solid
        "
      >
        <BaseDropdown v-if="usersStore.selectedUsers.length">
          <template #activator>
            <span
              class="
                flex
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <BaseIcon name="ChevronDownIcon" class="h-5" />
            </span>
          </template>
          <BaseDropdownItem @click="removeMultipleUsers">
            <BaseIcon name="TrashIcon" class="h-5 mr-3 text-gray-600" />
            {{ $t('general.delete') }}
          </BaseDropdownItem>
        </BaseDropdown>
      </div>

      <BaseTable
        ref="table"
        :data="fetchData"
        :columns="userTableColumns"
        class="mt-3"
      >
        <!-- Select All Checkbox -->
        <template #header>
          <div class="absolute z-10 items-center left-6 top-2.5 select-none">
            <BaseCheckbox
              v-model="selectAllFieldStatus"
              variant="primary"
              @change="usersStore.selectAllUsers"
            />
          </div>
        </template>
        <template #cell-status="{ row }">
          <div class="custom-control custom-checkbox">
            <BaseCheckbox
              :id="row.data.id"
              v-model="selectField"
              :value="row.data.id"
              variant="primary"
            />
          </div>
        </template>

        <template #cell-name="{ row }">
          <router-link
            :to="{ path: `users/${row.data.id}/edit` }"
            class="font-medium text-primary-500"
          >
            {{ row.data.name }}
          </router-link>
        </template>

        <template #cell-phone="{ row }">
          <span>{{ row.data.phone ? row.data.phone : '-' }} </span>
        </template>

        <template #cell-created_at="{ row }">
          <span>{{ row.data.formatted_created_at }}</span>
        </template>

        <template v-if="userStore.currentUser.is_owner" #cell-actions="{ row }">
          <UserDropdown
            :row="row.data"
            :table="table"
            :load-data="refreshTable"
          />
        </template>
      </BaseTable>
    </div>
  </BasePage>
</template>

<script setup>
import { computed, onUnmounted, ref, reactive, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useUsersStore } from '@/scripts/admin/stores/users'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useUserStore } from '@/scripts/admin/stores/user'
import AstronautIcon from '@/scripts/components/icons/empty/AstronautIcon.vue'
import UserDropdown from '@/scripts/admin/components/dropdowns/UserIndexDropdown.vue'
import abilities from '@/scripts/admin/stub/abilities'

const notificationStore = useNotificationStore()
const dialogStore = useDialogStore()
const usersStore = useUsersStore()
const userStore = useUserStore()

const router = useRouter()

let showFilters = ref(false)
let isFetchingInitialData = ref(true)
let id = ref(null)
let sortedBy = ref('created_at')
let isLoading = ref(false)
const { t } = useI18n()
let table = ref(null)

let filters = reactive({
  name: '',
  email: '',
  phone: '',
})

const userTableColumns = computed(() => {
  return [
    {
      key: 'status',
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
      sortable: false,
    },
    {
      key: 'name',
      label: t('users.name'),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    { key: 'email', label: 'Email' },
    {
      key: 'phone',
      label: t('users.phone'),
    },
    {
      key: 'created_at',
      label: t('users.added_on'),
    },
    {
      key: 'actions',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

const showEmptyScreen = computed(() => {
  return !usersStore.totalUsers && !isFetchingInitialData.value
})

const selectField = computed({
  get: () => usersStore.selectedUsers,
  set: (value) => {
    return usersStore.selectUser(value)
  },
})

const selectAllFieldStatus = computed({
  get: () => usersStore.selectAllField,
  set: (value) => {
    return usersStore.setSelectAllState(value)
  },
})

watch(
  filters,
  () => {
    setFilters()
  },
  { deep: true }
)

onMounted(() => {
  usersStore.fetchUsers()
  usersStore.fetchRoles()
})

onUnmounted(() => {
  if (usersStore.selectAllField) {
    usersStore.selectAllUsers()
  }
})

function selectAllUser(params) {
  usersStore.selectAllUsers()
}

function setFilters() {
  refreshTable()
}

function refreshTable() {
  table.value && table.value.refresh()
}

async function fetchData({ page, filter, sort }) {
  let data = {
    display_name: filters.name !== null ? filters.name : '',
    phone: filters.phone !== null ? filters.phone : '',
    email: filters.email !== null ? filters.email : '',
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  isFetchingInitialData.value = true

  let response = await usersStore.fetchUsers(data)

  isFetchingInitialData.value = false

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: 10,
    },
  }
}

function clearFilter() {
  filters.name = ''
  filters.email = ''
  filters.phone = null
}

function toggleFilter() {
  if (showFilters.value) {
    clearFilter()
  }

  showFilters.value = !showFilters.value
}

function removeUser(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('users.confirm_delete', 1),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then((res) => {
      if (res) {
        let user = [id]
        usersStore.deleteUser(user).then((response) => {
          if (response.data.success) {
            table.value && table.value.refresh()
            return true
          }

          if (response.data.error === 'user_attached') {
            notificationStore.showNotification({
              type: 'error',
              message: t('users.user_attached_message'),
            })
            return true
          }
        })
      }
    })
}

function removeMultipleUsers() {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('users.confirm_delete', 2),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then((res) => {
      if (res) {
        usersStore.deleteMultipleUsers().then((res) => {
          if (res.data.success) {
            table.value && table.value.refresh()
          }
        })
      }
    })
}
</script>
