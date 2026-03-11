<script setup lang="ts">
/**
 * Admin Fingerprint Enrollment — enroll student fingerprints via ESP32 proxy.
 * Flow: Select student → Start enrollment → Place finger twice → Save to DB.
 */
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

const { apiFetch } = useApi()
<<<<<<< HEAD
const toast = useToast()

// Search & filter
const searchQuery = ref('')
const filterTab = ref<'all' | 'enrolled' | 'unenrolled'>('all')
=======
>>>>>>> origin/frontend

// ESP32 device status
const espStatus = ref<any>(null)
const espOnline = ref(false)
const espLoading = ref(true)

// Student data
const students = ref<any[]>([])
const nextFingerprintId = ref(1)
const loading = ref(true)

// Enrollment state
const selectedStudent = ref<any>(null)
const fingerprintId = ref(1)
const enrolling = ref(false)
const enrollStep = ref(0) // 0=idle, 1=first scan, 2=remove, 3=second scan, 4=processing
const enrollMessage = ref('Select a student to begin enrollment.')
const enrollResult = ref<'idle' | 'success' | 'failed'>('idle')
const errorMessage = ref('')
const ghostId = ref<number | null>(null)

let pollTimer: ReturnType<typeof setInterval> | null = null

async function checkEspStatus() {
  espLoading.value = true
  try {
    const data = await apiFetch<any>('/esp/status')
    espStatus.value = data
    espOnline.value = data?.status === 'online'
  }
  catch {
    espOnline.value = false
  }
  finally {
    espLoading.value = false
  }
}

async function loadStudents() {
  loading.value = true
  try {
    const data = await apiFetch<any>('/esp/enroll/students')
    students.value = data.students ?? []
    nextFingerprintId.value = data.next_fingerprint_id ?? 1
    fingerprintId.value = nextFingerprintId.value
  }
  catch {
    //
  }
  finally {
    loading.value = false
  }
}

function selectStudent(student: any) {
  if (enrolling.value) return
  selectedStudent.value = student
  enrollResult.value = 'idle'
  errorMessage.value = ''
  ghostId.value = null
  enrollStep.value = 0

  // If student already has a fingerprint, show that
  if (student.fingerprint_id) {
    fingerprintId.value = student.fingerprint_id
    enrollMessage.value = `${student.first_name} already has fingerprint ID ${student.fingerprint_id}. You can re-enroll to overwrite.`
  }
  else {
    fingerprintId.value = nextFingerprintId.value
    enrollMessage.value = 'Ready to enroll. Click "Start Enrollment" to begin.'
  }
}

async function startEnrollment() {
  if (!selectedStudent.value || enrolling.value) return

  enrolling.value = true
  enrollResult.value = 'idle'
  errorMessage.value = ''
  ghostId.value = null
  enrollStep.value = 1
  enrollMessage.value = 'Place finger on sensor...'

  try {
    const data = await apiFetch<any>('/esp/enroll/start', {
      method: 'POST',
      body: {
        student_id: selectedStudent.value.id,
        fingerprint_id: fingerprintId.value,
      },
    })

    if (data.status === 'enrollment_started') {
      pollEnrollmentStatus()
    }
    else {
      enrollMessage.value = 'Failed to start enrollment.'
      errorMessage.value = data.error || 'Unknown error'
      enrolling.value = false
      enrollResult.value = 'failed'
    }
  }
  catch (err: any) {
    enrollMessage.value = 'Cannot connect to ESP32 device.'
    errorMessage.value = err?.data?.error || err?.message || 'Connection failed'
    enrolling.value = false
    enrollResult.value = 'failed'
  }
}

function pollEnrollmentStatus() {
  let attempts = 0
  const maxAttempts = 60 // 30 seconds

  pollTimer = setInterval(async () => {
    attempts++
    if (attempts > maxAttempts) {
      stopPolling()
      enrollMessage.value = 'Enrollment timed out.'
      errorMessage.value = 'The enrollment process took too long. Please try again.'
      enrolling.value = false
      enrollResult.value = 'failed'
      return
    }

    try {
      const data = await apiFetch<any>('/esp/enroll/status')

      switch (data.status) {
        case 'waiting_first_scan':
          enrollStep.value = 1
          enrollMessage.value = 'Step 1: Place finger on sensor...'
          break
        case 'remove_finger':
          enrollStep.value = 2
          enrollMessage.value = 'Step 2: Remove finger from sensor...'
          break
        case 'waiting_second_scan':
          enrollStep.value = 3
          enrollMessage.value = 'Step 3: Place same finger again...'
          break
        case 'processing':
          enrollStep.value = 4
          enrollMessage.value = 'Processing fingerprint...'
          break
        case 'success':
          stopPolling()
          await saveEnrollment()
          break
        case 'failed':
          stopPolling()
          enrollResult.value = 'failed'
          enrolling.value = false

          if (data.duplicate_id !== undefined) {
            const dupStudent = students.value.find(s => s.fingerprint_id === data.duplicate_id)
            if (dupStudent) {
              enrollMessage.value = `Duplicate! This finger belongs to ${dupStudent.first_name} ${dupStudent.last_name}.`
              errorMessage.value = `Fingerprint already enrolled as ID #${data.duplicate_id}`
            }
            else {
              enrollMessage.value = `Ghost fingerprint detected (ID #${data.duplicate_id}).`
              errorMessage.value = 'This fingerprint exists on the sensor but is not linked to any student in the database.'
              ghostId.value = data.duplicate_id
            }
          }
          else {
            enrollMessage.value = 'Enrollment failed.'
            errorMessage.value = data.error || 'Unknown error'
          }
          break
      }
    }
    catch {
      // Ignore transient connection issues during polling
    }
  }, 500)
}

function stopPolling() {
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
}

async function saveEnrollment() {
  try {
    await apiFetch('/esp/enroll/save', {
      method: 'POST',
      body: {
        student_id: selectedStudent.value.id,
        fingerprint_id: fingerprintId.value,
      },
    })

    enrollMessage.value = 'Enrollment successful!'
    enrollResult.value = 'success'
    enrollStep.value = 0
<<<<<<< HEAD
    toast.success(`Fingerprint enrolled for ${selectedStudent.value.first_name} ${selectedStudent.value.last_name}!`)
=======
>>>>>>> origin/frontend

    // Refresh student list
    await loadStudents()

    // Update the selected student's fingerprint
    if (selectedStudent.value) {
      selectedStudent.value.fingerprint_id = fingerprintId.value
    }
  }
  catch (err: any) {
    enrollMessage.value = 'Fingerprint captured but failed to save to database.'
    errorMessage.value = err?.data?.message || 'Database error'
    enrollResult.value = 'failed'
  }
  finally {
    enrolling.value = false
  }
}

async function deleteGhostFingerprint() {
  if (!ghostId.value) return
  if (!confirm(`Delete ghost fingerprint ID #${ghostId.value} from the sensor?`)) return

  try {
    await apiFetch('/esp/fingerprint/delete', {
      method: 'POST',
      body: { fingerprint_id: ghostId.value },
    })
    enrollMessage.value = 'Ghost fingerprint deleted. You can try enrolling again.'
    errorMessage.value = ''
    ghostId.value = null
    enrollResult.value = 'idle'
<<<<<<< HEAD
    toast.success('Ghost fingerprint deleted.')
=======
>>>>>>> origin/frontend
  }
  catch (err: any) {
    errorMessage.value = err?.data?.error || 'Failed to delete ghost fingerprint'
  }
}

function resetEnrollment() {
  stopPolling()
  enrolling.value = false
  enrollResult.value = 'idle'
  enrollStep.value = 0
  errorMessage.value = ''
  ghostId.value = null
  if (selectedStudent.value) {
    selectStudent(selectedStudent.value)
  }
}

// Factory reset
const showWipeModal = ref(false)
const wiping = ref(false)

async function factoryReset() {
  wiping.value = true
  try {
    const data = await apiFetch<any>('/esp/fingerprint/empty', { method: 'POST' })
    if (data?.status === 'cleared') {
      showWipeModal.value = false
      enrollMessage.value = 'Sensor memory wiped. All fingerprints cleared.'
      enrollResult.value = 'idle'
      errorMessage.value = ''
      selectedStudent.value = null
<<<<<<< HEAD
      toast.success('All fingerprints wiped successfully.')
=======
>>>>>>> origin/frontend
      await loadStudents()
      await checkEspStatus()
    }
    else {
      errorMessage.value = data?.error || 'Failed to wipe sensor.'
    }
  }
  catch (err: any) {
    errorMessage.value = err?.data?.error || 'Cannot connect to ESP32.'
  }
  finally {
    wiping.value = false
  }
}

<<<<<<< HEAD
// Filter + search students
const sortedStudents = computed(() => {
  const search = searchQuery.value.toLowerCase().trim()
  return [...students.value]
    .filter((s) => {
      // Filter tab
      if (filterTab.value === 'enrolled' && !s.fingerprint_id) return false
      if (filterTab.value === 'unenrolled' && s.fingerprint_id) return false
      // Search
      if (search) {
        const name = `${s.first_name} ${s.last_name}`.toLowerCase()
        return name.includes(search) || (s.email || '').toLowerCase().includes(search)
      }
      return true
    })
    .sort((a, b) => {
      if (!a.fingerprint_id && b.fingerprint_id) return -1
      if (a.fingerprint_id && !b.fingerprint_id) return 1
      return (a.first_name || '').localeCompare(b.first_name || '')
    })
=======
// Filter: show unenrolled students first
const sortedStudents = computed(() => {
  return [...students.value].sort((a, b) => {
    if (!a.fingerprint_id && b.fingerprint_id) return -1
    if (a.fingerprint_id && !b.fingerprint_id) return 1
    return (a.first_name || '').localeCompare(b.first_name || '')
  })
>>>>>>> origin/frontend
})

const enrolledCount = computed(() => students.value.filter(s => s.fingerprint_id).length)
const unenrolledCount = computed(() => students.value.filter(s => !s.fingerprint_id).length)

onMounted(() => {
  checkEspStatus()
  loadStudents()
})

onUnmounted(() => {
  stopPolling()
})
</script>

<template>
  <div>
<<<<<<< HEAD
    <div class="flex justify-between items-center mb-2">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
          Fingerprint Enrollment
        </h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1">
          Manage student fingerprint registrations on the ESP32 sensor.
        </p>
      </div>
=======
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Fingerprint Enrollment
      </h1>
>>>>>>> origin/frontend
      <button
        class="btn-secondary text-sm"
        :disabled="espLoading"
        @click="checkEspStatus"
      >
        <Icon
          name="heroicons:arrow-path"
          class="w-4 h-4 mr-1"
<<<<<<< HEAD
          :class="{ 'animate-spin': espLoading }"
        />
        Refresh Status
=======
        />
        Refresh ESP32 Status
>>>>>>> origin/frontend
      </button>
    </div>

    <!-- ESP32 Status Banner -->
    <div
<<<<<<< HEAD
      class="mb-6 mt-4 p-4 rounded-2xl flex items-center gap-3 border"
      :class="espOnline ? 'bg-emerald-50/80 dark:bg-emerald-500/10 border-emerald-200/60 dark:border-emerald-500/20' : 'bg-rose-50/80 dark:bg-rose-500/10 border-rose-200/60 dark:border-rose-500/20'"
    >
      <div
        class="w-3 h-3 rounded-full flex-shrink-0"
        :class="espOnline ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"
      />
      <div v-if="espLoading" class="text-sm text-slate-600 dark:text-slate-400">
        Checking ESP32 connection...
      </div>
      <div v-else-if="espOnline" class="text-sm text-emerald-700 dark:text-emerald-400">
        <span class="font-semibold">ESP32 Online</span>
        <span v-if="espStatus" class="ml-2 text-emerald-600 dark:text-emerald-500">
=======
      class="mb-6 p-4 rounded-lg flex items-center gap-3"
      :class="espOnline ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'"
    >
      <div
        class="w-3 h-3 rounded-full flex-shrink-0"
        :class="espOnline ? 'bg-green-500 animate-pulse' : 'bg-red-500'"
      />
      <div v-if="espLoading" class="text-sm text-gray-600">
        Checking ESP32 connection...
      </div>
      <div v-else-if="espOnline" class="text-sm text-green-700">
        <span class="font-medium">ESP32 Online</span>
        <span v-if="espStatus" class="ml-2 text-green-600">
>>>>>>> origin/frontend
          — Sensor: {{ espStatus.sensor ? 'OK' : 'Error' }}
          · Fingerprints: {{ espStatus.fingerprint_count }}/{{ espStatus.fingerprint_capacity }}
          · IP: {{ espStatus.ip }}
        </span>
      </div>
<<<<<<< HEAD
      <div v-else class="text-sm text-rose-700 dark:text-rose-400">
        <span class="font-semibold">ESP32 Offline</span>
=======
      <div v-else class="text-sm text-red-700">
        <span class="font-medium">ESP32 Offline</span>
>>>>>>> origin/frontend
        — Cannot connect to the fingerprint device. Check the device is powered on and connected to the network.
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="card text-center">
<<<<<<< HEAD
        <p class="text-2xl font-bold text-slate-900 dark:text-white">
          {{ students.length }}
        </p>
        <p class="text-sm text-slate-500 dark:text-slate-400">
=======
        <p class="text-2xl font-bold text-gray-900">
          {{ students.length }}
        </p>
        <p class="text-sm text-gray-500">
>>>>>>> origin/frontend
          Total Students
        </p>
      </div>
      <div class="card text-center">
<<<<<<< HEAD
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
          {{ enrolledCount }}
        </p>
        <p class="text-sm text-slate-500 dark:text-slate-400">
=======
        <p class="text-2xl font-bold text-green-600">
          {{ enrolledCount }}
        </p>
        <p class="text-sm text-gray-500">
>>>>>>> origin/frontend
          Enrolled
        </p>
      </div>
      <div class="card text-center">
<<<<<<< HEAD
        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">
          {{ unenrolledCount }}
        </p>
        <p class="text-sm text-slate-500 dark:text-slate-400">
=======
        <p class="text-2xl font-bold text-orange-600">
          {{ unenrolledCount }}
        </p>
        <p class="text-sm text-gray-500">
>>>>>>> origin/frontend
          Not Enrolled
        </p>
      </div>
    </div>

    <!-- Danger Zone: Factory Reset -->
<<<<<<< HEAD
    <div
      class="mb-6 card border bg-rose-50/50 dark:bg-rose-500/5 transition-opacity duration-200"
      :class="espOnline ? 'border-rose-200 dark:border-rose-500/20' : 'border-slate-200 dark:border-slate-700 opacity-60'"
    >
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold flex items-center gap-2" :class="espOnline ? 'text-rose-700 dark:text-rose-400' : 'text-slate-500 dark:text-slate-500'">
            <Icon name="heroicons:exclamation-triangle" class="w-5 h-5" />
            Danger Zone
          </h2>
          <p class="text-sm mt-1" :class="espOnline ? 'text-rose-600 dark:text-rose-400/80' : 'text-slate-400 dark:text-slate-500'">
            {{ espOnline ? 'Wipe all fingerprints from the ESP32 sensor and clear all enrollment records in the database.' : 'Sensor must be online to perform a factory reset.' }}
=======
    <div class="mb-6 card border border-red-200 bg-red-50/50">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-red-700 flex items-center gap-2">
            <Icon name="heroicons:exclamation-triangle" class="w-5 h-5" />
            Danger Zone
          </h2>
          <p class="text-sm text-red-600 mt-1">
            Wipe all fingerprints from the ESP32 sensor and clear all enrollment records in the database.
>>>>>>> origin/frontend
          </p>
        </div>
        <button
          class="btn-danger flex-shrink-0"
          :disabled="!espOnline || wiping"
          @click="showWipeModal = true"
        >
          <Icon name="heroicons:trash" class="w-4 h-4 mr-2" />
          Factory Reset
        </button>
      </div>
    </div>

    <!-- Wipe Confirmation Modal -->
    <Teleport to="body">
<<<<<<< HEAD
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showWipeModal"
          class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
          @click.self="showWipeModal = false"
        >
          <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-500/10 flex items-center justify-center flex-shrink-0">
                <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-rose-600 dark:text-rose-400" />
              </div>
              <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                Factory Reset Sensor?
              </h3>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-2">
              This will <strong>permanently delete ALL fingerprints</strong> stored on the ESP32 sensor chip and clear all enrollment records in the database.
            </p>
            <p class="text-sm text-rose-600 dark:text-rose-400 font-medium mb-6">
              This action cannot be undone. All students will need to be re-enrolled.
            </p>
            <div class="flex justify-end gap-3">
              <button
                class="btn-secondary"
                :disabled="wiping"
                @click="showWipeModal = false"
              >
                Cancel
              </button>
              <button
                class="btn-danger"
                :disabled="wiping"
                @click="factoryReset"
              >
                <Icon v-if="wiping" name="heroicons:arrow-path" class="w-4 h-4 mr-2 animate-spin" />
                {{ wiping ? 'Wiping...' : 'Wipe All Fingerprints' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Offline overlay message for enrollment section -->
    <div
      v-if="!espOnline && !espLoading"
      class="mb-6 p-4 rounded-2xl bg-slate-100 dark:bg-surface-800 border border-slate-200 dark:border-slate-700 text-center"
    >
      <Icon name="heroicons:wifi" class="w-8 h-8 text-slate-400 dark:text-slate-500 mx-auto mb-2" />
      <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
        Enrollment is unavailable while the sensor is offline.
      </p>
      <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
        Connect the ESP32 device and click "Refresh Status" to continue.
      </p>
    </div>

    <div
      class="grid lg:grid-cols-2 gap-6 transition-opacity duration-200"
      :class="{ 'opacity-50 pointer-events-none': !espOnline && !espLoading }"
    >
      <!-- Left: Student List -->
      <div class="card p-0 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 dark:bg-surface-850 border-b border-slate-200/60 dark:border-slate-700 space-y-3">
          <h2 class="font-semibold text-slate-700 dark:text-slate-200">
            Select Student
          </h2>
          <!-- Search -->
          <div class="relative">
            <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by name or email..."
              class="input !pl-9 !py-2 text-sm"
            >
          </div>
          <!-- Filter Tabs -->
          <div class="flex gap-1">
            <button
              v-for="tab in [
                { key: 'all', label: `All (${students.length})` },
                { key: 'unenrolled', label: `Pending (${unenrolledCount})` },
                { key: 'enrolled', label: `Enrolled (${enrolledCount})` },
              ]" :key="tab.key"
              class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors"
              :class="filterTab === tab.key
                ? 'bg-primary-600 text-white'
                : 'text-slate-500 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-600/30'"
              @click="filterTab = tab.key as any"
            >
              {{ tab.label }}
            </button>
          </div>
        </div>
=======
      <div
        v-if="showWipeModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showWipeModal = false"
      >
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
              <Icon name="heroicons:exclamation-triangle" class="w-6 h-6 text-red-600" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900">
              Factory Reset Sensor?
            </h3>
          </div>
          <p class="text-sm text-gray-600 mb-2">
            This will <strong>permanently delete ALL fingerprints</strong> stored on the ESP32 sensor chip and clear all enrollment records in the database.
          </p>
          <p class="text-sm text-red-600 font-medium mb-6">
            This action cannot be undone. All students will need to be re-enrolled.
          </p>
          <div class="flex justify-end gap-3">
            <button
              class="btn-secondary"
              :disabled="wiping"
              @click="showWipeModal = false"
            >
              Cancel
            </button>
            <button
              class="btn-danger"
              :disabled="wiping"
              @click="factoryReset"
            >
              <Icon v-if="wiping" name="heroicons:arrow-path" class="w-4 h-4 mr-2 animate-spin" />
              {{ wiping ? 'Wiping...' : 'Wipe All Fingerprints' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Left: Student List -->
      <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b">
          <h2 class="font-semibold text-gray-700">
            Select Student
          </h2>
        </div>
>>>>>>> origin/frontend

        <LoadingState :loading="loading">
          <div
            v-if="students.length === 0"
<<<<<<< HEAD
            class="p-8 text-center text-slate-500 dark:text-slate-400"
=======
            class="p-8 text-center text-gray-500"
>>>>>>> origin/frontend
          >
            No students registered.
          </div>
          <div
<<<<<<< HEAD
            v-else-if="sortedStudents.length === 0"
            class="p-8 text-center text-slate-400 dark:text-slate-500 text-sm"
          >
            No students match your search.
          </div>
          <div
            v-else
            class="divide-y divide-slate-100 dark:divide-slate-700/50 max-h-[480px] overflow-y-auto"
=======
            v-else
            class="divide-y divide-gray-100 max-h-[480px] overflow-y-auto"
>>>>>>> origin/frontend
          >
            <button
              v-for="student in sortedStudents"
              :key="student.id"
<<<<<<< HEAD
              class="w-full px-4 py-3 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors text-left"
              :class="{
                'bg-primary-50/80 dark:bg-primary-500/10 border-l-4 border-primary-500': selectedStudent?.id === student.id,
                'opacity-50 cursor-not-allowed': enrolling && selectedStudent?.id !== student.id,
              }"
              :disabled="enrolling || (!espOnline && !espLoading)"
              @click="selectStudent(student)"
            >
              <div
                class="h-9 w-9 flex-shrink-0 rounded-xl flex items-center justify-center text-sm font-medium"
                :class="student.fingerprint_id ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
=======
              class="w-full px-4 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors text-left"
              :class="{
                'bg-primary-50 border-l-4 border-primary-500': selectedStudent?.id === student.id,
                'opacity-50 cursor-not-allowed': enrolling && selectedStudent?.id !== student.id,
              }"
              :disabled="enrolling"
              @click="selectStudent(student)"
            >
              <div
                class="h-9 w-9 flex-shrink-0 rounded-full flex items-center justify-center text-sm font-medium"
                :class="student.fingerprint_id ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
>>>>>>> origin/frontend
              >
                {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
              </div>
              <div class="flex-1 min-w-0">
<<<<<<< HEAD
                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">
                  {{ student.first_name }} {{ student.last_name }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
=======
                <p class="text-sm font-medium text-gray-900 truncate">
                  {{ student.first_name }} {{ student.last_name }}
                </p>
                <p class="text-xs text-gray-500 truncate">
>>>>>>> origin/frontend
                  {{ student.email }}
                </p>
              </div>
              <div class="flex-shrink-0">
                <span
                  v-if="student.fingerprint_id"
<<<<<<< HEAD
                  class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-500/20"
=======
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
>>>>>>> origin/frontend
                >
                  <Icon
                    name="heroicons:finger-print"
                    class="w-3 h-3 mr-1"
                  />
                  ID #{{ student.fingerprint_id }}
                </span>
                <span
                  v-else
<<<<<<< HEAD
                  class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-500/20"
=======
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700"
>>>>>>> origin/frontend
                >
                  Not enrolled
                </span>
              </div>
            </button>
          </div>
        </LoadingState>
      </div>

      <!-- Right: Enrollment Panel -->
      <div class="card">
<<<<<<< HEAD
        <h2 class="font-semibold text-slate-700 dark:text-slate-200 mb-4">
=======
        <h2 class="font-semibold text-gray-700 mb-4">
>>>>>>> origin/frontend
          Enrollment
        </h2>

        <div
          v-if="!selectedStudent"
<<<<<<< HEAD
          class="text-center py-12 text-slate-400 dark:text-slate-500"
        >
          <div class="w-20 h-20 rounded-2xl bg-slate-100 dark:bg-surface-850 flex items-center justify-center mx-auto mb-4">
            <Icon
              name="heroicons:finger-print"
              class="w-10 h-10"
            />
          </div>
          <p class="font-medium">Select a student from the list to begin.</p>
=======
          class="text-center py-12 text-gray-400"
        >
          <Icon
            name="heroicons:finger-print"
            class="w-16 h-16 mx-auto mb-3"
          />
          <p>Select a student from the list to begin.</p>
>>>>>>> origin/frontend
        </div>

        <div v-else>
          <!-- Selected student info -->
<<<<<<< HEAD
          <div class="flex items-center gap-3 mb-6 p-3 rounded-xl bg-slate-50 dark:bg-surface-850">
            <div
              class="h-10 w-10 flex-shrink-0 rounded-xl flex items-center justify-center text-sm font-medium bg-primary-100 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400"
=======
          <div class="flex items-center gap-3 mb-6 p-3 rounded-lg bg-gray-50">
            <div
              class="h-10 w-10 flex-shrink-0 rounded-full flex items-center justify-center text-sm font-medium bg-primary-100 text-primary-700"
>>>>>>> origin/frontend
            >
              {{ selectedStudent.first_name?.[0] }}{{ selectedStudent.last_name?.[0] }}
            </div>
            <div>
<<<<<<< HEAD
              <p class="font-medium text-slate-900 dark:text-white">
                {{ selectedStudent.first_name }} {{ selectedStudent.last_name }}
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400">
=======
              <p class="font-medium text-gray-900">
                {{ selectedStudent.first_name }} {{ selectedStudent.last_name }}
              </p>
              <p class="text-xs text-gray-500">
>>>>>>> origin/frontend
                {{ selectedStudent.email }}
              </p>
            </div>
          </div>

          <!-- Fingerprint ID -->
          <div class="mb-4">
            <label class="label">Fingerprint ID</label>
            <input
              v-model.number="fingerprintId"
              type="number"
              class="input"
              min="1"
              :disabled="enrolling"
            >
          </div>

          <!-- Step Indicators -->
          <div class="flex items-center justify-center gap-2 mb-4">
            <div
              v-for="step in 3"
              :key="step"
<<<<<<< HEAD
              class="w-3 h-3 rounded-full transition-all duration-300"
              :class="{
                'bg-primary-500 scale-125': enrollStep === step,
                'bg-emerald-500': enrollResult === 'success',
                'bg-slate-200': enrollStep !== step && enrollResult !== 'success',
=======
              class="w-3 h-3 rounded-full transition-colors duration-300"
              :class="{
                'bg-primary-500 scale-125': enrollStep === step,
                'bg-green-500': enrollResult === 'success',
                'bg-gray-200': enrollStep !== step && enrollResult !== 'success',
>>>>>>> origin/frontend
              }"
            />
          </div>

          <!-- Fingerprint Icon -->
          <div class="flex justify-center mb-4">
            <div
<<<<<<< HEAD
              class="w-24 h-24 flex items-center justify-center rounded-2xl transition-all duration-300"
              :class="{
                'bg-slate-100 dark:bg-surface-850 text-slate-400 dark:text-slate-500': !enrolling && enrollResult === 'idle',
                'bg-primary-100 dark:bg-primary-500/10 text-primary-500 dark:text-primary-400 animate-pulse': enrolling,
                'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-500 dark:text-emerald-400': enrollResult === 'success',
                'bg-rose-100 dark:bg-rose-500/10 text-rose-500 dark:text-rose-400': enrollResult === 'failed',
=======
              class="w-24 h-24 flex items-center justify-center rounded-full transition-all duration-300"
              :class="{
                'bg-gray-100 text-gray-400': !enrolling && enrollResult === 'idle',
                'bg-primary-100 text-primary-500 animate-pulse': enrolling,
                'bg-green-100 text-green-500': enrollResult === 'success',
                'bg-red-100 text-red-500': enrollResult === 'failed',
>>>>>>> origin/frontend
              }"
            >
              <Icon
                name="heroicons:finger-print"
                class="w-12 h-12"
              />
            </div>
          </div>

          <!-- Status Message -->
          <p
            class="text-center text-sm mb-4"
            :class="{
<<<<<<< HEAD
              'text-slate-600 dark:text-slate-400': enrollResult === 'idle',
              'text-primary-600 dark:text-primary-400 font-medium': enrolling,
              'text-emerald-600 dark:text-emerald-400 font-medium': enrollResult === 'success',
              'text-rose-600 dark:text-rose-400': enrollResult === 'failed',
=======
              'text-gray-600': enrollResult === 'idle',
              'text-primary-600 font-medium': enrolling,
              'text-green-600 font-medium': enrollResult === 'success',
              'text-red-600': enrollResult === 'failed',
>>>>>>> origin/frontend
            }"
          >
            {{ enrollMessage }}
          </p>

          <!-- Error Details -->
          <div
            v-if="errorMessage"
<<<<<<< HEAD
            class="mb-4 p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-sm border border-rose-200/60 dark:border-rose-500/20"
=======
            class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
>>>>>>> origin/frontend
          >
            {{ errorMessage }}
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col gap-2">
            <button
              v-if="!enrolling && enrollResult !== 'success' && !ghostId"
              class="btn-primary w-full justify-center"
              :disabled="!espOnline"
              @click="startEnrollment"
            >
              <Icon
                name="heroicons:finger-print"
                class="w-4 h-4 mr-2"
              />
              {{ selectedStudent.fingerprint_id ? 'Re-enroll Fingerprint' : 'Start Enrollment' }}
            </button>

            <!-- Ghost fingerprint delete -->
            <button
              v-if="ghostId"
              class="btn-danger w-full justify-center"
              @click="deleteGhostFingerprint"
            >
              <Icon
                name="heroicons:trash"
                class="w-4 h-4 mr-2"
              />
              Delete Ghost Fingerprint (ID #{{ ghostId }})
            </button>

            <button
              v-if="enrollResult === 'failed' || enrollResult === 'success'"
              class="btn-secondary w-full justify-center"
              @click="resetEnrollment"
            >
              {{ enrollResult === 'success' ? 'Enroll Another' : 'Try Again' }}
            </button>
          </div>

          <!-- Instructions -->
<<<<<<< HEAD
          <div class="mt-6 p-4 rounded-xl bg-primary-50/50 dark:bg-primary-500/5 border border-primary-100 dark:border-primary-500/20 text-sm text-primary-700 dark:text-primary-400">
            <p class="font-semibold mb-2">
=======
          <div class="mt-6 p-4 rounded-lg bg-blue-50 text-sm text-blue-700">
            <p class="font-medium mb-2">
>>>>>>> origin/frontend
              Instructions
            </p>
            <ol class="list-decimal list-inside space-y-1">
              <li>Select a student from the list.</li>
              <li>Click "Start Enrollment".</li>
              <li>Place finger on sensor when prompted.</li>
              <li>Lift and place same finger again to confirm.</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
