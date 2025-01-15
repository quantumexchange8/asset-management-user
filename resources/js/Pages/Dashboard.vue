<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { IconCopy } from '@tabler/icons-vue';
import { useToast } from "primevue/usetoast";
import { usePage, Link } from '@inertiajs/vue3';
import Image from 'primevue/image';
import Tag from 'primevue/tag';
import Tooltip from 'primevue/tooltip';
import DashboardWallets from './Profile/Dashboard/DashboardWallets.vue';
import dayjs from 'dayjs';

const props = defineProps({
    user_wallet_count: Number,
    wallet_transaction_history: Object,
});

const user = usePage().props.auth.user

const toast = useToast();

const copyReferralCode = () => {
    const referralCode = document.querySelector('#userReferralCode').textContent;

    const tempInput = document.createElement('input');
    tempInput.value = referralCode;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    toast.add({
        severity: 'success',
        summary: 'Success',
        detail: 'Referral Code Copied!',
        life: 3000,
    });
}

</script>

<template>
    <AuthenticatedLayout :title="'Dashboard'">
        <div class="flex flex-col lg:flex-row gap-5">
            <!-- Left Section: Overview, Affiliate Program, and Wallets -->
            <div class="flex flex-col items-stretch gap-5 w-full lg:w-[90%]">
                <div class="flex flex-col 2xl:flex-row gap-5 w-full">
                    <!-- Overview Card -->
                    <div class="w-full flex flex-col flex-grow">
                        <Card class="h-full">
                            <template #content>
                                <div class="flex flex-col gap-4 sm:flex-row sm:justify-between">
                                    <div class="flex gap-3 items-center justify-start w-full pr-4">
                                        <Image class="object-cover w-14 h-14 rounded-full"
                                            :src="user.profile_photo ? user.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                                            alt="userPic" />
                                        <div>
                                            <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                                                {{ user.username }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full flex justify-end items-start">
                                        <Tag severity="info" :value="'rank'" />
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 mt-5">
                                    <div class="text-lg pb-2 border-b">
                                        Overview
                                    </div>
                                    <div class="grid grid-cols-6 gap-4 w-full mt-4">
                                        <!-- Add overview-specific content here -->
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>

                    <!-- Affiliate Program Card -->
                    <div class="w-full flex flex-col flex-grow">
                        <Card class="h-full">
                            <template #content>
                                <div class="p-1 w-full flex flex-col gap-4 justify-center overflow-hidden h-full">
                                    <div class="flex flex-col">
                                        <div class="text-lg">
                                            Affiliate Program
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-5 w-full">
                                        <div class="flex flex-col justify-center gap-5 w-full">
                                            <div class="flex flex-col gap-2 items-center justify-center w-full">
                                                QR code placeholder
                                                <div class="flex items-center gap-3">
                                                    <span class="text-lg">Referral Code:</span> 
                                                    <span class="text-xl" id="userReferralCode">{{ user.referral_code }}</span>
                                                    <div>
                                                        <IconCopy :size="20" stroke-width="1.5"
                                                            class="w-5 hover:cursor-pointer"
                                                            @click.prevent="copyReferralCode" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <div class="text-gray-400 text-sm dark:text-gray-500">
                                                    Share your referral link through a QR code
                                                </div>
                                                <div class="flex w-full rounded-md">
                                                    QR link placeholder
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- Wallets Section -->
                <DashboardWallets 
                    :user_wallet_count = "user_wallet_count"
                />
            </div>

            <!-- Right Section: Transaction History -->
            <div class="flex flex-col gap-5 w-full sm:w-[480px] lg:pl-5 lg:border-l-2 lg:border-surface-300 dark:lg:border-surface-500 h-full">
                <Card>
                    <template #content>
                        <DataTable :value="props.wallet_transaction_history" :rows="5">
                            <template #header>
                                Transaction History
                            </template>

                            <template #empty>
                                <div class="flex flex-col items-center">
                                    <span>No data</span>
                                </div>
                            </template>

                            <template v-if="props.wallet_transaction_history?.length > 0">
                                <Column
                                    field="approval_at"
                                    sortable
                                >
                                    <template #header>
                                        <span class="block">wallet type</span>
                                    </template>
                                    <template #body="{ data }">
                                        
                                        <span v-if="data.from_wallet">
                                            {{ data.from_wallet.type.replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase()) }}
                                        </span>

                                        <span v-else>
                                            {{ data.to_wallet.type.replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase()) }}
                                        </span>

                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ dayjs(data.approval_at).format('YYYY-MM-DD') }}
                                        </div>

                                    </template>
                                </Column>

                                <Column
                                    field="amount"
                                >
                                    <template #header>
                                        <span class="text-center">amount</span>
                                    </template>
                                    <template #body="{ data }">
                                        <span 
                                            v-if="data.transaction_type === 'withdrawal'"
                                            class="text-red-500"
                                        >
                                            <span v-if="data.to_wallet">
                                               - {{ data.to_wallet.currency_symbol }}{{ data.amount }}
                                            </span>
                                            <span v-else>
                                               - {{ data.from_wallet.currency_symbol }}{{ data.amount }}
                                            </span>
                                        </span>

                                        <span v-else class="text-green-500">
                                            <span v-if="data.to_wallet">
                                               + {{ data.to_wallet.currency_symbol }}{{ data.amount }}
                                            </span>
                                            <span v-else>
                                               + {{ data.from_wallet.currency_symbol }}{{ data.amount }}
                                            </span>
                                        </span>
                                    </template>
                                </Column>
                            </template>
                        </DataTable>
                        <div v-if="props.wallet_transaction_history?.length > 0" class="flex justify-center mt-2">
                            <Link
                                :href="route('wallet.getWalletHistory')"
                                class="text-sm text-gray-600 hover:text-primary dark:hover:text-primary-500 focus:outline-none dark:text-gray-400"
                            >
                                Show More
                            </Link>
                        </div>

                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
