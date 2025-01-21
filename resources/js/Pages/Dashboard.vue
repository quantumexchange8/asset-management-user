<template>
    <AuthenticatedLayout :title="'Dashboard'">
        <div class="flex flex-col gap-5">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- Left Section: User Overview and Wallets -->
                <div class="flex flex-col w-full lg:w-[70%] gap-5">
                    <!-- User Overview -->
                    <Card class="h-full">
                        <template #content>
                            <div class="flex justify-between items-center">
                                <!-- User Info -->
                                <div class="flex items-center gap-4">
                                    <Image
                                        class="w-16 h-16 rounded-full object-cover"
                                        :src="user.profile_photo || 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                                        alt="User Profile"
                                    />
                                    <div>
                                        <div class="text-lg font-semibold text-gray-800">Welcome Back, {{ user.username }}!</div>
                                    </div>
                                </div>
                                <!-- Total Assets Balance -->
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-800">$297,981.00</div>
                                    <div class="text-sm text-gray-500">Total Assets Balance</div>
                                </div>
                            </div>

                            <!-- Additional Overview Metrics -->
                            <div class="grid grid-cols-2 gap-4 mt-5">
                                <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                                    <div class="text-sm text-gray-600">Total Deposit</div>
                                    <div class="text-xl font-semibold text-gray-800">$10,000</div>
                                </div>
                                <div class="bg-gray-100 p-4 rounded-lg shadow-sm">
                                    <div class="text-sm text-gray-600">Total Group Sales</div>
                                    <div class="text-xl font-semibold text-gray-800">$50,000</div>
                                </div>
                            </div>
                        </template>
                    </Card>

          <!-- Wallet Section -->
<Card>
    <template #content>
        <div class="flex justify-between items-center">
            <div>
                <div class="text-lg font-semibold text-gray-800">My Wallet</div>
                <div class="text-sm text-gray-500">Manage your wallet</div>
            </div>
            <Button label="Manage Wallet" class="p-button-outlined" />
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
            <!-- Cash Wallet -->
            <Card class="bg-white shadow-md rounded-md">
                <template #content>
                    <div class="flex items-center gap-2 p-4 border-b border-gray-200">
                        <i class="pi pi-wallet text-2xl text-gray-700"></i>
                        <span class="text-lg font-bold text-gray-800">Cash Wallet (CW0000030)</span>
                    </div>
                    <div class="p-4">
                        <div class="text-2xl font-bold text-gray-900 mt-2">$ 0.00</div>
                        <div class="flex gap-2 mt-4">
                            <Button
                                label="Deposit"
                                icon="pi pi-plus"
                                class="p-button-sm"
                                style="background-color: #28a745; color: white; border: none"
                            />
                            <Button
                                label="Withdrawal"
                                icon="pi pi-minus"
                                class="p-button-sm"
                                style="background-color: #dc3545; color: white; border: none"
                            />
                            <Button
                                label="Internal Transfer"
                                icon="pi pi-exchange"
                                class="p-button-sm"
                                style="background-color: #007bff; color: white; border: none"
                            />
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Bonus Wallet -->
            <Card class="bg-white shadow-md rounded-md">
                <template #content>
                    <div class="flex items-center gap-2 p-4 border-b border-gray-200">
                        <i class="pi pi-gift text-2xl text-gray-700"></i>
                        <span class="text-lg font-bold text-gray-800">Bonus Wallet (BW0000030)</span>
                    </div>
                    <div class="p-4">
                        <div class="text-2xl font-bold text-gray-900 mt-2">$ 137.63</div>
                        <div class="flex gap-2 mt-4">
                            <Button
                                label="Internal Transfer"
                                icon="pi pi-exchange"
                                class="p-button-sm"
                                style="background-color: #007bff; color: white; border: none"
                            />
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </template>
</Card>


                    <!-- Last Transactions -->
                    <Card>
                        <template #content>
                            <div class="text-lg font-semibold text-gray-800">Last Transactions</div>
                            <DataTable :value="wallet_transaction_history" paginator :rows="5" class="mt-4">
                                <Column field="date" header="Date" />
                                <Column field="description" header="Description" />
                                <Column field="amount" header="Amount" />
                            </DataTable>
                        </template>
                    </Card>
                </div>

                <!-- Right Section: Transfers, Investments, and Referral Code -->
                <div class="flex flex-col w-full lg:w-[30%] gap-5">
                    <!-- Transfers -->
                    <Card>
                        <template #content>
                            <div class="text-lg font-semibold text-gray-800">Transfer</div>
                            <div class="flex flex-col gap-2 mt-4">
                                <Button label="Domestic Transfer" class="p-button-outlined" />
                                <Button label="Internal Bank Transfer" class="p-button-outlined" />
                                <Button label="Foreign Transfer" class="p-button-outlined" />
                            </div>
                        </template>
                    </Card>

                    <!-- Investments -->
                    <Card>
                        <template #content>
                            <div class="text-lg font-semibold text-gray-800">Investments</div>
                            <div class="text-sm text-gray-600 mt-4">Revenue: $18,752</div>
                            <div class="h-24 bg-gray-200 rounded-md mt-2 flex items-center justify-center">
                                Graph Placeholder
                            </div>
                        </template>
                    </Card>

                    <!-- Referral Code -->
                    <Card>
                        <template #content>
                            <div class="text-lg font-semibold text-gray-800">Referral Code</div>
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-800 font-medium">{{ user.referral_code || 'ABC123' }}</span>
                                    <Button label="Copy" class="p-button-outlined p-button-sm" @click="copyReferralCode" />
                                </div>
                                <div class="text-sm text-gray-500 mt-2">Share your referral code to earn rewards.</div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Image from 'primevue/image';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Mock Data
const user = usePage().props.auth.user;
const wallet_transaction_history = ref([
    { date: '2023-01-01', description: 'Deposit', amount: '$1000' },
    { date: '2023-01-02', description: 'Withdrawal', amount: '-$500' },
    { date: '2023-01-03', description: 'Transfer', amount: '$200' },
]);

// Copy referral code
const copyReferralCode = () => {
    navigator.clipboard.writeText(user.referral_code || 'ABC123').then(() => {
        alert('Referral code copied!');
    });g
};
</script>

<style scoped>
/* Scoped styles for custom adjustments */
</style>
