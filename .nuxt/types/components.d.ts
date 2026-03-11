
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

interface _GlobalComponents {
  AppNavbar: typeof import("../../app/components/AppNavbar.vue")['default']
  ConfirmModal: typeof import("../../app/components/ConfirmModal.vue")['default']
  CourseTable: typeof import("../../app/components/CourseTable.vue")['default']
  EmptyState: typeof import("../../app/components/EmptyState.vue")['default']
  LoadingState: typeof import("../../app/components/LoadingState.vue")['default']
  StatCard: typeof import("../../app/components/StatCard.vue")['default']
  StatusBadge: typeof import("../../app/components/StatusBadge.vue")['default']
  ToastContainer: typeof import("../../app/components/ToastContainer.vue")['default']
  WeeklySchedule: typeof import("../../app/components/WeeklySchedule.vue")['default']
  NuxtWelcome: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/welcome.vue")['default']
  NuxtLayout: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-layout")['default']
  NuxtErrorBoundary: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']
  ClientOnly: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/client-only")['default']
  DevOnly: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/dev-only")['default']
  ServerPlaceholder: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/server-placeholder")['default']
  NuxtLink: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-link")['default']
  NuxtLoadingIndicator: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']
  NuxtTime: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']
  NuxtRouteAnnouncer: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']
  NuxtImg: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']
  NuxtPicture: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']
  Icon: typeof import("../../node_modules/.pnpm/@nuxt+icon@1.15.0_magicast@0.5.2_vite@7.3.1_jiti@2.6.1_terser@5.46.0_yaml@2.8.2__vue@3.5.30_typescript@5.9.3_/node_modules/@nuxt/icon/dist/runtime/components/index")['default']
  NuxtPage: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/pages/runtime/page")['default']
  NoScript: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['NoScript']
  Link: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Link']
  Base: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Base']
  Title: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Title']
  Meta: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Meta']
  Style: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Style']
  Head: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Head']
  Html: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Html']
  Body: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Body']
  NuxtIsland: typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-island")['default']
  LazyAppNavbar: LazyComponent<typeof import("../../app/components/AppNavbar.vue")['default']>
  LazyConfirmModal: LazyComponent<typeof import("../../app/components/ConfirmModal.vue")['default']>
  LazyCourseTable: LazyComponent<typeof import("../../app/components/CourseTable.vue")['default']>
  LazyEmptyState: LazyComponent<typeof import("../../app/components/EmptyState.vue")['default']>
  LazyLoadingState: LazyComponent<typeof import("../../app/components/LoadingState.vue")['default']>
  LazyStatCard: LazyComponent<typeof import("../../app/components/StatCard.vue")['default']>
  LazyStatusBadge: LazyComponent<typeof import("../../app/components/StatusBadge.vue")['default']>
  LazyToastContainer: LazyComponent<typeof import("../../app/components/ToastContainer.vue")['default']>
  LazyWeeklySchedule: LazyComponent<typeof import("../../app/components/WeeklySchedule.vue")['default']>
  LazyNuxtWelcome: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/welcome.vue")['default']>
  LazyNuxtLayout: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-layout")['default']>
  LazyNuxtErrorBoundary: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-error-boundary.vue")['default']>
  LazyClientOnly: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/client-only")['default']>
  LazyDevOnly: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/dev-only")['default']>
  LazyServerPlaceholder: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/server-placeholder")['default']>
  LazyNuxtLink: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-link")['default']>
  LazyNuxtLoadingIndicator: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-loading-indicator")['default']>
  LazyNuxtTime: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-time.vue")['default']>
  LazyNuxtRouteAnnouncer: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-route-announcer")['default']>
  LazyNuxtImg: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtImg']>
  LazyNuxtPicture: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-stubs")['NuxtPicture']>
  LazyIcon: LazyComponent<typeof import("../../node_modules/.pnpm/@nuxt+icon@1.15.0_magicast@0.5.2_vite@7.3.1_jiti@2.6.1_terser@5.46.0_yaml@2.8.2__vue@3.5.30_typescript@5.9.3_/node_modules/@nuxt/icon/dist/runtime/components/index")['default']>
  LazyNuxtPage: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/pages/runtime/page")['default']>
  LazyNoScript: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['NoScript']>
  LazyLink: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Link']>
  LazyBase: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Base']>
  LazyTitle: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Title']>
  LazyMeta: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Meta']>
  LazyStyle: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Style']>
  LazyHead: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Head']>
  LazyHtml: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Html']>
  LazyBody: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/head/runtime/components")['Body']>
  LazyNuxtIsland: LazyComponent<typeof import("../../node_modules/.pnpm/nuxt@3.21.1_@parcel+watcher@2.5.6_@vue+compiler-sfc@3.5.30_cac@6.7.14_db0@0.3.4_ioredis_f47be8b5b16758c61b5da6aaf43b78bc/node_modules/nuxt/dist/app/components/nuxt-island")['default']>
}

declare module 'vue' {
  export interface GlobalComponents extends _GlobalComponents { }
}

export {}
