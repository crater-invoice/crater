<template>
  <BaseDropdown>
    <template #activator>
      <BaseButton v-if="route.name === 'expenses.view'" variant="primary">
        <BaseIcon name="DotsHorizontalIcon" class="h-5 text-white" />
      </BaseButton>
      <BaseIcon v-else name="DotsHorizontalIcon" class="h-5 text-gray-500" />
    </template>

    <!-- edit expense  -->
    <router-link
      v-if="userStore.hasAbilities(abilities.EDIT_EXPENSE)"
      :to="`/admin/expenses/${row.id}/edit`"
    >
      <BaseDropdownItem v-slot="slotProps">
        <BaseIcon
          name="PencilIcon"
          :class="slotProps.class"
        />
        {{ $t('general.edit') }}
      </BaseDropdownItem>
    </router-link>

    <!-- delete expense  -->
    <BaseDropdownItem
      v-if="userStore.hasAbilities(abilities.DELETE_EXPENSE)"
      v-slot="slotProps"
      @click="removeExpense(row.id)"
    >
      <BaseIcon
        name="TrashIcon"
        :class="slotProps.class"
      />
      {{ $t('general.delete') }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { useDialogStore } from '@/scripts/stores/dialog'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useI18n } from 'vue-i18n'
import { useExpenseStore } from '@/scripts/admin/stores/expense'
import { useRoute, useRouter } from 'vue-router'
import { inject } from 'vue'
import { useUserStore } from '@/scripts/admin/stores/user'
import abilities from '@/scripts/admin/stub/abilities'

const props = defineProps({
  row: {
    type: Object,
    default: null,
  },
  table: {
    type: Object,
    default: null,
  },
  loadData: {
    type: Function,
    default: null,
  },
})

const dialogStore = useDialogStore()
const notificationStore = useNotificationStore()
const { t } = useI18n()
const expenseStore = useExpenseStore()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const $utils = inject('utils')

function removeExpense(id) {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('expenses.confirm_delete', 1),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      size: 'lg',
      hideNoButton: false,
    })
    .then((res) => {
      if (res) {
        expenseStore.deleteExpense({ ids: [id] }).then((res) => {
          if (res) {
            props.loadData && props.loadData()
          }
        })
      }
    })
}
</script>
