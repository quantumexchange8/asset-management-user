<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { IconSun, IconMoon } from "@tabler/icons-vue";
import { toggleDarkMode, isDark } from '@/Composables'
import Button from "@/Components/Button.vue"
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: String
});
</script>

<template>
    <Head :title="$t(`public.${title}`)"></Head>

    <div class="flex flex-col min-h-screen bg-surface-0 dark:bg-surface-ground transition-all duration-200 w-full">
        <div class="flex py-3 px-5 md:px-10 justify-end items-center">
            <Button type="button" variant="gray-text" icon-only pill @click="() => { toggleDarkMode() }">
                <IconSun v-if="!isDark" size="20" stroke-width="1.5" />
                <IconMoon v-if="isDark" size="20" color="white" stroke-width="1.5" />
            </Button>
        </div>
        <div class="flex flex-col justify-center items-center pb-12 md:px-8 w-full">
            <div class="w-full flex flex-col gap-8 justify-center items-center">
                <ApplicationLogo class="h-16 w-fit fill-current text-primary" />
                <slot />
            </div>
        </div>
    </div>
</template>
