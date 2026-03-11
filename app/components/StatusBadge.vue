<script setup lang="ts">
defineProps<{
  status: 'Present' | 'Tardy' | 'Absent'
}>()
</script>

<template>
  <span
    :class="{
      'badge-present': status === 'Present',
      'badge-tardy': status === 'Tardy',
      'badge-absent': status === 'Absent',
    }"
  >
    {{ status }}
  </span>
</template>
