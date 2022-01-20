<template>
  <BaseContentPlaceholders
    class="grid grid-cols-10 mt-8 bg-white rounded shadow"
  >
    <!-- Chart -->
    <div
      class="
        grid grid-cols-1
        col-span-10
        px-4
        py-5
        lg:col-span-7
        xl:col-span-8
        sm:p-8
      "
    >
      <div class="flex items-center justify-between mb-2 xl:mb-4">
        <BaseContentPlaceholdersText class="h-10 w-36" :lines="1" />
        <BaseContentPlaceholdersText class="h-10 w-36 !mt-0" :lines="1" />
      </div>
      <BaseContentPlaceholdersBox class="h-80 xl:h-72 sm:w-full" />
    </div>

    <!-- Chart Labels -->
    <div
      class="
        grid grid-cols-3
        col-span-10
        text-center
        border-t border-l border-gray-200 border-solid
        lg:border-t-0 lg:text-right lg:col-span-3
        xl:col-span-2
        lg:grid-cols-1
      "
    >
      <div
        class="
          flex flex-col
          items-center
          justify-center
          p-6
          lg:justify-end lg:items-end
        "
      >
        <BaseContentPlaceholdersText class="h-3 w-14 xl:h-4" :lines="1" />
        <BaseContentPlaceholdersText class="w-20 h-5 xl:h-6" :lines="1" />
      </div>
      <div
        class="
          flex flex-col
          items-center
          justify-center
          p-6
          lg:justify-end lg:items-end
        "
      >
        <BaseContentPlaceholdersText class="h-3 w-14 xl:h-4" :lines="1" />
        <BaseContentPlaceholdersText class="w-20 h-5 xl:h-6" :lines="1" />
      </div>
      <div
        class="
          flex flex-col
          items-center
          justify-center
          p-6
          lg:justify-end lg:items-end
        "
      >
        <BaseContentPlaceholdersText class="h-3 w-14 xl:h-4" :lines="1" />
        <BaseContentPlaceholdersText class="w-20 h-5 xl:h-6" :lines="1" />
      </div>
      <div
        class="
          flex flex-col
          items-center
          justify-center
          col-span-3
          p-6
          border-t border-gray-200 border-solid
          lg:justify-end lg:items-end lg:col-span-1
        "
      >
        <BaseContentPlaceholdersText class="h-3 w-14 xl:h-4" :lines="1" />
        <BaseContentPlaceholdersText class="w-20 h-5 xl:h-6" :lines="1" />
      </div>
    </div>
  </BaseContentPlaceholders>
</template>
