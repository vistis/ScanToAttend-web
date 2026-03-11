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
        <tr class="border-b border-slate-100 dark:border-slate-700">
          <th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider">
            Course Code
          </th>
          <th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider">
            Course Title
          </th>
          <th
            v-if="showInstructor"
            class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider"
          >
            Instructor
          </th>
          <th class="py-3.5 px-4 font-semibold text-xs uppercase text-slate-400 dark:text-slate-500 tracking-wider">
            Schedule
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="cls in classes"
          :key="cls.id"
          class="border-b border-slate-50 dark:border-slate-700/50 hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors"
        >
          <td class="py-3.5 px-4 font-semibold text-slate-900 dark:text-white whitespace-nowrap">
            <component
              :is="linkBasePath ? resolveComponent('NuxtLink') : 'span'"
              v-bind="linkBasePath ? { to: `${linkBasePath}/${cls.id}`, class: 'text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 hover:underline decoration-primary-300 dark:decoration-primary-600 underline-offset-2' } : {}"
            >
              {{ cls.course_code ?? cls.code }} Section {{ cls.section }}
            </component>
          </td>
          <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
            {{ cls.course_name ?? cls.name }}
          </td>
          <td
            v-if="showInstructor"
            class="py-3.5 px-4 text-slate-600 dark:text-slate-300 whitespace-nowrap"
          >
            <template v-if="cls.instructor_first_name || cls.instructor_last_name">
              {{ cls.instructor_first_name }} {{ cls.instructor_last_name }}
            </template>
            <span
              v-else
              class="text-slate-300 dark:text-slate-600"
            >—</span>
          </td>
          <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">
            <div
              v-if="cls.sessions && cls.sessions.length"
              class="space-y-0.5"
            >
              <div
                v-for="session in cls.sessions"
                :key="session.id"
                class="text-xs font-medium"
              >
                {{ formatSession(session) }}
              </div>
            </div>
            <span
              v-else
              class="text-slate-300 dark:text-slate-600 text-xs"
            >No schedule</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
