<script setup>
import Button from "primevue/button";
import {ref} from "vue";
import {
    IconCircleLetterB,
    IconPlug
} from "@tabler/icons-vue";
import Dialog from "primevue/dialog";
import InputLabel from "@/Components/InputLabel.vue";
import Select from "primevue/select";
import {useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import InputNumber from "primevue/inputnumber";
import {useToast} from "primevue/usetoast";
import {trans} from "laravel-vue-i18n";

const visible = ref(false);

const openDialog = () => {
    visible.value = true;
    getBrokers();
}

const form = useForm({
    broker_id: '',
    broker_login: '',
    amount: null,
    master_password: '',
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

const toast = useToast();

const submitForm = () => {
    form.broker_id = selectedBroker.value?.id
    form.post(route('connections.connectBroker'), {
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: trans('public.success'),
                detail: trans('public.toast_submit_connect_success'),
                life: 3000,
            });
        }
    })
}

const closeDialog = () => {
    visible.value = false;
}
</script>

<template>
    <Button
        type="button"
        size="small"
        @click="openDialog"
        class="w-ful md:w-fit flex gap-2"
    >
        <IconPlug size="16" stroke-width="1.5" />
        {{ $t('public.connect') }}
    </Button>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.connect_to_broker')"
        class="dialog-xs md:dialog-md"
    >
        <form class="flex flex-col gap-5 items-center self-stretch w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
                <div class="flex flex-col gap-1 items-start self-stretch">
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
                    <InputError :message="form.errors.broker_id" />
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

                <div class="flex flex-col gap-1 items-start self-stretch">
                    <InputLabel
                        :value="$t('public.amount')"
                        for="amount"
                    />
                    <InputNumber
                        v-model="form.amount"
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
            </div>
            <div class="flex flex-col items-start gap-1 bg-surface-100 dark:bg-surface-800 p-5 w-full">
                <span class="self-stretch text-surface-950 dark:text-white text-sm font-semibold">{{ $t('public.connect_information') }}</span>
                <div v-for="(step, index) in 3" :key="index" class="self-stretch text-surface-600 dark:text-surface-400 text-xs">
                    {{ index + 1 }}. {{ $t(`public.connect_info_${index+1}`) }}
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
                    {{ $t('public.confirm') }}
                </Button>
            </div>
        </form>
    </Dialog>
</template>
