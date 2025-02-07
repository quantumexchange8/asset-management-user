<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Dialog from 'primevue/dialog';

// Icons
const bankIcon = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M9 10V2h6v8M4 22h16m-2-2V10H6v10m8-4h4" />
    </svg>`;
const cryptoIcon = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6c1.657 0 3-1.343 3-3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3zM4.5 16h15M5 20h14m-9-4h4" />
    </svg>`;
const editIcon = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-2.036a2.5 2.5 0 11-3.536-3.536l.707.707m0 0L4 15.5V19h3.5l10.293-10.293z" />
    </svg>`;
const deleteIcon = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 012 2v1m-1-3H6m-1 3V9a2 2 0 012-2m3-3h4m-4 0a2 2 0 00-2 2m6-2a2 2 0 00-2-2" />
    </svg>`;

const paymentAccounts = ref([
    {
        type: 'bank',
        bank_name: 'Example Bank',
        bank_account: '123456789',
        bank_type: 'Savings',
    },
    {
        type: 'crypto',
        wallet_name: 'My Crypto Wallet',
        crypto_network: 'Ethereum',
        token_address: '0x1234567890abcdef1234567890abcdef',
    },
]);

const showDialog = ref(false);
const selectedOption = ref('');
const isEditing = ref(false);
const editingIndex = ref(null);

const form = useForm({
    bank_name: '',
    bank_account: '',
    bank_type: '',
    wallet_name: '',
    crypto_network: '',
    token_address: '',
});

const openDialog = (type = '', index = null) => {
    showDialog.value = true;

    if (index !== null) {
        isEditing.value = true;
        editingIndex.value = index;

        const account = paymentAccounts.value[index];
        if (account.type === 'bank') {
            selectedOption.value = 'bank';
            form.bank_name = account.bank_name;
            form.bank_account = account.bank_account;
            form.bank_type = account.bank_type;
        } else if (account.type === 'crypto') {
            selectedOption.value = 'crypto';
            form.wallet_name = account.wallet_name;
            form.crypto_network = account.crypto_network;
            form.token_address = account.token_address;
        }
    } else {
        isEditing.value = false;
        editingIndex.value = null;
        selectedOption.value = '';
        form.reset();
    }
};

const savePaymentDetails = () => {
    const newAccount = {
        type: selectedOption.value,
        bank_name: form.bank_name,
        bank_account: form.bank_account,
        bank_type: form.bank_type,
        wallet_name: form.wallet_name,
        crypto_network: form.crypto_network,
        token_address: form.token_address,
    };

    if (isEditing.value && editingIndex.value !== null) {
        paymentAccounts.value[editingIndex.value] = newAccount;
    } else {
        paymentAccounts.value.push(newAccount);
    }

    form.reset();
    showDialog.value = false;
};

const deletePaymentAccount = (index) => {
    if (confirm('Are you sure you want to delete this payment account?')) {
        paymentAccounts.value.splice(index, 1);
    }
};
</script>

<template>
    <section>
        <!-- Header Section -->
        <header class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Payment Account</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Manage your payment accounts for withdrawals.
                </p>
            </div>
            <PrimaryButton @click="openDialog">+ Add Payment Account</PrimaryButton>
        </header>

        <!-- Payment Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div
                v-for="(account, index) in paymentAccounts"
                :key="account.bank_account || account.token_address"
                class="rounded-lg shadow-lg overflow-hidden"
            >
                <div
                    :class="account.type === 'bank' ? 'bg-gradient-to-r from-blue-400 to-blue-600' : 'bg-gradient-to-r from-yellow-400 to-yellow-600'"
                    class="px-6 py-4 text-white flex items-center gap-2"
                >
                    <span v-html="account.type === 'bank' ? bankIcon : cryptoIcon" class="w-8 h-8"></span>
                    <h3 class="text-lg font-semibold">
                        {{ account.type === 'bank' ? 'Bank Account' : 'Crypto Wallet' }}
                    </h3>
                </div>
                <div class="bg-white p-6">
                    <ul class="text-sm space-y-2 text-gray-800">
                        <li v-if="account.type === 'bank'">
                            <strong>Bank Name:</strong> {{ account.bank_name }}
                        </li>
                        <li v-if="account.type === 'bank'">
                            <strong>Account Number:</strong> {{ account.bank_account }}
                        </li>
                        <li v-if="account.type === 'bank'">
                            <strong>Bank Type:</strong> {{ account.bank_type }}
                        </li>
                        <li v-if="account.type === 'crypto'">
                            <strong>Wallet Name:</strong> {{ account.wallet_name }}
                        </li>
                        <li v-if="account.type === 'crypto'">
                            <strong>Crypto Network:</strong> {{ account.crypto_network }}
                        </li>
                        <li v-if="account.type === 'crypto'">
                            <strong>Token Address:</strong> {{ account.token_address }}
                        </li>
                    </ul>
                    <div class="mt-4 flex justify-end gap-4">
                        <PrimaryButton class="flex items-center gap-2 text-sm" @click="openDialog(account.type, index)">
                            <span v-html="editIcon"></span>
                            Edit
                        </PrimaryButton>
                        <PrimaryButton class="flex items-center gap-2 text-sm bg-red-500 hover:bg-red-600" @click="deletePaymentAccount(index)">
                            <span v-html="deleteIcon"></span>
                            Delete
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog for Adding/Editing Accounts -->
        <Dialog v-model:visible="showDialog" header="Manage Payment Account" :modal="true" :closable="true">
            <div>
                <p class="text-sm text-gray-600 mb-4">Select the type of payment account to add or edit:</p>
                <div class="flex justify-between gap-4">
                    <!-- Bank Account Button -->
                    <PrimaryButton
                        class="w-full bg-gradient-to-r from-blue-400 to-blue-600 text-white py-2 rounded-lg flex items-center justify-center gap-2"
                        @click="selectedOption = 'bank'"
                    >
                        <span v-html="bankIcon"></span>
                        BANK ACCOUNT
                    </PrimaryButton>

                    <!-- Crypto Wallet Button -->
                    <PrimaryButton
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded-lg flex items-center justify-center gap-2"
                        @click="selectedOption = 'crypto'"
                    >
                        <span v-html="cryptoIcon"></span>
                        CRYPTO WALLET
                    </PrimaryButton>
                </div>
            </div>

            <!-- Form -->
            <form v-if="selectedOption" @submit.prevent="savePaymentDetails" class="mt-6 space-y-4">
                <div v-if="selectedOption === 'bank'">
                    <InputLabel for="bank_name" value="Bank Name" />
                    <input
                        id="bank_name"
                        v-model="form.bank_name"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter bank name"
                    />
                    <InputLabel for="bank_account" value="Account Number" />
                    <input
                        id="bank_account"
                        v-model="form.bank_account"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter account number"
                    />
                    <InputLabel for="bank_type" value="Bank Type" />
                    <select
                        id="bank_type"
                        v-model="form.bank_type"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="savings">Savings</option>
                        <option value="checking">Checking</option>
                    </select>
                </div>

                <div v-if="selectedOption === 'crypto'">
                    <InputLabel for="wallet_name" value="Wallet Name" />
                    <input
                        id="wallet_name"
                        v-model="form.wallet_name"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                        placeholder="Enter wallet name"
                    />
                    <InputLabel for="crypto_network" value="Crypto Network" />
                    <input
                        id="crypto_network"
                        v-model="form.crypto_network"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                        placeholder="Enter crypto network"
                    />
                    <InputLabel for="token_address" value="Token Address" />
                    <input
                        id="token_address"
                        v-model="form.token_address"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                        placeholder="Enter token address"
                    />
                </div>

                <div class="flex justify-end gap-4">
                    <PrimaryButton type="button" @click="showDialog = false">Cancel</PrimaryButton>
                    <PrimaryButton type="submit">Save</PrimaryButton>
                </div>
            </form>
        </Dialog>
    </section>
</template>
