<template>
  <BaseContentPlaceholders class="grid grid-cols-12">
    <div class="col-span-12 xl:col-span-9 xxl:col-span-10">
      <div class="flex justify-between mt-1 mb-6">
        <BaseContentPlaceholdersText class="h-10 w-36" :lines="1" />
        <BaseContentPlaceholdersText class="h-10 w-40 !mt-0" :lines="1" />
      </div>
      <BaseContentPlaceholdersBox class="h-80 xl:h-72 sm:w-full" />
    </div>

    <div
      class="
        grid
        col-span-12
        mt-6
        text-center
        xl:mt-0
        sm:grid-cols-4
        xl:text-right xl:col-span-3 xl:grid-cols-1
        xxl:col-span-2
      "
    >
      <div
        class="
          flex flex-col
          items-center
          justify-center
          px-6
          py-2
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
          px-6
          py-2
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
          px-6
          py-2
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
          px-6
          py-2
          lg:justify-end lg:items-end
        "
      >
        <BaseContentPlaceholdersText class="h-3 w-14 xl:h-4" :lines="1" />
        <BaseContentPlaceholdersText class="w-20 h-5 xl:h-6" :lines="1" />
      </div>
    </div>
  </BaseContentPlaceholders>
</template>
