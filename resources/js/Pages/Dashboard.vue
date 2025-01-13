<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { IconCopy } from '@tabler/icons-vue';
import { useToast } from "primevue/usetoast";
import { usePage } from '@inertiajs/vue3';
import Image from 'primevue/image';
import Tag from 'primevue/tag';
import Tooltip from 'primevue/tooltip';
import DashboardWallets from './Profile/Dashboard/DashboardWallets.vue';

const props = defineProps({
    user_wallet_count: Number,
});

const transactions = [
    { code: "TRX001", name: "Item A", category: "Category 1" },
    { code: "TRX002", name: "Item B", category: "Category 2" },
    { code: "TRX003", name: "Item C", category: "Category 1" },
    { code: "TRX003", name: "Item C", category: "Category 1" },
    { code: "TRX003", name: "Item C", category: "Category 1" },
];

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
            <div class="flex flex-col items-stretch gap-5 w-full">
                <div class="flex flex-col 2xl:flex-row gap-5 w-full">
                    <!-- Overview Card -->
                    <div class="p-1 w-full flex flex-col flex-grow">
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
                    <div class="p-1 w-full flex flex-col flex-grow">
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
            <div class="flex flex-col gap-5 w-full sm:w-[480px] lg:pl-5 lg:border-l-2 lg:border-gray-300 h-full">
                <Card>
                    <template #content>
                        <DataTable :value="transactions" :rows="5">
                            <template #header>
                                Transaction History
                            </template>
                            <template>
                                <Column field="code" header="Code" />
                                <Column field="name" header="Name" />
                                <Column field="category" header="Category" />
                            </template>
                        </DataTable>
                    </template>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
