<template>
  <div class="col-span-5 pr-0">
    <div
      v-if="selectedCustomer"
      class="flex flex-col p-4 bg-white border border-gray-200 border-solid"
      style="min-height: 170px"
    >
      <div class="relative flex justify-between mb-1">
        <label class="flex-1 font-medium">{{ selectedCustomer.name }}</label>
        <a
          class="relative my-0 ml-0 mr-6 text-sm font-medium cursor-pointer text-primary-500"
          @click.prevent="editCustomer"
        >
          {{ $t('general.edit') }}
        </a>
        <a
          class="relative my-0 ml-0 mr-6 text-sm font-medium cursor-pointer text-primary-500"
          @click.prevent="resetSelectedCustomer"
        >
          {{ $t('general.deselect') }}
        </a>
      </div>

      <div class="grid grid-cols-2 gap-4 mt-1">
        <div v-if="selectedCustomer.billing_address">
          <div class="flex flex-col">
            <label
              class="mb-1 text-sm font-medium text-gray-500 uppercase whitespace-nowrap"
            >
              {{ $t('general.bill_to') }}
            </label>
            <div class="flex flex-col flex-1 p-0">
              <label
                v-if="selectedCustomer.billing_address.name"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.billing_address.name }}
              </label>
              <label
                v-if="selectedCustomer.billing_address.address_street_1"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.billing_address.address_street_1 }}
              </label>
              <label
                v-if="selectedCustomer.billing_address.address_street_2"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.billing_address.address_street_2 }}
              </label>
              <label
                v-if="
                  selectedCustomer.billing_address.city &&
                  selectedCustomer.billing_address.state
                "
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.billing_address.city }},
                {{ selectedCustomer.billing_address.state }}
                {{ selectedCustomer.billing_address.zip }}
              </label>
              <label
                v-if="selectedCustomer.billing_address.country"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.billing_address.country.name }}
              </label>
              <label
                v-if="selectedCustomer.billing_address.phone"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.billing_address.phone }}
              </label>
            </div>
          </div>
        </div>
        <div v-if="selectedCustomer.shipping_address">
          <div class="flex flex-col">
            <label
              class="mb-1 text-sm font-medium text-gray-500 uppercase whitespace-nowrap"
            >
              {{ $t('general.ship_to') }}
            </label>
            <div class="flex flex-col flex-1 p-0">
              <label
                v-if="selectedCustomer.shipping_address.name"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.shipping_address.name }}
              </label>
              <label
                v-if="selectedCustomer.shipping_address.address_street_1"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.shipping_address.address_street_1 }}
              </label>
              <label
                v-if="selectedCustomer.shipping_address.address_street_2"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.shipping_address.address_street_2 }}
              </label>
              <label
                v-if="
                  selectedCustomer.shipping_address.city &&
                  selectedCustomer.shipping_address
                "
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.shipping_address.city }},
                {{ selectedCustomer.shipping_address.state }}
                {{ selectedCustomer.shipping_address.zip }}
              </label>
              <label
                v-if="selectedCustomer.shipping_address.country"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.shipping_address.country.name }}
              </label>
              <label
                v-if="selectedCustomer.shipping_address.phone"
                class="relative w-11/12 text-sm truncate"
              >
                {{ selectedCustomer.shipping_address.phone }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <sw-popup
        :class="[
          'p-0',
          {
            'border border-solid border-danger rounded': valid.$error,
          },
        ]"
      >
        <div
          slot="activator"
          class="relative flex justify-center px-0 py-16 bg-white border border-gray-200 border-solid rounded"
          style="min-height: 170px"
        >
          <user-icon
            class="flex justify-center w-10 h-10 p-2 mr-5 text-sm text-white bg-gray-200 rounded-full font-base"
          />
          <div class="mt-1">
            <label class="text-lg">
              {{ $t('customers.new_customer') }}
              <span class="text-danger"> * </span>
            </label>
            <p v-if="valid.$error && !valid.required" class="text-danger">
              {{ $t('estimates.errors.required') }}
            </p>
          </div>
        </div>

        <customer-select-popup :user-id="customerId" type="estimate" />
      </sw-popup>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { UserIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    UserIcon,
  },
  props: {
    valid: {
      type: Object,
      default: () => {},
    },
    customerId: {
      type: Number,
      default: null,
    },
  },

  computed: {
    ...mapGetters('estimate', ['getTemplateName', 'selectedCustomer']),
  },

  created() {
    this.resetSelectedCustomer()
    if (this.customerId) {
      this.selectCustomer(this.customerId)
    }
  },

  methods: {
    ...mapActions('estimate', ['resetSelectedCustomer', 'selectCustomer']),

    ...mapActions('modal', ['openModal']),

    editCustomer() {
      this.openModal({
        title: this.$t('customers.edit_customer'),
        componentName: 'CustomerModal',
        id: this.selectedCustomer.id,
        data: this.selectedCustomer,
      })
    },
  },
}
</script>
