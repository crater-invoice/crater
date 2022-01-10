<template>
  <BaseContentPlaceholders>
    <div
      class="
        shadow-md
        border-2 border-gray-200 border-opacity-60
        rounded-lg
        cursor-pointer
        overflow-hidden
        h-100
      "
    >
      <BaseContentPlaceholdersBox class="h-48 lg:h-64 md:h-48 w-full" rounded />
      <div class="px-6 py-5 flex flex-col bg-gray-50 flex-1 justify-between">
        <BaseContentPlaceholdersText class="w-32 h-8" :lines="1" rounded />
        <div class="flex items-center mt-2">
          <BaseContentPlaceholdersBox
            class="h-10 w-10 rounded-full sm:inline-block mr-2"
          />
          <div>
            <BaseContentPlaceholdersText
              class="w-32 h-8 ml-2"
              :lines="1"
              rounded
            />
          </div>
        </div>
        <BaseContentPlaceholdersText
          class="pt-4 w-full h-16"
          :lines="1"
          rounded
        />
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
          <BaseContentPlaceholdersText class="w-32 h-8" :lines="1" rounded />
          <BaseContentPlaceholdersText class="w-32 h-8" :lines="1" rounded />
        </div>
      </div>
    </div>
  </BaseContentPlaceholders>
</template>