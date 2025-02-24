<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {Link, useForm} from '@inertiajs/vue3';
import AuthHeader from "@/Components/AuthHeader.vue";
import {IconLock, IconMail} from "@tabler/icons-vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Password from "primevue/password";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="reset_password">
        <div class="flex flex-col gap-3 rounded-md p-5 dark:bg-surface-900 w-full max-w-[90vw] sm:max-w-md items-center justify-center">
            <AuthHeader
                :header="$t('public.reset_password')"
                :caption="$t('public.reset_password_caption')"
            />

            <form @submit.prevent="submit" class="w-full">
                <div class="flex flex-col gap-3 w-full self-stretch">
                    <div class="flex flex-col gap-1 items-start self-stretch">
                        <InputLabel for="email">{{ $t('public.email') }}</InputLabel>
                        <InputIconWrapper>
                            <template #icon>
                                <IconMail :size="20" stroke-width="1.5"/>
                            </template>
                            <InputText
                                id="email"
                                type="email"
                                class="pl-10 block w-full"
                                v-model="form.email"
                                disabled
                                :invalid="!!form.errors.email"
                                autocomplete="username"
                                :placeholder="$t('public.enter_email')"
                            />
                        </InputIconWrapper>
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="flex flex-col gap-1 items-start self-stretch">
                        <InputLabel :value="$t('public.password')" for="password"/>
                        <InputIconWrapper>
                            <template #icon>
                                <IconLock :size="20" stroke-width="1.5" />
                            </template>

                            <Password
                                v-model="form.password"
                                :invalid="!!form.errors.password"
                                class="block pl-10 w-full"
                                placeholder="••••••••"
                                toggleMask
                                :promptLabel="$t('public.password')"
                                autofocus
                            />
                        </InputIconWrapper>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex flex-col gap-1 items-start self-stretch">
                        <InputLabel :value="$t('public.confirm_password')" for="password_confirmation"/>
                        <InputIconWrapper>
                            <template #icon>
                                <IconLock :size="20" stroke-width="1.5"/>
                            </template>

                            <Password
                                v-model="form.password_confirmation"
                                :invalid="!!form.errors.password"
                                class="block pl-10 w-full"
                                placeholder="••••••••"
                                toggleMask
                                :promptLabel="$t('public.password')"
                            />
                        </InputIconWrapper>
                    </div>

                    <div class="flex flex-col gap-1 pt-5 items-center">
                        <Button
                            class="w-full text-center font-semibold dark:text-surface-ground text-white"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            type="submit"
                        >
                            {{ $t('public.reset_password') }}
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
