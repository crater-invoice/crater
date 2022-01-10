<template>
  <form class="relative h-full mt-4" @submit.prevent="UpdateCustomerAddress">
    <BaseCard>
      <div class="mb-6">
        <h6 class="font-bold text-left">
          {{ $t('settings.menu_title.address_information') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-left text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.address_information.section_description') }}
        </p>
      </div>

      <!-- Billing Address   -->

      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 text-lg font-semibold text-left lg:col-span-1">
          {{ $t('customers.billing_address') }}
        </h6>

        <div
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <BaseInputGroup
            :label="$t('customers.name')"
            class="w-full md:col-span-3"
          >
            <BaseInput
              v-model.trim="userStore.userForm.billing.name"
              type="text"
              class="w-full"
              name="address_name"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('customers.country')"
            class="md:col-span-3"
          >
            <BaseMultiselect
              v-model="userStore.userForm.billing.country_id"
              value-prop="id"
              label="name"
              track-by="name"
              resolve-on-load
              searchable
              :options="globalStore.countries"
              :placeholder="$t('general.select_country')"
              class="w-full"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('customers.state')" class="md:col-span-3">
            <BaseInput
              v-model="userStore.userForm.billing.state"
              name="billing.state"
              type="text"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('customers.city')" class="md:col-span-3">
            <BaseInput
              v-model="userStore.userForm.billing.city"
              name="billing.city"
              type="text"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('customers.address')"
            class="md:col-span-3"
          >
            <BaseTextarea
              v-model.trim="userStore.userForm.billing.address_street_1"
              :placeholder="$t('general.street_1')"
              type="text"
              name="billing_street1"
              :container-class="`mt-3`"
            />

            <BaseTextarea
              v-model.trim="userStore.userForm.billing.address_street_2"
              :placeholder="$t('general.street_2')"
              type="text"
              class="mt-3"
              name="billing_street2"
              :container-class="`mt-3`"
            />
          </BaseInputGroup>

          <div class="md:col-span-3">
            <BaseInputGroup :label="$t('customers.phone')" class="text-left">
              <BaseInput
                v-model.trim="userStore.userForm.billing.phone"
                type="text"
                name="phone"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.zip_code')"
              class="mt-2 text-left"
            >
              <BaseInput
                v-model.trim="userStore.userForm.billing.zip"
                type="text"
                name="zip"
              />
            </BaseInputGroup>
          </div>
        </div>
      </div>

      <BaseDivider class="mb-5 md:mb-8" />

      <!-- Billing Address Copy Button  -->
      <div class="flex items-center justify-start mb-6 md:justify-end md:mb-0">
        <div class="p-1">
          <BaseButton
            ref="sameAddress"
            type="button"
            @click="userStore.copyAddress(true)"
          >
            <template #left="slotProps">
              <BaseIcon name="DocumentDuplicateIcon" :class="slotProps.class" />
            </template>
            {{ $t('customers.copy_billing_address') }}
          </BaseButton>
        </div>
      </div>

      <!-- Shipping Address  -->

      <div class="grid grid-cols-5 gap-4 mb-8">
        <h6 class="col-span-5 text-lg font-semibold text-left lg:col-span-1">
          {{ $t('customers.shipping_address') }}
        </h6>

        <div
          v-if="userStore.userForm.shipping"
          class="grid col-span-5 lg:col-span-4 gap-y-6 gap-x-4 md:grid-cols-6"
        >
          <BaseInputGroup :label="$t('customers.name')" class="md:col-span-3">
            <BaseInput
              v-model.trim="userStore.userForm.shipping.name"
              type="text"
              name="address_name"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('customers.country')"
            class="md:col-span-3"
          >
            <BaseMultiselect
              v-model="userStore.userForm.shipping.country_id"
              value-prop="id"
              label="name"
              track-by="name"
              resolve-on-load
              searchable
              :options="globalStore.countries"
              :placeholder="$t('general.select_country')"
              class="w-full"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('customers.state')" class="md:col-span-3">
            <BaseInput
              v-model="userStore.userForm.shipping.state"
              name="shipping.state"
              type="text"
            />
          </BaseInputGroup>

          <BaseInputGroup :label="$t('customers.city')" class="md:col-span-3">
            <BaseInput
              v-model="userStore.userForm.shipping.city"
              name="shipping.city"
              type="text"
            />
          </BaseInputGroup>

          <BaseInputGroup
            :label="$t('customers.address')"
            class="md:col-span-3"
          >
            <BaseTextarea
              v-model.trim="userStore.userForm.shipping.address_street_1"
              type="text"
              :placeholder="$t('general.street_1')"
              name="shipping_street1"
            />

            <BaseTextarea
              v-model.trim="userStore.userForm.shipping.address_street_2"
              type="text"
              :placeholder="$t('general.street_2')"
              name="shipping_street2"
              class="mt-3"
            />
          </BaseInputGroup>

          <div class="md:col-span-3">
            <BaseInputGroup :label="$t('customers.phone')" class="text-left">
              <BaseInput
                v-model.trim="userStore.userForm.shipping.phone"
                type="text"
                name="phone"
              />
            </BaseInputGroup>

            <BaseInputGroup
              :label="$t('customers.zip_code')"
              class="mt-2 text-left"
            >
              <BaseInput
                v-model.trim="userStore.userForm.shipping.zip"
                type="text"
                name="zip"
              />
            </BaseInputGroup>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end">
        <BaseButton :loading="isSaving" :disabled="isSaving">
          <template #left="slotProps">
            <BaseIcon
              v-if="!isSaving"
              name="SaveIcon"
              :class="slotProps.class"
            />
          </template>
          {{ $t('general.save') }}
        </BaseButton>
      </div>
    </BaseCard>
  </form>
</template>

<script setup>
import { useUserStore } from '@/scripts/customer/stores/user'
import { useGlobalStore } from '@/scripts/customer/stores/global'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import { ref } from 'vue'

//store

const userStore = useUserStore()
const route = useRoute()
const { tm, t } = useI18n()
const globalStore = useGlobalStore()

// local state
let isSaving = ref(false)

// created

globalStore.fetchCountries()

// methods

function UpdateCustomerAddress() {
  isSaving.value = true
  let data = userStore.userForm
  userStore
    .updateCurrentUser({
      data,
      message: tm('customers.address_updated_message'),
    })
    .then((res) => {
      isSaving.value = false
    })
    .catch((err) => {
      isSaving.value = false
    })
}
</script>
