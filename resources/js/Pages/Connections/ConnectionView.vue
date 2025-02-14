<script setup>
import {ref} from "vue";
import EmptyData from "@/Components/EmptyData.vue";
import {IconCircleLetterB} from "@tabler/icons-vue";
import {generalFormat} from "@/Composables/format.js";
import Skeleton from "primevue/skeleton";

defineProps({
    connectionsCount: Number
});

const connections = ref();
const isLoading = ref(false);
const selectedBroker = ref('');
const selectedConnection = ref(null);
const {formatAmount} = generalFormat();
const emit = defineEmits(['update:broker_id', 'update:broker_login']);

const getConnectionAccounts = async () => {
    isLoading.value = true;

    try {
        let url = '/connections/getConnectionAccounts';

        if (selectedBroker.value) {
            url += `&broker_id=${selectedBroker.value}`;
        }

        const response = await axios.get(url);
        connections.value = response.data.connections;
        selectedConnection.value = connections.value[0]
        if (selectedConnection.value) {
            emit('update:broker_id', selectedConnection.value.broker_id)
            emit('update:broker_login', selectedConnection.value.broker_login)
        }
    } catch (error) {
        console.error('Error getting connections:', error);
    } finally {
        isLoading.value = false;
    }
};

getConnectionAccounts();

const selectConnection = (data) => {
    selectedConnection.value = data;
    emit('update:broker_id', data.broker_id)
    emit('update:broker_login', data.broker_login)
}

const formattedBalance = (data) => {
    const amount = formatAmount(data.capital_fund);
    const parts = amount.split(".");

    return parts.length > 1
        ? `${parts[0]}.<span class='text-lg text-surface-400'>${parts[1]}</span>`
        : amount;
};

</script>

<template>
    <div
        v-if="connectionsCount === 0"
        class="w-full"
    >
        <EmptyData
            :title="$t('public.no_connections')"
            :message="$t('public.no_connections_caption')"
        />
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
                v-for="index in connectionsCount"
                :key="index"
                class="flex flex-col gap-5 min-w-80 md:min-w-96 rounded-lg shadow-toast overflow-hidden transition-all duration-200 p-3 border border-surface-200 dark:border-surface-700 select-none cursor-pointer dark:bg-surface-900 dark:text-white"
            >
                <div class="flex items-center gap-4 self-stretch">
                    <div
                        class="w-10 h-10 rounded-full grow-0 shrink-0 flex items-center justify-center border border-surface-200 dark:border-surface-800 text-surface-300 dark:text-surface-600"
                    >
                        <IconCircleLetterB size="28" stroke-width="1.5" />
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

        <div v-else-if="connections === null">
            {{ connections }}
            <EmptyData
                :title="$t('public.no_connections')"
                :message="$t('public.no_connections_caption')"
            />
        </div>

        <div v-else class="flex gap-5 items-center overflow-x-auto w-full">
            <div
                v-for="connection in connections"
                :key="connection.id"
                @click="selectConnection(connection)"
                :class="[
                    'flex flex-col gap-5 min-w-80 md:min-w-96 rounded-lg shadow-toast overflow-hidden transition-all duration-200 p-3 border border-surface-200 dark:border-surface-700 select-none cursor-pointer',
                    {
                        'bg-gradient-to-r from-surface-800 via-primary-800 to-primary dark:from-surface-950 dark:via-surface-800 dark:to-primary-800 text-white': selectedConnection.id === connection.id,
                        'dark:bg-surface-900 dark:text-white hover:border-primary-300 dark:hover:border-primary-800 hover:shadow-toast hover:shadow-primary-300 dark:hover:shadow-primary-900': selectedConnection.id !== connection.id,
                    }
                ]"
            >
                <div class="flex items-center gap-4 self-stretch">
                    <img
                        v-if="connection.broker.media"
                        class="object-cover w-10 h-10 rounded-full border border-surface-200 dark:border-surface-800"
                        :src="connection.broker.media[0].original_url"
                        alt="broker_image"
                    />
                    <div
                        v-else
                        class="w-10 h-10 rounded-full grow-0 shrink-0 flex items-center justify-center border border-surface-200 dark:border-surface-800 text-surface-300 dark:text-surface-600"
                    >
                        <IconCircleLetterB size="28" stroke-width="1.5" />
                    </div>
                    <div class="flex flex-col items-start">
                        <div class="self-stretch truncate font-bold">
                            {{ connection.broker.name }}
                        </div>
                        <a
                            :href="connection.broker.url"
                            target="_blank"
                            class="text-sm text-surface-400 hover:text-blue-500"
                        >
                            {{ connection.broker.url }}
                        </a>
                    </div>
                </div>

                <div class="flex justify-between items-end">
                    <div class="text-3xl font-medium">
                        $<span v-html="formattedBalance(connection)"></span>
                    </div>
                    <div class="font-medium">
                        {{ connection.broker_login }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
