<template>
  <div class="min-h-screen bg-surface-50 dark:bg-surface-950">
    <AppNavbar />
    <main class="lg:pl-64 pt-14 lg:pt-0">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <slot />
      </div>
    </main>
  </div>
</template>
