<template>
  <ModulePlaceholder v-if="isFetchingInitialData" />
  <BasePage v-else class="bg-white">
    <BasePageHeader :title="moduleData.name">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem :title="$t('general.home')" to="dashboard" />
        <BaseBreadcrumbItem :title="$t('modules.title')" to="/admin/modules" />
        <BaseBreadcrumbItem :title="moduleData.name" to="#" active />
      </BaseBreadcrumb>
    </BasePageHeader>
    <!-- Main Content -->
    <div
      class="
        lg:grid lg:grid-rows-1 lg:grid-cols-7 lg:gap-x-8 lg:gap-y-10
        xl:gap-x-16
        mt-6
      "
    >
      <!-- Product image -->
      <div class="lg:row-end-1 lg:col-span-4">
        <div class="flex flex-col-reverse">
          <div
            class="hidden mt-6 w-full max-w-2xl mx-auto sm:block lg:max-w-none"
          >
            <div
              class="grid grid-cols-3 xl:grid-cols-4 gap-6"
              aria-orientation="horizontal"
              role="tablist"
            >
              <button
                v-if="thumbnail && videoUrl"
                :class="[
                  'relative  md:h-24 lg:h-36 rounded hover:bg-gray-50',
                  {
                    'outline-none ring ring-offset-1 ring-primary-500':
                      displayVideo,
                  },
                ]"
                type="button"
                @click="setDisplayVideo"
              >
                <span class="absolute inset-0 rounded-md overflow-hidden">
                  <img
                    :src="thumbnail"
                    alt=""
                    class="w-full h-full object-center object-cover"
                  />
                </span>
                <span
                  class="
                    ring-transparent
                    absolute
                    inset-0
                    rounded-md
                    ring-2 ring-offset-2
                    pointer-events-none
                  "
                  aria-hidden="true"
                ></span>
              </button>

              <button
                v-for="(screenshot, ssIndx) in displayImages"
                id="tabs-1-tab-1"
                :key="ssIndx"
                :class="[
                  'relative  md:h-24 lg:h-36 rounded hover:bg-gray-50',
                  {
                    'outline-none ring ring-offset-1 ring-primary-500':
                      displayImage === screenshot.url,
                  },
                ]"
                type="button"
                @click="setDisplayImage(screenshot.url)"
              >
                <span class="absolute inset-0 rounded-md overflow-hidden">
                  <img
                    :src="screenshot.url"
                    alt=""
                    class="w-full h-full object-center object-cover"
                  />
                </span>
                <span
                  class="
                    ring-transparent
                    absolute
                    inset-0
                    rounded-md
                    ring-2 ring-offset-2
                    pointer-events-none
                  "
                  aria-hidden="true"
                ></span>
              </button>
            </div>
          </div>

          <div v-if="displayVideo" class="aspect-w-4 aspect-h-3">
            <iframe
              :src="videoUrl"
              class="sm:rounded-lg"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            >
            </iframe>
          </div>

          <div
            v-else
            class="aspect-w-4 aspect-h-3 rounded-lg bg-gray-100 overflow-hidden"
          >
            <img
              :src="displayImage"
              alt="Module Images"
              class="w-full h-full object-center object-cover sm:rounded-lg"
            />
          </div>
        </div>
      </div>

      <!-- Product details -->
      <div
        class="
          max-w-2xl
          mx-auto
          mt-10
          lg:max-w-none lg:mt-0 lg:row-end-2 lg:row-span-2 lg:col-span-3
          w-full
        "
      >
        <!-- Average Rating -->

        <h3 class="sr-only">Reviews</h3>

        <div class="flex items-center">
          <BaseRating :rating="averageRating" />
        </div>
        <p class="sr-only">4 out of 5 stars</p>

        <!-- Module Name and Version -->
        <div class="flex flex-col-reverse">
          <div class="mt-4">
            <h1
              class="
                text-2xl
                font-extrabold
                tracking-tight
                text-gray-900
                sm:text-3xl
              "
            >
              {{ moduleData.name }}
            </h1>

            <h2 id="information-heading" class="sr-only">
              Product information
            </h2>

            <p
              v-if="moduleData.latest_module_version"
              class="text-sm text-gray-500 mt-2"
            >
              {{ $t('modules.version') }}
              {{ moduleVersion }} ({{ $t('modules.last_updated') }}
              {{ updatedAt }})
            </p>
          </div>
        </div>

        <!-- Module Description  -->
        <div
          class="prose prose-sm max-w-none text-gray-500 text-sm my-10"
          v-html="moduleData.long_description"
        />

        <!-- Module Pricing -->
        <div v-if="!moduleData.purchased">
          <RadioGroup v-model="selectedPlan">
            <RadioGroupLabel class="sr-only"> Pricing plans </RadioGroupLabel>
            <div class="relative bg-white rounded-md -space-y-px">
              <RadioGroupOption
                v-for="(size, sizeIdx) in modulePrice"
                :key="size.name"
                v-slot="{ checked, active }"
                as="template"
                :value="size"
              >
                <div
                  :class="[
                    sizeIdx === 0 ? 'rounded-tl-md rounded-tr-md' : '',
                    sizeIdx === modulePrice.length - 1
                      ? 'rounded-bl-md rounded-br-md'
                      : '',
                    checked
                      ? 'bg-primary-50 border-primary-200 z-10'
                      : 'border-gray-200',
                    'relative border p-4 flex flex-col cursor-pointer md:pl-4 md:pr-6 md:grid md:grid-cols-2 focus:outline-none',
                  ]"
                >
                  <div class="flex items-center text-sm">
                    <span
                      :class="[
                        checked
                          ? 'bg-primary-600 border-transparent'
                          : 'bg-white border-gray-300',
                        active ? 'ring-2 ring-offset-2 ring-primary-500' : '',
                        'h-4 w-4 rounded-full border flex items-center justify-center',
                      ]"
                      aria-hidden="true"
                    >
                      <span class="rounded-full bg-white w-1.5 h-1.5" />
                    </span>
                    <RadioGroupLabel
                      as="span"
                      :class="[
                        checked ? 'text-primary-900' : 'text-gray-900',
                        'ml-3 font-medium',
                      ]"
                    >
                      {{ size.name }}
                    </RadioGroupLabel>
                  </div>
                  <RadioGroupDescription
                    class="ml-6 pl-1 text-base md:ml-0 md:pl-0 md:text-center"
                  >
                    <span
                      :class="[
                        checked ? 'text-primary-900' : 'text-gray-900',
                        'font-medium',
                      ]"
                    >
                      $ {{ size.price }}
                    </span>
                  </RadioGroupDescription>
                </div>
              </RadioGroupOption>
            </div>
          </RadioGroup>
        </div>

        <!-- Button Section  -->

        <!-- If Module is not purchased -->
        <a
          v-if="!moduleData.purchased"
          :href="`${globalStore.config.base_url}/modules/${moduleData.slug}`"
          target="_blank"
          class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2"
        >
          <BaseButton
            size="xl"
            class="items-center flex justify-center text-base mt-10"
          >
            <BaseIcon name="ShoppingCartIcon" class="mr-2" />
            {{ $t('modules.buy_now') }}
          </BaseButton>
        </a>

        <!-- When module is Purchased -->
        <div v-else>
          <!-- Module not installed -->
          <div
            v-if="!moduleData.installed"
            class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2"
          >
            <BaseButton
              v-if="moduleData.latest_module_version"
              size="xl"
              variant="primary-outline"
              outline
              :loading="isInstalling"
              :disabled="isInstalling"
              class="mr-4 flex items-center justify-center text-base"
              @click="installModule()"
            >
              <BaseIcon v-if="!isInstalling" name="DownloadIcon" class="mr-2" />
              {{ $t('modules.install') }}
            </BaseButton>
          </div>

          <!-- Module already installed -->
          <div v-else-if="isModuleInstalled">
            <!-- When new module version is available -->

            <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
              <BaseButton
                v-if="moduleData.update_available"
                variant="primary"
                size="xl"
                :loading="isInstalling"
                :disabled="isInstalling"
                class="mr-4 flex items-center justify-center text-base"
                @click="installModule()"
              >
                {{ $t('modules.update_to') }}
                <span class="ml-2">{{ moduleData.latest_module_version }}</span>
              </BaseButton>

              <BaseButton
                v-if="moduleData.enabled"
                variant="danger"
                size="xl"
                :loading="isDisabling"
                :disabled="isDisabling"
                class="mr-4 flex items-center justify-center text-base"
                @click="disableModule"
              >
                <BaseIcon v-if="!isDisabling" name="BanIcon" class="mr-2" />
                {{ $t('modules.disable') }}
              </BaseButton>
              <BaseButton
                v-else
                variant="primary-outline"
                size="xl"
                :loading="isEnabling"
                :disabled="isEnabling"
                class="mr-4 flex items-center justify-center text-base"
                @click="enableModule"
              >
                <BaseIcon v-if="!isEnabling" name="CheckIcon" class="mr-2" />
                {{ $t('modules.enable') }}
              </BaseButton>
            </div>
          </div>
        </div>

        <div class="mt-10"></div>

        <!-- HighLights  -->
        <div class="border-t border-gray-200 mt-10 pt-10">
          <h3 class="text-sm font-medium text-gray-900">
            {{ $t('modules.what_you_get') }}
          </h3>
          <div class="mt-4 prose prose-sm max-w-none text-gray-500">
            <div
              class="prose prose-sm max-w-none text-gray-500 text-sm"
              v-html="moduleData.highlights"
            />
          </div>
        </div>
        <div class="border-t border-gray-200 mt-10 pt-10">
          <div
            v-for="(link, key) in moduleData.links"
            :key="key"
            class="mb-4 last:mb-0 flex"
          >
            <BaseIcon :name="link.icon" class="mr-4" />
            <a :href="link.link" class="text-primary-500" target="_blank">
              {{ link.label }}
            </a>
          </div>
        </div>
        <!-- Installation Steps  -->
        <div v-if="isInstalling" class="border-t border-gray-200 mt-10 pt-10">
          <ul class="w-full p-0 list-none">
            <li
              v-for="step in installationSteps"
              :key="step.stepUrl"
              class="
                flex
                justify-between
                w-full
                py-3
                border-b border-gray-200 border-solid
                last:border-b-0
              "
            >
              <p class="m-0 text-sm leading-8">
                {{ $t(step.translationKey) }}
              </p>
              <div class="flex flex-row items-center">
                <span v-if="step.time" class="mr-3 text-xs text-gray-500">
                  {{ step.time }}
                </span>
                <span
                  :class="statusClass(step)"
                  class="block py-1 text-sm text-center uppercase rounded-full"
                  style="width: 88px"
                >
                  {{ getStatus(step) }}
                </span>
              </div>
            </li>
          </ul>
        </div>

        <!-- Social Share  -->
        <!-- <div class="border-t border-gray-200 mt-10 pt-10">
          <h3 class="text-sm font-medium text-gray-900">Share</h3>
          <ul role="list" class="flex items-center space-x-6 mt-4">
            <li>
              <a
                href="#"
                class="
                  flex
                  items-center
                  justify-center
                  w-6
                  h-6
                  text-gray-400
                  hover:text-gray-500
                "
              >
                <span class="sr-only">Share on Facebook</span>
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
            </li>
            <li>
              <a
                href="#"
                class="
                  flex
                  items-center
                  justify-center
                  w-6
                  h-6
                  text-gray-400
                  hover:text-gray-500
                "
              >
                <span class="sr-only">Share on Instagram</span>
                <svg
                  class="w-6 h-6"
                  fill="currentColor"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </a>
            </li>
            <li>
              <a
                href="#"
                class="
                  flex
                  items-center
                  justify-center
                  w-6
                  h-6
                  text-gray-400
                  hover:text-gray-500
                "
              >
                <span class="sr-only">Share on Twitter</span>
                <svg
                  class="w-5 h-5"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  aria-hidden="true"
                >
                  <path
                    d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"
                  />
                </svg>
              </a>
            </li>
          </ul>
        </div> -->
      </div>

      <div
        class="
          w-full
          max-w-2xl
          mx-auto
          mt-16
          lg:max-w-none lg:mt-0 lg:col-span-4
        "
      >
        <TabGroup as="div">
          <TabList class="-mb-px flex space-x-8 border-b border-gray-200">
            <Tab v-slot="{ selected }" as="template">
              <button
                :class="[
                  selected
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-700 hover:text-gray-800 hover:border-gray-300',
                  'whitespace-nowrap py-6 border-b-2 font-medium text-sm',
                ]"
              >
                {{ $t('modules.customer_reviews') }}
              </button>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <button
                :class="[
                  selected
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-700 hover:text-gray-800 hover:border-gray-300',
                  'whitespace-nowrap py-6 border-b-2 font-medium text-sm',
                ]"
              >
                {{ $t('modules.faq') }}
              </button>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <button
                :class="[
                  selected
                    ? 'border-primary-600 text-primary-600'
                    : 'border-transparent text-gray-700 hover:text-gray-800 hover:border-gray-300',
                  'whitespace-nowrap py-6 border-b-2 font-medium text-sm',
                ]"
              >
                {{ $t('modules.license') }}
              </button>
            </Tab>
          </TabList>
          <TabPanels as="template">
            <!-- Customer Reviews  -->
            <TabPanel class="-mb-10">
              <h3 class="sr-only">Customer Reviews</h3>
              <div v-if="moduleData.reviews.length">
                <div
                  v-for="(review, reviewIdx) in moduleData.reviews"
                  :key="reviewIdx"
                  class="flex text-sm text-gray-500 space-x-4"
                >
                  <div class="flex-none py-10">
                    <span
                      class="
                        inline-flex
                        items-center
                        justify-center
                        h-12
                        w-12
                        rounded-full
                        bg-gray-500
                      "
                    >
                      <span
                        class="
                          text-lg
                          font-medium
                          leading-none
                          text-white
                          uppercase
                        "
                        >{{ review.customer.name[0] }}</span
                      >
                    </span>
                  </div>
                  <div
                    :class="[
                      reviewIdx === 0 ? '' : 'border-t border-gray-200',
                      'py-10',
                    ]"
                  >
                    <h3 class="font-medium text-gray-900">
                      {{ review.customer.name }}
                    </h3>
                    <p>
                      {{ moment(review.created_at).format('MMMM Do YYYY') }}
                    </p>

                    <div class="flex items-center mt-4">
                      <BaseRating :rating="review.rating" />
                    </div>

                    <div
                      class="mt-4 prose prose-sm max-w-none text-gray-500"
                      v-html="review.feedback"
                    />
                  </div>
                </div>
              </div>
              <div v-else class="flex w-full items-center justify-center">
                <p class="text-gray-500 mt-10 text-sm">
                  {{ $t('modules.no_reviews_found') }}
                </p>
              </div>
            </TabPanel>

            <!-- FAQs  -->
            <TabPanel as="dl" class="text-sm text-gray-500">
              <h3 class="sr-only">Frequently Asked Questions</h3>

              <template v-for="faq in moduleData.faq" :key="faq.question">
                <dt class="mt-10 font-medium text-gray-900">
                  {{ faq.question }}
                </dt>
                <dd class="mt-2 prose prose-sm max-w-none text-gray-500">
                  <p>{{ faq.answer }}</p>
                </dd>
              </template>
            </TabPanel>

            <!-- License  -->
            <TabPanel class="pt-10">
              <h3 class="sr-only">License</h3>

              <div
                class="prose prose-sm max-w-none text-gray-500"
                v-html="moduleData.license"
              />
            </TabPanel>
          </TabPanels>
        </TabGroup>
      </div>
    </div>

    <!-- Other Modules -->
    <div
      v-if="otherModules && otherModules.length"
      class="mt-24 sm:mt-32 lg:max-w-none"
    >
      <div class="flex items-center justify-between space-x-4">
        <h2 class="text-lg font-medium text-gray-900">
          {{ $t('modules.other_modules') }}
        </h2>
        <a
          href="/admin/modules"
          class="
            whitespace-nowrap
            text-sm
            font-medium
            text-primary-600
            hover:text-primary-500
          "
          >{{ $t('modules.view_all')
          }}<span aria-hidden="true"> &rarr;</span></a
        >
      </div>
      <div
        class="
          mt-6
          grid grid-cols-1
          gap-x-8 gap-y-8
          sm:grid-cols-2 sm:gap-y-10
          lg:grid-cols-4
        "
      >
        <div v-for="(other, moduleIdx) in otherModules" :key="moduleIdx">
          <RecentModuleCard :data="other" />
        </div>
      </div>
    </div>

    <div class="p-6"></div>
  </BasePage>
</template>

<script setup>
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/vue'
import {
  RadioGroup,
  RadioGroupDescription,
  RadioGroupLabel,
  RadioGroupOption,
} from '@headlessui/vue'
import { useModuleStore } from '@/scripts/admin/stores/module'
import { computed, onMounted, ref, watch, reactive } from 'vue'
import { required, minLength, maxLength, helpers } from '@vuelidate/validators'
import { useVuelidate } from '@vuelidate/core'
import { useRoute } from 'vue-router'
import { useDialogStore } from '@/scripts/stores/dialog'
import { useI18n } from 'vue-i18n'
import moment from 'moment'
import axios from 'axios'
import ModulePlaceholder from './partials/ModulePlaceholder.vue'
import RecentModuleCard from './partials/RecentModuleCard.vue'
import { useNotificationStore } from '@/scripts/stores/notification'
import { useGlobalStore } from '@/scripts/admin/stores/global'
const globalStore = useGlobalStore()

const moduleStore = useModuleStore()
const notificationStore = useNotificationStore()
const dialogStore = useDialogStore()

const route = useRoute()
const { t } = useI18n()
let isInstalling = ref(false)
let isFetchingInitialData = ref(true)
let displayImage = ref('')
let isEnabling = ref(false)
let isDisabling = ref(false)
let isUpdating = ref(false)

loadData()

watch(
  () => route.params.slug,
  async (newSlug) => {
    loadData()
  }
)

const moduleData = computed(() => {
  return moduleStore.currentModule.data
})

const modulePrice = computed(() => {
  let priceList = []

  let monthlyPrice = reactive({
    name: t('modules.monthly'),
    price: moduleData?.value?.monthly_price / 100,
  })

  let yearlyPrice = reactive({
    name: t('modules.yearly'),
    price: moduleData?.value?.yearly_price / 100,
  })

  if (typeYearly.value) {
    priceList.push(yearlyPrice)
  } else if (typeMonthly.value) {
    priceList.push(monthlyPrice)
  } else {
    priceList.push(monthlyPrice)
    priceList.push(yearlyPrice)
  }

  return priceList
})

const typeYearly = computed(() => {
  if (moduleData.value) {
    return moduleData.value.type === 'YEARLY'
  }

  return false
})

const typeMonthly = computed(() => {
  if (moduleData.value) {
    return moduleData.value.type === 'MONTHLY'
  }

  return false
})

const isModuleInstalled = computed(() => {
  if (moduleData.value.installed && moduleData.value.latest_module_version) {
    return true
  }
  return false
})

const otherModules = computed(() => {
  return moduleStore.currentModule.meta.modules
})

let updatedAt = computed(() => {
  let latest = ref(moduleData.value.latest_module_version_updated_at)
  let installed = ref(moduleData.value.installed_module_version_updated_at)

  const date = installed.value ? installed.value : latest.value

  return moment(date).format('MMMM Do YYYY')
})

let moduleVersion = computed(() => {
  let latest = ref(moduleData.value.latest_module_version)
  let installed = ref(moduleData.value.installed_module_version)

  let data = installed.value ? installed.value : latest.value

  return data
})

let averageRating = computed(() => {
  return parseInt(moduleData.value.average_rating)
})

const displayImages = computed(() => {
  let images = reactive([])

  let cover = reactive({
    id: null,
    url: moduleData.value.cover,
  })

  images.push(cover)

  if (moduleData.value.screenshots) {
    moduleData.value.screenshots.forEach((image) => {
      images.push(image)
    })
  }

  return images
})

const displayVideo = ref(false)

const thumbnail = ref(null)

const videoUrl = ref(null)

const selectedPlan = ref(modulePrice.value[0])

const installationSteps = reactive([
  {
    translationKey: 'modules.download_zip_file',
    stepUrl: '/api/v1/modules/download',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'modules.unzipping_package',
    stepUrl: '/api/v1/modules/unzip',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'modules.copying_files',
    stepUrl: '/api/v1/modules/copy',
    time: null,
    started: false,
    completed: false,
  },
  {
    translationKey: 'modules.completing_installation',
    stepUrl: '/api/v1/modules/complete',
    time: null,
    started: false,
    completed: false,
  },
])

async function installModule() {
  let path = null

  for (let index = 0; index < installationSteps.length; index++) {
    let currentStep = installationSteps[index]

    try {
      isInstalling.value = true
      currentStep.started = true
      let updateParams = {
        version: moduleData.value.latest_module_version,
        path: path || null,
        module: moduleData.value.module_name,
      }

      let requestResponse = await axios.post(currentStep.stepUrl, updateParams)

      currentStep.completed = true
      if (requestResponse.data) {
        path = requestResponse.data.path
      }

      if (!requestResponse.data.success) {
        let displayMsg = ref('')

        if (
          requestResponse.data.message === 'crater_version_is_not_supported'
        ) {
          displayMsg.value = t('modules.version_not_supported', {
            version: requestResponse.data.min_crater_version,
          })
        } else {
          displayMsg.value = getErrorMessage(requestResponse.data.message)
        }

        notificationStore.showNotification({
          type: 'error',
          message: displayMsg.value,
        })

        isInstalling.value = false
        currentStep.started = false
        currentStep.completed = true
        return false
      }
      if (currentStep.translationKey == 'modules.completing_installation') {
        isInstalling.value = false
        notificationStore.showNotification({
          type: 'success',
          message: t('modules.install_success'),
        })

        setTimeout(() => {
          location.reload()
        }, 1500)
      }
    } catch (error) {
      isInstalling.value = false
      currentStep.started = false
      currentStep.completed = true
      return false
    }
  }
}

function getErrorMessage(message) {
  let msg = ref('')

  switch (message) {
    case 'module_not_found':
      msg = t('modules.module_not_found')
      break

    case 'module_not_purchased':
      msg = t('modules.module_not_purchased')
      break

    case 'version_not_supported':
      msg = t('modules.version_not_supported')
      break

    default:
      msg = message
      break
  }

  return msg
}

async function loadData() {
  if (!route.params.slug) {
    return
  }

  isFetchingInitialData.value = true
  await moduleStore.fetchModule(route.params.slug).then((response) => {
    selectedPlan.value = modulePrice.value[0]

    videoUrl.value = moduleData.value.video_link
    thumbnail.value = moduleData.value.video_thumbnail

    if (videoUrl.value) {
      setDisplayVideo()
      isFetchingInitialData.value = false
      return
    }
    displayImage.value = moduleData.value.cover
    isFetchingInitialData.value = false
    return
  })
}

function statusClass(step) {
  const status = getStatus(step)

  switch (status) {
    case 'pending':
      return 'text-primary-800 bg-gray-200'
    case 'finished':
      return 'text-teal-500 bg-teal-100'
    case 'running':
      return 'text-blue-400 bg-blue-100'
    case 'error':
      return 'text-danger bg-red-200'
    default:
      return ''
  }
}

function disableModule() {
  dialogStore
    .openDialog({
      title: t('general.are_you_sure'),
      message: t('modules.disable_warning'),
      yesLabel: t('general.ok'),
      noLabel: t('general.cancel'),
      variant: 'danger',
      hideNoButton: false,
      size: 'lg',
    })
    .then(async (res) => {
      if (res) {
        isDisabling.value = true
        await moduleStore
          .disableModule(moduleData.value.module_name)
          .then((res) => {
            if (res.data.success) {
              moduleData.value.enabled = 0
              isDisabling.value = false

              setTimeout(() => {
                location.reload()
              }, 1500)
              return
            }
          })
        isDisabling.value = false
        return
      }
    })
}

async function enableModule() {
  isEnabling.value = true

  await moduleStore.enableModule(moduleData.value.module_name).then((res) => {
    if (res.data.success) {
      moduleData.value.enabled = 1

      setTimeout(() => {
        location.reload()
      }, 1500)
    }
    isEnabling.value = false
    return
  })
  isEnabling.value = false
  return
}

function getStatus(step) {
  if (step.started && step.completed) {
    return 'finished'
  } else if (step.started && !step.completed) {
    return 'running'
  } else if (!step.started && !step.completed) {
    return 'pending'
  } else {
    return 'error'
  }
}

function setDisplayImage(url) {
  displayVideo.value = false
  displayImage.value = url
}

function setDisplayVideo() {
  displayVideo.value = true
  displayImage.value = null
}
</script>
