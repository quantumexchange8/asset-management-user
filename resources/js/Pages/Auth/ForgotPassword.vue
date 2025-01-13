<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { IconMail } from '@tabler/icons-vue';
import InputIconWrapper from '@/Components/InputIconWrapper.vue';


defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});


const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="grid gap-6 py-2">
                <div class="space-y-2">
                    <InputIconWrapper>
                        <template #icon>
                            <IconMail :size="20"/>
                        </template>
                        <InputText
                            id="email"
                            type="email"
                            class="pl-10 mt-1 block w-full"
                            v-model="form.email"
                            autofocus
                            placeholder="Email"
                            autocomplete="username"
                            :invalid="form.errors.email"
                        />
                    </InputIconWrapper>
                
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="flex items-center justify-between gap-3">
                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="w-full text-center font-semibold text-primary-500"
                        type="button"
                        outlined
                    >
                        <Link :href="route('login')">Cancel</Link>
                    </Button>

                    <Button
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="w-full text-center font-semibold dark:text-surface-ground text-white"
                        type="submit"
                    >
                       Submit
                    </Button>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
