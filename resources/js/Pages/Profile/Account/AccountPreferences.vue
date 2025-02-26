<script setup>
import Card from "primevue/card";
import UpdateProfileInformationForm from "@/Pages/Profile/Account/UpdateProfileInformationForm.vue";
import UploadKyc from "@/Pages/Profile/Account/UploadKyc.vue";
import {usePage} from "@inertiajs/vue3";
import Tag from "primevue/tag";
import Message from 'primevue/message';
import InputLabel from "@/Components/InputLabel.vue";
import Image from "primevue/image";

defineProps({
    title: String,
    front_identity_image: String,
    back_identity_image: String,
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
        <div class="flex flex-col md:flex-row gap-5 self-stretch w-full">
            <Card class="w-full">
                <template #title>
                    <div class="flex flex-col">
                        <div class="flex gap-2 items-center">
                            {{ $t('public.profile_information') }}
                        </div>
                        <span class="text-sm text-surface-500">{{ $t('public.profile_information_caption') }}</span>
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
                    <div class="flex flex-col gap-5 items-center w-full">
                        <div
                            v-if="usePage().props.auth.user.kyc_status === 'rejected'"
                            class="text-sm w-full"
                        >
                            <Message closable severity="error">{{ usePage().props.auth.user.kyc_approval_description }}ss</Message>
                        </div>
                        <div class="flex flex-col gap-2 bg-surface-100 dark:bg-surface-800 p-5">
                            <span class="text-sm dark:text-white font-semibold">{{ $t('public.take_id_photo') }}</span>
                            <div v-for="(step, index) in 2" :key="index" class="text-sm text-surface-500 dark:text-surface-400">
                                {{ $t(`public.upload_kyc_info_${index+1}`) }}
                            </div>
                        </div>
                        <div
                            v-if="usePage().props.auth.user.kyc_status !== 'verified'"
                            class="w-full"
                        >
                            <UploadKyc />
                        </div>
                        <div v-else class="w-full">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
                                <div class="flex flex-col gap-1 items-start self-stretch">
                                    <InputLabel for="front_identity">{{ $t('public.front_identity' )}}</InputLabel>
                                    <div
                                        class="flex flex-col gap-3 items-center self-stretch px-5 py-8 rounded-md border-2 border-dashed transition-colors duration-150 bg-surface-50 dark:bg-surface-ground border-surface-300 dark:border-surface-600"
                                    >
                                        <Image
                                            role="presentation"
                                            alt="front_identity_image"
                                            :src="front_identity_image"
                                            preview
                                            imageClass="w-full object-contain h-40"
                                        />
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 items-start self-stretch">
                                    <InputLabel for="front_identity">{{ $t('public.back_identity' )}}</InputLabel>
                                    <div
                                        class="flex flex-col gap-3 items-center self-stretch px-5 py-8 rounded-md border-2 border-dashed transition-colors duration-150 bg-surface-50 dark:bg-surface-ground border-surface-300 dark:border-surface-600"
                                    >
                                        <Image
                                            role="presentation"
                                            alt="back_identity_image"
                                            :src="back_identity_image"
                                            preview
                                            imageClass="w-full object-contain h-40"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>
