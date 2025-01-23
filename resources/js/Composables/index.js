import { useDark, useToggle } from '@vueuse/core'
import { reactive } from 'vue'

export const isDark = useDark({ disableTransition: false });
export const toggleDarkMode = useToggle(isDark)

export const sidebarState = reactive({
    isOpen: window.innerWidth > 1024,

    handleWindowResize() {
        sidebarState.isOpen = window.innerWidth > 1024;
    },
})
