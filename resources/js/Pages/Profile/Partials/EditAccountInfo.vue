<script setup>
import { ref, watch, defineEmits } from 'vue';

const emit = defineEmits(['update', 'close']);
const props = defineProps(['account']);

const show = ref(true);
const form = ref({ ...props.account });

watch(
    () => props.account,
    (newValue) => {
        form.value = { ...newValue };
    }
);

const closeDialog = () => {
    show.value = false;
    emit('close');
};

const saveAccount = () => {
    emit('update', { ...form.value });
    closeDialog();
};
</script>

<template>
    <Dialog v-model:visible="show" header="Edit Payment Account" modal>
        <div v-if="form.type === 'bank'" class="space-y-4">
            <label>Bank Name</label>
            <input v-model="form.bank_name" type="text" class="w-full border rounded-lg p-2" />
            <label>Account Number</label>
            <input v-model="form.bank_account" type="text" class="w-full border rounded-lg p-2" />
            <label>Bank Type</label>
            <select v-model="form.bank_type" class="w-full border rounded-lg p-2">
                <option value="savings">Savings</option>
                <option value="checking">Checking</option>
            </select>
        </div>

        <div v-if="form.type === 'crypto'" class="space-y-4">
            <label>Wallet Name</label>
            <input v-model="form.wallet_name" type="text" class="w-full border rounded-lg p-2" />
            <label>Crypto Network</label>
            <input v-model="form.crypto_network" type="text" class="w-full border rounded-lg p-2" />
            <label>Token Address</label>
            <input v-model="form.token_address" type="text" class="w-full border rounded-lg p-2" />
        </div>

        <footer class="flex justify-end gap-4 mt-4">
            <button class="px-4 py-2 bg-gray-500 text-white rounded-lg" @click="closeDialog">Cancel</button>
            <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg" @click="saveAccount">Save</button>
        </footer>
    </Dialog>
</template>
