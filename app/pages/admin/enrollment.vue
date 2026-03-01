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

// Filter: show unenrolled students first
const sortedStudents = computed(() => {
  return [...students.value].sort((a, b) => {
    if (!a.fingerprint_id && b.fingerprint_id) return -1
    if (a.fingerprint_id && !b.fingerprint_id) return 1
    return (a.first_name || '').localeCompare(b.first_name || '')
  })
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
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">
        Fingerprint Enrollment
      </h1>
      <button
        class="btn-secondary text-sm"
        :disabled="espLoading"
        @click="checkEspStatus"
      >
        <Icon
          name="heroicons:arrow-path"
          class="w-4 h-4 mr-1"
        />
        Refresh ESP32 Status
      </button>
    </div>

    <!-- ESP32 Status Banner -->
    <div
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
          — Sensor: {{ espStatus.sensor ? 'OK' : 'Error' }}
          · Fingerprints: {{ espStatus.fingerprint_count }}/{{ espStatus.fingerprint_capacity }}
          · IP: {{ espStatus.ip }}
        </span>
      </div>
      <div v-else class="text-sm text-red-700">
        <span class="font-medium">ESP32 Offline</span>
        — Cannot connect to the fingerprint device. Check the device is powered on and connected to the network.
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
      <div class="card text-center">
        <p class="text-2xl font-bold text-gray-900">
          {{ students.length }}
        </p>
        <p class="text-sm text-gray-500">
          Total Students
        </p>
      </div>
      <div class="card text-center">
        <p class="text-2xl font-bold text-green-600">
          {{ enrolledCount }}
        </p>
        <p class="text-sm text-gray-500">
          Enrolled
        </p>
      </div>
      <div class="card text-center">
        <p class="text-2xl font-bold text-orange-600">
          {{ unenrolledCount }}
        </p>
        <p class="text-sm text-gray-500">
          Not Enrolled
        </p>
      </div>
    </div>

    <!-- Danger Zone: Factory Reset -->
    <div class="mb-6 card border border-red-200 bg-red-50/50">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-red-700 flex items-center gap-2">
            <Icon name="heroicons:exclamation-triangle" class="w-5 h-5" />
            Danger Zone
          </h2>
          <p class="text-sm text-red-600 mt-1">
            Wipe all fingerprints from the ESP32 sensor and clear all enrollment records in the database.
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

        <LoadingState :loading="loading">
          <div
            v-if="students.length === 0"
            class="p-8 text-center text-gray-500"
          >
            No students registered.
          </div>
          <div
            v-else
            class="divide-y divide-gray-100 max-h-[480px] overflow-y-auto"
          >
            <button
              v-for="student in sortedStudents"
              :key="student.id"
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
              >
                {{ student.first_name?.[0] }}{{ student.last_name?.[0] }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                  {{ student.first_name }} {{ student.last_name }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                  {{ student.email }}
                </p>
              </div>
              <div class="flex-shrink-0">
                <span
                  v-if="student.fingerprint_id"
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"
                >
                  <Icon
                    name="heroicons:finger-print"
                    class="w-3 h-3 mr-1"
                  />
                  ID #{{ student.fingerprint_id }}
                </span>
                <span
                  v-else
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700"
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
        <h2 class="font-semibold text-gray-700 mb-4">
          Enrollment
        </h2>

        <div
          v-if="!selectedStudent"
          class="text-center py-12 text-gray-400"
        >
          <Icon
            name="heroicons:finger-print"
            class="w-16 h-16 mx-auto mb-3"
          />
          <p>Select a student from the list to begin.</p>
        </div>

        <div v-else>
          <!-- Selected student info -->
          <div class="flex items-center gap-3 mb-6 p-3 rounded-lg bg-gray-50">
            <div
              class="h-10 w-10 flex-shrink-0 rounded-full flex items-center justify-center text-sm font-medium bg-primary-100 text-primary-700"
            >
              {{ selectedStudent.first_name?.[0] }}{{ selectedStudent.last_name?.[0] }}
            </div>
            <div>
              <p class="font-medium text-gray-900">
                {{ selectedStudent.first_name }} {{ selectedStudent.last_name }}
              </p>
              <p class="text-xs text-gray-500">
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
              class="w-3 h-3 rounded-full transition-colors duration-300"
              :class="{
                'bg-primary-500 scale-125': enrollStep === step,
                'bg-green-500': enrollResult === 'success',
                'bg-gray-200': enrollStep !== step && enrollResult !== 'success',
              }"
            />
          </div>

          <!-- Fingerprint Icon -->
          <div class="flex justify-center mb-4">
            <div
              class="w-24 h-24 flex items-center justify-center rounded-full transition-all duration-300"
              :class="{
                'bg-gray-100 text-gray-400': !enrolling && enrollResult === 'idle',
                'bg-primary-100 text-primary-500 animate-pulse': enrolling,
                'bg-green-100 text-green-500': enrollResult === 'success',
                'bg-red-100 text-red-500': enrollResult === 'failed',
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
              'text-gray-600': enrollResult === 'idle',
              'text-primary-600 font-medium': enrolling,
              'text-green-600 font-medium': enrollResult === 'success',
              'text-red-600': enrollResult === 'failed',
            }"
          >
            {{ enrollMessage }}
          </p>

          <!-- Error Details -->
          <div
            v-if="errorMessage"
            class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm"
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
          <div class="mt-6 p-4 rounded-lg bg-blue-50 text-sm text-blue-700">
            <p class="font-medium mb-2">
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
