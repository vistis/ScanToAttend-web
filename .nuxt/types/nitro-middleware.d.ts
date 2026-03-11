export type MiddlewareKey = "admin" | "auth" | "guest" | "instructor" | "student" | "sanctum:auth" | "sanctum:guest"
declare module 'nitropack' {
  interface NitroRouteConfig {
    appMiddleware?: MiddlewareKey | MiddlewareKey[] | Record<MiddlewareKey, boolean>
  }
  interface NitroRouteRules {
    appMiddleware?: MiddlewareKey | MiddlewareKey[] | Record<MiddlewareKey, boolean>
  }
}
declare module 'nitropack/types' {
  interface NitroRouteConfig {
    appMiddleware?: MiddlewareKey | MiddlewareKey[] | Record<MiddlewareKey, boolean>
  }
  interface NitroRouteRules {
    appMiddleware?: MiddlewareKey | MiddlewareKey[] | Record<MiddlewareKey, boolean>
  }
}