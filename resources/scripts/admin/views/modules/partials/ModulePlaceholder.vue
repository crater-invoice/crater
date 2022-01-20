<template>
  <BaseContentPlaceholders rounded>
    <BasePage class="bg-white">

      <!-- Breadcrumb-->
      <BaseContentPlaceholdersText class="mt-4 h-8 w-40" :lines="1"/>
      <BaseContentPlaceholdersText class="mt-4 h-8 w-56 mb-4" :lines="1"/>

      <!-- Product -->
      <div class="lg:grid lg:grid-rows-1 lg:grid-cols-7 lg:gap-x-8 lg:gap-y-10 xl:gap-x-16 mt-6">
      
        <!-- Product image -->
        <div class="lg:row-end-1 lg:col-span-4">
          <BaseContentPlaceholdersBox class="h-96 sm:w-full" rounded />
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
          <div>
            <h3 class="sr-only">Reviews</h3>
            <BaseContentPlaceholdersText class="w-32 h-8" :lines="1" />

            <p class="sr-only">4 out of 5 stars</p>
          </div>

          <!-- Module Name and Version -->
          <div class="flex flex-col-reverse">
            <div class="mt-4">
              <BaseContentPlaceholdersText
                class="w-48 xl:w-80 h-12"
                :lines="1"
              />
              <BaseContentPlaceholdersText
                class="w-64 xl:w-80 h-8 mt-2"
                :lines="1"
              />
            </div>
          </div>

          <!-- Module Description  -->
          <div>
            <BaseContentPlaceholdersText
              class="w-full h-24 my-10"
              :lines="1"
            />
          </div>

          <!-- Module Pricing -->
          <div>
            <BaseContentPlaceholdersText
              class="w-full h-24 mt-6 mb-6"
              :lines="1"
            />
          </div>

          <!-- Button Section  -->
          <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <BaseContentPlaceholdersText class="w-full h-14" :lines="1" />
          </div>

          <div class="mt-10"></div>

          <!-- HightLight  -->
          <div class="border-t border-gray-200 mt-10 pt-10">
            <div>
              <BaseContentPlaceholdersText class="w-24 h-6" :lines="1" />
              <BaseContentPlaceholdersText
                class="mt-4 w-full h-20"
                :lines="1"
              />
            </div>
          </div>

          <!-- Social Share  -->
          <div class="border-t border-gray-200 mt-10 pt-10">
            <BaseContentPlaceholdersText class="h-6 w-24" :lines="1" />
            <BaseContentPlaceholdersText class="h-10 w-32 mt-4" :lines="1" />
          </div>
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
          <BaseContentPlaceholdersBox class="h-96 sm:w-full" rounded />
        </div>
      </div>
    </BasePage>
  </BaseContentPlaceholders>
</template>
