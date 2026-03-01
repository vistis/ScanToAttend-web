<script setup lang="ts">
defineProps<{
  title: string
  value: string | number
  icon?: string
  color?: 'blue' | 'green' | 'yellow' | 'red' | 'gray' | 'purple'
}>()  

const colorClasses: Record<string, string> = {
  blue: 'bg-blue-50 text-blue-700',
  green: 'bg-green-50 text-green-700',
  yellow: 'bg-yellow-50 text-yellow-700',
  red: 'bg-red-50 text-red-700',
  gray: 'bg-gray-50 text-gray-700',
  purple: 'bg-purple-50 text-purple-700',
}
</script>

<template>
  <div class="card flex items-center space-x-4">
    <div
      v-if="icon"
      class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center"
      :class="colorClasses[color ?? 'blue']"
    >
      <Icon
        :name="icon"
        class="w-6 h-6"
      />
    </div>
    <div>
      <p class="text-sm font-medium text-gray-500">
        {{ title }}
      </p>
      <p class="text-2xl font-semibold">
        {{ value }}
      </p>
    </div>
  </div>
</template>
