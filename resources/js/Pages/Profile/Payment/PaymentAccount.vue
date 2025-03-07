<script setup>
import Tag from "primevue/tag";
import AddPaymentAccount from "@/Pages/Profile/Payment/AddPaymentAccount.vue";
import EmptyData from "@/Components/EmptyData.vue";
import {ref, watchEffect} from "vue";
import {usePage} from "@inertiajs/vue3";
import PaymentAccountAction from "@/Pages/Profile/Payment/PaymentAccountAction.vue";

defineProps({
    title: String,
});

const paymentAccounts = ref([]);
const isLoading = ref(false);

const getResults = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/profile/getPaymentAccounts');
        paymentAccounts.value = response.data.paymentAccounts;
    } catch (error) {
        console.error('Error changing locale:', error);
    } finally {
        isLoading.value = false;
    }
};

getResults();

const tooltipText = ref('copy')
const copiedText = ref('')

function copyToClipboard(text) {
    const textToCopy = text;
    copiedText.value = text;

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

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
    }
});
</script>

<template>
    <div class="flex flex-col gap-5 items-center self-stretch w-full">
        <div class="flex flex-col md:flex-row md:justify-center gap-5 items-center self-stretch w-full">
            <div class="flex flex-col w-full">
                <div class="flex text-lg font-semibold items-center">
                    {{ $t('public.payment_account') }}
                </div>
                <span class="text-sm text-surface-500">{{ $t('public.payment_account_caption') }}</span>
            </div>

            <AddPaymentAccount />
        </div>

        <div v-if="usePage().props.auth.paymentAccountsCount === 0" class="">
            <EmptyData
                :title="$t('public.no_payment_account')"
                :message="$t('public.no_payment_account_desc')"
            />
        </div>
        <div
            v-else
            class="w-full"
        >
            <div
                v-if="isLoading"
                class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3 md:gap-5 w-full"
            >

            </div>

            <div v-else class="w-full">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-3 md:gap-5 w-full"
                >
                    <div
                        v-for="paymentAccount in paymentAccounts"
                        class="flex flex-col justify-between gap-5 bg-white dark:bg-surface-900 border-l-8 border-matisse-600 dark:border-matisse-800 shadow-toast rounded-md p-3 w-full"
                    >
                        <div class="flex gap-3 justify-between items-center self-stretch">
                            <span class="text-xs text-surface-600 dark:text-surface-400 uppercase">{{ $t(`public.${paymentAccount.payment_platform}`) }}</span>
                            <PaymentAccountAction
                                :paymentAccount="paymentAccount"
                            />
                        </div>
                        <div class="w-full relative">
                            <Tag
                                v-if="tooltipText === 'copied' && copiedText === paymentAccount.account_no"
                                class="absolute w-fit -top-6 left-24 !bg-surface-ground !text-white"
                                :value="$t(`public.${tooltipText}`)"
                            ></Tag>
                            <span @click="copyToClipboard(paymentAccount.account_no)" class="text-surface-ground dark:text-white break-words select-none cursor-pointer">{{ paymentAccount.account_no }}</span>
                        </div>
                        <div class="flex justify-between items-center gap-2 text-sm font-medium text-surface-600 dark:text-surface-400">
                            {{ paymentAccount.payment_account_name }}
                            <Tag
                                :severity="paymentAccount.platform === 'bank' ? 'info' : 'secondary'"
                                :value="paymentAccount.payment_platform_name"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
