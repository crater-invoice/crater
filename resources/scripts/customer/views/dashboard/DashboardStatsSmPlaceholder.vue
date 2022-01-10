<template>
  <BaseContentPlaceholders
    :rounded="true"
    class="
      relative
      flex
      justify-between
      w-full
      p-3
      bg-white
      rounded
      shadow
      lg:col-span-2
      xl:p-4
    "
  >
    <div>
      <BaseContentPlaceholdersText
        class="w-12 h-5 -mb-1 xl:mb-6 xl:h-7"
        :lines="1"
      />
      <BaseContentPlaceholdersText class="w-20 h-3 xl:h-4" :lines="1" />
    </div>
    <div class="flex items-center">
      <BaseContentPlaceholdersBox
        :circle="true"
        class="w-10 h-10 xl:w-12 xl:h-12"
      />
    </div>
  </BaseContentPlaceholders>
</template>
