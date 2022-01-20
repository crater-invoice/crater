<template>
  <BaseModal :show="modalActive" @close="closeRolesModal">
    <template #header>
      <div class="flex justify-between w-full">
        {{ modalStore.title }}

        <BaseIcon
          name="XIcon"
          class="w-6 h-6 text-gray-500 cursor-pointer"
          @click="closeRolesModal"
        />
      </div>
    </template>

    <form @submit.prevent="submitRoleData">
      <div class="px-4 md:px-8 py-4 md:py-6">
        <BaseInputGroup
          :label="$t('settings.roles.name')"
          class="mt-3"
          :error="v$.name.$error && v$.name.$errors[0].$message"
          required
          :content-loading="isFetchingInitialData"
        >
          <BaseInput
            v-model="roleStore.currentRole.name"
            :invalid="v$.name.$error"
            type="text"
            :content-loading="isFetchingInitialData"
            @input="v$.name.$touch()"
          />
        </BaseInputGroup>
      </div>
      <div class="flex justify-between">
        <h6
          class="
            text-sm
            not-italic
            font-medium
            text-gray-800
            px-4
            md:px-8
            py-1.5
          "
        >
          {{ $tc('settings.roles.permission', 2) }}
          <span class="text-sm text-red-500"> *</span>
        </h6>
        <div
          class="
            text-sm
            not-italic
            font-medium
            text-gray-300
            px-4
            md:px-8
            py-1.5
          "
        >
          <a
            class="cursor-pointer text-primary-400"
            @click="setSelectAll(true)"
          >
            {{ $t('settings.roles.select_all') }}
          </a>
          /
          <a
            class="cursor-pointer text-primary-400"
            @click="setSelectAll(false)"
          >
            {{ $t('settings.roles.none') }}
          </a>
        </div>
      </div>

      <div class="border-t border-gray-200 py-3">
        <div
          class="
            grid grid-cols-1
            sm:grid-cols-2
            md:grid-cols-3
            lg:grid-cols-4
            gap-4
            px-8
            sm:px-8
          "
        >
          <div
            v-for="(abilityGroup, gIndex) in roleStore.abilitiesList"
            :key="gIndex"
            class="flex flex-col space-y-1"
          >
            <p class="text-sm text-gray-500 border-b border-gray-200 pb-1 mb-2">
              {{ gIndex }}
            </p>
            <div
              v-for="(ability, index) in abilityGroup"
              :key="index"
              class="flex"
            >
              <BaseCheckbox
                v-model="roleStore.currentRole.abilities"
                :set-initial-value="true"
                variant="primary"
                :disabled="ability.disabled"
                :label="ability.name"
                :value="ability"
                @update:modelValue="onUpdateAbility(ability)"
              />
            </div>
          </div>
          <span
            v-if="v$.abilities.$error"
            class="block mt-0.5 text-sm text-red-500"
          >
            {{ v$.abilities.$errors[0].$message }}
          </span>
        </div>
      </div>
      <div
        class="
          z-0
          flex
          justify-end
          p-4
          border-t border-solid border--200 border-modal-bg
        "
      >
        <BaseButton
          class="mr-3 text-sm"
          variant="primary-outline"
          type="button"
          @click="closeRolesModal"
        >
          {{ $t('general.cancel') }}
        </BaseButton>
        <BaseButton
          :loading="isSaving"
          :disabled="isSaving"
          variant="primary"
          type="submit"
        >
          <template #left="slotProps">
            <BaseIcon name="SaveIcon" :class="slotProps.class" />
          </template>
          {{ !roleStore.isEdit ? $t('general.save') : $t('general.update') }}
        </BaseButton>
      </div>
    </form>
  </BaseModal>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { required, minLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useRoleStore } from '@/scripts/admin/stores/role'
import { useModalStore } from '@/scripts/stores/modal'

const modalStore = useModalStore()
const roleStore = useRoleStore()
const { t } = useI18n()

let isSaving = ref(false)
let isFetchingInitialData = ref(false)

const modalActive = computed(() => {
  return modalStore.active && modalStore.componentName === 'RolesModal'
})

const rules = computed(() => {
  return {
    name: {
      required: helpers.withMessage(t('validation.required'), required),
      minLength: helpers.withMessage(
        t('validation.name_min_length', { count: 3 }),
        minLength(3)
      ),
    },
    abilities: {
      required: helpers.withMessage(
        t('validation.at_least_one_ability'),
        required
      ),
    },
  }
})

const v$ = useVuelidate(
  rules,
  computed(() => roleStore.currentRole)
)

async function submitRoleData() {
  v$.value.$touch()

  if (v$.value.$invalid) {
    return true
  }
  try {
    const action = roleStore.isEdit ? roleStore.updateRole : roleStore.addRole
    isSaving.value = true
    await action(roleStore.currentRole)
    isSaving.value = false
    modalStore.refreshData ? modalStore.refreshData() : ''
    closeRolesModal()
  } catch (error) {
    isSaving.value = false
    return true
  }
}

function onUpdateAbility(currentAbility) {
  const fd = roleStore.currentRole.abilities.find(
    (_abl) => _abl.ability === currentAbility.ability
  )

  if (!fd && currentAbility?.depends_on?.length) {
    enableAbilities(currentAbility)
    return
  }

  currentAbility?.depends_on?.forEach((_d) => {
    Object.keys(roleStore.abilitiesList).forEach((group) => {
      roleStore.abilitiesList[group].forEach((_a) => {
        if (_d === _a.ability) {
          _a.disabled = true

          let found = roleStore.currentRole.abilities.find(
            (_af) => _af.ability === _d
          )

          if (!found) {
            roleStore.currentRole.abilities.push(_a)
          }
        }
      })
    })
  })
}

function setSelectAll(checked) {
  let dependList = []
  Object.keys(roleStore.abilitiesList).forEach((group) => {
    roleStore.abilitiesList[group].forEach((_a) => {
      _a?.depends_on && (dependList = [...dependList, ..._a.depends_on])
    })
  })

  Object.keys(roleStore.abilitiesList).forEach((group) => {
    roleStore.abilitiesList[group].forEach((_a) => {
      if (dependList.includes(_a.ability)) {
        checked ? (_a.disabled = true) : (_a.disabled = false)
      }
      roleStore.currentRole.abilities.push(_a)
    })
  })

  if (!checked) roleStore.currentRole.abilities = []
}

function enableAbilities(ability) {
  ability.depends_on.forEach((_d) => {
    Object.keys(roleStore.abilitiesList).forEach((group) => {
      roleStore.abilitiesList[group].forEach((_a) => {
        // CHECK IF EXISTS IN CURRENT ROLE ABILITIES
        let found = roleStore.currentRole.abilities.find((_r) =>
          _r.depends_on?.includes(_a.ability)
        )

        if (_d === _a.ability && !found) {
          _a.disabled = false
        }
      })
    })
  })
}

function closeRolesModal() {
  modalStore.closeModal()

  setTimeout(() => {
    roleStore.currentRole = {
      id: null,
      name: '',
      abilities: [],
    }

    // Enable all disabled ability
    Object.keys(roleStore.abilitiesList).forEach((group) => {
      roleStore.abilitiesList[group].forEach((_a) => {
        _a.disabled = false
      })
    })

    v$.value.$reset()
  }, 300)
}
</script>
