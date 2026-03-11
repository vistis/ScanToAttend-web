import type { ComputedRef, MaybeRef } from 'vue'

type ComponentProps<T> = T extends new(...args: any) => { $props: infer P } ? NonNullable<P>
  : T extends (props: infer P, ...args: any) => any ? P
  : {}

declare module 'nuxt/app' {
  interface NuxtLayouts {
    auth: ComponentProps<typeof import("/Users/long/Library/Mobile Documents/com~apple~CloudDocs/Developer/Paragon IU/Semester V/CS 397/Scan2Attend/ScanToAttend-nuxt/app/layouts/auth.vue").default>,
    dashboard: ComponentProps<typeof import("/Users/long/Library/Mobile Documents/com~apple~CloudDocs/Developer/Paragon IU/Semester V/CS 397/Scan2Attend/ScanToAttend-nuxt/app/layouts/dashboard.vue").default>,
    default: ComponentProps<typeof import("/Users/long/Library/Mobile Documents/com~apple~CloudDocs/Developer/Paragon IU/Semester V/CS 397/Scan2Attend/ScanToAttend-nuxt/app/layouts/default.vue").default>,
}
  export type LayoutKey = keyof NuxtLayouts extends never ? string : keyof NuxtLayouts
  interface PageMeta {
    layout?: MaybeRef<LayoutKey | false> | ComputedRef<LayoutKey | false>
  }
}