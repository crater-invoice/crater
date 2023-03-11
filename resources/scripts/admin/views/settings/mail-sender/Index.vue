<template>
  <BaseSettingCard
    :title="$tc(`${pre_t}.title`, 2)"
    :description="$t(`${pre_t}.description`)"
  >
    <MailSenderModal />
    <MailSenderTestModal />

    <template #action>
      <BaseButton
        type="submit"
        variant="primary-outline"
        @click="openMailSenderModal"
      >
        <template #left="slotProps">
          <BaseIcon :class="slotProps.class" name="PlusIcon" />
        </template>
        {{ $t(`${pre_t}.add_new_mail_sender`) }}
      </BaseButton>
    </template>

    <BaseTable
      ref="table"
      class="mt-16"
      :data="fetchData"
      :columns="mailSenderColumns"
    >
      <template #cell-is_default="{ row }">
        <BaseBadge
          :bg-color="
            utils.getBadgeStatusColor(row.data.is_default ? 'YES' : 'NO')
              .bgColor
          "
          :color="
            utils.getBadgeStatusColor(row.data.is_default ? 'YES' : 'NO').color
          "
        >
          {{ row.data.is_default ? $t('general.yes') : $t('general.no') }}
        </BaseBadge>
      </template>

      <template #cell-actions="{ row }">
        <MailSenderDropdown
          :row="row.data"
          :table="table"
          :load-data="refreshTable"
        />
      </template>
    </BaseTable>
  </BaseSettingCard>
</template>

<script setup>
import { computed, ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useModalStore } from '@/scripts/stores/modal'
import MailSenderModal from '@/scripts/admin/components/modal-components/MailSenderModal.vue'
import { useMailSenderStore } from '@/scripts/admin/stores/mail-sender'
import MailSenderDropdown from '@/scripts/admin/components/dropdowns/MailSenderIndexDropdown.vue'
import MailSenderTestModal from '@/scripts/admin/components/modal-components/MailSenderTestModal.vue'

const pre_t = 'settings.mail_sender'
const modalStore = useModalStore()
const mailSenderStore = useMailSenderStore()
const { t } = useI18n()
const table = ref(null)
const utils = inject('utils')

function openMailSenderModal() {
  modalStore.openModal({
    title: t(`${pre_t}.add_new_mail_sender`),
    componentName: 'MailSenderModal',
    size: 'md',
    refreshData: refreshTable,
  })
}

const mailSenderColumns = computed(() => {
  return [
    {
      key: 'name',
      label: t(`${pre_t}.name`),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'driver',
      label: t(`${pre_t}.driver`),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'from_address',
      label: t(`${pre_t}.from_address`),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'is_default',
      label: t(`${pre_t}.is_default`),
      thClass: 'extra',
      tdClass: 'font-medium text-gray-900',
    },
    {
      key: 'actions',
      label: '',
      tdClass: 'text-right text-sm font-medium',
      sortable: false,
    },
  ]
})

async function fetchData({ page, filter, sort }) {
  let data = {
    orderByField: sort.fieldName || 'created_at',
    orderBy: sort.order || 'desc',
    page,
  }

  let response = await mailSenderStore.fetchMailSenders(data)

  return {
    data: response.data.data,
    pagination: {
      totalPages: response.data.meta.last_page,
      currentPage: page,
      totalCount: response.data.meta.total,
      limit: response.data.meta.per_page ? response.data.meta.per_page : 10,
    },
  }
}

async function refreshTable() {
  table.value && table.value.refresh()
}
</script>
