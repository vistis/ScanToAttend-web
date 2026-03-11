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
<<<<<<< HEAD
    baseUrl: 'http://localhost',
=======
    baseUrl: 'http://localhost:8000',
>>>>>>> origin/frontend
    mode: 'cookie',
    userStateKey: 'sanctum.user.identity',
    redirectIfAuthenticated: false,
    redirectIfUnauthenticated: false,
    endpoints: {
      csrf: '/sanctum/csrf-cookie',
<<<<<<< HEAD
      login: '/api/login',
      logout: '/api/logout',
      user: '/api/user',
    },
    csrf: {
      cookie: 'XSRF-TOKEN',
      header: 'X-XSRF-TOKEN',
    },
=======
      login: '/login',
      logout: '/logout',
      user: '/api/user',
    },
    // csrf: {
    //   cookie: 'XSRF-TOKEN',
    //   header: 'X-XSRF-TOKEN',
    // },
>>>>>>> origin/frontend
  },

  runtimeConfig: {
    public: {
<<<<<<< HEAD
      apiBase: 'http://localhost/api',
=======
      apiBase: 'http://localhost:8000/api',
>>>>>>> origin/frontend
    },
  },
})
