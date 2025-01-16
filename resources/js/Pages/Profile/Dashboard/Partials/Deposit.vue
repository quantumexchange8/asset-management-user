<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import InputIconWrapper from '@/Components/InputIconWrapper.vue';
import InputError from '@/Components/InputError.vue';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';

import { ref } from 'vue';
import { IconBuildingBank, IconCurrencyBitcoin, IconX, IconPhotoPlus, IconUpload } from '@tabler/icons-vue';
import { usePrimeVue } from 'primevue/config';

const props = defineProps({
    wallet: Object,
})

const visible = ref(false);

const selectedPaymentType = ref(null);
const form = useForm({
    amount: '',
    paymentType: '',
    bankSlipDetails: {
        bankName: '',
        slipNumber: '',
    },
    cryptoDetails: {
        walletAddress: '',
        transactionHash: '',
    },
});

//file
const $primevue = usePrimeVue();
// const files = ref([]);

const onRemoveTemplatingFile = (removeFileCallback, index) => {
    removeFileCallback(index);
};

const onSelectedFiles = (event) => {
    form.kyc_image = Array.from(event.target.files); 
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
</script>


<template>
    <Button 
        class="w-full flex justify-center gap-1"
        @click="visible = true"
    >
        Deposit
    </Button>

    <Dialog v-model:visible="visible" modal :header="'Deposit'" class="dialog-md lg:dialog-lg max-w-2xl w-full">

        <div class="flex flex-col gap-5 self-start">
            <div class="p-6 flex flex-col items-center gap-1 bg-gray-200 dark:bg-surface-800">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Balance
                </div>
                <div class="text-xl font-bold text-gray-950 dark:text-white">
                    {{ props.wallet.currency_symbol }} {{props.wallet.balance}}
                </div>
            </div>

            <!-- Payment Type Selection -->
            <div class="gap-1 items-start self-stretch">
                <InputLabel value="Type" />
                <div class="flex gap-4 mt-2">
                    <!-- Bank Button -->
                    <Button
                        class="w-full flex flex-col items-center gap-2 px-4 py-6 rounded-lg border-0"
                        :class="{
                            'text-white': selectedPaymentType === 'bank',
                            'bg-surface-800 hover:bg-surface-800 text-gray-300': selectedPaymentType !== 'bank'
                        }"
                        @click="selectedPaymentType = 'bank'"
                    >
                        <IconBuildingBank size="36" stroke-width="1.5" />
                        <span>Bank</span>
                    </Button>

                    <!-- Crypto Button -->
                    <Button
                        class="w-full flex flex-col items-center gap-2 px-4 py-6 rounded-lg border-0"
                        :class="{
                            'text-white': selectedPaymentType === 'crypto',
                            'bg-surface-800 hover:bg-surface-800 text-gray-300': selectedPaymentType !== 'crypto'
                        }"
                        @click="selectedPaymentType = 'crypto'"
                    >
                        <IconCurrencyBitcoin size="36" stroke-width="1.5" />
                        <span>Crypto</span>
                    </Button>
                </div>
            </div>

            <form>
                <!-- Bank slip -->
                <div v-if="selectedPaymentType === 'bank'" class="py-2">
                    <div class="space-y-2 mb-2">
                        <InputLabel value="Bank Name" for="bankname" />
                        <InputIconWrapper>
                            <template #icon>
                                <IconBuildingBank :size="20" stroke-width="1.5"/>
                            </template>
                            <InputText
                                id="bankname"
                                class="pl-10 block w-full"
                                v-model="form.bankSlipDetails.bankName"
                                autofocus
                                :invalid="!!form.errors.bankName"
                                autocomplete="username"
                                placeholder="Bank Name"
                            />
                        </InputIconWrapper>
                        <InputError :message="form.errors.email" />
                    </div>
                    
                    <div class="space-y-2">
                        <InputLabel value="Payment Slip" for="paymentslip"/>
                        <FileUpload
                            name="pay_slip"
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
                                <InputError :message="form.errors.kyc_image" />
                            </template>
                            <template #content="{ files, removeFileCallback }">
                                <div class="flex flex-col gap-3">
                                    <div v-if="files.length > 0">
                                        <div class="flex overflow-x-scroll gap-4">  
                                            <div
                                                v-for="(file, index) of files" :key="file.name + file.type + file.size"
                                                class="p-5 rounded-border w-full max-w-64 flex flex-col border dark:border-surface items-center gap-4 relative"
                                            >
                                                <div class="absolute top-2 right-2">
                                                    <Button
                                                        outlined
                                                        severity="danger"
                                                        size="sm"
                                                        class="border-none"
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
                                    <p class="text-sm">Drag and drop files to here to upload.</p>
                                </div>
                            </template>
                        </FileUpload>
                    </div>
                </div>

                <!-- payment gateway -->
                <div v-if="selectedPaymentType === 'crypto'" class="flex flex-col gap-4 py-1">
                    <InputLabel value="Wallet Address" />
                    <InputText v-model="form.cryptoDetails.walletAddress" placeholder="Enter Wallet Address" />

                    <InputLabel value="Transaction Hash" />
                    <InputText v-model="form.cryptoDetails.transactionHash" placeholder="Enter Transaction Hash" />
                </div>

                <!-- Submit Button -->
                <Button 
                    class="w-full mt-4" 
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="handleSubmit"
                >
                    Submit
                </Button>
            </form>
        </div>
    </Dialog>
</template>