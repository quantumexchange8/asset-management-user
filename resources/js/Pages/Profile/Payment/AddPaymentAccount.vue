<script setup>
import { ref } from 'vue';

const show = ref(true);
const form = ref({
    type: '',
    bank_name: '',
    bank_account: '',
    bank_type: '',
    wallet_name: '',
    crypto_network: '',
    token_address: '',
});

const closeDialog = () => {
    show.value = false;
    emit('close');
};

const saveAccount = () => {
    emit('add', { ...form.value });
    closeDialog();
};
</script>

<template>
    <Dialog v-model:visible="show" header="Add Payment Account" modal>
        <div>
            <button
                class="px-4 py-2 bg-blue-600 text-white rounded-lg mr-4"
                @click="form.type = 'bank'"
            >
                Bank Account
            </button>
            <button
                class="px-4 py-2 bg-green-600 text-white rounded-lg"
                @click="form.type = 'crypto'"
            >
                Crypto Wallet
            </button>
        </div>

        <div v-if="form.type === 'bank'" class="mt-4 space-y-4">
            <label>Bank Name</label>
            <input v-model="form.bank_name" class="w-full border rounded-lg p-2" />
            <label>Account Number</label>
            <input v-model="form.bank_account" class="w-full border rounded-lg p-2" />
            <label>Bank Type</label>
            <select v-model="form.bank_type" class="w-full border rounded-lg p-2">
                <option value="savings">Savings</option>
                <option value="checking">Checking</option>
            </select>
        </div>

        <div v-if="form.type === 'crypto'" class="mt-4 space-y-4">
            <label>Wallet Name</label>
            <input v-model="form.wallet_name" class="w-full border rounded-lg p-2" />
            <label>Crypto Network</label>
            <input v-model="form.crypto_network" class="w-full border rounded-lg p-2" />
            <label>Token Address</label>
            <input v-model="form.token_address" class="w-full border rounded-lg p-2" />
        </div>

        <footer class="mt-4 flex justify-end gap-4">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg" @click="closeDialog">Cancel</button>
            <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg" @click="saveAccount">Save</button>
        </footer>
    </Dialog>
</template>
