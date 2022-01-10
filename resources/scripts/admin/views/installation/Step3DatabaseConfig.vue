<template>
  <BaseWizardStep
    :title="$t('wizard.database.database')"
    :description="$t('wizard.database.desc')"
    step-container="w-full p-8 mb-8 bg-white border border-gray-200 border-solid rounded md:w-full"
  >
    <component
      :is="databaseData.database_connection"
      :config-data="databaseData"
      :is-saving="isSaving"
      @on-change-driver="getDatabaseConfig"
      @submit-data="next"
    />
  </BaseWizardStep>
</template>

<script>
import { ref, computed } from 'vue'
import Mysql from './database/MysqlDatabase.vue'
import Pgsql from './database/PgsqlDatabase.vue'
import Sqlite from './database/SqliteDatabase.vue'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useInstallationStore } from '@/scripts/admin/stores/installation'
import { useI18n } from 'vue-i18n'

export default {
  components: {
    Mysql,
    Pgsql,
    Sqlite,
  },

  emits: ['next'],

  setup(props, { emit }) {
    const database_connection = ref('mysql')
    const isSaving = ref(false)
    const { t } = useI18n()

    const notificationStore = useNotificationStore()
    const installationStore = useInstallationStore()

    const databaseData = computed(() => {
      return installationStore.currentDataBaseData
    })

    async function getDatabaseConfig(connection) {
      let params = {
        connection,
      }

      const res = await installationStore.fetchInstallationDatabase(params)

      if (res.data.success) {
        databaseData.value.database_connection =
          res.data.config.database_connection
      }

      if (connection === 'sqlite') {
        databaseData.value.database_name = res.data.config.database_name
      } else {
        databaseData.value.database_name = null
      }
    }

    async function next(databaseData) {
      isSaving.value = true

      try {
        let res = await installationStore.addInstallationDatabase(databaseData)
        isSaving.value = false

        if (res.data.success) {
          await installationStore.addInstallationFinish()

          emit('next', 3)

          notificationStore.showNotification({
            type: 'success',
            message: t('wizard.success.' + res.data.success),
          })

          return
        } else if (res.data.error) {
          if (res.data.requirement) {
            notificationStore.showNotification({
              type: 'error',
              message: t('wizard.errors.' + res.data.error, {
                version: res.data.requirement.minimum,
                name: databaseData.value.database_connection,
              }),
            })
            return
          }

          notificationStore.showNotification({
            type: 'error',
            message: t('wizard.errors.' + res.data.error),
          })
        } else if (res.data.errors) {
          notificationStore.showNotification({
            type: 'error',
            message: res.data.errors[0],
          })
        } else if (res.data.error_message) {
          notificationStore.showNotification({
            type: 'error',
            message: res.data.error_message,
          })
        }
      } catch (e) {
        notificationStore.showNotification({
          type: 'error',
          message: t('validation.something_went_wrong'),
        })
        isSaving.value = false
      } finally {
        isSaving.value = false
      }
    }

    return {
      databaseData,
      database_connection,
      isSaving,
      getDatabaseConfig,
      next,
    }
  },
}
</script>
