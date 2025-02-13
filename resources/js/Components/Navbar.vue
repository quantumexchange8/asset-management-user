<script setup>
import { isDark, sidebarState, toggleDarkMode } from '@/Composables'
import {
    IconLogout,
    IconMenu2,
    IconMoon,
    IconSun,
    IconWorld
} from '@tabler/icons-vue';
import { router } from "@inertiajs/vue3";
import Button from '@/Components/Button.vue';
import { Link } from '@inertiajs/vue3';
import {ref} from "vue";
import {loadLanguageAsync} from "laravel-vue-i18n";
import Menu from "primevue/menu";
import Avatar from 'primevue/avatar';

defineProps({
    title: String
})

const menu = ref();
const locales = ref([
    {
        label: 'English',
        command: () => {
            changeLanguage('en');
        }
    },
    {
        label: '中文',
        command: () => {
            changeLanguage('cn')
        }
    }
]);

const toggle = (event) => {
    menu.value.toggle(event);
};

const changeLanguage = async (langVal) => {
    try {
        await loadLanguageAsync(langVal);
        await axios.get(`/locale/${langVal}`);
    } catch (error) {
        console.error('Error changing locale:', error);
    }
};

const handleLogOut = () => {
    router.post(route('logout'))
}
</script>

<template>
    <nav aria-label="secondary"
        class="sticky top-0 z-20 py-2 px-3 md:px-5 bg-surface-50 dark:bg-surface-ground transition-all duration-200 flex items-center gap-3">
        <Button type="button" variant="gray-text" icon-only pill @click="sidebarState.isOpen = !sidebarState.isOpen">
            <IconMenu2 size="20" stroke-width="1.25" />
        </Button>
        <div class="font-semibold text-gray-700 dark:text-gray-300 w-full">
            {{ $t(`public.${title}`) }}
        </div>
        <div class="flex items-center">
            <Button type="button" variant="gray-text" icon-only pill @click="() => { toggleDarkMode() }">
                <IconSun v-if="!isDark" size="20" stroke-width="1.5" />
                <IconMoon v-if="isDark" size="20" stroke-width="1.5" />
            </Button>

            <Button
                type="button"
                variant="gray-text"
                icon-only
                @click="toggle"
                aria-haspopup="true"
                aria-controls="overlay_menu"
                pill
            >
                <IconWorld size="20" stroke-width="1.5" />
            </Button>

            <Button external type="button" variant="gray-text" icon-only pill @click="handleLogOut">
                <IconLogout size="20" stroke-width="1.5" />
            </Button>
        </div>
    </nav>

    <Menu
        ref="menu"
        id="overlay_menu"
        :model="locales"
        :popup="true"
        class="w-32"
    />
</template>
