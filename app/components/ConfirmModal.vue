<script setup lang="ts">
const props = withDefaults(defineProps<{
  title: string
  message: string
  confirmText?: string
  cancelText?: string
  danger?: boolean
  loading?: boolean
}>(), {
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  danger: false,
  loading: false,
})

const show = defineModel<boolean>({ default: false })
const emit = defineEmits<{ confirm: [] }>()
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
        @click.self="show = false"
      >
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
              :class="danger ? 'bg-rose-100 dark:bg-rose-500/10' : 'bg-primary-100 dark:bg-primary-500/10'"
            >
              <Icon
                :name="danger ? 'heroicons:exclamation-triangle' : 'heroicons:question-mark-circle'"
                class="w-6 h-6"
                :class="danger ? 'text-rose-600 dark:text-rose-400' : 'text-primary-600 dark:text-primary-400'"
              />
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
              {{ title }}
            </h3>
          </div>
          <p class="text-sm text-slate-600 dark:text-slate-300 mb-6">
            {{ message }}
          </p>
          <div class="flex justify-end gap-3">
            <button
              class="btn-secondary"
              :disabled="loading"
              @click="show = false"
            >
              {{ cancelText }}
            </button>
            <button
              :class="danger ? 'btn-danger' : 'btn-primary'"
              :disabled="loading"
              @click="emit('confirm')"
            >
              <Icon
                v-if="loading"
                name="heroicons:arrow-path"
                class="w-4 h-4 mr-2 animate-spin"
              />
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
