import type { NavigationGuard } from 'vue-router'
export type MiddlewareKey = "admin" | "auth" | "guest" | "instructor" | "student" | "sanctum:auth" | "sanctum:guest"
declare module 'nuxt/app' {
  interface PageMeta {
    middleware?: MiddlewareKey | NavigationGuard | Array<MiddlewareKey | NavigationGuard>
  }
}