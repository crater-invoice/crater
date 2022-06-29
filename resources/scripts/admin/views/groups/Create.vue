<template>
  <BasePage>
    <BasePageHeader :title="pageTitle">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />
        <BaseBreadcrumbItem :title="$tc('groups.group', 2)" to="/admin/groups" />
        <BaseBreadcrumbItem :title="pageTitle" to="#" active />
      </BaseBreadcrumb>
    </BasePageHeader>

    <ItemUnitModal />

    <form
      class="grid lg:grid-cols-2 mt-6"
      action="submit"
      @submit.prevent="submitGroup"
    >
      <BaseCard class="w-full">
        <BaseInputGrid layout="one-column">
          <BaseInputGroup
            :label="$t('groups.name')"
            :content-loading="isFetchingInitialData"
            required
            :error="
              v$.currentGroup.name.$error &&
              v$.currentGroup.name.$errors[0].$message
            "
          >
            <BaseInput
              v-model="groupStore.currentGroup.name"
              :content-loading="isFetchingInitialData"
              :invalid="v$.currentGroup.name.$error"
              @input="v$.currentGroup.name.$touch()"
            />
          </BaseInputGroup>

          <div>
            <BaseButton
              :content-loading="isFetchingInitialData"
              type="submit"
              :loading="isSaving"
            >
              <template #left="slotProps">
                <BaseIcon
                  v-if="!isSaving"
                  name="SaveIcon"
                  :class="slotProps.class"
                />
              </template>

              {{ isEdit ? $t('groups.update_group') : $t('groups.save_group') }}
            </BaseButton>
          </div>
        </BaseInputGrid>
      </BaseCard>
    </form>
  </BasePage>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import {
  required,
  minLength,
  helpers,
} from '@vuelidate/validators'
import useVuelidate from '@vuelidate/core'
import { useGroupStore } from '@/scripts/admin/stores/group'
import { useModalStore } from '@/scripts/stores/modal'
import ItemUnitModal from '@/scripts/admin/components/modal-components/ItemUnitModal.vue'
import { useUserStore } from '@/scripts/admin/stores/user'

const groupStore = useGroupStore()
const modalStore = useModalStore()
const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const isSaving = ref(false)

let isFetchingInitialData = ref(false)

groupStore.$reset()
loadData()

const isEdit = computed(() => route.name === 'groups.edit')

const pageTitle = computed(() =>
  isEdit.value ? t('groups.edit_group') : t('groups.new_group')
)

const rules = computed(() => {
  return {
    currentGroup: {
      name: {
        required: helpers.withMessage(t('validation.required'), required),
        minLength: helpers.withMessage(
          t('validation.name_min_length', { count: 3 }),
          minLength(3)
        ),
      },
    },
  }
})

const v$ = useVuelidate(rules, groupStore)

async function loadData() {
  isFetchingInitialData.value = true

  await groupStore.fetchGroups()
  if (isEdit.value) {
    let id = route.params.id
    await groupStore.fetchGroup(id)
  }

  isFetchingInitialData.value = false
}

async function submitGroup() {
  v$.value.currentGroup.$touch()

  if (v$.value.currentGroup.$invalid) {
    return false
  }

  isSaving.value = true

  try {
    let data = {
      id: route.params.id,
      ...groupStore.currentGroup,
    }

    const action = isEdit.value ? groupStore.updateGroup : groupStore.addGroup

    await action(data)
    isSaving.value = false
    router.push('/admin/groups')
    closeItemModal()
  } catch (err) {
    isSaving.value = false
    return
  }
  function closeItemModal() {
    modalStore.closeModal()
    setTimeout(() => {
      groupStore.resetCurrentGroup()
      modalStore.$reset()
      v$.value.$reset()
    }, 300)
  }
}
</script>
