<script setup>
import {ref} from "vue";
import {generalFormat} from "@/Composables/format.js";
import Skeleton from "primevue/skeleton";

const wallets = ref([]);
const loadingWallets = ref(false);
const {formatAmount} = generalFormat();
const emit = defineEmits(["update:wallets"])

const getWallets = async () => {
    loadingWallets.value = true;
    try {
        const response = await axios.get('/dashboard/getWallets');
        wallets.value = response.data.wallets;
        emit('update:wallets', wallets.value)
    } catch (error) {
        console.error('Error fetching wallets:', error);
    } finally {
        loadingWallets.value = false;
    }
}

getWallets();
</script>

<template>
    <div class="flex flex-col md:flex-row gap-3 md:gap-5 w-full items-start self-stretch">
        <div
            v-if="loadingWallets"
            v-for="wallet in 2"
            class="flex flex-col gap-8 p-3 rounded-lg w-full bg-surface-100 dark:bg-surface-800"
        >
            <div class="text-sm font-bold text-white">
                <Skeleton width="5rem" height="1.25rem"></Skeleton>
            </div>
            <div class="flex flex-col self-stretch">
                <div class="text-2xl font-bold text-white">
                    <Skeleton width="10rem" height="1.75rem" class="mb-1.5"></Skeleton>
                </div>
                <div class="flex justify-between items-center">
                    <Skeleton width="4rem"></Skeleton>
                    <Skeleton width="8rem"></Skeleton>
                </div>
            </div>
        </div>
        <div
            v-else
            v-for="wallet in wallets"
            :key="wallet.id"
            class="flex flex-col gap-8 p-3 rounded-lg w-full transition-colors duration-200"
            :class="wallet.type === 'cash_wallet' ? 'bg-primary-300 dark:bg-primary' : 'bg-matisse-500 dark:bg-matisse-900'"
        >
            <div class="text-sm font-bold text-white">
                {{ $t(`public.${wallet.type}`) }}
            </div>
            <div class="flex flex-col self-stretch">
                <div class="text-2xl font-bold text-white">
                    {{ formatAmount(wallet.balance, 4) }}
                </div>
                <div
                    class="flex justify-between items-center"
                    :class="wallet.type === 'cash_wallet' ? 'text-surface-600 dark:text-surface-700' : 'text-surface-200'"
                >
                    <span class="text-xs">{{ wallet.user.username }}</span>
                    <span class="text-xs font-medium">{{ wallet.address }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
