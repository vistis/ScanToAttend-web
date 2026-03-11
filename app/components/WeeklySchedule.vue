<script setup lang="ts">
/**
<<<<<<< HEAD
 * WeeklySchedule — university-style weekly timetable with colored session blocks.
=======
 * WeeklySchedule — A visual weekly calendar grid that displays
 * class sessions as colored blocks, similar to a university timetable.
>>>>>>> origin/frontend
 */

interface Session {
  id: number
  day: string
  start_at: string
  end_at: string
  course_code: string
  course_name: string
  section: number | string
  class_id: number
}

const props = defineProps<{
<<<<<<< HEAD
  title: string
  schedule: Record<string, Session[]>
  days: string[]
  linkable?: boolean
  linkBasePath?: string
}>()

=======
  /** Title displayed above the calendar */
  title: string
  /** Grouped schedule: { Monday: [...], Tuesday: [...], ... } */
  schedule: Record<string, Session[]>
  /** Days to display as columns */
  days: string[]
  /** Whether session blocks should be clickable links */
  linkable?: boolean
  /** Base path for links (e.g. '/instructor/class') */
  linkBasePath?: string
}>()

// Short day labels for column headers
>>>>>>> origin/frontend
const dayLabels: Record<string, string> = {
  Monday: 'MON',
  Tuesday: 'TUE',
  Wednesday: 'WED',
  Thursday: 'THU',
  Friday: 'FRI',
  Saturday: 'SAT',
  Sunday: 'SUN',
}

<<<<<<< HEAD
const startHour = 7
const endHour = 19 // 7 PM
=======
// Time range for the grid (hours)
const startHour = 7
const endHour = 21 // up to 9 PM
>>>>>>> origin/frontend

const hours = computed(() => {
  const h: number[] = []
  for (let i = startHour; i <= endHour; i++) {
    h.push(i)
  }
  return h
})

<<<<<<< HEAD
function formatHour(hour: number): string {
  if (hour === 0) return '12 AM'
  if (hour === 12) return '12 PM'
  return `${hour > 12 ? hour - 12 : hour} ${hour >= 12 ? 'PM' : 'AM'}`
}

=======
/** Format hour number to display label like "8 AM", "1 PM" */
function formatHour(hour: number): string {
  if (hour === 0 || hour === 12) {
    return `12 ${hour < 12 ? 'AM' : 'PM'}`
  }
  return `${hour > 12 ? hour - 12 : hour} ${hour >= 12 ? 'PM' : 'AM'}`
}

/** Parse "HH:MM" or "HH:MM:SS" time string to fractional hours */
>>>>>>> origin/frontend
function timeToHours(time: string): number {
  const parts = time.split(':')
  return Number.parseInt(parts[0]) + Number.parseInt(parts[1]) / 60
}

<<<<<<< HEAD
const hourHeight = 64

function timeToTop(time: string): number {
  return (timeToHours(time) - startHour) * hourHeight
}

=======
/** Height of one hour row in pixels */
const hourHeight = 60

/** Convert a time string to a top offset in pixels relative to the grid */
function timeToTop(time: string): number {
  const h = timeToHours(time)
  return (h - startHour) * hourHeight
}

/** Get the height of a session block in pixels */
>>>>>>> origin/frontend
function sessionHeight(start: string, end: string): number {
  return (timeToHours(end) - timeToHours(start)) * hourHeight
}

<<<<<<< HEAD
// Vibrant course colors matching the reference screenshot
const courseColors = [
  { bg: '#2563eb', border: '#1d4ed8' }, // blue
  { bg: '#dc2626', border: '#b91c1c' }, // red
  { bg: '#0891b2', border: '#0e7490' }, // cyan
  { bg: '#e11d48', border: '#be123c' }, // rose
  { bg: '#0d9488', border: '#0f766e' }, // teal
  { bg: '#7c3aed', border: '#6d28d9' }, // violet
  { bg: '#ea580c', border: '#c2410c' }, // orange
  { bg: '#059669', border: '#047857' }, // emerald
  { bg: '#4f46e5', border: '#4338ca' }, // indigo
  { bg: '#be185d', border: '#9d174d' }, // pink
]

const courseColorMap = computed(() => {
  const map: Record<string, typeof courseColors[0]> = {}
  const codes = new Set<string>()
=======
// Color palette for different courses — vibrant colors like the reference
const courseColors = [
  { bg: '#3b82f6', text: '#ffffff' }, // blue
  { bg: '#8b5cf6', text: '#ffffff' }, // violet
  { bg: '#06b6d4', text: '#ffffff' }, // cyan
  { bg: '#10b981', text: '#ffffff' }, // emerald
  { bg: '#f59e0b', text: '#ffffff' }, // amber
  { bg: '#ef4444', text: '#ffffff' }, // red
  { bg: '#ec4899', text: '#ffffff' }, // pink
  { bg: '#6366f1', text: '#ffffff' }, // indigo
  { bg: '#14b8a6', text: '#ffffff' }, // teal
  { bg: '#f97316', text: '#ffffff' }, // orange
]

/** Map course_code to a consistent color */
const courseColorMap = computed(() => {
  const map: Record<string, { bg: string, text: string }> = {}
  const codes = new Set<string>()

>>>>>>> origin/frontend
  for (const day of props.days) {
    for (const session of (props.schedule[day] ?? [])) {
      codes.add(session.course_code)
    }
  }
<<<<<<< HEAD
=======

>>>>>>> origin/frontend
  let i = 0
  for (const code of codes) {
    map[code] = courseColors[i % courseColors.length]
    i++
  }
<<<<<<< HEAD
  return map
})

function getColor(courseCode: string) {
  return courseColorMap.value[courseCode] ?? courseColors[0]
}

/** Detect overlapping sessions and assign column positions */
function layoutSessions(sessions: Session[]): Array<Session & { col: number, totalCols: number }> {
  if (!sessions.length) return []
  const sorted = [...sessions].sort((a, b) => timeToHours(a.start_at) - timeToHours(b.start_at))
  const result: Array<Session & { col: number, totalCols: number }> = []
  const groups: Session[][] = []
  let currentGroup: Session[] = [sorted[0]]
  let currentEnd = timeToHours(sorted[0].end_at)
  for (let i = 1; i < sorted.length; i++) {
    if (timeToHours(sorted[i].start_at) < currentEnd) {
=======

  return map
})

function getSessionColor(courseCode: string) {
  return courseColorMap.value[courseCode] ?? courseColors[0]
}

/** Detect overlapping sessions in a day and assign column positions */
function layoutSessions(sessions: Session[]): Array<Session & { col: number, totalCols: number }> {
  if (!sessions.length) return []

  const sorted = [...sessions].sort((a, b) => timeToHours(a.start_at) - timeToHours(b.start_at))
  const result: Array<Session & { col: number, totalCols: number }> = []

  // Group overlapping sessions
  const groups: Session[][] = []
  let currentGroup: Session[] = [sorted[0]]
  let currentEnd = timeToHours(sorted[0].end_at)

  for (let i = 1; i < sorted.length; i++) {
    const sessionStart = timeToHours(sorted[i].start_at)
    if (sessionStart < currentEnd) {
      // Overlapping
>>>>>>> origin/frontend
      currentGroup.push(sorted[i])
      currentEnd = Math.max(currentEnd, timeToHours(sorted[i].end_at))
    }
    else {
      groups.push(currentGroup)
      currentGroup = [sorted[i]]
      currentEnd = timeToHours(sorted[i].end_at)
    }
  }
  groups.push(currentGroup)
<<<<<<< HEAD
=======

>>>>>>> origin/frontend
  for (const group of groups) {
    const totalCols = group.length
    for (let col = 0; col < group.length; col++) {
      result.push({ ...group[col], col, totalCols })
    }
  }
<<<<<<< HEAD
  return result
}

=======

  return result
}

/** Compute the total grid height */
>>>>>>> origin/frontend
const gridHeight = computed(() => (endHour - startHour) * hourHeight)
</script>

<template>
  <div class="w-full">
<<<<<<< HEAD
    <!-- Title bar -->
    <div class="text-center mb-2">
      <h2 class="text-lg font-bold tracking-widest text-slate-700 dark:text-slate-300 uppercase">
        {{ title }}
      </h2>
    </div>
    <!-- Accent gradient bar -->
    <div class="h-1 rounded-full bg-gradient-to-r from-teal-400 via-cyan-400 to-blue-500 mb-5" />

    <!-- Calendar container -->
    <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-[#1a1f2e] overflow-x-auto">
      <div class="min-w-[820px]">
        <!-- Day header row -->
        <div
          class="grid bg-slate-50 dark:bg-[#151926] border-b border-slate-200 dark:border-slate-700/60"
          :style="{ gridTemplateColumns: `56px repeat(${days.length}, 1fr)` }"
        >
          <div />
          <div
            v-for="day in days"
            :key="day"
            class="py-3 text-center text-xs font-bold tracking-widest text-slate-500 dark:text-slate-400 border-l border-slate-200 dark:border-slate-700/60 select-none"
=======
    <!-- Title -->
    <div class="text-center mb-4">
      <h2 class="text-xl font-bold tracking-wide text-gray-800 uppercase">
        {{ title }}
      </h2>
    </div>

    <!-- Calendar container -->
    <div class="border border-gray-200 rounded-lg bg-white overflow-x-auto">
      <div class="min-w-[800px]">
        <!-- Day headers -->
        <div class="grid border-b border-gray-200" :style="{ gridTemplateColumns: `60px repeat(${days.length}, 1fr)` }">
          <!-- Empty corner cell -->
          <div class="p-2" />
          <!-- Day columns -->
          <div
            v-for="day in days"
            :key="day"
            class="p-3 text-center font-bold text-sm text-gray-600 border-l border-gray-200"
>>>>>>> origin/frontend
          >
            {{ dayLabels[day] || day.slice(0, 3).toUpperCase() }}
          </div>
        </div>

<<<<<<< HEAD
        <!-- Grid body -->
        <div class="relative grid" :style="{ gridTemplateColumns: `56px repeat(${days.length}, 1fr)` }">
          <!-- Time labels -->
          <div class="relative" :style="{ height: `${gridHeight}px` }">
            <template v-for="hour in hours" :key="hour">
              <div
                class="absolute right-0 pr-2 text-[11px] font-semibold text-slate-400 dark:text-slate-500 -translate-y-1/2 select-none"
                :style="{ top: `${(hour - startHour) * hourHeight}px` }"
              >
                {{ formatHour(hour) }}
              </div>
            </template>
          </div>

          <!-- Day columns -->
          <div
            v-for="day in days"
            :key="day"
            class="relative border-l border-slate-200 dark:border-slate-700/60"
            :style="{ height: `${gridHeight}px` }"
          >
            <!-- Hour gridlines -->
            <div
              v-for="hour in hours"
              :key="`g-${hour}`"
              class="absolute w-full border-t border-slate-100 dark:border-slate-700/30"
=======
        <!-- Time grid body -->
        <div class="relative grid" :style="{ gridTemplateColumns: `60px repeat(${days.length}, 1fr)` }">
          <!-- Time labels column -->
          <div class="relative" :style="{ height: `${gridHeight}px` }">
            <div
              v-for="hour in hours"
              :key="hour"
              class="absolute right-2 text-xs text-gray-400 -translate-y-1/2"
              :style="{ top: `${(hour - startHour) * hourHeight}px` }"
            >
              {{ formatHour(hour) }}
            </div>
          </div>

          <!-- Day columns with sessions -->
          <div
            v-for="day in days"
            :key="day"
            class="relative border-l border-gray-200"
            :style="{ height: `${gridHeight}px` }"
          >
            <!-- Hour grid lines -->
            <div
              v-for="hour in hours"
              :key="`line-${hour}`"
              class="absolute w-full border-t border-gray-100"
>>>>>>> origin/frontend
              :style="{ top: `${(hour - startHour) * hourHeight}px` }"
            />

            <!-- Session blocks -->
            <template v-for="session in layoutSessions(schedule[day] ?? [])" :key="session.id">
              <component
                :is="linkable ? resolveComponent('NuxtLink') : 'div'"
                v-bind="linkable ? { to: `${linkBasePath}/${session.class_id}` } : {}"
<<<<<<< HEAD
                class="absolute z-10 mx-[3px] rounded-lg overflow-hidden flex flex-col justify-center transition-all duration-150"
                :class="[linkable ? 'hover:brightness-110 hover:shadow-lg cursor-pointer' : 'cursor-default']"
                :style="{
                  top: `${timeToTop(session.start_at) + 1}px`,
                  height: `${sessionHeight(session.start_at, session.end_at) - 2}px`,
                  backgroundColor: getColor(session.course_code).bg,
                  borderLeft: `3px solid ${getColor(session.course_code).border}`,
                  left: session.totalCols > 1 ? `${(session.col / session.totalCols) * 100}%` : '0',
                  right: session.totalCols > 1 ? `${((session.totalCols - session.col - 1) / session.totalCols) * 100}%` : '0',
                }"
              >
                <div class="flex flex-col items-center justify-center flex-1 px-1.5 py-1 text-white text-center min-h-0">
                  <p class="font-bold text-sm leading-tight truncate w-full">
                    {{ session.course_code }}
                  </p>
                  <p class="text-[11px] leading-tight opacity-90 truncate w-full mt-0.5">
                    {{ session.start_at?.slice(0, 5) }} - {{ session.end_at?.slice(0, 5) }}
                  </p>
                  <p class="text-[10px] leading-tight opacity-75 truncate w-full mt-0.5">
                    Section {{ session.section }}
                  </p>
                </div>
=======
                class="absolute mx-1 rounded-md shadow-sm overflow-hidden cursor-default flex flex-col items-center justify-center text-center px-1 transition-all"
                :class="[linkable ? 'hover:shadow-md hover:brightness-110 cursor-pointer' : '']"
                :style="{
                  top: `${timeToTop(session.start_at)}px`,
                  height: `${sessionHeight(session.start_at, session.end_at)}px`,
                  backgroundColor: getSessionColor(session.course_code).bg,
                  color: getSessionColor(session.course_code).text,
                  left: session.totalCols > 1 ? `${(session.col / session.totalCols) * 100}%` : '4px',
                  right: session.totalCols > 1 ? `${((session.totalCols - session.col - 1) / session.totalCols) * 100}%` : '4px',
                }"
              >
                <p class="font-bold text-xs sm:text-sm leading-tight truncate w-full">
                  {{ session.course_code }}
                </p>
                <p class="text-[10px] sm:text-xs leading-tight opacity-90 truncate w-full">
                  {{ session.start_at?.slice(0, 5) }} - {{ session.end_at?.slice(0, 5) }}
                </p>
                <p class="text-[9px] sm:text-[11px] leading-tight opacity-80 truncate w-full">
                  Section {{ session.section }}
                </p>
>>>>>>> origin/frontend
              </component>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
