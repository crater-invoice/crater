<template>
  <div>
    <sw-dropdown :is-show="isShow" variant="search-dropdown">
      <sw-input
        slot="activator"
        v-model="name"
        :placeholder="$t('global_search.search')"
        variant="search-input"
        @input="throttledMethod"
      >
        <search-icon slot="leftIcon" class="h-5 m-1 text-gray-500" />
        <loading-icon
          slot="rightIcon"
          v-show="isLoading"
          class="absolute right-0 h-5 m-1 animate-spin text-primary-400"
        />
      </sw-input>

      <div class="w-64 h-40 overflow-y-scroll box">
        <div v-if="getCustomerList.length > 0 && !isLoading">
          <label class="text-xs text-gray-400 uppercase">
            {{ $t('global_search.customers') }}
          </label>
          <router-link
            v-for="d in getCustomerList"
            :key="d.id"
            :to="`/admin/customers/${d.id}/view`"
          >
            <sw-dropdown-item>
              <span
                class="flex items-center justify-center w-8 h-8 mr-4 text-xs font-semibold bg-gray-300 rounded-full text-primary-500"
              >
                {{ initGenerator(d.name) }}
              </span>

              <div v-if="d.contact_name" class="flex flex-col">
                <span class="text-sm text-black">{{ d.name }}</span>

                <span class="text-xs text-gray-500">{{ d.contact_name }}</span>
              </div>
              <div v-else class="flex items-center">
                <span class="text-sm text-black">{{ d.name }}</span>
              </div>
            </sw-dropdown-item>
          </router-link>
        </div>

        <div v-if="getUserList.length > 0 && !isLoading">
          <label class="text-xs text-gray-400 uppercase">{{
            $t('global_search.users')
          }}</label>
          <router-link
            v-for="d in getUserList"
            :key="d.id"
            :to="`/admin/users/${d.id}/edit`"
          >
            <sw-dropdown-item>
              <span
                class="flex items-center justify-center w-8 h-8 mr-4 text-xs font-semibold bg-gray-300 rounded-full text-primary-500"
              >
                {{ initGenerator(d.name) }}
              </span>

              <div class="flex items-center">
                <span class="text-sm text-black">{{ d.name }}</span>
              </div>
            </sw-dropdown-item>
          </router-link>
        </div>

        <div
          v-if="
            getUserList.length === 0 &&
            getCustomerList.length === 0 &&
            !isLoading
          "
        >
          <span
            class="flex items-center justify-center text-sm font-normal text-gray-500"
          >
            {{ $t('global_search.no_results_found') }}
          </span>
        </div>
      </div>
    </sw-dropdown>
  </div>
</template>

<script>
import { SearchIcon } from '@vue-hero-icons/solid'
import { mapActions, mapGetters } from 'vuex'
import LoadingIcon from '../components/icon/LoadingIcon'
import _ from 'lodash'

export default {
  components: {
    SearchIcon,
    LoadingIcon,
  },
  data() {
    return {
      isShow: false,
      isLoading: false,
      name: '',
    }
  },

  computed: {
    ...mapGetters('search', ['getCustomerList', 'getUserList']),
  },

  created() {
    this.searchUsers()
  },

  methods: {
    ...mapActions('search', ['searchUsers']),

    throttledMethod: _.debounce(async function () {
      this.isLoading = true
      await this.searchUsers({ search: this.name }).then(() => {
        this.isShow = true
      })
      if (this.name === '') {
        this.isShow = false
      }
      this.isLoading = false
    }, 500),

    initGenerator(name) {
      if (name) {
        let nameSplit = name.split('')
        let initials =
          nameSplit[0].charAt(0).toUpperCase() +
          nameSplit[1].charAt(0).toUpperCase()
        return initials
      }
    },
  },
}
</script>

<style scoped>
.box::-webkit-scrollbar {
  width: 4px;
}

.box::-webkit-scrollbar-thumb {
  background-color: transparent;
  outline: 1px solid white;
  border-radius: 0.42rem !important;
}

.box::-webkit-scrollbar-thumb {
  background-color: #e4e6ef;
}
</style>
