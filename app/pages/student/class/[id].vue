<script setup lang="ts">
/**
 * Student class attendance records page.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['student'],
})

const route = useRoute()
const classId = route.params.id as string
const { apiFetch } = useApi()

const loading = ref(true)
const records = ref<any[]>([])
const error = ref('')

onMounted(async () => {
  try {
    const data = await apiFetch<any>('/attendance/class', {
      params: { id: classId },
    })
    records.value = data.records ?? []
  }
  catch (err: any) {
    error.value = err?.data?.message || 'Failed to load attendance.'
  }
  finally {
    loading.value = false
  }
})

const stats = computed(() => {
  const total = records.value.length
  const present = records.value.filter(r => r.status === 'Present').length
  const tardy = records.value.filter(r => r.status === 'Tardy').length
  const absent = records.value.filter(r => r.status === 'Absent').length
  const rate = total > 0 ? Math.round(((present + tardy) / total) * 100) : 0
  return { total, present, tardy, absent, rate }
})
</script>

<template>
  <div>
    <div class="flex items-center space-x-2 mb-6">
      <NuxtLink
        to="/student"
        class="text-gray-400 hover:text-gray-600"
      >
        <Icon
          name="heroicons:arrow-left"
          class="w-5 h-5"
        />
      </NuxtLink>
      <h1 class="text-2xl font-bold">
        Attendance Records
      </h1>
    </div>

    <div
      v-if="error"
      class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
    >
      {{ error }}
    </div>

    <LoadingState :loading="loading">
      <!-- Stats row -->
      <div class="grid gap-4 sm:grid-cols-4 mb-6">
        <StatCard
          title="Total Sessions"
          :value="stats.total"
          icon="heroicons:calendar"
          color="blue"
        />
        <StatCard
          title="Present"
          :value="stats.present"
          icon="heroicons:check-circle"
          color="green"
        />
        <StatCard
          title="Tardy"
          :value="stats.tardy"
          icon="heroicons:clock"
          color="yellow"
        />
        <StatCard
          title="Absent"
          :value="stats.absent"
          icon="heroicons:x-circle"
          color="red"
        />
      </div>

      <!-- Attendance rate -->
      <div class="card mb-6">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-500">Attendance Rate</span>
          <span class="text-2xl font-bold text-primary-600">{{ stats.rate }}%</span>
        </div>
        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-primary-600 h-2 rounded-full transition-all"
            :style="{ width: `${stats.rate}%` }"
          />
        </div>
      </div>

      <!-- Records table -->
      <div class="card overflow-hidden p-0">
        <EmptyState
          v-if="records.length === 0"
          message="No attendance records yet."
        />

        <table
          v-else
          class="min-w-full divide-y divide-gray-200"
        >
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Day
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Time
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Status
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="record in records"
              :key="record.date + record.day"
            >
              <td class="px-6 py-4 text-sm text-gray-900">
                {{ record.date }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ record.day }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                {{ record.start_at?.slice(0, 5) }}–{{ record.end_at?.slice(0, 5) }}
              </td>
              <td class="px-6 py-4">
                <StatusBadge :status="record.status" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </LoadingState>
  </div>
</template>
