<script setup lang="ts">
const { init } = useDarkMode()
onMounted(init)
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
  <ToastContainer />
</template>
