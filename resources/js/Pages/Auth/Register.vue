<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Button from 'primevue/button';
import { Link, useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import FileUpload from 'primevue/fileupload';
import Image from 'primevue/image';
import InputIconWrapper from '@/Components/InputIconWrapper.vue';
import { IconMail, IconLock, IconUser, IconPhotoPlus, IconX, IconUpload, IconLabel, IconUsersPlus, IconFlag, IconPhone, IconCode  } from '@tabler/icons-vue';
import { IconArrowLeft } from '@tabler/icons-vue';
import { IconArrowRight } from '@tabler/icons-vue';
import Stepper from 'primevue/stepper';
import StepList from 'primevue/steplist';
import StepPanels from 'primevue/steppanels';
import Step from 'primevue/step';
import StepPanel from 'primevue/steppanel';
import { onMounted, ref } from 'vue';
import { usePrimeVue } from 'primevue/config';
import Password from 'primevue/password';
import AuthHeader from "@/Components/AuthHeader.vue";

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
    kyc_image: [],
    referral_code: '',
    password: '',
    password_confirmation: '',
});

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
</script>

<template>
    <GuestLayout title="register">
        <div class="flex flex-col gap-3 rounded-md p-5 dark:bg-surface-900 w-full max-w-[90vw] sm:max-w-2xl items-center justify-center">
            <form @submit.prevent class="w-full">
                <Stepper class="basis-[50rem]" v-model:value="activeStep" linear>
                    <StepList>
                        <Step :value="1">
                            <span class="hidden sm:block">Basics</span>
                        </Step>
                        <Step :value="2">
                            <span class="hidden sm:block">Credentials</span>
                        </Step>
                        <Step :value="3">
                            <span class="hidden sm:block">Identification</span>
                        </Step>
                    </StepList>

                    <StepPanels>

                        <!-- Step 1 -->
                        <StepPanel v-slot="{ activateCallback }" :value="1">
                            <div class="flex flex-col gap-5">
                                <AuthHeader
                                    :header="$t('public.register_step_1')"
                                    :caption="$t('public.register_step_1_caption')"
                                />
                                <div class="flex flex-col gap-3 items-center self-stretch">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Name" for="name"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconUser :size="20" stroke-width="1.5"/>
                                                </template>

                                                <InputText
                                                    id="name"
                                                    type="text"
                                                    class="pl-10 block w-full"
                                                    v-model="form.name"
                                                    placeholder="Name"
                                                    :invalid="!!form.errors.name"
                                                    autofocus
                                                />
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.name"/>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Email" for="email"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconMail :size="20" stroke-width="1.5"/>
                                                </template>

                                                <InputText
                                                    id="email"
                                                    type="text"
                                                    class="pl-10 block w-full"
                                                    v-model="form.email"
                                                    placeholder="Email"
                                                    :invalid="!!form.errors.email"
                                                />
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.email"/>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Username" for="username"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconLabel :size="20" stroke-width="1.5"/>
                                                </template>
                                                <InputText
                                                    id="username"
                                                    type="text"
                                                    class="pl-10 block w-full"
                                                    v-model="form.username"
                                                    placeholder="Username"
                                                    :invalid="!!form.errors.username"
                                                />
                                            </InputIconWrapper>

                                            <InputError :message="form.errors.username"/>
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Country" for="country"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconFlag :size="20" stroke-width="1.5" class="mt-1"/>
                                                </template>

                                                <Select
                                                    v-model="selectedCountry"
                                                    :options="countries"
                                                    :loading="loadingCountries"
                                                    optionLabel="name"
                                                    placeholder="Select Country"
                                                    class="pl-7 block w-full"
                                                    :invalid="!!form.errors.country"
                                                    filter
                                                >
                                                    <template #value="slotProps">
                                                        <div v-if="slotProps.value" class="flex items-center">
                                                            <div>{{ slotProps.value.name }}</div>
                                                        </div>
                                                        <span v-else class="text-surface-400 dark:text-surface-500">{{ slotProps.placeholder }}</span>
                                                    </template>
                                                    <template #option="slotProps">
                                                        <div class="flex items-center gap-1">
                                                            <div>{{ slotProps.option.emoji }}</div>
                                                            <div class="max-w-[200px] truncate">{{ slotProps.option.name }}</div>
                                                        </div>
                                                    </template>
                                                </Select>
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.country" />
                                        </div>

                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Phone Number" for="phone"/>
                                            <div class="flex gap-2 items-center self-stretch relative">
                                                <Select
                                                    v-model="selectedPhoneCode"
                                                    :options="countries"
                                                    :loading="loadingCountries"
                                                    optionLabel="name"
                                                    placeholder="60"
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
                                                            <div>{{ slotProps.option.emoji }}</div>
                                                            <div>{{ slotProps.option.iso2 }}</div>
                                                            <div class="max-w-[200px] truncate text-gray-500">({{ slotProps.option.phone_code }})</div>
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
                                >
                                    <IconArrowLeft :size="20"/>
                                    <span class="font-semibold">Back to LOGIN</span>
                                </Button>

                                <Button class="flex items-center space-x-1" iconPos="right" @click="handleContinue">
                                    <span class="font-semibold dark:text-surface-ground text-white">Next</span>
                                    <IconArrowRight :size="20" class="dark:text-surface-ground text-white"/>
                                </Button>
                            </div>
                        </StepPanel>

                        <!-- Step 2 -->
                        <StepPanel v-slot="{ activateCallback }" :value="2">
                            <div class="flex flex-col gap-5">
                                <AuthHeader
                                    :header="$t('public.register_step_2')"
                                    :caption="$t('public.register_step_2_caption')"
                                />
                                <div class="flex flex-col gap-3 items-center self-stretch">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-5 w-full">
                                        <!-- Password Field -->
                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Password" for="password"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconLock :size="20" stroke-width="1.5" />
                                                </template>

                                                <Password
                                                    v-model="form.password"
                                                    :invalid="!!form.errors.password"
                                                    class="block w-full"
                                                    placeholder="Password"
                                                    toggleMask
                                                    autofocus
                                                />
                                            </InputIconWrapper>
                                            <InputError :message="form.errors.password" />
                                        </div>

                                        <!-- Confirm Password Field -->
                                        <div class="flex flex-col gap-1 items-start self-stretch">
                                            <InputLabel value="Confirm Password" for="password_confirmation"/>
                                            <InputIconWrapper>
                                                <template #icon>
                                                    <IconLock :size="20" stroke-width="1.5"/>
                                                </template>

                                                <Password
                                                    v-model="form.password_confirmation"
                                                    :invalid="!!form.errors.password"
                                                    class="block w-full"
                                                    placeholder="Confirm Password"
                                                    toggleMask
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
                                <Button class="flex items-center space-x-1" severity="secondary" @click="activateCallback(1)">
                                    <IconArrowLeft :size="20"/>
                                    <span class="font-semibold">Back</span>
                                </Button>
                                <Button class="flex items-center space-x-1" iconPos="right" @click="handleContinue">
                                    <span class="font-semibold dark:text-surface-ground text-white">Next</span>
                                    <IconArrowRight :size="20" class="dark:text-surface-ground text-white"/>
                                </Button>
                            </div>
                        </StepPanel>

                        <!-- Step 3 -->
                        <StepPanel v-slot="{ activateCallback }" :value="3">
                            <div class="flex flex-col gap-5">
                                <AuthHeader
                                    :header="$t('public.register_step_3')"
                                    :caption="$t('public.register_step_3_caption')"
                                />

                                <div class="flex flex-col gap-1 items-start self-stretch">
                                    <InputLabel for="referral_code">{{ $t('public.kyc' )}}</InputLabel>
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
                                                <p class="text-sm">Drag and drop files to here to upload.</p>
                                            </div>
                                        </template>
                                    </FileUpload>
                                    <InputError :message="form.errors.kyc_image" />
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
                                            placeholder="Referral Code"
                                            type="text"
                                        />
                                    </InputIconWrapper>
                                    <InputError :message="form.errors.referral_code" />
                                </div>
                            </div>
                            <div class="flex pt-6 justify-between">
                                <Button class="flex items-center space-x-1" severity="secondary" @click="activateCallback(2)">
                                    <IconArrowLeft :size="20"/>
                                    <span class="font-semibold">Back</span>
                                </Button>

                                <Button class="flex items-center space-x-1" @click="handleContinue">
                                    <span class="font-semibold dark:text-surface-ground text-white">Register</span>
                                    <IconArrowRight :size="20" class="dark:text-surface-ground text-white"/>
                                </Button>
                            </div>
                        </StepPanel>
                    </StepPanels>
                </Stepper>
            </form>
        </div>
    </GuestLayout>
</template>
