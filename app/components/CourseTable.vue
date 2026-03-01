<script setup lang="ts">
/**
 * CourseTable — displays a list of courses in a table format
 * with Course Code + Section, Course Title, Instructor, and Schedule columns.
 */

interface Session {
  id: number
  day: string
  start_at: string
  end_at: string
}

interface CourseClass {
  id: number
  course_code?: string
  code?: string
  course_name?: string
  name?: string
  section: number | string
  instructor_first_name?: string
  instructor_last_name?: string
  sessions?: Session[]
}

defineProps<{
  /** List of class objects from the API */
  classes: CourseClass[]
  /** Whether to show the instructor column */
  showInstructor?: boolean
  /** If provided, rows become links to this base path + class id */
  linkBasePath?: string
}>()

/** Short day abbreviation */
function dayAbbrev(day: string): string {
  const map: Record<string, string> = {
    Monday: 'Mon',
    Tuesday: 'Tue',
    Wednesday: 'Wed',
    Thursday: 'Thu',
    Friday: 'Fri',
    Saturday: 'Sat',
    Sunday: 'Sun',
  }
  return map[day] ?? day.slice(0, 3)
}

/** Format a session into a readable string like "(Tue) 10:00 - 10:50" */
function formatSession(session: Session): string {
  const day = dayAbbrev(session.day)
  const start = session.start_at?.slice(0, 5) ?? ''
  const end = session.end_at?.slice(0, 5) ?? ''
  return `(${day}) ${start} - ${end}`
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead>
        <tr class="border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
          <th class="py-3 px-4 font-semibold">
            Course Code
          </th>
          <th class="py-3 px-4 font-semibold">
            Course Title
          </th>
          <th
            v-if="showInstructor"
            class="py-3 px-4 font-semibold"
          >
            Instructor
          </th>
          <th class="py-3 px-4 font-semibold">
            Schedule
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="cls in classes"
          :key="cls.id"
          class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
        >
          <td class="py-3 px-4 font-medium text-gray-900 whitespace-nowrap">
            <component
              :is="linkBasePath ? resolveComponent('NuxtLink') : 'span'"
              v-bind="linkBasePath ? { to: `${linkBasePath}/${cls.id}`, class: 'text-primary-600 hover:text-primary-800 hover:underline' } : {}"
            >
              {{ cls.course_code ?? cls.code }} Section {{ cls.section }}
            </component>
          </td>
          <td class="py-3 px-4 text-gray-700">
            {{ cls.course_name ?? cls.name }}
          </td>
          <td
            v-if="showInstructor"
            class="py-3 px-4 text-gray-700 whitespace-nowrap"
          >
            <template v-if="cls.instructor_first_name || cls.instructor_last_name">
              {{ cls.instructor_first_name }} {{ cls.instructor_last_name }}
            </template>
            <span
              v-else
              class="text-gray-400"
            >—</span>
          </td>
          <td class="py-3 px-4 text-gray-600">
            <div
              v-if="cls.sessions && cls.sessions.length"
              class="space-y-0.5"
            >
              <div
                v-for="session in cls.sessions"
                :key="session.id"
                class="text-xs"
              >
                {{ formatSession(session) }}
              </div>
            </div>
            <span
              v-else
              class="text-gray-400 text-xs"
            >No schedule</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
