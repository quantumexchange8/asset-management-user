<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import { Link, useForm } from '@inertiajs/vue3';
import {IconMail} from '@tabler/icons-vue';
import InputIconWrapper from '@/Components/InputIconWrapper.vue';
import AuthHeader from "@/Components/AuthHeader.vue";
import {ref} from "vue";

defineProps({
    status: {
        type: String,
    },
});

const isDisabled = ref(false);
const remainingTime = ref(0);

const form = useForm({
    email: '',
});

const submit = () => {
    isDisabled.value = true;
    remainingTime.value = 60;

    const timer = setInterval(() => {
        remainingTime.value--;
        if (remainingTime.value <= 0) {
            clearInterval(timer);
            isDisabled.value = false;
        }
    }, 1000);

    // Perform form submission logic here
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout title="forgot_password">
        <div class="flex flex-col gap-3 rounded-md p-5 dark:bg-surface-900 w-full max-w-[90vw] sm:max-w-md items-center justify-center">
            <AuthHeader
                :header="$t('public.forgot_password')"
                :caption="$t('public.forgot_password_caption')"
            />

            <div
                v-if="status"
                class="flex flex-col items-center text-sm text-green-600 dark:text-green-400"
            >
                <span>{{ $t('public.password_reset_link_sent') }}</span>
                <span class="font-semibold">{{ form.email }}</span>
            </div>

            <form @submit.prevent="submit" class="w-full">
                <div class="flex flex-col gap-3 w-full self-stretch">
                    <div class="flex flex-col gap-1 items-start self-stretch">
                        <InputLabel
                            :value="$t('public.email')"
                            for="email"
                        />
                        <InputIconWrapper>
                            <template #icon>
                                <IconMail :size="20" stroke-width="1.5"/>
                            </template>
                            <InputText
                                id="email"
                                type="email"
                                class="pl-10 block w-full"
                                v-model="form.email"
                                autofocus
                                :invalid="!!form.errors.email"
                                autocomplete="username"
                                :placeholder="$t('public.enter_email')"
                            />
                        </InputIconWrapper>
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="flex flex-col gap-1 pt-5 items-center">
                        <Button
                            class="w-full text-center font-semibold dark:text-surface-ground text-white"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing || isDisabled"
                            type="submit"
                        >
                            {{ isDisabled ? `${$t('public.retry_after')} ${remainingTime}s` : $t('public.send') }}
                        </Button>

                        <Link
                            :href="route('login')"
                            class="text-sm text-surface-600 hover:text-primary dark:hover:text-primary-500 focus:outline-none dark:text-surface-400"
                        >
                            {{ $t('public.back_to_login') }}
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
