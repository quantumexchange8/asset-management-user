<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WalletOverview from './WalletOverview.vue';
import WalletHistoryTable from './WalletHistoryTable.vue';

const props = defineProps({
    user_wallet_count: Number,
});
</script>


<template>
    <AuthenticatedLayout :title="'Wallet History'">
        <div class="flex flex-col items-stretch gap-5 w-full">
            <div class="flex flex-col 2xl:flex-row gap-5 w-full">
                <WalletOverview 
                    :user_wallet_count="user_wallet_count"
                />
            </div>

            <WalletHistoryTable />
        </div>
       


    </AuthenticatedLayout>
</template>