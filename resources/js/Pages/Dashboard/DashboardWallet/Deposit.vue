<script setup>
import {computed, ref} from "vue";
import {generalFormat} from "@/Composables/format.js";
import Select from "primevue/select";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import {
    IconCircleCheckFilled, IconPhotoPlus, IconUpload, IconX
} from "@tabler/icons-vue";
import QrcodeVue from 'qrcode.vue';
import Tag from "primevue/tag";
import InputError from "@/Components/InputError.vue";
import InputNumber from "primevue/inputnumber";
import Button from "primevue/button";
import Image from "primevue/image";
import FileUpload from "primevue/fileupload";
import { usePrimeVue } from 'primevue/config';
import {useToast} from "primevue/usetoast";
import {trans} from "laravel-vue-i18n";
import InputText from "primevue/inputtext";

const props = defineProps({
    wallets: Array
});

const cashWallets = computed(() => props.wallets.filter(wallet => wallet.type === 'cash_wallet'));

const selectedWallet = ref(cashWallets.value.length > 0 ? cashWallets.value[0] : null);
const {formatAmount} = generalFormat();
const depositAmount = ref();
const emit = defineEmits(['update:visible'])

const form = useForm({
    wallet_id: '',
    amount: '',
    txn_hash: '',
    payment_slips: null
});

const $primevue = usePrimeVue();

const onRemoveTemplatingFile = (removeFileCallback, index) => {
    removeFileCallback(index);
};

const onSelectedFiles = (event) => {
    form.payment_slips = Array.from(event.target.files);
};

const formatSize = (bytes) => {
    const k = 1024;
    const dm = 3;
    const sizes = $primevue.config.locale.fileSizeTypes;

    if (bytes === 0) {
        return `0 ${sizes[0]}`;
    }

    const i = Math.floor(Math.log(bytes) / Math.log(k));
    const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

    return `${formattedSize} ${sizes[i]}`;
};

const toast = useToast();

const submitForm = () => {
    form.wallet_id = selectedWallet.value.id;
    form.amount = depositAmount.value;

    form.post(route('deposit'), {
        onSuccess: () => {
            closeDialog();

            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_success_request_deposit_message'),
                life: 3000,
            })
        }
    })
}

const closeDialog = () => {
    emit('update:visible', false)
}
</script>

<template>
    <form class="flex flex-col gap-5 items-center self-stretch">
        <div class="flex flex-col items-center gap-1 p-5 w-full bg-surface-100 dark:bg-surface-800">
            <span class="text-surface-500 text-sm">{{ $t('public.balance') }}</span>
            <span class="text-xl font-semibold">{{ formatAmount(selectedWallet.balance, 4) }}</span>
        </div>

        <div class="flex flex-col items-start gap-1 w-full">
            <span class="self-stretch text-surface-950 dark:text-white text-sm font-semibold">{{ $t('public.deposit_information') }}</span>
            <div class="self-stretch text-surface-600 dark:text-surface-400 text-xs">
                {{ $t('public.click_the_link_to_broker') }} - <a
                href="https://client.bgifx.co/"
                target="_blank"
                class="underline hover:text-blue-500"
            >https://client.bgifx.co/</a>
            </div>
            <div class="self-stretch text-surface-600 dark:text-surface-400 text-xs">
                {{ $t('public.email') }} - {{ usePage().props.auth.user.email }}
            </div>
            <div class="self-stretch text-surface-600 dark:text-surface-400 text-xs mb-2">
                {{ $t('public.password') }} - Abc123456
            </div>
            <div v-for="(step, index) in 3" :key="index" class="self-stretch text-surface-600 dark:text-surface-500 text-xs">
                {{ index + 1 }}. {{ $t(`public.deposit_info_${index+1}`) }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
            <div class="flex flex-col gap-1 items-start self-stretch">
                <InputLabel
                    :value="$t('public.amount')"
                    for="amount"
                />
                <InputNumber
                    v-model="depositAmount"
                    inputId="currency-us"
                    mode="currency"
                    currency="USD"
                    class="w-full"
                    :min="0"
                    :step="100"
                    placeholder="$0.00"
                    fluid
                    :invalid="!!form.errors.amount"
                />
                <InputError :message="form.errors.amount" />
            </div>
            <div
                class="flex flex-col gap-1 items-start self-stretch"
            >
                <InputLabel
                    :value="$t('public.txn_hash')"
                    for="txn_hash"
                />
                <InputText
                    id="txn_hash"
                    type="text"
                    class="block w-full"
                    v-model="form.txn_hash"
                    :placeholder="$t('public.enter_txn_hash')"
                    :invalid="!!form.errors.txn_hash"
                />
                <InputError :message="form.errors.txn_hash" />
            </div>
        </div>

        <div class="flex flex-col gap-1 items-start self-stretch">
            <InputLabel
                for="payment_slips"
                :value="$t('public.upload_payment_slip' )"
            />
            <FileUpload
                name="kyc_image"
                :multiple="true"
                accept="image/*"
                @input="onSelectedFiles"
            >
                <template #header="{ chooseCallback, clearCallback, files }">
                    <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                        <div class="flex gap-2">
                            <Button
                                @click="chooseCallback()"
                                rounded
                                outlined
                                severity="secondary"
                                class="!p-0 flex items-center justify-center h-10 w-10"
                            >
                                <IconPhotoPlus :size="20" stroke-width="1.5"/>
                            </Button>

                            <Button
                                @click="clearCallback()"
                                rounded
                                outlined
                                severity="danger"
                                class="!p-0 flex items-center justify-center h-10 w-10"
                                :disabled="!files || files.length === 0"
                            >
                                <IconX :size="20" stroke-width="1.5"/>
                            </Button>
                        </div>
                    </div>
                </template>
                <template #content="{ files, removeFileCallback }">
                    <div class="flex flex-col gap-3">
                        <div v-if="files.length > 0">
                            <div class="flex overflow-x-scroll gap-4">
                                <div
                                    v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                    class="p-5 rounded-border w-full max-w-64 flex flex-col border dark:border-surface-700 items-center gap-4 relative"
                                >
                                    <div class="absolute top-2 right-2">
                                        <Button
                                            outlined
                                            severity="danger"
                                            size="sm"
                                            rounded
                                            text
                                            class="p-2"
                                            @click="onRemoveTemplatingFile(removeFileCallback, index)"
                                        >
                                            <IconX size="16" stroke-width="1.5" />
                                        </Button>
                                    </div>
                                    <div class="max-h-20 mt-5">
                                        <Image role="presentation" :alt="file.name" :src="file.objectURL" preview imageClass="w-48 object-contain h-20" />
                                    </div>
                                    <div class="flex flex-col gap-1 items-center self-stretch w-52">
                                        <span class="font-semibold text-center text-xs truncate w-full max-w-52">{{ file.name }}</span>
                                        <div class="text-xxs">{{ formatSize(file.size) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template #empty>
                    <div class="flex items-center justify-center flex-col gap-3 mt-3">
                        <div class="flex items-center justify-center p-3 text-surface-400 dark:text-surface-600 rounded-full border border-surface-400 dark:border-surface-600">
                            <IconUpload size="24" stroke-width="1.5" />
                        </div>
                        <p class="text-sm">{{ $t('public.drag_and_drop') }}</p>
                    </div>
                </template>
            </FileUpload>
            <InputError :message="form.errors.payment_slips" />
        </div>

        <div class="flex w-full justify-end gap-3">
            <Button
                type="button"
                severity="secondary"
                text
                class="justify-center w-full md:w-auto px-6"
                @click="closeDialog"
                :disabled="form.processing"
            >
                {{ $t('public.cancel') }}
            </Button>
            <Button
                type="submit"
                class="justify-center w-full md:w-auto px-6"
                @click.prevent="submitForm"
                :disabled="form.processing"
            >
                {{ $t('public.confirm') }}
            </Button>
        </div>
    </form>
</template>
