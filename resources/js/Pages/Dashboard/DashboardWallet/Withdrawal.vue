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

const props = defineProps({
    wallets: Array
})

const emit = defineEmits(['update:visible']);
const {formatAmount} = generalFormat();
const toast = useToast();

const form = useForm({
    wallet_id: '',
    transaction_charges: 0,
    payment_account_id: '',
    amount: 0,
});

const walletOptions = computed(() => props.wallets.filter(wallet => wallet.type === 'bonus_wallet'));

const selectedWallet = ref(walletOptions.value.length > 0 ? walletOptions.value[0] : null);

//payment account
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
        console.error('Error fetching payment accounts data:', error);
    } finally {
        loadingPaymentAccounts.value = false
    }
}

onMounted(() => {
    getPaymentAccounts();
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
    form.wallet_id = selectedWallet.value.id;
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
}

</script>

<template>
    <form @submit.prevent="submitForm" class="flex flex-col gap-5 self-start">
        <div class="p-6 flex flex-col items-center gap-1 bg-surface-200 dark:bg-surface-800">
            <div class="text-sm text-surface-600 dark:text-surface-400">
                {{ $t('public.available_balance_to_withdraw')}}
            </div>
            <div class="text-xl font-bold text-surface-950 dark:text-white">
                {{ formatAmount(selectedWallet.balance, 4) }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
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
                        input-id="wallet"
                        v-model="selectedWallet"
                        :options="walletOptions"
                        optionLabel="type"
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
                        input-id="amount"
                        class="block w-full"
                        :min="0"
                        mode="currency"
                        :max-fraction-digits="4"
                        currency="USD"
                        fluid
                        placeholder="$0.00"
                        :invalid="!!form.errors.amount"
                        :step="10"
                    />
                </InputIconWrapper>
                <div class="self-stretch text-surface-500 text-xs">
                    {{ $t('public.min') }}:
                    <span class="font-semibold dark:text-white">{{ formatAmount(100) }}</span>
                </div>
                <InputError :message="form.errors.amount"/>
            </div>

            <!-- To Payment Account -->
            <div class="flex flex-col gap-1 self-start w-full md:col-span-2">
                <InputLabel
                    for="to_payment_account"
                    :value="$t('public.payment_account')"
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
                                {{ slotProps.value.payment_platform_name }} - ({{ slotProps.value.payment_account_name}})
                            </div>
                            <span v-else>{{ slotProps.placeholder }}</span>
                        </template>
                        <template #option="slotProps">
                            {{ slotProps.option.payment_platform_name }} - ({{ slotProps.option.payment_account_name}})
                        </template>
                    </Select>
                </InputIconWrapper>
                <InputError :message="form.errors.payment_account_id"/>
            </div>
        </div>

        <!-- withdrawal detail -->
        <div class="flex flex-col gap-3 border-t border-surface-300 dark:border-surface-700 pt-5">
            <!-- Withdraw Amount -->
            <div class="flex flex-col gap-1 self-stretch w-full">
                <div class="flex items-center justify-between">
                    <span class="text-xs dark:text-surface-400">{{ $t('public.withdrawal_amount')}}</span>
                    <span class="text-sm dark:text-white font-bold">{{ formatAmount(form.amount || 0, 4) }}</span>
                </div>
            </div>

            <!-- Fee -->
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="text-xs dark:text-surface-400">{{ $t('public.withdrawal_charges') }}</span>
                </div>
                <div class="text-sm dark:text-white text-right font-bold">
                    {{ formatAmount(form.transaction_charges, 4) }}
                </div>
            </div>

            <!-- Withdraw Amount -->
            <div class="flex items-center justify-between">
                <span class="text-xs dark:text-surface-400">{{$t('public.receive_amount')}}</span>
                <span class="text-sm dark:text-white font-bold">{{ formatAmount(amountReceive, 4) }}</span>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <Button
                severity="secondary"
                class="w-full md:w-auto md:px-8"
                :disabled="form.processing"
                @click="$emit('update:visible', false)"
            >
                {{ $t('public.cancel') }}
            </Button>
            <Button
                class="w-full md:w-auto md:!px-8"
                :disabled="form.processing"
                type="submit"
            >
                {{ $t('public.confirm') }}
            </Button>
        </div>
    </form>
</template>
