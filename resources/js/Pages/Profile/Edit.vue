<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import PaymentAccountForm from './Partials/PaymentAccountForm.vue';
import AddPaymentAccount from './Partials/AddPaymentAccount.vue';
import EditAccountInfo from './Partials/EditAccountInfo.vue';
import KYCVerificationForm from './Partials/KYCVerificationForm.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

// Props for email verification and status
defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

// State for managing payment accounts
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

// State for dialogs
const isAdding = ref(false);
const isEditing = ref(false);
const editingIndex = ref(null);

// Methods for dialogs
const openAddDialog = () => {
    isAdding.value = true;
};

const openEditDialog = (index) => {
    editingIndex.value = index;
    isEditing.value = true;
};

const addPaymentAccount = (newAccount) => {
    paymentAccounts.value.push(newAccount);
    isAdding.value = false;
};

const updatePaymentAccount = (updatedAccount) => {
    if (editingIndex.value !== null) {
        paymentAccounts.value[editingIndex.value] = updatedAccount;
        isEditing.value = false;
    }
};
</script>

<template>
    <AuthenticatedLayout title="profile">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <!-- Profile Information and Password Update -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-surface-900">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-surface-900">
                    <UpdatePasswordForm />
                </div>
            </div>

            <!-- KYC Verification Section -->
            <!--                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-surface-900">-->
            <!--                    <KYCVerificationForm />-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Payment Account Section &ndash;&gt;-->
            <!--                <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-surface-900">-->
            <!--                    <PaymentAccountForm-->
            <!--                        :accounts="paymentAccounts"-->
            <!--                        @edit="openEditDialog"-->
            <!--                        @add="openAddDialog"-->
            <!--                    />-->
            <!--                </div>-->

            <!--                &lt;!&ndash; Add Payment Account Dialog &ndash;&gt;-->
            <!--                <AddPaymentAccount-->
            <!--                    v-if="isAdding"-->
            <!--                    @add="addPaymentAccount"-->
            <!--                    @close="isAdding = false"-->
            <!--                />-->

            <!--                &lt;!&ndash; Edit Payment Account Dialog &ndash;&gt;-->
            <!--                <EditAccountInfo-->
            <!--                    v-if="isEditing"-->
            <!--                    :account="paymentAccounts[editingIndex]"-->
            <!--                    @update="updatePaymentAccount"-->
            <!--                    @close="isEditing = false"-->
            <!--                />-->
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@media (max-width: 1024px) {
    .grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
