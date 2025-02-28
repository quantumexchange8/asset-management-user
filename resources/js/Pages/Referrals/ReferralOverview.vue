<script setup>
import Card from "primevue/card";
import {
    IconCopy,
    IconUsers,
    IconUsersGroup,
    IconCurrencyDollar,
    IconCircleCheck
} from "@tabler/icons-vue";
import QrcodeVue from 'qrcode.vue';
import {generalFormat} from "@/Composables/format.js";
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";
import {trans} from "laravel-vue-i18n";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import InputLabel from "@/Components/InputLabel.vue";
import Dialog from "primevue/dialog";

defineProps({
    total_directs: Number,
    total_networks: Number,
    total_earnings: Number,
})

const {formatAmount} = generalFormat();
const visible = ref(false);
const registerLink = ref(`${window.location.origin}/register/${usePage().props.auth.user.referral_code}`);
const tooltipText = ref('copy')

const copyToClipboard = (text) => {
    const textToCopy = text;

    const textArea = document.createElement('textarea');
    document.body.appendChild(textArea);

    textArea.value = textToCopy;
    textArea.select();

    try {
        const successful = document.execCommand('copy');

        tooltipText.value = 'copied';
        setTimeout(() => {
            tooltipText.value = 'copy';
        }, 1500);
    } catch (err) {
        console.error('Copy to clipboard failed:', err);
    }

    document.body.removeChild(textArea);
}

const qrcodeContainer = ref(null);
const canWebShare = ref(!!navigator.share);

const buttonLabel = computed(() =>
    canWebShare.value && window.innerWidth < 768
        ? 'share_referral_code'
        : 'reveal_qr_code'
);

const handleClick = () => {
    if (canWebShare.value) {
        shareRegisterLink();
    } else {
        revealQrCode();
    }
};

const toast = useToast();

const shareRegisterLink = async () => {
    try {
        await navigator.share({
            title: trans('public.invite_friends'),
            text: trans('public.click_the_link_to_register'),
            url: registerLink.value,
        });

        toast.add({
            severity: 'success',
            summary: trans('public.success'),
            detail: trans('public.register_link_shared'),
            life: 3000,
        })
    } catch (error) {
        console.error('Error sharing the register link:', error);
    }
};

const revealQrCode = () => {
    visible.value = true;
};

const downloadQrCode = () => {
    const canvas = qrcodeContainer.value.querySelector("canvas");
    const link = document.createElement("a");
    link.download = "referral-qr-code.png";
    link.href = canvas.toDataURL("image/png");
    link.click();
}
</script>

<template>
    <Card class="w-full">
        <template #content>
            <div class="flex gap-5 items-center self-stretch">
                <div ref="qrcodeContainer" class="hidden xl:block border border-surface-200 dark:border-surface-700 rounded">
                    <qrcode-vue
                        ref="qrcode"
                        :value="registerLink"
                        :size="200"
                        :margin="2"
                    />
                </div>
                <div class="flex flex-col gap-5 items-center self-stretch w-full">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 w-full transition-all duration-200">
                        <!-- Total Directs -->
                        <div class="flex justify-between items-center bg-surface-100 dark:bg-surface-800 rounded-md p-5">
                            <div class="flex items-end gap-1">
                                <div class="flex flex-col items-start gap-2">
                                    <div class="text-surface-500 text-sm uppercase font-medium">
                                        {{ $t('public.total_referrals') }}
                                    </div>
                                    <div class="text-3xl font-semibold">
                                        {{ formatAmount(total_directs, 0, '') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/40 w-[72px] h-[72px]">
                                <div class="flex items-center justify-center rounded-full bg-primary-200 dark:bg-primary-700 w-14 h-14 text-primary-600 dark:text-primary-300">
                                    <IconUsers size="36" stroke-width="1.5" />
                                </div>
                            </div>
                        </div>

                        <!-- Total Networks -->
                        <div class="flex justify-between items-center bg-surface-100 dark:bg-surface-800 rounded-md p-5">
                            <div class="flex items-end gap-1">
                                <div class="flex flex-col items-start gap-2">
                                    <div class="text-surface-500 text-sm uppercase font-medium">
                                        {{ $t('public.total_networks') }}
                                    </div>
                                    <div class="text-3xl font-semibold">
                                        {{ formatAmount(total_networks, 0, '') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 w-[72px] h-[72px]">
                                <div class="flex items-center justify-center rounded-full bg-blue-200 dark:bg-blue-700 w-14 h-14 text-blue-600 dark:text-blue-300">
                                    <IconUsersGroup size="36" stroke-width="1.5" />
                                </div>
                            </div>
                        </div>

                        <!-- Total Earning -->
                        <div class="flex justify-between items-center bg-surface-100 dark:bg-surface-800 rounded-md p-5">
                            <div class="flex items-end gap-1">
                                <div class="flex flex-col items-start gap-2">
                                    <div class="text-surface-500 text-sm uppercase font-medium">
                                        {{ $t('public.total_earnings') }}
                                    </div>
                                    <div class="text-3xl font-semibold">
                                        {{ formatAmount(total_earnings, 4) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-center rounded-full bg-green-200 dark:bg-green-900/40 w-[72px] h-[72px]">
                                <div class="flex items-center justify-center rounded-full bg-green-300 dark:bg-green-700 w-14 h-14 text-green-600 dark:text-green-300">
                                    <IconCurrencyDollar size="36" stroke-width="1.5" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 items-start self-stretch w-full">
                        <InputLabel>
                            {{ $t('public.your_referral_link') }}
                        </InputLabel>
                        <div class="flex flex-col md:flex-row gap-5 w-full">
                            <div class="relative w-full">
                                <InputText
                                    v-model="registerLink"
                                    readonly
                                    class="py-3"
                                />
                                <div
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 cursor-pointer text-sm font-semibold"
                                >
                                    <Button
                                        type="button"
                                        severity="secondary"
                                        @click="copyToClipboard(registerLink)"
                                        size="small"
                                        class="items-center gap-2"
                                    >
                                    <span
                                        :class="{
                                            'text-green-500 dark:text-green-400': tooltipText === 'copied'
                                        }"
                                    >{{ $t(`public.${tooltipText}`) }}</span>
                                        <IconCopy v-if="tooltipText === 'copy'" size="20" stroke-width="1.5" />
                                        <div v-else-if="tooltipText === 'copied'" class="text-green-500 dark:text-green-400">
                                            <IconCircleCheck  size="20" stroke-width="1.5" />
                                        </div>
                                    </Button>
                                </div>
                            </div>
                            <Button
                                type="button"
                                class="block w-full md:max-w-40 "
                                @click="handleClick"
                            >
                                {{ $t(`public.${buttonLabel}`) }}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Card>

    <Dialog
        v-model:visible="visible"
        modal
        header="&nbsp"
        class="dialog-xs md:dialog-sm"
    >
        <div class="flex flex-col gap-5 items-center self-stretch">
            <div class="flex flex-col gap-1 items-center self-stretch">
                <span class="font-semibold">{{ $t('public.referral_qr_code') }}</span>
                <span class="text-xs">{{ $t('public.scan_to_register') }}</span>
            </div>
            <div class="flex flex-col items-center gap-3 self-stretch pb-6">
                <div
                    ref="qrcodeContainer">
                    <qrcode-vue
                        ref="qrcode"
                        :value="registerLink"
                        :margin="2"
                        :size="200"
                    />
                </div>
                <Button
                    type="button"
                    @click="downloadQrCode"
                >
                    <span class="text-xs font-semibold">{{ $t('public.download_qr_to_share') }}</span>
                </Button>
            </div>
        </div>
    </Dialog>
</template>
