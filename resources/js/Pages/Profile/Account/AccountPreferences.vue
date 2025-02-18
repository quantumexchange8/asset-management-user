<script setup>
import Card from "primevue/card";
import {
    IconUserCircle
} from "@tabler/icons-vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Account/UpdateProfileInformationForm.vue";
import UploadKyc from "@/Pages/Profile/Account/UploadKyc.vue";
import {usePage} from "@inertiajs/vue3";
import Tag from "primevue/tag";

defineProps({
    title: String
});

const getSeverity = (status) => {
    switch (status) {
        case 'verified':
            return 'success';

        case 'unverified':
            return 'danger';

        case 'rejected':
            return 'danger';

        case 'pending':
            return 'warn';
    }
};
</script>

<template>
    <div class="flex flex-col gap-5 items-center w-full">
        <Card class="w-full">
            <template #title>
                <div class="flex gap-2 items-center">
                    <IconUserCircle size="28" />
                    <span>{{ $t(`public.${title}`) }}</span>
                </div>
            </template>
            <template #content>
                <UpdateProfileInformationForm />
            </template>
        </Card>

        <Card class="w-full">
            <template #title>
                <div class="flex flex-col">
                    <div class="flex gap-2 items-center">
                        <span>{{ $t('public.upload_identity_proof') }}</span>
                        <Tag
                            :value="$t(`public.${usePage().props.auth.user.kyc_status}`)"
                            :severity="getSeverity(usePage().props.auth.user.kyc_status)"
                        />
                    </div>
                    <span class="text-sm text-surface-500">{{ $t('public.upload_identity_proof_caption') }}</span>
                </div>
            </template>
            <template #content>
                <div
                    v-if="usePage().props.auth.user.kyc_status === 'rejected'"
                    class="text-sm mb-5"
                >
                    <span class="text-xs font-semibold text-surface-400">{{ $t('public.reason') }}: </span>
                    {{ usePage().props.auth.user.kyc_approval_description }}
                </div>
                <UploadKyc v-if="usePage().props.auth.user.kyc_status !== 'verified'" />
            </template>
        </Card>
    </div>
</template>
