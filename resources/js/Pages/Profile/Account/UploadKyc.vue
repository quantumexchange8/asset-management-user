<script setup>
import {IconPhotoPlus, IconUpload, IconX} from "@tabler/icons-vue";
import Image from "primevue/image";
import Button from "primevue/button";
import FileUpload from "primevue/fileupload";
import {usePrimeVue} from "primevue/config";
import {useForm} from "@inertiajs/vue3";
import {useToast} from "primevue/usetoast";
import {trans} from "laravel-vue-i18n";
import InputError from "@/Components/InputError.vue"

//file
const $primevue = usePrimeVue();

const form = useForm({
    kyc_image: null
})

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

const toast = useToast();

const submitForm = () => {
    form.post(route('profile.uploadKyc'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_upload_kyc_success_message'),
                life: 3000,
            });
        },
        preserveState: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <div class="flex flex-col gap-5">
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
        <InputError :message="form.errors.kyc_image" />

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
