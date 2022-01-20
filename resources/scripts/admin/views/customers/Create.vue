<template>
  <BasePage>
    <form @submit.prevent="submitCustomerData">
      <BasePageHeader :title="pageTitle">
        <BaseBreadcrumb>
          <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />

          <BaseBreadcrumbItem
            :title="$tc('customers.customer', 2)"
            to="/admin/customers"
          />

          <BaseBreadcrumb-item :title="pageTitle" to="#" active />
        </BaseBreadcrumb>

        <template #actions>
          <div class="flex items-center justify-end">
            <BaseButton type="submit" :loading="isSaving" :disabled="isSaving">
              <template #left="slotProps">
                <BaseIcon name="SaveIcon" :class="slotProps.class" />
              </template>
              {{
                isEdit
                  ? $t('customers.update_customer')
                  : $t('customers.save_customer')
              }}
            </BaseButton>
          </div>
        </template>
      </BasePageHeader>

      <BaseCard class="mt-5">
        <!-- Basic Info -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 text-lg font-semibold text-left lg:col-span-1">
            {{ $t('customers.basic_info') }}
          </h6>

          <BaseInputGrid class="col-span-5 lg:col-span-4">
            <BaseInputGroup
              :label="$t('customers.display_name')"
              required
              :error="
                v$.currentCustomer.name.$error &&
                v$.currentCustomer.name.$errors[0].$message
              "
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.name"
                :content-loading="isFetchingInitialData"
                type="text"
                name="name"
                class=""
                :invalid="v$.currentCustomer.name.$error"
                @input="v$.currentCustomer.name.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.primary_contact_name')"
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.contact_name"
                :content-loading="isFetchingInitialData"
                type="text"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :error="
                v$.currentCustomer.email.$error &&
                v$.currentCustomer.email.$errors[0].$message
              "
              :content-loading="isFetchingInitialData"
              :label="$t('customers.email')"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.email"
                :content-loading="isFetchingInitialData"
                type="text"
                name="email"
                :invalid="v$.currentCustomer.email.$error"
                @input="v$.currentCustomer.email.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.phone')"
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.phone"
                :content-loading="isFetchingInitialData"
                type="text"
                name="phone"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.primary_currency')"
              :content-loading="isFetchingInitialData"
              :error="
                v$.currentCustomer.currency_id.$error &&
                v$.currentCustomer.currency_id.$errors[0].$message
              "
              required
            >
              <BaseMultiselect
                v-model="customerStore.currentCustomer.currency_id"
                value-prop="id"
                label="name"
                track-by="name"
                :content-loading="isFetchingInitialData"
                :options="globalStore.currencies"
                searchable
                :can-deselect="false"
                :placeholder="$t('customers.select_currency')"
                :invalid="v$.currentCustomer.currency_id.$error"
                class="w-full"
              >
              </BaseMultiselect>
            </BaseInputGroup>

            <BaseInputGroup
              :error="
                v$.currentCustomer.website.$error &&
                v$.currentCustomer.website.$errors[0].$message
              "
              :label="$t('customers.website')"
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.website"
                :content-loading="isFetchingInitialData"
                type="url"
                @input="v$.currentCustomer.website.$touch()"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.prefix')"
              :error="
                v$.currentCustomer.prefix.$error &&
                v$.currentCustomer.prefix.$errors[0].$message
              "
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.prefix"
                :content-loading="isFetchingInitialData"
                type="text"
                name="name"
                class=""
                :invalid="v$.currentCustomer.prefix.$error"
                @input="v$.currentCustomer.prefix.$touch()"
              />
            </BaseInputGroup>
          </BaseInputGrid>
        </div>

        <BaseDivider class="mb-5 md:mb-8" />

        <!-- Portal Access-->

        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 text-lg font-semibold text-left lg:col-span-1">
            {{ $t('customers.portal_access') }}
          </h6>

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
              :error="
                v$.currentCustomer.password.$error &&
                v$.currentCustomer.password.$errors[0].$message
              "
              :label="$t('customers.password')"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.password"
                :content-loading="isFetchingInitialData"
                :type="isShowPassword ? 'text' : 'password'"
                name="password"
                :invalid="v$.currentCustomer.password.$error"
                @input="v$.currentCustomer.password.$touch()"
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
                v$.currentCustomer.confirm_password.$error &&
                v$.currentCustomer.confirm_password.$errors[0].$message
              "
              :content-loading="isFetchingInitialData"
              label="Confirm Password"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.confirm_password"
                :content-loading="isFetchingInitialData"
                :type="isShowConfirmPassword ? 'text' : 'password'"
                name="confirm_password"
                :invalid="v$.currentCustomer.confirm_password.$error"
                @input="v$.currentCustomer.confirm_password.$touch()"
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
        </div>

        <BaseDivider class="mb-5 md:mb-8" />

        <!-- Billing Address   -->
        <div class="grid grid-cols-5 gap-4 mb-8">
          <h6 class="col-span-5 text-lg font-semibold text-left lg:col-span-1">
            {{ $t('customers.billing_address') }}
          </h6>

          <BaseInputGrid
            v-if="customerStore.currentCustomer.billing"
            class="col-span-5 lg:col-span-4"
          >
            <BaseInputGroup
              :label="$t('customers.name')"
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.billing.name"
                :content-loading="isFetchingInitialData"
                type="text"
                class="w-full"
                name="address_name"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.country')"
              :content-loading="isFetchingInitialData"
            >
              <BaseMultiselect
                v-model="customerStore.currentCustomer.billing.country_id"
                value-prop="id"
                label="name"
                track-by="name"
                resolve-on-load
                searchable
                :content-loading="isFetchingInitialData"
                :options="globalStore.countries"
                :placeholder="$t('general.select_country')"
                class="w-full"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.state')"
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.billing.state"
                :content-loading="isFetchingInitialData"
                name="billing.state"
                type="text"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :content-loading="isFetchingInitialData"
              :label="$t('customers.city')"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.billing.city"
                :content-loading="isFetchingInitialData"
                name="billing.city"
                type="text"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.address')"
              :error="
                (v$.currentCustomer.billing.address_street_1.$error &&
                  v$.currentCustomer.billing.address_street_1.$errors[0]
                    .$message) ||
                (v$.currentCustomer.billing.address_street_2.$error &&
                  v$.currentCustomer.billing.address_street_2.$errors[0]
                    .$message)
              "
              :content-loading="isFetchingInitialData"
            >
              <BaseTextarea
                v-model.trim="
                  customerStore.currentCustomer.billing.address_street_1
                "
                :content-loading="isFetchingInitialData"
                :placeholder="$t('general.street_1')"
                type="text"
                name="billing_street1"
                :container-class="`mt-3`"
                @input="v$.currentCustomer.billing.address_street_1.$touch()"
              />

              <BaseTextarea
                v-model.trim="
                  customerStore.currentCustomer.billing.address_street_2
                "
                :content-loading="isFetchingInitialData"
                :placeholder="$t('general.street_2')"
                type="text"
                class="mt-3"
                name="billing_street2"
                :container-class="`mt-3`"
                @input="v$.currentCustomer.billing.address_street_2.$touch()"
              />
            </BaseInputGroup>

            <div class="space-y-6">
              <BaseInputGroup
                :content-loading="isFetchingInitialData"
                :label="$t('customers.phone')"
                class="text-left"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.billing.phone"
                  :content-loading="isFetchingInitialData"
                  type="text"
                  name="phone"
                />
              </BaseInputGroup>

              <BaseInputGroup
                :label="$t('customers.zip_code')"
                :content-loading="isFetchingInitialData"
                class="mt-2 text-left"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.billing.zip"
                  :content-loading="isFetchingInitialData"
                  type="text"
                  name="zip"
                />
              </BaseInputGroup>
            </div>
          </BaseInputGrid>
        </div>

        <BaseDivider class="mb-5 md:mb-8" />

        <!-- Billing Address Copy Button  -->
        <div
          class="flex items-center justify-start mb-6 md:justify-end md:mb-0"
        >
          <div class="p-1">
            <BaseButton
              type="button"
              :content-loading="isFetchingInitialData"
              size="sm"
              variant="primary-outline"
              @click="customerStore.copyAddress(true)"
            >
              <template #left="slotProps">
                <BaseIcon
                  name="DocumentDuplicateIcon"
                  :class="slotProps.class"
                />
              </template>
              {{ $t('customers.copy_billing_address') }}
            </BaseButton>
          </div>
        </div>

        <!-- Shipping Address  -->
        <div
          v-if="customerStore.currentCustomer.shipping"
          class="grid grid-cols-5 gap-4 mb-8"
        >
          <h6 class="col-span-5 text-lg font-semibold text-left lg:col-span-1">
            {{ $t('customers.shipping_address') }}
          </h6>

          <BaseInputGrid class="col-span-5 lg:col-span-4">
            <BaseInputGroup
              :content-loading="isFetchingInitialData"
              :label="$t('customers.name')"
            >
              <BaseInput
                v-model.trim="customerStore.currentCustomer.shipping.name"
                :content-loading="isFetchingInitialData"
                type="text"
                name="address_name"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.country')"
              :content-loading="isFetchingInitialData"
            >
              <BaseMultiselect
                v-model="customerStore.currentCustomer.shipping.country_id"
                value-prop="id"
                label="name"
                track-by="name"
                resolve-on-load
                searchable
                :content-loading="isFetchingInitialData"
                :options="globalStore.countries"
                :placeholder="$t('general.select_country')"
                class="w-full"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.state')"
              :content-loading="isFetchingInitialData"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.shipping.state"
                :content-loading="isFetchingInitialData"
                name="shipping.state"
                type="text"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :content-loading="isFetchingInitialData"
              :label="$t('customers.city')"
            >
              <BaseInput
                v-model="customerStore.currentCustomer.shipping.city"
                :content-loading="isFetchingInitialData"
                name="shipping.city"
                type="text"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.address')"
              :content-loading="isFetchingInitialData"
              :error="
                (v$.currentCustomer.shipping.address_street_1.$error &&
                  v$.currentCustomer.shipping.address_street_1.$errors[0]
                    .$message) ||
                (v$.currentCustomer.shipping.address_street_2.$error &&
                  v$.currentCustomer.shipping.address_street_2.$errors[0]
                    .$message)
              "
            >
              <BaseTextarea
                v-model.trim="
                  customerStore.currentCustomer.shipping.address_street_1
                "
                :content-loading="isFetchingInitialData"
                type="text"
                :placeholder="$t('general.street_1')"
                name="shipping_street1"
                @input="v$.currentCustomer.shipping.address_street_1.$touch()"
              />

              <BaseTextarea
                v-model.trim="
                  customerStore.currentCustomer.shipping.address_street_2
                "
                :content-loading="isFetchingInitialData"
                type="text"
                :placeholder="$t('general.street_2')"
                name="shipping_street2"
                class="mt-3"
                :container-class="`mt-3`"
                @input="v$.currentCustomer.shipping.address_street_2.$touch()"
              />
            </BaseInputGroup>

            <div class="space-y-6">
              <BaseInputGroup
                :content-loading="isFetchingInitialData"
                :label="$t('customers.phone')"
                class="text-left"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.shipping.phone"
                  :content-loading="isFetchingInitialData"
                  type="text"
                  name="phone"
                />
              </BaseInputGroup>

              <BaseInputGroup
                :label="$t('customers.zip_code')"
                :content-loading="isFetchingInitialData"
                class="mt-2 text-left"
              >
                <BaseInput
                  v-model.trim="customerStore.currentCustomer.shipping.zip"
                  :content-loading="isFetchingInitialData"
                  type="text"
                  name="zip"
                />
              </BaseInputGroup>
            </div>
          </BaseInputGrid>
        </div>

        <BaseDivider
          v-if="customFieldStore.customFields.length > 0"
          class="mb-5 md:mb-8"
        />

        <!-- Customer Custom Fields -->
        <div class="grid grid-cols-5 gap-2 mb-8">
          <h6
            v-if="customFieldStore.customFields.length > 0"
            class="col-span-5 text-lg font-semibold text-left lg:col-span-1"
          >
            {{ $t('settings.custom_fields.title') }}
          </h6>

          <div class="col-span-5 lg:col-span-4">
            <CustomerCustomFields
              type="Customer"
              :store="customerStore"
              store-prop="currentCustomer"
              :is-edit="isEdit"
              :is-loading="isLoadingContent"
              :custom-field-scope="customFieldValidationScope"
            />
          </div>
        </div>
      </BaseCard>
    </form>
  </BasePage>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import {
  required,
  minLength,
  url,
  maxLength,
  helpers,
  email,
  sameAs,
  requiredIf,
} from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useCustomerStore } from '@/scripts/admin/stores/customer'
import { useCustomFieldStore } from '@/scripts/admin/stores/custom-field'
import CustomerCustomFields from '@/scripts/admin/components/custom-fields/CreateCustomFields.vue'
import { useGlobalStore } from '@/scripts/admin/stores/global'
import CopyInputField from '@/scripts/admin/components/CopyInputField.vue'
import { useCompanyStore } from '@/scripts/admin/stores/company'

const customerStore = useCustomerStore()
const customFieldStore = useCustomFieldStore()
const globalStore = useGlobalStore()
const companyStore = useCompanyStore()

const customFieldValidationScope = 'customFields'

const { t } = useI18n()

const router = useRouter()
const route = useRoute()

let isFetchingInitialData = ref(false)
let isShowPassword = ref(false)
let isShowConfirmPassword = ref(false)

let active = ref(false)
const isSaving = ref(false)

const isEdit = computed(() => route.name === 'customers.edit')

let isLoadingContent = computed(() => customerStore.isFetchingInitialSettings)

const pageTitle = computed(() =>
  isEdit.value ? t('customers.edit_customer') : t('customers.new_customer')
)

const rules = computed(() => {
  return {
    currentCustomer: {
      name: {
        required: helpers.withMessage(t('validation.required'), required),
        minLength: helpers.withMessage(
          t('validation.name_min_length', { count: 3 }),
          minLength(3)
        ),
      },
      prefix: {
        minLength: helpers.withMessage(
          t('validation.name_min_length', { count: 3 }),
          minLength(3)
        ),
      },
      currency_id: {
        required: helpers.withMessage(t('validation.required'), required),
      },

      email: {
        required: helpers.withMessage(
          t('validation.required'),
          requiredIf(customerStore.currentCustomer.enable_portal == true)
        ),
        email: helpers.withMessage(t('validation.email_incorrect'), email),
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
    },
  }
})

const getCustomerPortalUrl = computed(() => {
  return `${window.location.origin}/${companyStore.selectedCompany.slug}/customer/login`
})

const v$ = useVuelidate(rules, customerStore, {
  $scope: customFieldValidationScope,
})

customerStore.resetCurrentCustomer()

customerStore.fetchCustomerInitialSettings(isEdit.value)

async function submitCustomerData() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }

  isSaving.value = true

  let data = {
    ...customerStore.currentCustomer,
  }

  let response = null

  try {
    const action = isEdit.value
      ? customerStore.updateCustomer
      : customerStore.addCustomer
    response = await action(data)
  } catch (err) {
    isSaving.value = false
    return
  }

  router.push(`/admin/customers/${response.data.data.id}/view`)
}
</script>
