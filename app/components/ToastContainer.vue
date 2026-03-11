<script setup lang="ts">
const { toasts, dismiss } = useToast()

const iconMap = {
  success: 'heroicons:check-circle',
  error: 'heroicons:x-circle',
  info: 'heroicons:information-circle',
}

const colorMap = {
  success: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200/60 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300',
  error: 'bg-rose-50 dark:bg-rose-500/10 border-rose-200/60 dark:border-rose-500/20 text-rose-800 dark:text-rose-300',
  info: 'bg-primary-50 dark:bg-primary-500/10 border-primary-200/60 dark:border-primary-500/20 text-primary-800 dark:text-primary-300',
}

const iconColorMap = {
  success: 'text-emerald-500 dark:text-emerald-400',
  error: 'text-rose-500 dark:text-rose-400',
  info: 'text-primary-500 dark:text-primary-400',
}
</script>

<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none">
      <TransitionGroup
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-x-8 opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-8 opacity-0"
        move-class="transition duration-200 ease-in-out"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl border shadow-lg backdrop-blur-sm max-w-sm"
          :class="colorMap[toast.type]"
        >
          <Icon
            :name="iconMap[toast.type]"
            class="w-5 h-5 flex-shrink-0"
            :class="iconColorMap[toast.type]"
          />
          <p class="text-sm font-medium flex-1">
            {{ toast.message }}
          </p>
          <button
            class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity"
            @click="dismiss(toast.id)"
          >
            <Icon
              name="heroicons:x-mark"
              class="w-4 h-4"
            />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>
