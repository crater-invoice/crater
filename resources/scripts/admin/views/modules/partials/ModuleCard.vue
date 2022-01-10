<template>
  <div
    class="
      relative
      shadow-md
      border-2 border-gray-200 border-opacity-60
      rounded-lg
      cursor-pointer
      overflow-hidden
      h-100
    "
    @click="$router.push(`/admin/modules/${data.slug}`)"
  >
    <div
      v-if="data.purchased"
      class="absolute mt-5 px-6 w-full flex justify-end"
    >
      <label
        v-if="data.purchased"
        class="
          bg-white bg-opacity-75
          text-xs
          px-3
          py-1
          font-semibold
          tracking-wide
          rounded
        "
      >
        {{ $t('modules.purchased') }}
      </label>
      <label
        v-if="data.installed"
        class="
          ml-2
          bg-white bg-opacity-75
          text-xs
          px-3
          py-1
          font-semibold
          tracking-wide
          rounded
        "
      >
        <span v-if="data.update_available">
          {{ $t('modules.update_available') }}
        </span>
        <span v-else>
          {{ $t('modules.installed') }}
        </span>
      </label>
    </div>
    <img
      class="lg:h-64 md:h-48 w-full object-cover object-center"
      :src="data.cover"
      alt="cover"
    />
    <div class="px-6 py-5 flex flex-col bg-gray-50 flex-1 justify-between">
      <span
        class="
          text-lg
          sm:text-2xl
          font-medium
          whitespace-nowrap
          truncate
          text-primary-500
        "
      >
        {{ data.name }}
      </span>
      <div v-if="data.author_avatar" class="flex items-center mt-2">
        <img
          class="hidden h-10 w-10 rounded-full sm:inline-block mr-2"
          :src="
            data.author_avatar
              ? data.author_avatar
              : 'http://localhost:3000/img/default-avatar.jpg'
          "
          alt=""
        />
        <span>by</span>
        <span class="ml-2 text-base font-semibold truncate"
          >{{ data.author_name }}
        </span>
      </div>
      <base-text
        :text="data.short_description"
        class="pt-4 text-gray-500 h-16 line-clamp-2"
        :length="110"
      >
      </base-text>
      <div
        class="
          flex
          justify-between
          mt-4
          flex-col
          space-y-2
          sm:space-y-0 sm:flex-row
        "
      >
        <div><BaseRating :rating="averageRating" /></div>
        <div
          class="
            text-xl
            md:text-2xl
            font-semibold
            whitespace-nowrap
            text-primary-500
          "
        >
          $
          {{
            data.monthly_price
              ? data.monthly_price / 100
              : data.yearly_price / 100
          }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { computed, onMounted, ref, watch, reactive } from 'vue'

const { t } = useI18n()
const props = defineProps({
  data: {
    type: Object,
    default: null,
    required: true,
  },
})

let averageRating = computed(() => {
  return parseInt(props.data.average_rating)
})
</script>
