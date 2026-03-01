<script setup lang="ts">
defineProps<{
  loading?: boolean
}>()
</script>

<template>
  <div
    v-if="loading"
    class="flex items-center justify-center py-12"
  >
    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600" />
  </div>
  <slot v-else />
</template>
