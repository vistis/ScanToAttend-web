<script setup lang="ts">
defineProps<{
  message?: string
}>()
</script>

<template>
  <div class="text-center py-16">
    <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center">
      <Icon
        name="heroicons:inbox"
        class="w-8 h-8 text-slate-300 dark:text-slate-600"
      />
    </div>
    <p class="text-slate-500 dark:text-slate-400 font-medium">
      {{ message ?? 'No data found.' }}
    </p>
  </div>
</template>
