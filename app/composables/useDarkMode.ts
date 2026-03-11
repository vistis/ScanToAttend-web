export function useDarkMode() {
  const isDark = useState('dark-mode', () => false)

  function init() {
    if (import.meta.server) return
    const stored = localStorage.getItem('dark-mode')
    if (stored === 'true' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      isDark.value = true
    }
    apply()
  }

  function apply() {
    if (import.meta.server) return
    document.documentElement.classList.toggle('dark', isDark.value)
  }

  function toggle() {
    isDark.value = !isDark.value
    localStorage.setItem('dark-mode', String(isDark.value))
    apply()
  }

  return { isDark, init, toggle }
}
