<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AccountListing from "@/Pages/Account/AccountListing.vue";
import {ref, watch} from "vue";
import dayjs from "dayjs";
import Tag from "primevue/tag";
import IconField from "primevue/iconfield";
import Popover from "primevue/popover";
import ProgressSpinner from "primevue/progressspinner";
import {IconAdjustments, IconSearch, IconXboxX} from "@tabler/icons-vue";
import InputText from "primevue/inputtext";
import DatePicker from "primevue/datepicker";
import InputIcon from "primevue/inputicon";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Button from "primevue/button";
import Card from "primevue/card";
import {generalFormat} from "@/Composables/format.js";
import {FilterMatchMode} from "@primevue/core/api";
import debounce from "lodash/debounce.js";

defineProps({
    brokerAccountsCount: Number
});

const isLoading = ref(false);
const dt = ref(null);
const connections = ref([]);
const {formatAmount} = generalFormat();
const totalRecords = ref(0);
const first = ref(0);
const account = ref('');

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    broker_id: { value: account.value.broker_id, matchMode: FilterMatchMode.EQUALS },
    broker_login: { value: account.value.broker_login, matchMode: FilterMatchMode.EQUALS },
    start_join_date: { value: null, matchMode: FilterMatchMode.EQUALS },
    end_join_date: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const lazyParams = ref({});

const loadLazyData = (event) => {
    isLoading.value = true;

    lazyParams.value = { ...lazyParams.value, first: event?.first || first.value };
    lazyParams.value.filters = filters.value;
    try {
        setTimeout(async () => {
            const params = {
                page: JSON.stringify(event?.page + 1),
                sortField: event?.sortField,
                sortOrder: event?.sortOrder,
                include: [],
                lazyEvent: JSON.stringify(lazyParams.value)
            };

            const url = route('account.getConnectionsData', params);
            const response = await fetch(url);
            const results = await response.json();

            connections.value = results?.data?.data;
            totalRecords.value = results?.data?.total;
            isLoading.value = false;

        }, 100);
    }  catch (e) {
        connections.value = [];
        totalRecords.value = 0;
        isLoading.value = false;
    }
};
const onPage = (event) => {
    lazyParams.value = event;
    loadLazyData(event);
};
const onSort = (event) => {
    lazyParams.value = event;
    loadLazyData(event);
};
const onFilter = (event) => {
    lazyParams.value.filters = filters.value ;
    loadLazyData(event);
};

const op = ref();
const toggle = (event) => {
    op.value.toggle(event);
}

watch(account, (newAccount) => {
    filters.value.broker_id = newAccount.broker_id;
    filters.value.broker_login = newAccount.broker_login;

    lazyParams.value = {
        first: dt.value.first,
        rows: dt.value.rows,
        sortField: null,
        sortOrder: null,
        filters: filters.value
    };

    loadLazyData();
})

watch(
    filters.value['global'],
    debounce(() => {
        loadLazyData();
    }, 300)
);

const clearFilterGlobal = () => {
    filters.value['global'].value = null;
}

const selectedDate = ref([]);

const clearJoinDate = () => {
    selectedDate.value = [];
}

watch(selectedDate, (newDateRange) => {
    if (Array.isArray(newDateRange)) {
        const [startDate, endDate] = newDateRange;
        filters.value['start_join_date'].value = startDate;
        filters.value['end_join_date'].value = endDate;

        if (startDate !== null && endDate !== null) {
            loadLazyData();
        }
    } else {
        console.warn('Invalid date range format:', newDateRange);
    }
})

const clearFilter = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        start_join_date: { value: null, matchMode: FilterMatchMode.EQUALS },
        end_join_date: { value: null, matchMode: FilterMatchMode.EQUALS },
    };

    selectedDate.value = [];
    lazyParams.value.filters = filters.value ;
};

const getSeverity = (status) => {
    switch (status) {
        case 'removed':
            return 'danger';

        case 'active':
            return 'success';

        case 'pending':
            return 'info';
    }
}
</script>

<template>
    <AuthenticatedLayout title="account">
        <div class="flex flex-col gap-5 items-center self-stretch w-full">
            <AccountListing
                :brokerAccountsCount="brokerAccountsCount"
                @update:account="account = $event"
            />

            <Card v-if="brokerAccountsCount > 0" class="w-full">
                <template #content>
                    <DataTable
                        :value="connections"
                        lazy
                        paginator
                        removableSort
                        :rows="10"
                        :rowsPerPageOptions="[10, 20, 50, 100]"
                        :first="first"
                        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
                        :currentPageReportTemplate="$t('public.paginator_caption')"
                        v-model:filters="filters"
                        ref="dt"
                        dataKey="id"
                        :loading="isLoading"
                        :totalRecords="totalRecords"
                        @page="onPage($event)"
                        @sort="onSort($event)"
                        @filter="onFilter($event)"
                        :globalFilterFields="['name', 'email', 'connection_number']"
                    >
                        <template #header>
                            <div class="flex flex-wrap justify-between items-center">
                                <div class="flex items-center space-x-4 w-full md:w-auto">

                                    <!-- Search bar -->
                                    <IconField class="w-full md:w-auto">
                                        <InputIcon>
                                            <IconSearch :size="16" stroke-width="1.5" />
                                        </InputIcon>
                                        <InputText
                                            v-model="filters['global'].value"
                                            :placeholder="$t('public.search_keyword')"
                                            type="text"
                                            class="block w-full pl-10 pr-10"
                                        />
                                        <!-- Clear filter button -->
                                        <div
                                            v-if="filters['global'].value"
                                            class="absolute top-1/2 -translate-y-1/2 right-4 text-surface-300 hover:text-surface-400 select-none cursor-pointer"
                                            @click="clearFilterGlobal"
                                        >
                                            <IconXboxX aria-hidden="true" :size="15" />
                                        </div>
                                    </IconField>

                                    <!-- filter button -->
                                    <Button
                                        class="w-full md:w-28 flex gap-2"
                                        outlined
                                        @click="toggle"
                                    >
                                        <IconAdjustments :size="15"/>
                                        <span class="text-sm">{{ $t('public.filter') }}</span>
                                    </Button>
                                </div>
                            </div>
                        </template>

                        <template #empty>
                            <div class="flex flex-col">
                                <span>{{ $t('public.no_data') }}</span>
                            </div>
                        </template>

                        <template #loading>
                            <div class="flex flex-col gap-2 items-center justify-center">
                                <ProgressSpinner
                                    strokeWidth="4"
                                />
                            </div>
                        </template>

                        <template v-if="connections?.length > 0">
                            <Column
                                field="joined_at"
                                :header="$t('public.join_date')"
                                sortable
                                class="hidden md:table-cell min-w-36"
                            >
                                <template #body="{ data }">
                                    {{ dayjs(data.joined_at).format('YYYY-MM-DD') }}
                                </template>
                            </Column>

                            <Column
                                field="broker"
                                :header="$t('public.broker')"
                                class="hidden md:table-cell min-w-32"
                            >
                                <template #body="{ data }">
                                    <div class="flex gap-2 items-center">
                                        <img :src="data.broker.media[0].original_url" alt="broker_image" class="w-6 h-6 grow-0 shrink-0 rounded-full object-contain border border-surface-100 dark:border-surface-800">
                                        <div class="flex flex-col">
                                            <span class="text-surface-950 dark:text-white font-medium">{{ data.broker_login }}</span>
                                            <span class="text-surface-500">{{ data.broker.name }}</span>
                                        </div>
                                    </div>
                                </template>
                            </Column>

                            <Column
                                field="connection_number"
                                :header="$t('public.connection_number')"
                                sortable
                                class="hidden md:table-cell min-w-48"
                            >
                                <template #body="{ data }">
                                    {{ data.connection_number }}
                                </template>
                            </Column>

                            <Column
                                field="capital_fund"
                                :header="$t('public.fund')"
                                sortable
                                class="hidden md:table-cell"
                            >
                                <template #body="{ data }">
                                    {{ formatAmount(data.capital_fund) }}
                                </template>
                            </Column>

                            <Column
                                field="status"
                                :header="$t('public.status')"
                                sortable
                                class="hidden md:table-cell"
                            >
                                <template #body="{ data }">
                                    <Tag
                                        :value="$t(`public.${data.status}`)"
                                        :severity="getSeverity(data.status)"
                                    />
                                </template>
                            </Column>

                            <!-- Mobile view -->
                            <Column
                                field="mobile"
                                class="md:hidden"
                            >
                                <template #body="{ data }">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col items-start">
                                            <div class="flex gap-1 items-center">
                                                <img :src="data.broker.media[0].original_url" alt="broker_image" class="w-6 h-6 grow-0 shrink-0 rounded-full object-contain border border-surface-100 dark:border-surface-800">
                                                <div class="flex gap-1">
                                                    <span class="text-surface-950 dark:text-white font-medium">{{ data.broker_login }}</span>
                                                    <span>|</span>
                                                    <span class="text-surface-500">{{ data.broker.name }}</span>
                                                </div>
                                            </div>
                                            <div class="flex gap-1 items-center text-surface-500 text-xs">
                                                {{ dayjs(data.join_at).format('YYYY-MM-DD') }}
                                            </div>
                                        </div>
                                        <div class="text-base font-semibold">
                                            {{ formatAmount(data.capital_fund) }}
                                        </div>
                                    </div>
                                </template>
                            </Column>
                        </template>
                    </DataTable>
                </template>
            </Card>
        </div>

        <Popover ref="op">
            <div class="flex flex-col gap-6 w-60">
                <!-- Filter Join Date-->
                <div class="flex flex-col gap-2 items-center self-stretch">
                    <div class="flex self-stretch text-xs text-surface-950 dark:text-white font-semibold">
                        {{ $t('public.filter_by_date') }}
                    </div>
                    <div class="relative w-full">
                        <DatePicker
                            v-model="selectedDate"
                            dateFormat="dd/mm/yy"
                            class="w-full"
                            selectionMode="range"
                            placeholder="dd/mm/yyyy - dd/mm/yyyy"
                        />
                        <div
                            v-if="selectedDate && selectedDate.length > 0"
                            class="absolute top-2/4 -mt-1.5 right-2 text-surface-400 select-none cursor-pointer bg-transparent"
                            @click="clearJoinDate"
                        >
                            <IconXboxX size="12" stoke-width="1.5" />
                        </div>
                    </div>
                </div>
                <Button
                    type="button"
                    severity="info"
                    class="w-full"
                    outlined
                    @click="clearFilter"
                >
                    {{ $t('public.clear_all') }}
                </Button>
            </div>
        </Popover>
    </AuthenticatedLayout>
</template>
