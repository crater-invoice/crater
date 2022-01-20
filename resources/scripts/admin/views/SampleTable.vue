<template>
  <BasePage>
    <h1 class="mb-2">Sample Table Local</h1>

    <BaseTable :data="data" :columns="columns">
      <template #cell-status="{ row }">
        <span
          v-if="row.data.status === 'Active'"
          class="
            inline-flex
            px-2
            text-xs
            font-semibold
            leading-5
            text-green-800
            bg-green-100
            rounded-full
          "
        >
          {{ row.data.status }}
        </span>

        <span
          v-else
          class="
            inline-flex
            px-2
            text-xs
            font-semibold
            leading-5
            text-red-800
            bg-red-100
            rounded-full
          "
        >
          {{ row.data.status }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <base-dropdown width-class="w-48" margin-class="mt-1">
          <template #activator>
            <div class="flex items-center justify-center">
              <DotsHorizontalIcon class="w-6 h-6 text-gray-600" />
            </div>
          </template>

          <base-dropdown-item>
            <document-text-icon
              class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
              aria-hidden="true"
            />
            New Invoice
          </base-dropdown-item>

          <base-dropdown-item>
            <document-icon
              class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
              aria-hidden="true"
            />
            New Estimate
          </base-dropdown-item>

          <base-dropdown-item>
            <user-icon
              class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
              aria-hidden="true"
            />
            New Customer
          </base-dropdown-item>
        </base-dropdown>
      </template>
    </BaseTable>

    <h1 class="mt-8 mb-2">Sample Table Remote</h1>

    <BaseTable :data="fetchData" :columns="columns2"> </BaseTable>
  </BasePage>
</template>

<script>
import { computed, reactive } from 'vue'
import { useItemStore } from '@/scripts/admin/stores/item'
import {
  UserIcon,
  DocumentIcon,
  DocumentTextIcon,
  DotsHorizontalIcon,
} from '@heroicons/vue/solid'

export default {
  components: {
    BaseTable,
    DotsHorizontalIcon,
    UserIcon,
    DocumentIcon,
    DocumentTextIcon,
  },

  setup() {
    const itemStore = useItemStore()
    const data = reactive([
      { name: 'Tom', age: 3, image: 'tom.jpg', status: 'Active' },
      { name: 'Felix', age: 5, image: 'felix.jpg', status: 'Disabled' },
      { name: 'Sylvester', age: 7, image: 'sylvester.jpg', status: 'Active' },
    ])

    const columns = computed(() => {
      return [
        {
          key: 'name',
          label: 'Name',
          thClass: 'extra',
          tdClass: 'font-medium text-gray-900',
        },
        { key: 'age', label: 'Age' },
        { key: 'image', label: 'Image' },
        { key: 'status', label: 'Status' },
        {
          key: 'actions',
          label: '',
          tdClass: 'text-right text-sm font-medium',
          sortable: false,
        },
      ]
    })

    const columns2 = computed(() => {
      return [
        {
          key: 'name',
          label: 'Name',
          thClass: 'extra',
          tdClass: 'font-medium text-gray-900',
        },
        { key: 'price', label: 'Price' },
        { key: 'created_at', label: 'Created At' },
        {
          key: 'actions',
          label: '',
          tdClass: 'text-right text-sm font-medium',
          sortable: false,
        },
      ]
    })

    async function fetchData({ page, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await itemStore.fetchItems(data)

      return {
        data: response.data.items.data,
        pagination: {
          totalPages: response.data.items.last_page,
          currentPage: page,
          totalCount: response.data.itemTotalCount,
          limit: 10,
        },
      }
    }

    return {
      data,
      columns,
      fetchData,
      columns2,
    }
  },
}
</script>
