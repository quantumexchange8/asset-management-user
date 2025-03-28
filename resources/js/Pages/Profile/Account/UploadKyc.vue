<script setup>
import {IconAlertSquareRounded, IconFileCheck, IconPhotoPlus} from "@tabler/icons-vue";
import Button from "primevue/button";
import {useForm} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";
import {trans} from "laravel-vue-i18n";
import InputError from "@/Components/InputError.vue"
import Tag from "primevue/tag";
import InputLabel from "@/Components/InputLabel.vue";
import {ref} from "vue";

const form = useForm({
    front_identity: null,
    back_identity: null,
})

const toast = useToast();

// Front Identity
const frontFile = ref(null);
const isDraggingFront = ref(false);

const dragOverFront = () => {
    isDraggingFront.value = true;
};

const dragLeaveFront = () => {
    isDraggingFront.value = false;
};

const handleDropFront = (event) => {
    isDraggingFront.value = false;
    form.errors.front_identity = null;
    const droppedFiles = event.dataTransfer.files;
    if (droppedFiles.length > 0) {
        validateFile(droppedFiles[0], 'front_identity');
    }
};

const handleFrontFileSelect = (event) => {
    form.errors.front_identity = null;
    const selectedFile = event.target.files[0];
    validateFile(selectedFile, 'front_identity');
};

// Back Identity
const backFile = ref(null);
const isDraggingBack = ref(false);

const dragOverBack = () => {
    isDraggingBack.value = true;
};

const dragLeaveBack = () => {
    isDraggingBack.value = false;
};

const handleDropBack = (event) => {
    isDraggingBack.value = false;
    form.errors.back_identity = null;
    const droppedFiles = event.dataTransfer.files;
    if (droppedFiles.length > 0) {
        validateFile(droppedFiles[0], 'back_identity');
    }
};

const handleBackFileSelect = (event) => {
    form.errors.back_identity = null;
    const selectedFile = event.target.files[0];
    validateFile(selectedFile, 'back_identity');
};

const validateFile = (fileInput, identity_type) => {
    if (fileInput.type.startsWith("image/")) {
        if (identity_type === "front_identity") {
            frontFile.value = fileInput;
            form.front_identity = frontFile.value;
        } else {
            backFile.value = fileInput;
            form.back_identity = backFile.value;
        }
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
    form.post(route('profile.uploadKyc'), {
        onSuccess: () => {
            frontFile.value = null;
            backFile.value = null;
            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_upload_kyc_success_message'),
                life: 3000,
            });
        },
    })
}
</script>

<template>
    <div class="flex flex-col gap-5 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
            <div class="flex flex-col gap-1 items-start self-stretch">
                <InputLabel for="front_identity">{{ $t('public.front_identity' )}}</InputLabel>
                <div
                    :class="[
                        'flex flex-col gap-3 items-center self-stretch px-5 py-8 rounded-md border-2 border-dashed transition-colors duration-150',
                        {
                            'border-blue-500 dark:text-blue-400 bg-blue-200/50 dark:bg-blue-800/10': isDraggingFront,
                            'bg-surface-50 dark:bg-surface-ground border-surface-300 dark:border-surface-600': !isDraggingFront,
                        }
                    ]"
                    @dragover.prevent="dragOverFront"
                    @dragleave.prevent="dragLeaveFront"
                    @drop.prevent="handleDropFront"
                >
                    <div
                        v-if="form.errors.front_identity"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-red-300 dark:border-red-600 flex items-center justify-center text-red-500 dark:text-red-400"
                    >
                        <IconAlertSquareRounded size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else-if="frontFile"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-green-300 dark:border-green-600 flex items-center justify-center text-green-500 dark:text-green-400"
                    >
                        <IconFileCheck size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else
                        :class="[
                            'rounded-lg w-12 h-12 shrink-0 grow-0 border border-surface-300 dark:border-surface-600 flex items-center justify-center',
                            {
                                'text-blue-500 dark:text-blue-400': isDraggingFront,
                                'text-surface-600 dark:text-surface-400': !isDraggingFront,
                            }
                        ]"
                    >
                        <IconPhotoPlus size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-if="frontFile"
                        class="flex flex-col items-center justify-center self-stretch"
                    >
                        <span class="text-xs text-surface-600 dark:text-surface-400">{{ frontFile.name }}</span>
                        <label
                            for="fileInputFront"
                            class="text-xs text-blue-500 cursor-pointer underline select-none hover:text-blue-600"
                        >
                            {{ $t('public.replace_file') }}
                        </label>
                    </div>
                    <div v-else class="flex flex-col items-center text-surface-500 dark:text-surface-400 text-xs text-center">
                        {{ $t('public.drag_and_drop') }} <label for="fileInputFront" class="text-blue-500 cursor-pointer underline select-none hover:text-blue-600">{{ $t('public.choose_file') }}</label>
                    </div>
                    <input type="file" id="fileInputFront" class="hidden" @change="handleFrontFileSelect" accept="image/*" />
                    <InputError :message="form.errors.front_identity" class="text-center" />
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
                    {{ $t('public.max_size') }}: 8MB
                </div>
            </div>

            <!-- Back Identity -->
            <div class="flex flex-col gap-1 items-start self-stretch">
                <InputLabel for="back_identity">{{ $t('public.back_identity' )}}</InputLabel>
                <div
                    :class="[
                        'flex flex-col gap-3 items-center self-stretch px-5 py-8 rounded-md border-2 border-dashed transition-colors duration-150',
                        {
                            'border-blue-500 dark:text-blue-400 bg-blue-200/50 dark:bg-blue-800/10': isDraggingBack,
                            'bg-surface-50 dark:bg-surface-ground border-surface-300 dark:border-surface-600': !isDraggingBack,
                        }
                    ]"
                    @dragover.prevent="dragOverBack"
                    @dragleave.prevent="dragLeaveBack"
                    @drop.prevent="handleDropBack"
                >
                    <div
                        v-if="form.errors.back_identity"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-red-300 dark:border-red-600 flex items-center justify-center text-red-500 dark:text-red-400"
                    >
                        <IconAlertSquareRounded size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else-if="backFile"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-green-300 dark:border-green-600 flex items-center justify-center text-green-500 dark:text-green-400"
                    >
                        <IconFileCheck size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else-if="form.errors.back_identity"
                        class="rounded-lg w-12 h-12 shrink-0 grow-0 border border-red-300 dark:border-red-600 flex items-center justify-center text-red-500 dark:text-red-400"
                    >
                        <IconAlertSquareRounded size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-else
                        :class="[
                            'rounded-lg w-12 h-12 shrink-0 grow-0 border border-surface-300 dark:border-surface-600 flex items-center justify-center',
                            {
                                'text-blue-500 dark:text-blue-400': isDraggingBack,
                                'text-surface-600 dark:text-surface-400': !isDraggingBack,
                            }
                        ]"
                    >
                        <IconPhotoPlus size="28" stroke-width="1.5" />
                    </div>
                    <div
                        v-if="backFile"
                        class="flex flex-col items-center justify-center self-stretch"
                    >
                        <span class="text-xs text-surface-600 dark:text-surface-400">{{ backFile.name }}</span>
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
                    <input type="file" id="fileInputBack" class="hidden" @change="handleBackFileSelect" accept="image/*" />
                    <InputError :message="form.errors.back_identity" class="text-center" />
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
                    {{ $t('public.max_size') }}: 8MB
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <Button
                type="submit"
                @click="submitForm"
                :label="$t('public.submit')"
                :disabled="form.processing"
            />
        </div>
    </div>
</template>
