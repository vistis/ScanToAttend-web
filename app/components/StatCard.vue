<script setup lang="ts">
defineProps<{
  title: string
  value: string | number
  icon?: string
  color?: 'blue' | 'green' | 'yellow' | 'red' | 'gray' | 'purple'
}>()  

<<<<<<< HEAD
const colorClasses: Record<string, { icon: string, accent: string }> = {
  blue: { icon: 'bg-primary-100 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400', accent: 'from-primary-500 to-primary-600' },
  green: { icon: 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400', accent: 'from-emerald-500 to-emerald-600' },
  yellow: { icon: 'bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400', accent: 'from-amber-500 to-amber-600' },
  red: { icon: 'bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400', accent: 'from-rose-500 to-rose-600' },
  gray: { icon: 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400', accent: 'from-slate-500 to-slate-600' },
  purple: { icon: 'bg-violet-100 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400', accent: 'from-violet-500 to-violet-600' },
=======
const colorClasses: Record<string, string> = {
  blue: 'bg-blue-50 text-blue-700',
  green: 'bg-green-50 text-green-700',
  yellow: 'bg-yellow-50 text-yellow-700',
  red: 'bg-red-50 text-red-700',
  gray: 'bg-gray-50 text-gray-700',
  purple: 'bg-purple-50 text-purple-700',
>>>>>>> origin/frontend
}
</script>

<template>
<<<<<<< HEAD
  <div class="card relative overflow-hidden group hover:shadow-soft-lg transition-all duration-300">
    <!-- Accent bar -->
    <div
      class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r"
      :class="colorClasses[color ?? 'blue'].accent"
    />
    <div class="flex items-center space-x-4">
      <div
        v-if="icon"
        class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center"
        :class="colorClasses[color ?? 'blue'].icon"
      >
        <Icon
          :name="icon"
          class="w-6 h-6"
        />
      </div>
      <div>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
          {{ title }}
        </p>
        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-0.5">
          {{ value }}
        </p>
      </div>
=======
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
>>>>>>> origin/frontend
    </div>
  </div>
</template>
