// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-05-04',
  devtools: { enabled: true },

  future: {
    compatibilityVersion: 4,
  },

  css: ['~/assets/css/main.css'],

  modules: [
    '@nuxtjs/tailwindcss',
    'nuxt-auth-sanctum',
    '@nuxt/icon',
  ],

  tailwindcss: {
    cssPath: '~/assets/css/main.css',
    configPath: 'tailwind.config.ts',
  },

  sanctum: {
    baseUrl: 'http://localhost:8000',
    mode: 'cookie',
    userStateKey: 'sanctum.user.identity',
    redirectIfAuthenticated: false,
    redirectIfUnauthenticated: false,
    endpoints: {
      csrf: '/sanctum/csrf-cookie',
      login: '/login',
      logout: '/logout',
      user: '/api/user',
    },
    csrf: {
      cookie: 'XSRF-TOKEN',
      header: 'X-XSRF-TOKEN',
    },
  },

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api',
    },
  },
})
