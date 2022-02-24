<template>
  <div class="grid gap-8 md:grid-cols-12 pt-10">
    <div class="col-span-8 md:col-span-4">
      <BaseInputGroup
        :label="$t('reports.sales.date_range')"
        class="col-span-12 md:col-span-8"
      >
        <BaseMultiselect
          v-model="selectedRange"
          :options="dateRange"
          value-prop="key"
          track-by="key"
          label="label"
          object
          @update:modelValue="onChangeDateRange"
        />
      </BaseInputGroup>

      <div class="flex flex-col mt-6 lg:space-x-3 lg:flex-row">
        <BaseInputGroup :label="$t('reports.expenses.from_date')">
          <BaseDatePicker v-model="formData.from_date" />
        </BaseInputGroup>

        <div
          class="
            hidden
            w-5
            h-0
            mx-4
            border border-gray-400 border-solid
            xl:block
          "
          style="margin-top: 2.5rem"
        />

        <BaseInputGroup :label="$t('reports.expenses.to_date')">
          <BaseDatePicker v-model="formData.to_date" />
        </BaseInputGroup>
      </div>

      <BaseButton
        variant="primary-outline"
        class="content-center hidden mt-0 w-md md:flex md:mt-8"
        type="submit"
        @click.prevent="getReports"
      >
        {{ $t('reports.update_report') }}
      </BaseButton>
    </div>

    <div class="col-span-8">
      <iframe
        :src="getReportUrl"
        class="
          hidden
          w-full
          h-screen
          border-gray-100 border-solid
          rounded
          md:flex
        "
      />

      <a
        class="
          flex
          items-center
          justify-center
          h-10
          px-5
          py-1
          text-sm
          font-medium
          leading-none
          text-center text-white
          rounded
          whitespace-nowrap
          md:hidden
          bg-primary-500
          cursor-pointer
        "
        @click="viewReportsPDF"
      >
        <BaseIcon name="DocumentTextIcon" class="h-5 mr-2" />
        <span>{{ $t('reports.view_pdf') }}</span>
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, reactive } from 'vue'
import moment from 'moment'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useI18n } from 'vue-i18n'
import { useGlobalStore } from '@/scripts/admin/stores/global'
const globalStore = useGlobalStore()
const companyStore = useCompanyStore()
const { t } = useI18n()

globalStore.downloadReport = downloadReport

const dateRange = reactive([
  {
    label: t('dateRange.today'),
    key: 'Today',
  },
  {
    label: t('dateRange.this_week'),
    key: 'This Week',
  },
  {
    label: t('dateRange.this_month'),
    key: 'This Month',
  },
  {
    label: t('dateRange.this_quarter'),
    key: 'This Quarter',
  },
  {
    label: t('dateRange.this_year'),
    key: 'This Year',
  },
  {
    label: t('dateRange.previous_week'),
    key: 'Previous Week',
  },
  {
    label: t('dateRange.previous_month'),
    key: 'Previous Month',
  },
  {
    label: t('dateRange.previous_quarter'),
    key: 'Previous Quarter',
  },
  {
    label: t('dateRange.previous_year'),
    key: 'Previous Year',
  },
  {
    label: t('dateRange.custom'),
    key: 'Custom',
  },
])

const selectedRange = ref(dateRange[2])
let range = ref(new Date())
let url = ref(null)
let siteURL = ref(null)

const formData = reactive({
  from_date: moment().startOf('month').toString(),
  to_date: moment().endOf('month').toString(),
})

const getReportUrl = computed(() => {
  return url.value
})

const getSelectedCompany = computed(() => {
  return companyStore.selectedCompany
})

const dateRangeUrl = computed(() => {
  return `${siteURL.value}?from_date=${moment(formData.from_date).format(
    'YYYY-MM-DD'
  )}&to_date=${moment(formData.to_date).format('YYYY-MM-DD')}`
})

onMounted(() => {
  siteURL.value = `/reports/expenses/${getSelectedCompany.value.unique_hash}`
  url.value = dateRangeUrl.value
})

watch(
  () => range,
  (newRange) => {
    formData.from_date = moment(newRange).startOf('year').toString()
    formData.to_date = moment(newRange).endOf('year').toString()
  }
)

function getThisDate(type, time) {
  return moment()[type](time).format('YYYY-MM-DD')
}

function getPreDate(type, time) {
  return moment().subtract(1, time)[type](time).format('YYYY-MM-DD')
}

function onChangeDateRange() {
  let key = selectedRange.value.key

  switch (key) {
    case 'Today':
      formData.from_date = moment().format('YYYY-MM-DD')
      formData.to_date = moment().format('YYYY-MM-DD')
      break

    case 'This Week':
      formData.from_date = getThisDate('startOf', 'isoWeek')
      formData.to_date = getThisDate('endOf', 'isoWeek')
      break

    case 'This Month':
      formData.from_date = getThisDate('startOf', 'month')
      formData.to_date = getThisDate('endOf', 'month')
      break

    case 'This Quarter':
      formData.from_date = getThisDate('startOf', 'quarter')
      formData.to_date = getThisDate('endOf', 'quarter')
      break

    case 'This Year':
      formData.from_date = getThisDate('startOf', 'year')
      formData.to_date = getThisDate('endOf', 'year')
      break

    case 'Previous Week':
      formData.from_date = getPreDate('startOf', 'isoWeek')
      formData.to_date = getPreDate('endOf', 'isoWeek')
      break

    case 'Previous Month':
      formData.from_date = getPreDate('startOf', 'month')
      formData.to_date = getPreDate('endOf', 'month')
      break

    case 'Previous Quarter':
      formData.from_date = getPreDate('startOf', 'quarter')
      formData.to_date = getPreDate('endOf', 'quarter')
      break

    case 'Previous Year':
      formData.from_date = getPreDate('startOf', 'year')
      formData.to_date = getPreDate('endOf', 'year')
      break

    default:
      break
  }
}

async function viewReportsPDF() {
  let data = await getReports()
  window.open(getReportUrl.value, '_blank')
  return data
}

function getReports() {
  url.value = dateRangeUrl.value
  return true
}

function downloadReport() {
  if (!getReports()) {
    return false
  }

  window.open(getReportUrl.value + '&download=true')
  setTimeout(() => {
    url.value = dateRangeUrl.value
  }, 200)
}
</script>
