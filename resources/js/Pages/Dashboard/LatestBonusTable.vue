<script setup>
import Card from "primevue/card";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import {generalFormat} from "@/Composables/format.js";
import dayjs from "dayjs";

const props = defineProps({
    latestBonuses: Array
});

const {formatAmount} = generalFormat();
</script>

<template>
    <Card>
        <template #title>
            {{ $t('public.latest_bonus') }}
        </template>
        <template #content>
            <div
                v-if="!latestBonuses.length"
                class="flex flex-col gap-3 items-center self-stretch"
            >
                <div class="text-lg font-medium text-surface-500">{{ $t('public.no_data') }}</div>
            </div>
            <div v-else class="w-full">
                <DataTable
                    :value="latestBonuses"
                    paginator
                    :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    removableSort
                >
                    <Column
                        field="created_at"
                        :header="$t('public.date')"
                        sortable
                        class="hidden md:table-cell"
                    >
                        <template #body="{ data }">
                            {{ dayjs(data.created_at).format('YYYY-MM-DD') }}
                        </template>
                    </Column>
                    <Column
                        field="purpose"
                        :header="$t('public.description')"
                        class="hidden md:table-cell"
                    >
                        <template #body="{ data }">
                            {{ $t(`public.${data.purpose}`) }}
                        </template>
                    </Column>
                    <Column
                        field="amount"
                        :header="`${$t('public.amount')} ($)`"
                        sortable
                        class="hidden md:table-cell w-40"
                    >
                        <template #body="{ data }">
                            {{ formatAmount(data.amount, 4) }}
                        </template>
                    </Column>

                    <!-- Mobile view -->
                    <Column
                        field="mobile"
                        sortable
                        class="md:hidden"
                    >
                        <template #body="{ data }">
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col items-start">
                                    <div class="font-semibold">
                                        {{ $t(`public.${data.purpose}`) }}
                                    </div>
                                    <div class="flex gap-1 items-center text-surface-500 text-xs">
                                        {{ dayjs(data.created_at).format('YYYY-MM-DD') }}
                                    </div>
                                </div>
                                <div class="text-base font-semibold">
                                    ${{ formatAmount(data.amount, 4) }}
                                </div>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </template>
    </Card>
</template>
