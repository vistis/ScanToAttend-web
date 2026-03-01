<script setup lang="ts">
/**
 * WeeklySchedule — A visual weekly calendar grid that displays
 * class sessions as colored blocks, similar to a university timetable.
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
const dayLabels: Record<string, string> = {
  Monday: 'MON',
  Tuesday: 'TUE',
  Wednesday: 'WED',
  Thursday: 'THU',
  Friday: 'FRI',
  Saturday: 'SAT',
  Sunday: 'SUN',
}

// Time range for the grid (hours)
const startHour = 7
const endHour = 21 // up to 9 PM

const hours = computed(() => {
  const h: number[] = []
  for (let i = startHour; i <= endHour; i++) {
    h.push(i)
  }
  return h
})

/** Format hour number to display label like "8 AM", "1 PM" */
function formatHour(hour: number): string {
  if (hour === 0 || hour === 12) {
    return `12 ${hour < 12 ? 'AM' : 'PM'}`
  }
  return `${hour > 12 ? hour - 12 : hour} ${hour >= 12 ? 'PM' : 'AM'}`
}

/** Parse "HH:MM" or "HH:MM:SS" time string to fractional hours */
function timeToHours(time: string): number {
  const parts = time.split(':')
  return Number.parseInt(parts[0]) + Number.parseInt(parts[1]) / 60
}

/** Height of one hour row in pixels */
const hourHeight = 60

/** Convert a time string to a top offset in pixels relative to the grid */
function timeToTop(time: string): number {
  const h = timeToHours(time)
  return (h - startHour) * hourHeight
}

/** Get the height of a session block in pixels */
function sessionHeight(start: string, end: string): number {
  return (timeToHours(end) - timeToHours(start)) * hourHeight
}

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

  for (const day of props.days) {
    for (const session of (props.schedule[day] ?? [])) {
      codes.add(session.course_code)
    }
  }

  let i = 0
  for (const code of codes) {
    map[code] = courseColors[i % courseColors.length]
    i++
  }

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

  for (const group of groups) {
    const totalCols = group.length
    for (let col = 0; col < group.length; col++) {
      result.push({ ...group[col], col, totalCols })
    }
  }

  return result
}

/** Compute the total grid height */
const gridHeight = computed(() => (endHour - startHour) * hourHeight)
</script>

<template>
  <div class="w-full">
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
          >
            {{ dayLabels[day] || day.slice(0, 3).toUpperCase() }}
          </div>
        </div>

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
              :style="{ top: `${(hour - startHour) * hourHeight}px` }"
            />

            <!-- Session blocks -->
            <template v-for="session in layoutSessions(schedule[day] ?? [])" :key="session.id">
              <component
                :is="linkable ? resolveComponent('NuxtLink') : 'div'"
                v-bind="linkable ? { to: `${linkBasePath}/${session.class_id}` } : {}"
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
              </component>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
