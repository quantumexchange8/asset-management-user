<script setup>
import Button from "@/Components/Button.vue";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import {IconCircleCheckFilled} from "@tabler/icons-vue";
import InputError from "@/Components/InputError.vue";
import Dialog from "primevue/dialog";
import InputLabel from "@/Components/InputLabel.vue";
import {computed, onMounted, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import {trans} from "laravel-vue-i18n";
import {useToast} from "primevue/usetoast";

const props = defineProps({
    paymentAccount: Object
});

const emit = defineEmits(["update:visible"]);

const form = useForm({
    payment_account_id: props.paymentAccount.id,
    payment_platform: '',
    payment_account_name: props.paymentAccount.payment_account_name,
    payment_platform_name: '',
    account_no: props.paymentAccount.account_no,
    country: null,
    currency: '',
    bank_code: '',
});

const selectedPaymentAccountType = ref(props.paymentAccount.payment_platform);
const paymentAccountTypes = ref([
    // 'bank',
    'crypto'
]);

const selectPaymentAccountType = (type) => {
    if (selectedPaymentAccountType !== type) {
        form.payment_account_name = '';
        form.account_no = '';
        form.bank_code = '';
    }
    selectedPaymentAccountType.value = type;
}

// crypto
const selectedCryptoNetwork = ref('');
const cryptoNetworks = ref([
    'trc20',
    'bep20'
]);

const currency = props.paymentAccount.currency;
if (currency && currency.includes('-')) {
    const network = currency.split('-')[1].toLowerCase();
    selectedCryptoNetwork.value = cryptoNetworks.value.includes(network)
        ? network
        : cryptoNetworks.value[0];
} else {
    selectedCryptoNetwork.value = cryptoNetworks.value[0];
}

const selectCryptoNetwork = (type) => {
    selectedCryptoNetwork.value = type;
};

const toast = useToast();

const submit = () => {
    form.payment_platform = selectedPaymentAccountType.value;

    if (selectedPaymentAccountType.value === 'crypto') {
        form.payment_platform_name = selectedCryptoNetwork.value;
    }

    form.post(route('profile.updatePaymentAccount'), {
        onSuccess: () => {
            closeDialog();
            form.reset();
            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_update_payment_account_success'),
                life: 3000,
            });
        },
    })
}

const closeDialog = () => {
    emit('update:visible', false)
}
</script>

<template>
    <form>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-5">
            <!-- Account type-->
            <div class="flex flex-col items-start gap-1 self-stretch md:col-span-2">
                <InputLabel for="payment_platform_name" :value="$t('public.payment_account_type')" />
                <div class="flex items-start gap-5 self-stretch">
                    <div
                        v-for="type in paymentAccountTypes"
                        :key="type"
                        @click="selectPaymentAccountType(type)"
                        class="group col-span-1 items-start py-2 px-4 gap-1 self-stretch rounded-lg border shadow-input transition-colors duration-300 select-none cursor-pointer w-fit"
                        :class="{
                                'bg-primary-50 dark:bg-primary-700/20 border-primary-500 dark:border-primary-700': selectedPaymentAccountType === type,
                                'bg-white dark:bg-surface-ground border-gray-300 dark:border-surface-600 hover:bg-primary-50 hover:border-primary-500': selectedPaymentAccountType !== type,
                            }"
                    >
                        <div class="flex items-center gap-3 self-stretch">
                            <span
                                class="flex-grow text-sm font-semibold transition-colors duration-300 group-hover:text-primary-600"
                                :class="{
                                    'text-primary-700 dark:text-primary-300': selectedPaymentAccountType === type,
                                    'text-gray-950 dark:text-white': selectedPaymentAccountType !== type
                                }"
                            >
                                {{ $t(`public.${type}`) }}
                            </span>
                            <IconCircleCheckFilled v-if="selectedPaymentAccountType === type" size="20" stroke-width="1.25" color="#cca05a" />
                        </div>
                    </div>
                </div>
                <InputError :message="form.errors.payment_platform" />
            </div>

            <!-- Crypto Account type-->
            <div
                v-if="selectedPaymentAccountType === 'crypto'"
                class="flex flex-col items-start gap-1 self-stretch md:col-span-2"
            >
                <InputLabel for="crypto_network" :value="$t('public.crypto_network')" />
                <div class="flex items-start gap-5 self-stretch">
                    <div
                        v-for="type in cryptoNetworks"
                        :key="type"
                        @click="selectCryptoNetwork(type)"
                        class="group col-span-1 items-start py-2 px-4 gap-1 self-stretch rounded-lg border shadow-input transition-colors duration-300 select-none cursor-pointer w-full"
                        :class="{
                                'bg-primary-50 dark:bg-primary-700/20 border-primary-500 dark:border-primary-700': selectedCryptoNetwork === type,
                                'bg-white dark:bg-surface-ground border-gray-300 dark:border-surface-600 hover:bg-primary-50 hover:border-primary-500': selectedCryptoNetwork !== type,
                            }"
                    >
                        <div class="flex items-center gap-3 self-stretch">
                                <span
                                    class="flex-grow text-sm font-semibold transition-colors duration-300 group-hover:text-primary-600"
                                    :class="{
                                        'text-primary-700 dark:text-primary-300': selectedCryptoNetwork === type,
                                        'text-gray-950 dark:text-white': selectedCryptoNetwork !== type
                                    }"
                                >
                                    <span class="uppercase">{{ type }}</span>
                                </span>
                            <IconCircleCheckFilled v-if="selectedCryptoNetwork === type" size="20" stroke-width="1.25" color="#cca05a" />
                        </div>
                    </div>
                </div>
                <InputError :message="form.errors.payment_platform_name" />
            </div>

            <!-- Bank Name -->
            <div
                v-if="selectedPaymentAccountType === 'bank'"
                class="flex flex-col gap-1 items-start self-stretch"
            >
                <InputLabel
                    for="bank_name"
                    :value="$t('public.bank_name')"
                />
                <InputText
                    id="bank_name"
                    type="text"
                    class="block w-full"
                    v-model="form.payment_platform_name"
                    :placeholder="$t('public.bank_name')"
                />
                <InputError :message="form.errors.payment_platform_name" />
            </div>

            <!-- Bank Code -->
            <div
                v-if="selectedPaymentAccountType === 'bank'"
                class="flex flex-col gap-1 items-start self-stretch"
            >
                <InputLabel
                    for="bank_code"
                    :value="$t('public.bank_code')"
                />
                <InputText
                    id="bank_code"
                    type="text"
                    class="block w-full"
                    v-model="form.bank_code"
                    :placeholder="$t('public.bank_code')"
                />
            </div>

            <!-- Account name -->
            <div class="flex flex-col gap-1 items-start self-stretch">
                <InputLabel
                    for="payment_account_name"
                    :value="selectedPaymentAccountType === 'crypto'
                            ? $t('public.wallet_name')
                            : $t('public.account_name')"
                />
                <InputText
                    id="payment_account_name"
                    type="text"
                    class="block w-full"
                    v-model="form.payment_account_name"
                    :invalid="!!form.errors.payment_account_name"
                />
                <InputError :message="form.errors.payment_account_name" />
            </div>

            <!-- Account no -->
            <div class="flex flex-col gap-1 items-start self-stretch">
                <InputLabel
                    for="account_no"
                    :value="selectedPaymentAccountType === 'crypto'
                             ? $t('public.token_address')
                            : $t('public.account_no')"
                />
                <InputText
                    id="account_no"
                    type="text"
                    class="block w-full"
                    v-model="form.account_no"
                    :invalid="!!form.errors.account_no"
                />
                <InputError :message="form.errors.account_no" />
            </div>
        </div>

        <div class="pt-5 flex justify-end">
            <Button
                type="submit"
                class="flex justify-center"
                variant="primary-flat"
                :disabled="form.processing"
                @click="submit"
            >
                {{ $t('public.save') }}
            </Button>
        </div>
    </form>
</template>
