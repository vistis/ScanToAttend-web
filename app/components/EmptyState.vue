<script setup lang="ts">
defineProps<{
  message?: string
}>()
</script>

<template>
  <div class="text-center py-12">
    <Icon
      name="heroicons:inbox"
      class="w-12 h-12 text-gray-300 mx-auto mb-4"
    />
    <p class="text-gray-500">
      {{ message ?? 'No data found.' }}
    </p>
  </div>
</template>
