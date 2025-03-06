<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import Skeleton from 'primevue/skeleton';
import InputIconWrapper from '@/Components/InputIconWrapper.vue';
import InputError from '@/Components/InputError.vue';
import { generalFormat } from '@/Composables/format';
import { IconCash, IconUserCheck, IconWallet } from '@tabler/icons-vue';
import { useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import {trans} from "laravel-vue-i18n";

const emit = defineEmits(['update:visible']);
const {formatAmount} = generalFormat();
const toast = useToast();

const form = useForm({
    wallet_id: '',
    transaction_charges: '',
    payment_account_id: '',
    amount: 0,
});

//wallet
const selectedWallet = ref();
const wallets = ref([]);
const loadingWallets = ref(false);
const totalBalance = ref(0);

const getWithdrawalWallets = async () => {
    try {
        loadingWallets.value = true
        const response = await axios.get('/getWithdrawalWallets');
        totalBalance.value = response.data.total_balance;
        if (response.data.wallets.length > 1) {
            wallets.value = [response.data.wallets[1]]; // Only show the bonus wallet
            selectedWallet.value = wallets.value[0];
            form.wallet_id = wallets.value[0].id;
        } else {
            wallets.value = [];
            selectedWallet.value = null;
            form.wallet = null;
        }
    } catch (error) {
        console.error('Error fetching withdrawal wallets data:', error);
    } finally {
        loadingWallets.value = false
    }
};

//max balance
const maxBalance = computed(() => Number(selectedWallet.value?.balance) || 0);

//payment_acount
const selectedPaymentAccount = ref();
const paymentAccounts = ref([]);
const loadingPaymentAccounts = ref(false);

const getPaymentAccounts = async () => {
    try {
        loadingPaymentAccounts.value = true
        const response = await axios.get('/getPaymentAccounts');
        paymentAccounts.value = response.data.paymentAccounts;
        selectedPaymentAccount.value = paymentAccounts.value[0];
    } catch (error) {
        console.error('Error fetching withdrawal wallets data:', error);
    } finally {
        loadingPaymentAccounts.value = false
    }
}

onMounted(() => {
    getPaymentAccounts();
    getWithdrawalWallets();
});

//fee
const withdrawalFee = computed(() => {
    const amount = Number(form.amount) || 0; // Ensure number
    return amount < 500 ? 3 : 5;
});

//receive
const amountReceive = computed(() => {
    const amount = Number(form.amount) || 0; // Ensure number
    return Math.max(amount - withdrawalFee.value, 0);
});

watch(() => form.amount, (newAmount) => {
    form.transaction_charges = withdrawalFee.value; // Update fee in form
});

const submitForm = () => {
    form.payment_account_id = selectedPaymentAccount.value.id;
    form.post(route('withdrawal'), {
        onSuccess: () => {
            emit('update:visible', false);
            form.reset();
            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_withdrawal_success'),
                life: 3000,
            });
        },
        onError: (errors) => {
            console.error(errors);
        }
    })
console.log(form);
}

</script>

<template>
    <form @submit.prevent="submitForm" class="flex flex-col gap-5 self-start">
        <div class="p-6 flex flex-col items-center gap-1 bg-gray-200 dark:bg-surface-800">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('public.available_balance_to_withdraw')}}
            </div>
            <Skeleton
                v-if="loadingWallets"
                width="8rem"
                height="2rem"
            />
            <div v-else class="text-xl font-bold text-gray-950 dark:text-white">
                {{ formatAmount(totalBalance) }}
            </div>
        </div>

        <div class="flex gap-5 self-stretch w-full">
            <div class="flex flex-col gap-1 self-start w-full">
                <InputLabel
                    :value="$t('public.wallet')"
                    for="wallet"
                />
                <InputIconWrapper>
                    <template #icon>
                        <IconWallet :size="20" stroke-width="1.5"/>
                    </template>
                    <Select 
                        v-model="selectedWallet"
                        :options="wallets"
                        optionLabel="type"
                        :loading="loadingWallets"
                        :placeholder="$t('public.select_wallet')"
                        class="pl-7 block w-full"
                        :invalid="!!form.errors.wallet_id"
                    >
                        <template #value="slotProps">
                            <div v-if="slotProps.value" class="flex items-center">
                                <div>{{ $t(`public.${slotProps.value.type}`) }}</div>
                            </div>
                            <span v-else>{{ slotProps.placeholder }}</span>
                        </template>

                        <template #option="slotProps">
                            {{ $t(`public.${slotProps.option.type}`) }}
                        </template>
                    </Select>

                </InputIconWrapper>
                <InputError :message="form.errors.wallet_id"/>
            </div>

            <div class="flex flex-col gap-1 self-start w-full">
                <InputLabel
                    :value="$t('public.amount')"
                    for="amount"
                />
                <InputIconWrapper>
                    <template #icon>
                        <IconCash size="20" stroke-width="1.5"/>
                    </template>
                    <InputNumber
                        v-model="form.amount"
                        class="block w-full"
                        :min="0"
                        :max="maxBalance"
                        mode="currency"
                        currency="USD"
                        fluid
                        placeholder="$0.00"
                        :invalid="!!form.errors.amount"
                        showButtons
                        :step="100"
                    />
                </InputIconWrapper>
                <div class="self-stretch text-gray-500 text-xs">
                    {{ $t('public.max') }}:
                    <span class="font-semibold text-sm dark:text-white">{{ formatAmount(maxBalance) }}</span>
                </div>
                <InputError :message="form.errors.amount"/>
            </div>
        </div>

        <!-- To Payment Account -->
        <div class="flex flex-col gap-1 self-start w-full">
            <InputLabel
                for="to_payment_account"
                :value="$t('public.to_account')"
            />
            <InputIconWrapper>
                <template #icon>
                    <IconUserCheck size="20" stroke-width="1.5"/>
                </template>
                <Select
                    input-id="to_payment_account"
                    v-model="selectedPaymentAccount"
                    :options="paymentAccounts"
                    :placeholder="$t('public.select_account')"
                    class="pl-7 block w-full"
                    :invalid="!!form.errors.payment_account_id"
                    :loading="loadingPaymentAccounts"
                >
                    <template #value="slotProps">
                        <div v-if="slotProps.value">
                            {{ slotProps.value.payment_platform_name }} ({{ slotProps.value.payment_account_name}})
                        </div>
                        <span v-else>{{ slotProps.placeholder }}</span>
                    </template>
                    <template #option="slotProps">
                        {{ slotProps.option.payment_platform_name }} ({{ slotProps.option.payment_account_name}})
                    </template>
                </Select>
            </InputIconWrapper>
            <InputError :message="form.errors.payment_account_id"/>
        </div>

        <!-- withdrawal detail -->
        <div class="flex flex-col gap-3 border-t border-gray-300 dark:border-gray-700 pt-5">
            <!-- Withdraw Amount -->
            <div class="flex flex-col gap-1 self-stretch w-full">
                <div class="flex items-center justify-between">
                    <span class="text-sm dark:text-gray-400">{{$t('public.withdrawal_amount')}}</span>
                    <span class="text-sm dark:text-white font-bold">{{ formatAmount(form.amount || 0) }}</span>
                </div>
            </div>

            <!-- Fee -->
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="text-sm dark:text-gray-400">{{ $t('public.withdrawal_charges') }}</span>
                </div>
                <div class="text-sm dark:text-white text-right font-bold">
                    {{ formatAmount(form.transaction_charges) }}
                </div>
            </div>

            <!-- Withdraw Amount -->
            <div class="flex items-center justify-between">
                <span class="text-sm dark:text-gray-400">{{$t('public.receive_amount')}}</span>
                <span class="text-sm dark:text-white font-bold">{{ formatAmount(amountReceive) }}</span>
            </div>
        </div>

        <div class="flex justify-end">
            <Button
                severity="secondary"
                class="text-center mr-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="$emit('update:visible', false)"
            >
                {{ $t('public.cancel') }}
            </Button>
            <Button
                class="text-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                type="submit"
            >
                {{ $t('public.confirm') }}
            </Button>
        </div>
    </form>
</template>
