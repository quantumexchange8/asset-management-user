<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { IconSun, IconMoon } from "@tabler/icons-vue";
import { toggleDarkMode, isDark } from '@/Composables'
import Button from "@/Components/Button.vue"
import { ref, computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'login', // Default type is "login"
  },
});

const isRegister = computed(() => props.type === 'register');
</script>

<template>
    <div class="flex flex-col min-h-screen bg-surface-0 dark:bg-surface-ground">
        <div class="flex py-3 px-5 md:px-10 justify-end items-center">
            <Button type="button" variant="gray-text" icon-only pill @click="() => { toggleDarkMode() }">
                <IconSun v-if="!isDark" size="20" stroke-width="1.5" />
                <IconMoon v-if="isDark" size="20" color="white" stroke-width="1.5" />
            </Button>
        </div>
        <div class="flex flex-1 flex-col justify-center items-center pb-12 md:px-8 xs:gap-y-[60px]">
            <div class="w-full flex md:flex-1 justify-center">
                <div
                    class="w-full flex flex-col justify-center items-center mx-5"
                    :class="isRegister ? 'max-w-3xl' : 'md:w-[420px]'"
                >
                    <ApplicationLogo class="h-16 w-96 fill-current text-primary" />
                    <div
                        class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:rounded dark:bg-surface-900"
                        :class="{
                            'sm:max-w-3xl lg:max-w-2xl md:max-w-xl xs:max-w-[95%]': isRegister, // Responsive width for register
                            'sm:max-w-md lg:max-w-sm xs:max-w-[90%]': !isRegister, // Responsive width for login
                        }"
                    >

                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>