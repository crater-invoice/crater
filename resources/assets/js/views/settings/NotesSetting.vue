<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.customization.notes.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.customization.notes.description') }}
        </p>
      </div>

      <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button
          size="lg"
          variant="primary-outline"
          @click="openNoteSelectModal"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.customization.notes.add_note') }}
        </sw-button>
      </div>
    </div>

    <sw-table-component
      ref="table"
      variant="gray"
      :show-filter="false"
      :data="fetchData"
    >
      <sw-table-column
        :label="$t('settings.customization.notes.name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.notes.name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>
      <sw-table-column
        :label="$t('settings.customization.notes.type')"
        show="type"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.notes.type') }}</span>
          <span class="mt-6">{{ row.type }}</span>
        </template>
      </sw-table-column>
      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.action') }}</span>

          <sw-dropdown>
            <dot-icon slot="activator" class="h-5" />

            <sw-dropdown-item @click="editNote(row)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeNote(row.id)">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('notes', ['fetchNotes', 'deleteNote']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchNotes(data)

      return {
        data: response.data.notes.data,
        pagination: {
          totalPages: response.data.notes.last_page,
          currentPage: page,
          count: response.data.notes.count,
        },
      }
    },
    removeNote(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.notes.note_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteNote(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.notes.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.notes.already_in_use')
          )
        }
      })
    },

    editNote(data) {
      this.openModal({
        title: this.$t('settings.customization.notes.edit_note'),
        componentName: 'NoteSelectModal',
        id: data.id,
        data: data,
        variant: 'lg',
        refreshData: this.$refs.table.refresh,
      })
    },

    openNoteSelectModal() {
      this.openModal({
        title: this.$t('settings.customization.notes.add_note'),
        componentName: 'NoteSelectModal',
        variant: 'lg',
        refreshData: this.$refs.table.refresh,
      })
    },
  },
}
</script>
