<template>
  <nav>
    <ol class="flex flex-wrap py-4 text-gray-900 rounded list-reset dark:text-gray-400">
      <slot />
    </ol>
  </nav>
</template>

<script>
export default {
  name: 'BaseBreadcrumb',
}
</script>
