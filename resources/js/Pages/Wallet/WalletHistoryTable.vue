<script setup>
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import ProgressSpinner from 'primevue/progressspinner';
import { IconXboxX, IconAdjustments, IconSearch } from '@tabler/icons-vue';
import { onMounted, ref } from 'vue';
import dayjs from 'dayjs';

const isLoading = ref(false);

// get wallet transaction history
const walletTransactionHistory = ref([]);
const getResult = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get('/wallet/get_wallet_history_data');
        walletTransactionHistory.value = response.data.wallet_transaction_history;
        console.log('test', response.data.wallet_transaction_history)
    } catch (error) {
        console.error('Error fetching users:', error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    getResult();
});


</script>

<template>
    <Card>
        <template #content>
            <div class="w-full">
                <DataTable
                 
                    :value="walletTransactionHistory"
                    paginator 
                    removableSort
                    :rows="10" 
                    :rowsPerPageOptions="[10, 20, 50, 100]"
                 
                    paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
                
                    ref="dt"
                    dataKey="id"
                    :loading="isLoading"
  
                >
                    <template #header>
                        <div class="flex flex-wrap justify-between items-center">
                            <div class="flex items-center space-x-4 w-full md:w-auto">
                                <!-- Search bar -->
                                <IconField>
                                    <InputIcon>
                                        <IconSearch :size="16" stroke-width="1.5" />
                                    </InputIcon>
                                    <InputText 
                                     
                                        placeholder="Keyword Search" 
                                        type="text"
                                        class="block w-full pl-10 pr-10"
                                    />
                                    
                                    <!-- Clear filter button -->
                                    <div
                                      
                                        class="absolute top-1/2 -translate-y-1/2 right-4 text-gray-300 hover:text-gray-400 select-none cursor-pointer"
                                        @click="clearFilterGlobal"
                                    >
                                        <IconXboxX aria-hidden="true" :size="15" />
                                    </div>
                                </IconField>
                                
                                <!-- filter button -->
                                <Button
                                    class="w-full md:w-28 flex gap-2"
                                    outlined
                                   
                                >
                                    <IconAdjustments :size="15"/>
                                    Filter
                                </Button>
                            </div>
                        </div>
                    </template>

                    <template #empty>
                        <div class="flex flex-col">
                            <span>No data</span>
                        </div>
                    </template>

                    <template #loading>
                        <div class="flex flex-col gap-2 items-center justify-center">
                            <ProgressSpinner
                                strokeWidth="4"
                            />
                            <span class="text-sm text-gray-700 dark:text-gray-300">Loading data. Please wait. </span>
                        </div>
                    </template>

                    <template v-if="walletTransactionHistory?.length > 0 ">
                        <Column
                            field="approval_at"
                            sortable
                        >
                            <template #header>
                                <span class="block">date</span>
                            </template>
                            <template #body="{ data }">
                                {{ dayjs(data.approval_at).format('YYYY-MM-DD') }}

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ dayjs(data.approval_at).add(8, 'hour').format('hh:mm:ss A') }}
                                </div>
                            </template>
                        </Column>

                        <Column
                            field="transaction_type"
                            sortable
                        >
                            <template #header>
                                <span class="block">transaction type</span>
                            </template>
                            <template #body="{ data }">
                                {{ data.transaction_type }}
                            </template>
                        </Column>

                        <Column
                            field="from_wallet"
                            sortable
                        >
                            <template #header>
                                <span class="block">from</span>
                            </template>
                            <template #body="{ data }">
                                <span v-if="data.from_wallet">
                                    {{ data.from_wallet.user.name }}
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ data.from_wallet.type.replace('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase()) }}
                                    </div>
                                </span>
                                <span v-else>-</span>
                            </template>
                        </Column>

                        <Column
                            field="to_wallet"
                            sortable
                        >
                            <template #header>
                                <span class="block">To</span>
                            </template>
                            <template #body="{ data }">
                                <span v-if="data.to_wallet">
                                    {{ data.to_wallet.user.name }} 
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ data.to_wallet.type.replace('_', ' ').replace(/^\w/, (char) => char.toUpperCase()) }}
                                    </div>
                                </span>
                                <span v-else>-</span>
                            </template>
                        </Column>


                        <Column
                            field="transaction_number"
                            sortable
                        >
                            <template #header>
                                <span class="block">transaction no</span>
                            </template>
                            <template #body="{ data }">
                                {{ data.transaction_number }}
                            </template>
                        </Column>

                        <Column
                            field="amount"
                            sortable
                        >
                            <template #header>
                                <span class="block">amount</span>
                            </template>
                            <template #body="{ data }">

                                <span v-if="data.to_wallet">
                                    {{ data.to_wallet.currency_symbol }}{{ data.amount }}
                                </span>
                                <span v-else>
                                    {{ data.from_wallet.currency_symbol }}{{ data.amount }}
                                </span>
                            </template>
                        </Column>

                        <Column
                            field="status"
                            sortable
                        >
                            <template #header>
                                <span class="block">status</span>
                            </template>
                            <template #body="{ data }">
                                {{ data.status }}
                            </template>
                        </Column>

                        <Column
                            field="action"
                            frozen
                            alignFrozen="right"
                            header=""
                        >
                           
                        </Column>
                    </template>
                </DataTable>
            </div>
        </template>
    </Card>
</template>