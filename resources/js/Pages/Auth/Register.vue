<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import { useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import InputIconWrapper from '@/Components/InputIconWrapper.vue';
import { IconMail,
    IconLock,
    IconUser,
    IconPhotoPlus,
    IconAlertSquareRounded,
    IconFileCheck,
    IconLabel,
    IconFlag,
    IconPhone,
    IconCode,
    IconId
} from '@tabler/icons-vue';
import { IconArrowLeft } from '@tabler/icons-vue';
import { IconArrowRight } from '@tabler/icons-vue';
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import { onMounted, ref } from 'vue';
import Password from 'primevue/password';
import AuthHeader from "@/Components/AuthHeader.vue";
import {useLangObserver} from "@/Composables/localeObserver.js";
import {useToast} from "primevue/usetoast";
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    referral_code: String
})

const activeStep = ref(1);
const totalSteps = 3;

const form = useForm({
    step: '', //to track the stepper's steps
    name: '',
    email: '',
    username: '',
    country: '',
    dial_code: '',
    phone: '',
    phone_number: '',
    identity_number: '',
    front_identity: null,
    back_identity: null,
    referral_code: props.referral_code ? props.referral_code : '',
    password: '',
    password_confirmation: '',
});

const {locale} = useLangObserver();

//get countries/phone code
const selectedCountry = ref();
const selectedPhoneCode = ref();
const countries = ref([]);
const loadingCountries = ref(false);
const getCountries = async () => {
    loadingCountries.value = true;
    try {
        const response = await axios.get('/get_countries');
        countries.value = response.data.countries;
    } catch (error) {
        console.error('Error fetching selectedCountry:', error);
    } finally {
        loadingCountries.value = false;
    }
}

onMounted(() => {
    getCountries();
});

//handle active step and submit detail to BE
const handleContinue = () => {
    form.step = activeStep.value;
    form.country = selectedCountry.value;
    form.dial_code = selectedPhoneCode.value;

    if(selectedPhoneCode.value){
        form.phone_number = selectedPhoneCode.value.phone_code + form.phone;
    }

    form.post(route('register'), {
        onSuccess: () => {
            if(activeStep === totalSteps){
                form.reset();
                activeStep.value = 1;
            } else {
                activeStep.value += 1;
            }
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

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
</script>

<template>
    <GuestLayout title="register">
        <div class="flex flex-col gap-3 rounded-md p-5 dark:bg-surface-900 w-full max-w-[90vw] sm:max-w-2xl items-center justify-center">
            <form @submit.prevent class="w-full">
                <Stepper class="basis-[50rem]" v-model:value="activeStep" linear>
                    <StepList>
                        <Step :value="1">
                            <span class="hidden sm:block">{{ $t('public.basics') }}</span>
                        </Step>
                        <Step :value="2">
                            <span class="hidden sm:block">{{ $t('public.credentials') }}</span>
                        </Step>
                        <Step :value="3">
                            <span class="hidden sm:block">{{ $t('public.identification') }}</span>
                        </Step>
                    </StepList>

                    <StepPanels>

                        <!-- Step 1 -->
                        <StepPanel v-slot="{ activateCallback }" :value="1">
                            <div class="flex flex-col gap-5">
                                <AuthHeader
                                    :header="$t('public.basics')"
                                    :caption="$t('public.register_step_1_caption')"
                                />
                                <div class="flex flex-col gap-3 items-center self-stretch">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.name')" for="name"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconUser :size="20" stroke-width="1.5"/>
                                                </template>

                                                <InputText
                                                    id="name"
                                                    type="text"
                                                    class="pl-10 block w-full"
                                                    v-model="form.name"
                                                    :placeholder="$t('public.enter_name')"
                                                    :invalid="!!form.errors.name"
                                                    autofocus
                                                />
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.name"/>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.email')" for="email"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconMail :size="20" stroke-width="1.5"/>
                                                </template>

                                                <InputText
                                                    id="email"
                                                    type="text"
                                                    class="pl-10 block w-full"
                                                    v-model="form.email"
                                                    :placeholder="$t('public.enter_email')"
                                                    :invalid="!!form.errors.email"
                                                />
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.email"/>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.username')" for="username"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconLabel :size="20" stroke-width="1.5"/>
                                                </template>
                                                <InputText
                                                    id="username"
                                                    type="text"
                                                    class="pl-10 block w-full"
                                                    v-model="form.username"
                                                    :placeholder="$t('public.enter_username')"
                                                    :invalid="!!form.errors.username"
                                                />
                                            </InputIconWrapper>

                                            <InputError :message="form.errors.username"/>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.country')" for="country"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconFlag :size="20" stroke-width="1.5" />
                                                </template>

                                                <Select
                                                    v-model="selectedCountry"
                                                    :options="countries"
                                                    :loading="loadingCountries"
                                                    optionLabel="name"
                                                    :placeholder="$t('public.select_country')"
                                                    class="pl-7 block w-full"
                                                    :invalid="!!form.errors.country"
                                                    filter
                                                    :filter-fields="['name', 'iso2']"
                                                >
                                                    <template #value="slotProps">
                                                        <div v-if="slotProps.value" class="flex items-center">
                                                            <div class="leading-tight">{{ JSON.parse(slotProps.value.translations)[locale] || slotProps.value.name }}</div>
                                                        </div>
                                                        <span v-else class="text-surface-400 dark:text-surface-500">{{ slotProps.placeholder }}</span>
                                                    </template>
                                                    <template #option="slotProps">
                                                        <div class="flex items-center gap-1">
                                                            <img
                                                                v-if="slotProps.option.iso2"
                                                                :src="`https://flagcdn.com/w40/${slotProps.option.iso2.toLowerCase()}.png`"
                                                                :alt="slotProps.option.iso2"
                                                                width="18"
                                                                height="12"
                                                            />
                                                            <div class="max-w-[200px] truncate">{{ JSON.parse(slotProps.option.translations)[locale] || slotProps.option.name }}</div>
                                                        </div>
                                                    </template>
                                                </Select>
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.country" />
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.phone_number')" for="phone"/>
                                            <div class="flex gap-2 items-center self-stretch relative">
                                                <Select
                                                    v-model="selectedPhoneCode"
                                                    :options="countries"
                                                    :loading="loadingCountries"
                                                    optionLabel="name"
                                                    placeholder="86"
                                                    class="w-[100px]"
                                                    :invalid="!!form.errors.dial_code"
                                                    filter
                                                    :filterFields="['name', 'iso2', 'phone_code']"
                                                >
                                                    <template #value="slotProps">
                                                        <div v-if="slotProps.value" class="flex items-center">
                                                            <div>{{ slotProps.value.phone_code }}</div>
                                                        </div>
                                                        <span v-else class="text-surface-400 dark:text-surface-500">{{ slotProps.placeholder }}</span>
                                                    </template>
                                                    <template #option="slotProps">
                                                        <div class="flex items-center gap-1">
                                                            <img
                                                                v-if="slotProps.option.iso2"
                                                                :src="`https://flagcdn.com/w40/${slotProps.option.iso2.toLowerCase()}.png`"
                                                                :alt="slotProps.option.iso2"
                                                                width="18"
                                                                height="12"
                                                            />
                                                            <div>{{ slotProps.option.phone_code }}</div>
                                                            <div class="max-w-[200px] truncate text-gray-500">({{ slotProps.option.iso2 }})</div>
                                                        </div>
                                                    </template>
                                                </Select>
                                                <InputIconWrapper>
                                                    <template #icon>
                                                        <IconPhone :size="20" stroke-width="1.5"/>
                                                    </template>

                                                    <InputText
                                                        id="phone"
                                                        type="text"
                                                        class="pl-10 block w-full"
                                                        v-model="form.phone"
                                                        placeholder="1234567"
                                                        :invalid="!!form.errors.phone"
                                                    />
                                                </InputIconWrapper>
                                            </div>
                                            <InputError :message="form.errors.phone" />
                                            <InputError :message="form.errors.dial_code" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex pt-6 justify-between items-center">
                                <Button
                                    class="flex items-center space-x-1"
                                    severity="secondary"
                                    as="a"
                                    :href="route('login')"
                                    size="small"
                                >
                                    <IconArrowLeft :size="18"/>
                                    <span class="font-semibold">{{ $t('public.back_to_login') }}</span>
                                </Button>

                                <Button
                                    class="flex items-center space-x-1"
                                    iconPos="right"
                                    @click="handleContinue"
                                    size="small"
                                >
                                    <span class="font-semibold dark:text-surface-ground text-white">{{ $t('public.next') }}</span>
                                    <IconArrowRight :size="18" class="dark:text-surface-ground text-white"/>
                                </Button>
                            </div>
                        </StepPanel>

                        <!-- Step 2 -->
                        <StepPanel v-slot="{ activateCallback }" :value="2">
                            <div class="flex flex-col gap-5">
                                <AuthHeader
                                    :header="$t('public.credentials')"
                                    :caption="$t('public.register_step_2_caption')"
                                />
                                <div class="flex flex-col gap-3 items-center self-stretch">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
                                        <!-- Password Field -->
                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.password')" for="password"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconLock :size="20" stroke-width="1.5" />
                                                </template>

                                                <Password
                                                    v-model="form.password"
                                                    :invalid="!!form.errors.password"
                                                    class="block pl-10 w-full"
                                                    placeholder="••••••••"
                                                    toggleMask
                                                    autofocus
                                                    :promptLabel="$t('public.password')"
                                                />
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.password" />
                                        </div>

                                        <!-- Confirm Password Field -->
                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel :value="$t('public.confirm_password')" for="password_confirmation"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconLock :size="20" stroke-width="1.5"/>
                                                </template>

                                                <Password
                                                    v-model="form.password_confirmation"
                                                    :invalid="!!form.errors.password"
                                                    class="block pl-10 w-full"
                                                    placeholder="••••••••"
                                                    toggleMask
                                                    :promptLabel="$t('public.password')"
                                                />
                                            </InputIconWrapper>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-start gap-1 bg-surface-100 dark:bg-surface-800 p-5">
                                    <span class="self-stretch text-surface-950 dark:text-white text-sm font-semibold">{{ $t('public.password_information') }}</span>
                                    <div v-for="(step, index) in 4" :key="index" class="self-stretch text-surface-600 dark:text-surface-400 text-xs">
                                        {{ index + 1 }}. {{ $t(`public.password_info_${index+1}`) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex pt-6 justify-between">
                                <Button
                                    class="flex items-center space-x-1"
                                    severity="secondary"
                                    @click="activateCallback(1)"
                                    size="small"
                                >
                                    <IconArrowLeft :size="18"/>
                                    <span class="font-semibold">{{ $t('public.back') }}</span>
                                </Button>
                                <Button
                                    class="flex items-center space-x-1"
                                    iconPos="right"
                                    @click="handleContinue"
                                    size="small"
                                >
                                    <span class="font-semibold dark:text-surface-ground text-white">{{ $t('public.next') }}</span>
                                    <IconArrowRight :size="18" class="dark:text-surface-ground text-white"/>
                                </Button>
                            </div>
                        </StepPanel>

                        <!-- Step 3 -->
                        <StepPanel v-slot="{ activateCallback }" :value="3">
                            <div class="flex flex-col gap-5">
                                <AuthHeader
                                    :header="$t('public.identification')"
                                    :caption="$t('public.register_step_3_caption')"
                                />

                                <div class="flex flex-col gap-1 items-start self-stretch">
                                    <InputLabel :value="$t('public.identity_number')" for="identity_number"/>
                                    <InputIconWrapper>
                                        <template #icon>
                                            <IconId :size="20" stroke-width="1.5"/>
                                        </template>
                                        <InputText
                                            id="identity_number"
                                            type="text"
                                            class="pl-10 block w-full"
                                            v-model="form.identity_number"
                                            :placeholder="$t('public.enter_identity_number')"
                                            :invalid="!!form.errors.identity_number"
                                        />
                                    </InputIconWrapper>
                                    <InputError :message="form.errors.identity_number"/>
                                </div>

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
                                                class="flex flex-col items-center self-stretch"
                                            >
                                                <span class="text-xs text-surface-600 dark:text-surface-400">{{ frontFile.name }}</span>
                                                <label
                                                    for="fileInputFront"
                                                    class="text-xs text-blue-500 cursor-pointer underline select-none hover:text-blue-600"
                                                >
                                                    {{ $t('public.replace_file') }}
                                                </label>
                                            </div>
                                            <div v-else class="text-surface-500 dark:text-surface-400 text-xs text-center">
                                                {{ $t('public.drag_and_drop') }} <label for="fileInputFront" class="text-blue-500 cursor-pointer underline select-none hover:text-blue-600">{{ $t('public.choose_file') }}</label>.
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
                                            {{ $t('public.max_size') }}: 2MB
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
                                                class="flex flex-col items-center self-stretch"
                                            >
                                                <span class="text-xs text-surface-600 dark:text-surface-400">{{ backFile.name }}</span>
                                                <label
                                                    for="fileInputBack"
                                                    class="text-xs text-blue-500 cursor-pointer underline select-none hover:text-blue-600"
                                                >
                                                    {{ $t('public.replace_file') }}
                                                </label>
                                            </div>
                                            <div v-else class="text-surface-500 dark:text-surface-400 text-xs text-center">
                                                {{ $t('public.drag_and_drop') }} <label for="fileInputBack" class="text-blue-500 cursor-pointer underline select-none hover:text-blue-600">{{ $t('public.choose_file') }}</label>.
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
                                            {{ $t('public.max_size') }}: 2MB
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1 items-start self-stretch">
                                    <InputLabel for="referral_code">{{ $t('public.referral_code' )}}</InputLabel>
                                    <InputIconWrapper class="w-2/5 self-center">
                                        <template #icon>
                                            <IconCode :size="20" stroke-width="1.5"/>
                                        </template>

                                        <InputText
                                            id="referral_code"
                                            v-model="form.referral_code"
                                            :invalid="!!form.errors.referral_code"
                                            class="pl-10 block w-full"
                                            :placeholder="$t('public.referral_code')"
                                            type="text"
                                        />
                                    </InputIconWrapper>
                                    <InputError :message="form.errors.referral_code" />
                                </div>
                            </div>
                            <div class="flex pt-6 justify-between">
                                <Button
                                    class="flex items-center space-x-1"
                                    severity="secondary"
                                    @click="activateCallback(2)"
                                    size="small"
                                >
                                    <IconArrowLeft :size="18"/>
                                    <span class="font-semibold">{{ $t('public.back') }}</span>
                                </Button>

                                <Button
                                    class="flex items-center space-x-1"
                                    @click="handleContinue"
                                    size="small"
                                >
                                    <span class="font-semibold dark:text-surface-ground text-white">{{ $t('public.register') }}</span>
                                    <IconArrowRight :size="18" class="dark:text-surface-ground text-white"/>
                                </Button>
                            </div>
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </form>
        </div>
    </GuestLayout>
</template>
