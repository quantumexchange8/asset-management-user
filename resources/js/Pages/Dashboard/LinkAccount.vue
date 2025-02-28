<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import InputText from "primevue/inputtext";
import {useForm} from "@inertiajs/vue3";
import {IconAlertSquareRounded, IconCircleLetterB, IconFileCheck, IconPhotoPlus} from "@tabler/icons-vue";
import Select from "primevue/select";
import {ref} from "vue";
import Password from "primevue/password";
import Tag from "primevue/tag";
import {trans} from "laravel-vue-i18n";
import {useToast} from "primevue/usetoast";
import Button from "primevue/button";

const emit = defineEmits(['update:visible'])

const form = useForm({
    broker_id: '',
    broker_login: '',
    master_password: '',
    account_proof: null,
});

const selectedBroker = ref();
const brokers = ref();
const loadingBrokers = ref(false);

const getBrokers = async () => {
    loadingBrokers.value = true;
    try {
        const response = await axios.get('/getBrokers');
        brokers.value = response.data.brokers;
        selectedBroker.value = brokers.value[0];
    } catch (error) {
        console.error(error);
    } finally {
        loadingBrokers.value = false;
    }
}

getBrokers();

const toast = useToast();
const accountProofFile = ref(null);
const isDragging = ref(false);

const dragOver = () => {
    isDragging.value = true;
};

const dragLeave = () => {
    isDragging.value = false;
};

const handleDrop = (event) => {
    isDragging.value = false;
    form.errors.account_proof = null;
    const droppedFiles = event.dataTransfer.files;
    if (droppedFiles.length > 0) {
        validateFile(droppedFiles[0]);
    }
};

const handleFileSelect = (event) => {
    form.errors.account_proof = null;
    const selectedFile = event.target.files[0];
    validateFile(selectedFile);
};

const validateFile = (fileInput) => {
    if (fileInput.type.startsWith("image/")) {
        accountProofFile.value = fileInput;
        form.account_proof = accountProofFile.value;
    } else {
        toast.add({
            severity: 'error',
            summary: trans('public.error'),
            detail: trans('public.toast_image_type_error'),
            life: 5000,
        });
    }
};

const submitForm = () => {
    form.broker_id = selectedBroker.value.id;
    form.account_proof = accountProofFile.value;
    form.post(route('account.linkAccount'), {
        onSuccess: () => {
            closeDialog();
            form.reset();
            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_link_request_success'),
                life: 5000,
            });
        },
        onError: (errors) => {
            closeDialog();
            if (errors.status_pending) {
                toast.add({
                    severity: 'warn',
                    summary: trans('public.warning'),
                    detail: errors.status_pending,
                    life: 5000,
                });
            } else if (errors.status_linked) {
                toast.add({
                    severity: 'warn',
                    summary: trans('public.warning'),
                    detail: errors.status_linked,
                    life: 5000,
                });
            } else if (errors.invalid_user) {
                toast.add({
                    severity: 'error',
                    summary: trans('public.error'),
                    detail: errors.invalid_user,
                    life: 5000,
                });
            }
        }
    });
}

const closeDialog = () => {
    emit("update:visible", false);
}
</script>

<template>
    <form class="flex flex-col gap-6 items-center self-stretch">
        <div class="flex flex-col gap-3 items-center self-stretch">
            <span class="font-bold text-sm text-gray-950 dark:text-white w-full text-left">{{ $t('public.account_information') }}</span>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
                <div class="flex flex-col gap-1 items-start self-stretch md:col-span-2">
                    <InputLabel
                        :value="$t('public.broker')"
                        for="broker_id"
                    />
                    <Select
                        v-model="selectedBroker"
                        :options="brokers"
                        optionLabel="name"
                        :placeholder="$t('public.select_broker')"
                        class="block w-full"
                        :invalid="!!form.errors.broker_id"
                        :loading="loadingBrokers"
                    >
                        <template #value="slotProps">
                            <div v-if="slotProps.value">
                                <div class="flex items-center gap-2">
                                    <img
                                        v-if="slotProps.value.media"
                                        class="object-cover w-5 h-5 rounded-full border border-surface-200 dark:border-surface-800"
                                        :src="slotProps.value.media[0].original_url"
                                        alt="broker_image"
                                    />
                                    <div
                                        v-else
                                        class="w-5 h-5 rounded-full grow-0 shrink-0 flex items-center justify-center border border-surface-200 dark:border-surface-800 text-surface-300 dark:text-surface-600"
                                    >
                                        <IconCircleLetterB size="28" stroke-width="1.5"/>
                                    </div>
                                    <div>
                                        {{ slotProps.value.name }}
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                {{ slotProps.placeholder }}
                            </div>
                        </template>
                        <template #option="slotProps">
                            <div class="flex items-center gap-2">
                                <img
                                    v-if="slotProps.option.media"
                                    class="object-cover w-5 h-5 rounded-full border border-surface-200 dark:border-surface-800"
                                    :src="slotProps.option.media[0].original_url"
                                    alt="broker_image"
                                />
                                <div
                                    v-else
                                    class="w-5 h-5 rounded-full grow-0 shrink-0 flex items-center justify-center border border-surface-200 dark:border-surface-800 text-surface-300 dark:text-surface-600"
                                >
                                    <IconCircleLetterB size="28" stroke-width="1.5"/>
                                </div>
                                <div>
                                    {{ slotProps.option.name }}
                                </div>
                            </div>
                        </template>
                    </Select>
                    <InputError :message="form.errors.broker_id"/>
                </div>

                <div class="flex flex-col gap-1 items-start self-stretch">
                    <InputLabel
                        :value="$t('public.broker_login')"
                        for="broker_login"
                    />
                    <InputText
                        id="broker_login"
                        type="text"
                        class="block w-full"
                        v-model="form.broker_login"
                        :placeholder="$t('public.enter_broker_login')"
                        :invalid="!!form.errors.broker_login"
                    />
                    <InputError :message="form.errors.broker_login" />
                </div>

                <div class="flex flex-col gap-1 items-start self-stretch">
                    <InputLabel
                        :value="$t('public.master_password')"
                        for="master_password"
                    />
                    <Password
                        id="master_password"
                        v-model="form.master_password"
                        class="w-full"
                        placeholder="••••••••"
                        toggleMask
                        :invalid="!!form.errors.master_password"
                    />
                    <InputError :message="form.errors.master_password" />
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 items-center self-stretch">
            <span class="font-bold text-sm text-gray-950 dark:text-white w-full text-left">{{ $t('public.proof_of_account') }}</span>

            <div class="flex flex-col gap-1 items-start self-stretch">
                <div
                    :class="[
                        'flex flex-col gap-3 items-center self-stretch px-5 py-8 rounded-md border-2 border-dashed transition-colors duration-150',
                        {
                            'border-blue-500 dark:text-blue-400 bg-blue-200/50 dark:bg-blue-800/10': isDragging,
                            'bg-surface-50 dark:bg-surface-ground border-surface-300 dark:border-surface-600': !isDragging,
                        }
                    ]"
                    @dragover.prevent="dragOver"
                    @dragleave.prevent="dragLeave"
                    @drop.prevent="handleDrop"
                >
                    <div
                        v-if="form.errors.account_proof"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-red-300 dark:border-red-600 flex items-center justify-center text-red-500 dark:text-red-400"
                    >
                        <IconAlertSquareRounded size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else-if="accountProofFile"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-green-300 dark:border-green-600 flex items-center justify-center text-green-500 dark:text-green-400"
                    >
                        <IconFileCheck size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else-if="form.errors.account_proof"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-red-300 dark:border-red-600 flex items-center justify-center text-red-500 dark:text-red-400"
                    >
                        <IconAlertSquareRounded size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else
                        :class="[
                            'rounded-lg w-12 h-12 shrink-0 grow-0 border border-surface-300 dark:border-surface-600 flex items-center justify-center',
                            {
                                'text-blue-500 dark:text-blue-400': isDragging,
                                'text-surface-600 dark:text-surface-400': !isDragging,
                            }
                        ]"
                    >
                        <IconPhotoPlus size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-if="accountProofFile"
                        class="flex flex-col items-center justify-center self-stretch"
                    >
                        <span class="text-xs text-surface-600 dark:text-surface-400">{{ accountProofFile.name }}</span>
                        <label
                            for="fileInputBack"
                            class="text-xs text-blue-500 cursor-pointer underline select-none hover:text-blue-600"
                        >
                            {{ $t('public.replace_file') }}
                        </label>
                    </div>
                    <div v-else class="flex flex-col items-center text-surface-500 dark:text-surface-400 text-xs text-center">
                        {{ $t('public.drag_and_drop') }} <label for="fileInputBack" class="text-blue-500 cursor-pointer underline select-none hover:text-blue-600">{{ $t('public.choose_file') }}</label>
                    </div>
                    <input type="file" id="fileInputBack" class="hidden" @change="handleFileSelect" accept="image/*" />
                    <InputError :message="form.errors.account_proof" class="text-center" />
                    <div class="flex items-center gap-2 self-stretch justify-center w-full">
                        <Tag severity="secondary">
                            <span class="dark:text-surface-500">PNG</span>
                        </Tag>
                        <Tag severity="secondary">
                            <span class="dark:text-surface-500">JPG</span>
                        </Tag>
                        <Tag severity="secondary">
                            <span class="dark:text-surface-500">JPEG</span>
                        </Tag>
                    </div>
                </div>
                <div class="text-xs text-right w-full text-surface-500 dark:text-surface-400">
                    {{ $t('public.max_size') }}: 2MB
                </div>
            </div>
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
                {{ $t('public.submit') }}
            </Button>
        </div>
    </form>
</template>
