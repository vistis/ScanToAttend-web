
import type { DefineComponent, SlotsType } from 'vue'
type IslandComponent<T> = DefineComponent<{}, {refresh: () => Promise<void>}, {}, {}, {}, {}, {}, {}, {}, {}, {}, {}, SlotsType<{ fallback: { error: unknown } }>> & T

type HydrationStrategies = {
  hydrateOnVisible?: IntersectionObserverInit | true
  hydrateOnIdle?: number | true
  hydrateOnInteraction?: keyof HTMLElementEventMap | Array<keyof HTMLElementEventMap> | true
  hydrateOnMediaQuery?: string
  hydrateAfter?: number
  hydrateWhen?: boolean
  hydrateNever?: true
}
type LazyComponent<T> = DefineComponent<HydrationStrategies, {}, {}, {}, {}, {}, {}, { hydrated: () => void }> & T


export const AppNavbar: typeof import("../app/components/AppNavbar.vue")['default']
export const ConfirmModal: typeof import("../app/components/ConfirmModal.vue")['default']
export const CourseTable: typeof import("../app/components/CourseTable.vue")['default']
export const EmptyState: typeof import("../app/components/EmptyState.vue")['default']
export const LoadingState: typeof import("../app/components/LoadingState.vue")['default']
export const StatCard: typeof import("../app/components/StatCard.vue")['default']
export const StatusBadge: typeof import("../app/components/StatusBadge.vue")['default']
export const ToastContainer: typeof import("../app/components/ToastContainer.vue")['default']
export const WeeklySchedule: typeof import("../app/components/WeeklySchedule.vue")['default']
export const NuxtWelcome: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/welcome.vue")['default']
export const NuxtLayout: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-layout")['default']
export const NuxtErrorBoundary: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
export const ClientOnly: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/client-only")['default']
export const DevOnly: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/dev-only")['default']
export const ServerPlaceholder: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/server-placeholder")['default']
export const NuxtLink: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-link")['default']
export const NuxtLoadingIndicator: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
export const NuxtTime: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
export const NuxtRouteAnnouncer: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
export const NuxtImg: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
export const NuxtPicture: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
export const Icon: typeof import("../node_modules/.pnpm/@nuxt+icon@1.15.0_magicast@0.5.2_vite@7.3.1_jiti@2.6.1_terser@5.46.0_yaml@2.8.2__vue@3.5.30_typescript@5.9.3_/node_modules/@nuxt/icon/dist/runtime/components/index")['default']
export const NuxtPage: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/pages/runtime/page")['default']
export const NoScript: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['NoScript']
export const Link: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Link']
export const Base: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Base']
export const Title: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Title']
export const Meta: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Meta']
export const Style: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Style']
export const Head: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Head']
export const Html: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Html']
export const Body: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Body']
export const NuxtIsland: typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-island")['default']
export const LazyAppNavbar: LazyComponent<typeof import("../app/components/AppNavbar.vue")['default']>
export const LazyConfirmModal: LazyComponent<typeof import("../app/components/ConfirmModal.vue")['default']>
export const LazyCourseTable: LazyComponent<typeof import("../app/components/CourseTable.vue")['default']>
export const LazyEmptyState: LazyComponent<typeof import("../app/components/EmptyState.vue")['default']>
export const LazyLoadingState: LazyComponent<typeof import("../app/components/LoadingState.vue")['default']>
export const LazyStatCard: LazyComponent<typeof import("../app/components/StatCard.vue")['default']>
export const LazyStatusBadge: LazyComponent<typeof import("../app/components/StatusBadge.vue")['default']>
export const LazyToastContainer: LazyComponent<typeof import("../app/components/ToastContainer.vue")['default']>
export const LazyWeeklySchedule: LazyComponent<typeof import("../app/components/WeeklySchedule.vue")['default']>
export const LazyNuxtWelcome: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/welcome.vue")['default']>
export const LazyNuxtLayout: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
export const LazyNuxtErrorBoundary: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
export const LazyClientOnly: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/client-only")['default']>
export const LazyDevOnly: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/dev-only")['default']>
export const LazyServerPlaceholder: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/server-placeholder")['default']>
export const LazyNuxtLink: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-link")['default']>
export const LazyNuxtLoadingIndicator: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
export const LazyNuxtTime: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
export const LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
export const LazyNuxtImg: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
export const LazyNuxtPicture: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
export const LazyIcon: LazyComponent<typeof import("../node_modules/.pnpm/@nuxt+icon@1.15.0_magicast@0.5.2_vite@7.3.1_jiti@2.6.1_terser@5.46.0_yaml@2.8.2__vue@3.5.30_typescript@5.9.3_/node_modules/@nuxt/icon/dist/runtime/components/index")['default']>
export const LazyNuxtPage: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/pages/runtime/page")['default']>
export const LazyNoScript: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['NoScript']>
export const LazyLink: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Link']>
export const LazyBase: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Base']>
export const LazyTitle: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Title']>
export const LazyMeta: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Meta']>
export const LazyStyle: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Style']>
export const LazyHead: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Head']>
export const LazyHtml: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Html']>
export const LazyBody: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Body']>
export const LazyNuxtIsland: LazyComponent<typeof import("../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-island")['default']>

export const componentNames: string[]
