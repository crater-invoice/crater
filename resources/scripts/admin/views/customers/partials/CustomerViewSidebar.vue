<template>
  <div
    class="
      fixed
      top-0
      left-0
      hidden
      h-full
      pt-16
      pb-4
      ml-56
      bg-white
      xl:ml-64
      w-88
      xl:block
    "
  >
    <div
      class="
        flex
        items-center
        justify-between
        px-4
        pt-8
        pb-2
        border border-gray-200 border-solid
        height-full
      "
    >
      <BaseInput
        v-model="searchData.searchText"
        :placeholder="$t('general.search')"
        container-class="mb-6"
        type="text"
        variant="gray"
        @input="onSearch()"
      >
        <BaseIcon name="SearchIcon" class="text-gray-500" />
      </BaseInput>

      <div class="flex mb-6 ml-3" role="group" aria-label="First group">
        <BaseDropdown
          :close-on-select="false"
          position="bottom-start"
          width-class="w-40"
          position-class="left-0"
        >
          <template #activator>
            <BaseButton variant="gray">
              <BaseIcon name="FilterIcon" />
            </BaseButton>
          </template>

          <div
            class="
              px-4
              py-3
              pb-2
              mb-2
              text-sm
              border-b border-gray-200 border-solid
            "
          >
            {{ $t('general.sort_by') }}
          </div>

          <div class="px-2">
            <BaseDropdownItem
              class="flex px-1 py-2 mt-1 cursor-pointer hover:rounded-md"
            >
              <BaseInputGroup class="pt-2 -mt-4">
                <BaseRadio
                  id="filter_create_date"
                  v-model="searchData.orderByField"
                  :label="$t('customers.create_date')"
                  size="sm"
                  name="filter"
                  value="invoices.created_at"
                  @update:modelValue="onSearch"
                />
              </BaseInputGroup>
            </BaseDropdownItem>
          </div>

          <div class="px-2">
            <BaseDropdownItem class="flex px-1 cursor-pointer hover:rounded-md">
              <BaseInputGroup class="pt-2 -mt-4">
                <BaseRadio
                  id="filter_display_name"
                  v-model="searchData.orderByField"
                  :label="$t('customers.display_name')"
                  size="sm"
                  name="filter"
                  value="name"
                  @update:modelValue="onSearch"
                />
              </BaseInputGroup>
            </BaseDropdownItem>
          </div>
        </BaseDropdown>

        <BaseButton class="ml-1" size="md" variant="gray" @click="sortData">
          <BaseIcon v-if="getOrderBy" name="SortAscendingIcon" />
          <BaseIcon v-else name="SortDescendingIcon" />
        </BaseButton>
      </div>
    </div>

    <div
      class="
        h-full
        pb-32
        overflow-y-scroll
        border-l border-gray-200 border-solid
        sidebar
        base-scroll
      "
    >
      <div v-for="(customer, index) in customerStore.customers" :key="index">
        <router-link
          v-if="customer && !isFetching"
          :id="'customer-' + customer.id"
          :to="`/admin/customers/${customer.id}/view`"
          :class="[
            'flex justify-between p-4 items-center cursor-pointer hover:bg-gray-100 border-l-4 border-transparent',
            {
              'bg-gray-100 border-l-4 border-primary-500 border-solid':
                hasActiveUrl(customer.id),
            },
          ]"
          style="border-top: 1px solid rgba(185, 193, 209, 0.41)"
        >
          <div>
            <BaseText
              :text="customer.name"
              :length="30"
              class="
                pr-2
                text-sm
                not-italic
                font-normal
                leading-5
                text-black
                capitalize
                truncate
              "
            />
            
            <BaseText
              v-if="customer.contact_name"
              :text="customer.contact_name"
              :length="30"
              class="
                mt-1
                text-xs
                not-italic
                font-medium
                leading-5
                text-gray-600
              "
            />
          </div>
          <div class="flex-1 font-bold text-right whitespace-nowrap">
            <BaseFormatMoney
              :amount="customer.due_amount"
              :currency="customer.currency"
            />
          </div>
        </router-link>
      </div>
      <div class="flex justify-center p-4 items-center">
        <LoadingIcon
          v-if="isFetching"
          class="h-6 m-1 animate-spin text-primary-400"
        />
      </div>
      <p
        v-if="!customerStore.customers.length && !isFetching"
        class="flex justify-center px-4 mt-5 text-sm text-gray-600"
      >
        {{ $t('customers.no_matching_customers') }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive, watch, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import { useCustomerStore } from '@/scripts/admin/stores/customer'
import LoadingIcon from '@/scripts/components/icons/LoadingIcon.vue'
import { debounce } from 'lodash'

const customerStore = useCustomerStore()
const title = 'Customer View'
const route = useRoute()
const { t } = useI18n()

let isSearching = ref(false)

let isFetching = ref(false)

let searchData = reactive({
  orderBy: '',
  orderByField: '',
  searchText: '',
})

onSearch = debounce(onSearch, 500)

const getOrderBy = computed(() => {
  if (searchData.orderBy === 'asc' || searchData.orderBy == null) {
    return true
  }
  return false
})

const getOrderName = computed(() =>
  getOrderBy.value ? t('general.ascending') : t('general.descending')
)

function hasActiveUrl(id) {
  return route.params.id == id
}

async function loadCustomers() {
  isFetching.value = true

  await customerStore.fetchCustomers({ limit: 'all' })

  isFetching.value = false

  setTimeout(() => {
    scrollToCustomer()
  }, 500)
}

function scrollToCustomer() {
  const el = document.getElementById(`customer-${route.params.id}`)

  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
    el.classList.add('shake')
  }
}

async function onSearch() {
  let data = {}
  if (
    searchData.searchText !== '' &&
    searchData.searchText !== null &&
    searchData.searchText !== undefined
  ) {
    data.display_name = searchData.searchText
  }

  if (searchData.orderBy !== null && searchData.orderBy !== undefined) {
    data.orderBy = searchData.orderBy
  }

  if (
    searchData.orderByField !== null &&
    searchData.orderByField !== undefined
  ) {
    data.orderByField = searchData.orderByField
  }

  isSearching.value = true

  try {
    let response = await customerStore.fetchCustomers(data)
    isSearching.value = false
    if (response.data) {
      customerStore.customers = response.data.data
    }
  } catch (error) {
    isSearching.value = false
  }
}

function sortData() {
  if (searchData.orderBy === 'asc') {
    searchData.orderBy = 'desc'
    onSearch()
    return true
  }
  searchData.orderBy = 'asc'
  onSearch()
  return true
}

loadCustomers()
</script>
