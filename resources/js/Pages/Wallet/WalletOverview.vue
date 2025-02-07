<script setup>
import { usePage } from '@inertiajs/vue3';
import Card from 'primevue/card';
import Skeleton from 'primevue/skeleton';
import { useToast } from "primevue/usetoast";
import { onMounted, ref, watchEffect } from 'vue';
import { IconCopy } from '@tabler/icons-vue';

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
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 w-full gap-3 md:gap-5">
        <Card v-for="index in user_wallet_count">
            <template #content>
                <div class="flex flex-col  gap-2">
                    <div class="text-lg text-gray-600 dark:text-gray-400 font-bold">
                        <Skeleton width="10rem" class="my-[11px]"></Skeleton>
                        <Skeleton width="20rem" class="my-[11px]"></Skeleton>
                        <Skeleton width="20rem" class="my-[11px]"></Skeleton>
                        <Skeleton width="20rem" class="my-[11px]"></Skeleton>
                    </div>
                </div>
            </template>
        </Card>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 w-full gap-3 md:gap-5">
     
    </div>
</template>