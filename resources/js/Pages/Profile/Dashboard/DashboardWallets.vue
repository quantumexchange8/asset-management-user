<script setup>
import { usePage } from '@inertiajs/vue3';
import Card from 'primevue/card';
import { onMounted, ref, watchEffect } from 'vue';
import Skeleton from 'primevue/skeleton';
import { IconCopy } from '@tabler/icons-vue';
import { useToast } from "primevue/usetoast";
import WalletAction from './WalletAction.vue';

const props = defineProps({
    user_wallet_count: Number,
});

const toast = useToast();

//copy wallet address
const copyWalletAddress = (address) => {
    const tempInput = document.createElement('input');
    tempInput.value = address; // Use the passed wallet address
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    toast.add({
        severity: 'success',
        summary: 'Success',
        detail: 'Wallet Address Copied!',
        life: 3000,
    });
}

//get wallets
const wallets = ref([]);
const isLoading = ref(false);

const getWalletData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/get_wallet_data');
        wallets.value = response.data.user_wallets;
    } catch (error) {
        console.error('Error refreshing wallets data:', error);
    } finally {
        isLoading.value = false
    }
};

onMounted(() => {
    getWalletData();
});

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getWalletData();
    }
});
</script>

<template>
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full">
        <Card v-for="index in user_wallet_count" class="col-span-1 border-t-8">
            <template #content>
                <div class="flex flex-col gap-2">
                    <div class="text-lg text-gray-600 dark:text-gray-400 font-bold">
                        <Skeleton width="10rem" class="my-[11px]"></Skeleton>
                        <Skeleton width="23rem md:w-10" class="my-[11px]"></Skeleton>
                        <Skeleton width="23rem md:w-10" class="my-[11px]"></Skeleton>
                        <Skeleton width="23rem md:w-10" class="my-[11px]"></Skeleton>
                        <Skeleton width="23rem md:w-10" class="my-[11px] "></Skeleton>
                    </div>
                </div>
            </template>
        </Card>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-5 w-full">
        <Card 
            v-for="wallet in wallets" 
            class="col-span-1 border-t-8"
            :class="[ 
                { 'border-t-primary-600 dark:border-t-primary-400': wallet.type === 'cash_wallet' },
                { 'border-t-purple-500 dark:border-t-[#D3AAFB]': wallet.type === 'bonus_wallet' },
            ]"
        >
            <template #content>
                <div class="flex flex-col gap-2">
                    <!-- Wallet Type and Address -->
                    <div class="text-lg text-gray-600 dark:text-gray-400 font-bold">
                        {{ wallet.type.replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase()) }}
                        <span class="inline-flex items-center gap-2">
                            ({{ wallet.address }})
                            <IconCopy 
                                :size="20" 
                                stroke-width="1.5"
                                class="w-5 hover:cursor-pointer" 
                                @click.prevent="copyWalletAddress(wallet.address)" />
                        </span>
                    </div>

                    <!-- Wallet Balance -->
                    <div class="text-xl font-semibold">
                        {{ wallet.currency_symbol }} {{ wallet.balance }}
                    </div>

                    <WalletAction 
                        :wallet="wallet"
                    />

                </div>
            </template>

        </Card>
    </div>
</template>
