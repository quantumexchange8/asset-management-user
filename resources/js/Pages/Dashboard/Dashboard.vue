<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DashboardOverview from "@/Pages/Dashboard/DashboardOverview.vue";
import Wallet from "@/Pages/Dashboard/DashboardWallet/Wallet.vue";
import LatestBonusTable from "@/Pages/Dashboard/LatestBonusTable.vue";
import WalletTransfer from "@/Pages/Dashboard/DashboardWallet/WalletTransfer.vue";
import DashboardReferral from "@/Pages/Dashboard/DashboardReferral.vue";
import {ref} from "vue";

defineProps({
    currentAssetCapital: {
        type: [String, Number]
    },
    currentTeamCapital: {
        type: [String, Number]
    },
    totalBonus: {
        type: [String, Number]
    },
    latestBonuses: Array
});

const wallets = ref([]);
</script>

<template>
    <AuthenticatedLayout title="dashboard">
        <div class="flex flex-col lg:flex-row gap-3 md:gap-5 w-full items-start self-stretch">
            <!-- Main Features -->
            <div class="flex flex-col gap-3 md:gap-5 w-full lg:basis-2/3">
                <!-- Overview -->
                <DashboardOverview
                    :currentAssetCapital="currentAssetCapital"
                    :currentTeamCapital="currentTeamCapital"
                    :totalBonus="totalBonus"
                />

                <!-- Wallet -->
                <Wallet
                    @update:wallets="wallets = $event"
                />

                <!-- Latest Bonus -->
                <LatestBonusTable
                    :latestBonuses="latestBonuses"
                />
            </div>

            <!-- Extra features -->
            <div class="flex flex-col gap-3 md:gap-5 w-full lg:basis-1/3">
                <WalletTransfer
                    :wallets="wallets"
                />
                <DashboardReferral />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
