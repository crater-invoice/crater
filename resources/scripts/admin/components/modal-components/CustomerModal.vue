<template>
  <BaseModal
    :show="modalActive"
    @close="closeCustomerModal"
    @open="setInitialData"
  >
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}

        <BaseIcon
          name="XIcon"
          class="h-6 w-6 text-gray-500 cursor-pointer"
          @click="closeCustomerModal"
        />
      </div>
    </template>
    <form action="" @submit.prevent="submitCustomerData">
      <div class="px-6 pb-3">
        <BaseTabGroup>
          <BaseTab :title="$t('customers.basic_info')" class="!mt-2">
            <BaseInputGrid layout="one-column">
              <BaseInputGroup
                :label="$t('customers.display_name')"
                required
                :error="v$.name.$error && v$.name.$errors[0].$message"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.name"
                  type="text"
                  name="name"
                  class="mt-1 md:mt-0"
                  :invalid="v$.name.$error"
                  @input="v$.name.$touch()"
                />
              </BaseInputGroup>

              <BaseInputGroup
                :label="$tc('settings.currencies.currency')"
                required
                :error="
                  v$.currency_id.$error && v$.currency_id.$errors[0].$message
                "
              >
                <BaseMultiselect
                  v-model="customerStore.currentCustomer.currency_id"
                  :options="globalStore.currencies"
                  value-prop="id"
                  searchable
                  :placeholder="$t('customers.select_currency')"
                  :max-height="200"
                  class="mt-1 md:mt-0"
                  track-by="name"
                  :invalid="v$.currency_id.$error"
                  label="name"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.primary_contact_name')">
                <BaseInput
                  v-model="customerStore.currentCustomer.contact_name"
                  type="text"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>
              <BaseInputGroup
                :label="$t('login.email')"
                :error="v$.email.$error && v$.email.$errors[0].$message"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.email"
                  type="text"
                  name="email"
                  class="mt-1 md:mt-0"
                  :invalid="v$.email.$error"
                  @input="v$.email.$touch()"
                />
              </BaseInputGroup>

              <BaseInputGroup
                :label="$t('customers.prefix')"
                :error="v$.prefix.$error && v$.prefix.$errors[0].$message"
                :content-loading="isFetchingInitialData"
              >
                <BaseInput
                  v-model="customerStore.currentCustomer.prefix"
                  :content-loading="isFetchingInitialData"
                  type="text"
                  name="name"
                  class=""
                  :invalid="v$.prefix.$error"
                  @input="v$.prefix.$touch()"
                />
              </BaseInputGroup>

              <BaseInputGrid>
                <BaseInputGroup :label="$t('customers.phone')">
                  <BaseInput
                    v-model.trim="customerStore.currentCustomer.phone"
                    type="text"
                    name="phone"
                    class="mt-1 md:mt-0"
                  />
                </BaseInputGroup>

                <BaseInputGroup
                  :label="$t('customers.website')"
                  :error="v$.website.$error && v$.website.$errors[0].$message"
                >
                  <BaseInput
                    v-model="customerStore.currentCustomer.website"
                    type="url"
                    class="mt-1 md:mt-0"
                    :invalid="v$.website.$error"
                    @input="v$.website.$touch()"
                  />
                </BaseInputGroup>
              </BaseInputGrid>
            </BaseInputGrid>
          </BaseTab>

          <BaseTab :title="$t('customers.portal_access')">
            <BaseInputGrid class="col-span-5 lg:col-span-4">
              <div class="md:col-span-2">
                <p class="text-sm text-gray-500">
                  {{ $t('customers.portal_access_text') }}
                </p>

                <BaseSwitch
                  v-model="customerStore.currentCustomer.enable_portal"
                  class="mt-1 flex"
                />
              </div>

              <BaseInputGroup
                v-if="customerStore.currentCustomer.enable_portal"
                :content-loading="isFetchingInitialData"
                :label="$t('customers.portal_access_url')"
                class="md:col-span-2"
                :help-text="$t('customers.portal_access_url_help')"
              >
                <CopyInputField :token="getCustomerPortalUrl" />
              </BaseInputGroup>

              <BaseInputGroup
                v-if="customerStore.currentCustomer.enable_portal"
                :content-loading="isFetchingInitialData"
                :error="v$.password.$error && v$.password.$errors[0].$message"
                :label="$t('customers.password')"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.password"
                  :content-loading="isFetchingInitialData"
                  :type="isShowPassword ? 'text' : 'password'"
                  name="password"
                  :invalid="v$.password.$error"
                  @input="v$.password.$touch()"
                >
                  <template #right>
                    <BaseIcon
                      v-if="isShowPassword"
                      name="EyeOffIcon"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="isShowPassword = !isShowPassword"
                    />
                    <BaseIcon
                      v-else
                      name="EyeIcon"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="isShowPassword = !isShowPassword"
                    /> </template
                ></BaseInput>
              </BaseInputGroup>
              <BaseInputGroup
                v-if="customerStore.currentCustomer.enable_portal"
                :error="
                  v$.confirm_password.$error &&
                  v$.confirm_password.$errors[0].$message
                "
                :content-loading="isFetchingInitialData"
                label="Confirm Password"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.confirm_password"
                  :content-loading="isFetchingInitialData"
                  :type="isShowConfirmPassword ? 'text' : 'password'"
                  name="confirm_password"
                  :invalid="v$.confirm_password.$error"
                  @input="v$.confirm_password.$touch()"
                >
                  <template #right>
                    <BaseIcon
                      v-if="isShowConfirmPassword"
                      name="EyeOffIcon"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="isShowConfirmPassword = !isShowConfirmPassword"
                    />
                    <BaseIcon
                      v-else
                      name="EyeIcon"
                      class="w-5 h-5 mr-1 text-gray-500 cursor-pointer"
                      @click="isShowConfirmPassword = !isShowConfirmPassword"
                    /> </template
                ></BaseInput>
              </BaseInputGroup>
            </BaseInputGrid>
          </BaseTab>

          <BaseTab :title="$t('customers.billing_address')" class="!mt-2">
            <BaseInputGrid layout="one-column">
              <BaseInputGroup :label="$t('customers.name')">
                <BaseInput
                  v-model="customerStore.currentCustomer.billing.name"
                  type="text"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.country')">
                <BaseMultiselect
                  v-model="customerStore.currentCustomer.billing.country_id"
                  :options="globalStore.countries"
                  searchable
                  :show-labels="false"
                  :placeholder="$t('general.select_country')"
                  :allow-empty="false"
                  track-by="name"
                  class="mt-1 md:mt-0"
                  label="name"
                  value-prop="id"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.state')">
                <BaseInput
                  v-model="customerStore.currentCustomer.billing.state"
                  type="text"
                  name="billingState"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.city')">
                <BaseInput
                  v-model="customerStore.currentCustomer.billing.city"
                  type="text"
                  name="billingCity"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup
                :label="$t('customers.address')"
                :error="
                  v$.billing.address_street_1.$error &&
                  v$.billing.address_street_1.$errors[0].$message
                "
              >
                <BaseTextarea
                  v-model="
                    customerStore.currentCustomer.billing.address_street_1
                  "
                  :placeholder="$t('general.street_1')"
                  rows="2"
                  cols="50"
                  class="mt-1 md:mt-0"
                  :invalid="v$.billing.address_street_1.$error"
                  @input="v$.billing.address_street_1.$touch()"
                />
              </BaseInputGroup>
            </BaseInputGrid>

            <BaseInputGrid layout="one-column">
              <BaseInputGroup
                :error="
                  v$.billing.address_street_2.$error &&
                  v$.billing.address_street_2.$errors[0].$message
                "
              >
                <BaseTextarea
                  v-model="
                    customerStore.currentCustomer.billing.address_street_2
                  "
                  :placeholder="$t('general.street_2')"
                  rows="2"
                  cols="50"
                  :invalid="v$.billing.address_street_2.$error"
                  @input="v$.billing.address_street_2.$touch()"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.phone')">
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.billing.phone"
                  type="text"
                  name="phone"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.zip_code')">
                <BaseInput
                  v-model="customerStore.currentCustomer.billing.zip"
                  type="text"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>
            </BaseInputGrid>
          </BaseTab>

          <BaseTab :title="$t('customers.shipping_address')" class="!mt-2">
            <div class="grid md:grid-cols-12">
              <div class="flex justify-end col-span-12">
                <BaseButton
                  variant="primary"
                  type="button"
                  size="xs"
                  @click="copyAddress(true)"
                >
                  {{ $t('customers.copy_billing_address') }}
                </BaseButton>
              </div>
            </div>

            <BaseInputGrid layout="one-column">
              <BaseInputGroup :label="$t('customers.name')">
                <BaseInput
                  v-model="customerStore.currentCustomer.shipping.name"
                  type="text"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.country')">
                <BaseMultiselect
                  v-model="customerStore.currentCustomer.shipping.country_id"
                  :options="globalStore.countries"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="false"
                  :placeholder="$t('general.select_country')"
                  track-by="name"
                  class="mt-1 md:mt-0"
                  label="name"
                  value-prop="id"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.state')">
                <BaseInput
                  v-model="customerStore.currentCustomer.shipping.state"
                  type="text"
                  name="shippingState"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.city')">
                <BaseInput
                  v-model="customerStore.currentCustomer.shipping.city"
                  type="text"
                  name="shippingCity"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup
                :label="$t('customers.address')"
                :error="
                  v$.shipping.address_street_1.$error &&
                  v$.shipping.address_street_1.$errors[0].$message
                "
              >
                <BaseTextarea
                  v-model="
                    customerStore.currentCustomer.shipping.address_street_1
                  "
                  :placeholder="$t('general.street_1')"
                  rows="2"
                  cols="50"
                  class="mt-1 md:mt-0"
                  :invalid="v$.shipping.address_street_1.$error"
                  @input="v$.shipping.address_street_1.$touch()"
                />
              </BaseInputGroup>
            </BaseInputGrid>

            <BaseInputGrid layout="one-column">
              <BaseInputGroup
                :error="
                  v$.shipping.address_street_2.$error &&
                  v$.shipping.address_street_2.$errors[0].$message
                "
              >
                <BaseTextarea
                  v-model="
                    customerStore.currentCustomer.shipping.address_street_2
                  "
                  :placeholder="$t('general.street_2')"
                  rows="2"
                  cols="50"
                  :invalid="v$.shipping.address_street_1.$error"
                  @input="v$.shipping.address_street_2.$touch()"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.phone')">
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.shipping.phone"
                  type="text"
                  name="phone"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>

              <BaseInputGroup :label="$t('customers.zip_code')">
                <BaseInput
                  v-model="customerStore.currentCustomer.shipping.zip"
                  type="text"
                  class="mt-1 md:mt-0"
                />
              </BaseInputGroup>
            </BaseInputGrid>
          </BaseTab>
        </BaseTabGroup>
      </div>

      <div
        class="z-0 flex justify-end p-4 border-t border-gray-200 border-solid"
      >
        <BaseButton
          class="mr-3 text-sm"
          type="button"
          variant="primary-outline"
          @click="closeCustomerModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>

        <BaseButton :loading="isLoading" variant="primary" type="submit">
          <template #left="slotProps">
            <BaseIcon
              v-if="!isLoading"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.save') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

import {
  required,
  minLength,
  maxLength,
  email,
  alpha,
  url,
  helpers,
  requiredIf,
  sameAs,
} from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'

import { useModalStore } from '@/scripts/stores/modal'
import { useEstimateStore } from '@/scripts/admin/stores/estimate'
import { useCustomerStore } from '@/scripts/admin/stores/customer'
import { useCompanyStore } from '@/scripts/admin/stores/company'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import { useInvoiceStore } from '@/scripts/admin/stores/invoice'
import CopyInputField from '@/scripts/admin/components/CopyInputField.vue'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useRecurringInvoiceStore } from '@/scripts/admin/stores/recurring-invoice'

const recurringInvoiceStore = useRecurringInvoiceStore()
const modalStore = useModalStore()
const estimateStore = useEstimateStore()
const customerStore = useCustomerStore()
const companyStore = useCompanyStore()
const globalStore = useGlobalStore()
const invoiceStore = useInvoiceStore()
const notificationStore = useNotificationStore()

let isFetchingInitialData = ref(false)

const { t } = useI18n()
const route = useRoute()
const isEdit = ref(false)
const isLoading = ref(false)
let isShowPassword = ref(false)
let isShowConfirmPassword = ref(false)

const modalActive = computed(
  () => modalStore.active && modalStore.componentName === 'CustomerModal'
)

const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
    currency_id: {
      required: helpers.withMessage(t('validation.required'), required),
    },
    password: {
      required: helpers.withMessage(
        t('validation.required'),
        requiredIf(
          customerStore.currentCustomer.enable_portal == true &&
            !customerStore.currentCustomer.password_added
        )
      ),
      minLength: helpers.withMessage(
        t('validation.password_min_length', { count: 8 }),
        minLength(8)
      ),
    },
    confirm_password: {
      sameAsPassword: helpers.withMessage(
        t('validation.password_incorrect'),
        sameAs(customerStore.currentCustomer.password)
      ),
    },
    email: {
      required: helpers.withMessage(
        t('validation.required'),
        requiredIf(customerStore.currentCustomer.enable_portal == true)
      ),
      email: helpers.withMessage(t('validation.email_incorrect'), email),
    },
    prefix: {
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
    website: {
      url: helpers.withMessage(t('validation.invalid_url'), url),
    },

    billing: {
      address_street_1: {
        maxLength: helpers.withMessage(
          t('validation.address_maxlength', { count: 255 }),
          maxLength(255)
        ),
      },
      address_street_2: {
        maxLength: helpers.withMessage(
          t('validation.address_maxlength', { count: 255 }),
          maxLength(255)
        ),
      },
    },

    shipping: {
      address_street_1: {
        maxLength: helpers.withMessage(
          t('validation.address_maxlength', { count: 255 }),
          maxLength(255)
        ),
      },
      address_street_2: {
        maxLength: helpers.withMessage(
          t('validation.address_maxlength', { count: 255 }),
          maxLength(255)
        ),
      },
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => customerStore.currentCustomer)
)

const getCustomerPortalUrl = computed(() => {
  return `${window.location.origin}/${companyStore.selectedCompany.slug}/customer/login`
})

function copyAddress() {
  customerStore.copyAddress()
}

async function setInitialData() {
  if (!customerStore.isEdit) {
    customerStore.currentCustomer.currency_id =
      companyStore.selectedCompanyCurrency.id
  }
}

async function submitCustomerData() {
  v$.value.$touch()

  if (v$.value.$invalid && customerStore.currentCustomer.email === '') {
    notificationStore.showNotification({
      type: 'error',
      message: t('settings.notification.please_enter_email'),
    })
  }

  if (v$.value.$invalid) {
    return true
  }

  isLoading.value = true

  let data = {
    ...customerStore.currentCustomer,
  }

  try {
    let response = null
    if (customerStore.isEdit) {
      response = await customerStore.updateCustomer(data)
    } else {
      response = await customerStore.addCustomer(data)
    }

    if (response.data) {
      isLoading.value = false
      // Automatically create newly created customer
      if (route.name === 'invoices.create' || route.name === 'invoices.edit') {
        invoiceStore.selectCustomer(response.data.data.id)
      }
      if (
        route.name === 'estimates.create' ||
        route.name === 'estimates.edit'
      ) {
        estimateStore.selectCustomer(response.data.data.id)
      }
      if (
        route.name === 'recurring-invoices.create' ||
        route.name === 'recurring-invoices.edit'
      ) {
        recurringInvoiceStore.selectCustomer(response.data.data.id)
      }
      closeCustomerModal()
    }
  } catch (err) {
    console.error(err)
    isLoading.value = false
  }
}

function closeCustomerModal() {
  modalStore.closeModal()
  setTimeout(() => {
    customerStore.resetCurrentCustomer()
    v$.value.$reset()
  }, 300)
}
</script>
