<script setup>
import {ref} from "vue";
import EmptyData from "@/Components/EmptyData.vue";
import {IconCircleLetterB} from "@tabler/icons-vue";
import {generalFormat} from "@/Composables/format.js";
import Skeleton from "primevue/skeleton";
import Tag from "primevue/tag";
import Button from "primevue/button";

defineProps({
    brokerAccountsCount: Number
});

const brokerAccounts = ref();
const isLoading = ref(false);
const selectedBroker = ref('');
const selectedAccount = ref(null);
const {formatAmount} = generalFormat();
const emit = defineEmits(['update:account']);

const getBrokerAccounts = async () => {
    isLoading.value = true;

    try {
        let url = '/account/getBrokerAccounts';

        if (selectedBroker.value) {
            url += `&broker_id=${selectedBroker.value}`;
        }

        const response = await axios.get(url);
        brokerAccounts.value = response.data.brokerAccounts;
        selectedAccount.value = brokerAccounts.value[0]
        if (selectedAccount.value) {
            emit('update:account', selectedAccount.value)
        }
    } catch (error) {
        console.error('Error getting brokerAccounts:', error);
    } finally {
        isLoading.value = false;
    }
};

getBrokerAccounts();

const selectAccount = (data) => {
    selectedAccount.value = data;
    emit('update:account', data)
}

const formattedBalance = (data) => {
    const amount = formatAmount(data.broker_capital);
    const parts = amount.split(".");

    return parts.length > 1
        ? `${parts[0]}.<span class='text-lg text-surface-400'>${parts[1]}</span>`
        : amount;
};

const getSeverity = (status) => {
    switch (status) {
        case 'linked':
            return 'success';

        case 'unlink':
            return 'danger';

        case 'rejected':
            return 'danger';

        case 'pending':
            return 'warn';
    }
};
</script>

<template>
    <div
        v-if="brokerAccountsCount === 0"
        class="flex flex-col justify-center items-center w-full"
    >
        <EmptyData
            :title="$t('public.no_connections')"
            :message="$t('public.no_connections_caption')"
        />
        <!--        <CreateConnection />-->
    </div>

    <div
        v-else
        class="w-full"
    >
        <div
            v-if="isLoading"
            class="flex gap-5 items-center overflow-x-auto w-full"
        >
            <div
                v-for="index in brokerAccountsCount"
                :key="index"
                class="flex flex-col gap-5 min-w-80 md:min-w-96 rounded-lg shadow-toast overflow-hidden transition-all duration-200 p-3 border border-surface-200 dark:border-surface-700 select-none cursor-pointer dark:bg-surface-900 dark:text-white"
            >
                <div class="flex items-center gap-4 self-stretch">
                    <div
                        class="w-10 h-10 rounded-full grow-0 shrink-0 flex items-center justify-center border border-surface-200 dark:border-surface-800 text-surface-300 dark:text-surface-600"
                    >
                        <IconCircleLetterB size="28" stroke-width="1.5"/>
                    </div>
                    <div class="flex flex-col items-start">
                        <Skeleton width="5rem" height="1.25rem" class="my-1"></Skeleton>
                        <Skeleton width="10rem" class="mb-1"></Skeleton>
                    </div>
                </div>

                <div class="flex justify-between items-end">
                    <div class="text-3xl font-medium">
                        <Skeleton width="5rem" height="2rem"></Skeleton>
                    </div>
                    <div class="font-medium">
                        <Skeleton width="5rem" height="1.25rem"></Skeleton>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="brokerAccounts === null">
            {{ brokerAccounts }}
            <EmptyData
                :title="$t('public.no_connections')"
                :message="$t('public.no_connections_caption')"
            />
        </div>

        <div v-else class="flex gap-5 items-center overflow-x-auto w-full">
            <div
                v-for="account in brokerAccounts"
                class="flex flex-col gap-3 min-w-80 md:min-w-96 rounded-lg border border-gray-200 dark:border-surface-800 bg-white dark:bg-surface-900 p-5"
            >
                <div class="flex gap-3 items-center justify-between">
                    <div class="flex items-center gap-2 self-stretch">
                        <img
                            v-if="account.broker.media"
                            class="object-cover w-5 h-5 rounded-full border border-surface-200 dark:border-surface-800"
                            :src="account.broker.media[0].original_url"
                            alt="broker_image"
                        />
                        <div
                            v-else
                            class="w-5 h-5 rounded-full grow-0 shrink-0 flex items-center justify-center border border-surface-200 dark:border-surface-800 text-surface-300 dark:text-surface-600"
                        >
                            <IconCircleLetterB size="28" stroke-width="1.5"/>
                        </div>
                        <div class="self-stretch flex items-center gap-2 truncate text-sm text-surface-600 dark:text-surface-500">
                            {{ account.broker.name }}
                            <Tag
                                :severity="getSeverity(account.status)"
                                :value="$t(`public.${account.status}`)"
                            />
                        </div>
                    </div>
                    <div class="font-semibold text-lg dark:text-white">
                        #{{ account.broker_login }}
                    </div>
                </div>
                <div class="text-3xl font-medium dark:text-white">
                    <span v-html="formattedBalance(account)"></span>
                </div>

                <Button
                    type="button"
                    :severity="selectedAccount.id === account.id ? 'success' : 'secondary'"
                    @click="selectAccount(account)"
                    class="w-full"
                    size="small"
                    :outlined="selectedAccount.id !== account.id"
                    :label="selectedAccount.id === account.id ? $t('public.viewing_report') : $t('public.view_account_report')"
                />
            </div>
        </div>
    </div>

</template>
