<script setup lang="ts">
defineProps<{
  loading?: boolean
}>()
</script>

<template>
  <div
    v-if="loading"
    class="flex flex-col items-center justify-center py-16"
  >
    <div class="relative w-10 h-10">
      <div class="absolute inset-0 rounded-full border-2 border-slate-200 dark:border-slate-700" />
      <div class="absolute inset-0 rounded-full border-2 border-primary-600 dark:border-primary-400 border-t-transparent animate-spin" />
    </div>
    <p class="mt-3 text-sm text-slate-400 dark:text-slate-500 font-medium">Loading...</p>
  </div>
  <slot v-else />
</template>
