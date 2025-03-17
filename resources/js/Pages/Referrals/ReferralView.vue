<script setup>
import Card from "primevue/card";
import {IconUsersGroup, IconSearch, IconX, IconChevronUp, IconMinus, IconUserCircle} from "@tabler/icons-vue";
import ScrollPanel from 'primevue/scrollpanel';
import {ref, watch} from "vue";
import {generalFormat} from "@/Composables/format.js";
import Tag from "primevue/tag";
import Dialog from "primevue/dialog";
import {usePage} from "@inertiajs/vue3";
import Button from "primevue/button";
import Skeleton from "primevue/skeleton";

const isLoading = ref(false);
const referrals = ref([]);
const {formatAmount} = generalFormat();

const getResults = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/referral_programme/getReferralsData');
        referrals.value = response.data.referrals;
    } catch (error) {
        console.error('Error refreshing trading accounts data:', error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

const visible = ref(false);
const selectedUser = ref();

const openDialog = (userId) => {
    visible.value = true;
    selectedUser.value = userId
    parent_id.value = userId
    getNetwork()
}

const emit = defineEmits();

const search = ref('');
const checked = ref(true);
const upline = ref(null);
const parent = ref([]);
const children = ref([]);
const upline_id = ref();
const parent_id = ref();
const loading = ref(false);
const user = usePage().props.auth.user;

const getNetwork = async (filterUplineId = upline_id.value, filterParentId = parent_id.value, filterSearch = search.value) => {
    loading.value = true;
    try {
        let url = `/referral_programme/getDownlineData?search=` + filterSearch;

        if (filterUplineId) {
            url += `&upline_id=${filterUplineId}`;
        }

        if (filterParentId) {
            url += `&parent_id=${filterParentId}`;
        }

        const response = await axios.get(url);

        upline.value = response.data.upline;
        parent.value = response.data.parent;
        children.value = response.data.direct_children;

        // Check upline first
        if (upline.value && upline.value.total_agent_count === 0 && upline.value.total_member_count === 0) {
            emit('noData');
        }
        // If upline is not available, check parent
        else if (!upline.value && parent.value && parent.value.total_agent_count === 0 && parent.value.total_member_count === 0) {
            emit('noData');
        }

    } catch (error) {
        console.error('Error get network:', error);
    } finally {
        loading.value = false;
    }
};

watch(search, (newSearchValue) => {
        getNetwork(upline_id.value, parent_id.value, newSearchValue)
});

const selectDownline = (downlineId) => {
    upline_id.value = parent.value.id;
    parent_id.value = downlineId;
    search.value = '';

    getNetwork(upline_id.value, parent_id.value)
}

const collapseAll = () => {
    parent_id.value = selectedUser.value;
    upline_id.value = null;
    search.value = '';
    getNetwork()
}

const backToUpline = (parentLevel) => {
    if (parentLevel === 1) {
        upline_id.value = null;
        parent_id.value = null;
        search.value = '';
        getNetwork()
    } else {
        parent_id.value = parent.value.upline_id;
        upline_id.value = parent.value.upper_upline_id;
        search.value = '';
        getNetwork(upline_id.value, parent_id.value)
    }
}

const clearSearch = () => {
    search.value = '';
}
</script>

<template>
    <Card class="w-full">
        <template #title>
            <div class="flex gap-2 items-center">
                <span>{{ $t('public.my_referrals') }}</span>
            </div>
        </template>
        <template #content>
            <div class="hidden md:flex flex-col items-center self-stretch">
                <!-- Header -->
                <div class="flex items-center justify-between px-3 py-1 w-full uppercase text-xs text-surface-500">
                    <div class="w-full">
                        {{ $t('public.user') }}
                    </div>
                    <div class="w-full">
                        {{ $t('public.total_networks') }}
                    </div>
                    <div class="w-full">
                        {{ $t('public.current_asset_capital') }}
                    </div>
                    <div class="flex justify-end w-full">
                        {{ $t('public.bonus_from_user') }}
                    </div>
                </div>

                <div
                    v-if="isLoading"
                    class="flex items-center justify-between px-3 py-3.5 my-1 w-full bg-surface-50 dark:bg-surface-800 rounded-md border border-surface-200 dark:border-transparent transition-all duration-200"
                >
                    <div class="flex gap-2 items-center w-full">
                        <Skeleton width="5rem" class="my-0.5" height="1rem"></Skeleton>
                    </div>
                    <div class="flex gap-2 items-center w-full">
                        <div class="text-surface-400 dark:text-surface-500">
                            <IconUsersGroup size="18" />
                        </div>
                        <Skeleton width="5rem" class="my-0.5" height="1.rem"></Skeleton>
                    </div>
                    <div class="w-full font-medium">
                        <Skeleton width="5rem" class="my-0.5" height="1rem"></Skeleton>
                    </div>
                    <div class="flex justify-end w-full font-semibold text-green-500">
                        <Skeleton width="5rem" class="my-0.5" height="1rem"></Skeleton>
                    </div>
                </div>

                <!-- Body -->
                <ScrollPanel style="width: 100%; height:100%; max-height: 228px">
                    <div
                        v-for="user in referrals"
                        class="flex items-center justify-between p-3 my-1 w-full bg-surface-50 dark:bg-surface-800 rounded-md border border-surface-200 dark:border-transparent select-none hover:border-primary dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-800/20 cursor-pointer transition-all duration-200"
                        @click="openDialog(user.id)"
                    >
                        <div class="flex gap-2 items-center w-full">
                            <span class="text-sm">{{ user.username }}</span>
                            <Tag
                                severity="info"
                                :value="user.rank.rank_name === 'member' ? $t(`public.${user.rank.rank_name}`) : user.rank.rank_name"
                            />
                        </div>
                        <div class="flex gap-2 items-center w-full">
                            <div class="text-surface-400 dark:text-surface-500">
                                <IconUsersGroup size="18" />
                            </div>
                            <span class="font-semibold">{{ formatAmount(user.total_downlines, 0, '') }}</span>
                            <Tag severity="success">
                                {{ formatAmount(user.total_downline_capital_fund) }}
                            </Tag>
                        </div>
                        <div class="w-full font-medium">
                            {{ formatAmount(user.capital_fund_sum) }}
                        </div>
                        <div class="flex justify-end w-full font-semibold text-green-500">
                            {{ formatAmount(user.total_direct_downline_earnings, 4) }}
                        </div>
                    </div>
                </ScrollPanel>
            </div>

            <!-- Mobile View -->
            <ScrollPanel style="width: 100%; height:100%; max-height: 220px" class="block md:hidden">
                <div
                    v-for="user in referrals"
                    class="flex items-center justify-between p-3 my-1 w-full bg-surface-50 dark:bg-surface-800 rounded-md border border-surface-200 dark:border-transparent select-none hover:border-primary dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-800/20 cursor-pointer transition-all duration-200"
                    @click.prevent="openDialog(user.id)"
                >
                    <div class="flex justify-between items-center w-full">
                        <div class="flex flex-col">
                            <div class="flex gap-2 items-center w-full">
                                <span class="text-sm">{{ user.username }}</span>
                                <Tag
                                    severity="info"
                                    :value="user.rank.rank_name === 'member' ? $t(`public.${user.rank.rank_name}`) : user.rank.rank_name"
                                />
                            </div>
                            <div class="flex gap-2 items-center text-xs w-full">
                                <div class="text-surface-400 dark:text-surface-500">
                                    <IconUsersGroup size="18" />
                                </div>
                                <span class="font-semibold">{{ formatAmount(user.total_downlines, 0, '') }}</span>
                                <span>|</span>
                                <span class="font-semibold">{{ formatAmount(user.total_downline_capital_fund) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col justify-end text-right">
                            <span class="font-semibold">{{ formatAmount(user.capital_fund_sum) }}</span>
                            <span class="text-xs">{{ $t('public.current_asset_capital') }}</span>
                        </div>
                    </div>
                </div>
            </ScrollPanel>
        </template>
    </Card>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.network')"
        class="dialog-xs md:dialog-lg"
    >
        <div class="w-full flex flex-col justify-center items-center px-3 py-5 gap-5 self-stretch rounded-lg shadow-card md:p-6 md:gap-10">
            <div class="flex flex-col md:flex-row gap-3 items-center self-stretch">
<!--                <div class="relative w-full md:w-60">-->
<!--                    <div class="absolute top-2/4 -mt-[9px] left-4 text-surface-500">-->
<!--                        <IconSearch size="20" stroke-width="1.25" />-->
<!--                    </div>-->
<!--                    <InputText v-model="search" placeholder="Search" size="search" class="font-normal w-full md:w-60" />-->
<!--                    <div-->
<!--                        v-if="search"-->
<!--                        class="absolute top-2/4 -mt-2 right-4 text-surface-300 hover:text-surface-400 select-none cursor-pointer"-->
<!--                        @click="clearSearch"-->
<!--                    >-->
<!--                        <IconX size="16" />-->
<!--                    </div>-->
<!--                </div>-->
                <div class="grid grid-cols-1 w-full gap-3">
                    <!-- <div class="flex items-center gap-1">
                            <Toggleswitch v-model="checked" class="w-[42px] h-6" />
                        <div class="text-surface-950">
                            {{ $t('public.show_upline') }}
                        </div>
                    </div> -->
                    <div class="w-full flex justify-end">
                        <Button
                            severity="secondary"
                            @click="collapseAll"
                            class="w-full md:w-auto flex gap-1"
                            size="small"
                        >
                            <IconMinus size="20" stroke-width="1.25" />
                            {{ $t('public.collapse_all') }}
                        </Button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center gap-6 w-full">
                <!-- Upline Section -->
                <div v-if="checked && upline" class="flex flex-col items-center gap-6 w-full">
                    <div class="flex items-center self-stretch gap-3">
                        <span class="text-sm font-medium text-surface-400 dark:text-surface-500 uppercase">{{ $t('public.level' ) }} {{ upline.level ?? 0 }}</span>
                        <div class="h-[1px] flex-1 bg-surface-200 dark:bg-surface-700" />
                    </div>

                    <!-- loading state -->
                    <div v-if="loading" class="flex justify-center flex-wrap w-full">
                        <div
                            class="rounded flex flex-col items-center w-full md:max-w-[240px] shadow-card border-l-4 border border-surface-200 dark:border-surface-700 select-none cursor-pointer md:basis-1/2 bg-white dark:bg-surface-800"
                        >
                            <div class="pt-3 pb-2 px-3 rounded-t flex flex-col w-full self-stretch">
                                <Skeleton width="5rem" class="my-0.5" height="1rem"></Skeleton>
                                <Skeleton width="7rem" class="my-0.5" height="1rem"></Skeleton>
                                <Skeleton width="9rem" class="my-0.5" height="1rem"></Skeleton>
                            </div>
                            <div class="pb-2 px-3 rounded-b flex items-center gap-3 w-full self-stretch">
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <Skeleton width="3rem" class="my-0.5" height="1rem"></Skeleton>
                                    <span class="text-xs uppercase">{{ $t('public.directs') }}</span>
                                </div>
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <Skeleton width="3rem" class="my-0.5" height="1rem"></Skeleton>
                                    <span class="text-xs uppercase">{{ $t('public.networks') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex justify-center flex-wrap w-full relative">
                        <div
                            class="rounded flex flex-col items-center md:max-w-[240px] shadow-card border-l-4 select-none cursor-pointer md:basis-1/2 bg-white dark:bg-surface-800 w-full border-blue-500 border-t border-t-surface-200 dark:border-t-surface-700 border-b border-b-surface-200 dark:border-b-surface-700 border-r border-r-surface-200 dark:border-r-surface-700 hover:border-t hover:border-t-blue-500 hover:border-b hover:border-b-blue-500 hover:border-r hover:border-r-blue-500 transition-colors duration-200"
                        >
                            <div class="pt-3 pb-2 px-3 rounded-t flex flex-col items-center w-full self-stretch">
                                <div class="w-full text-sm font-semibold text-surface-950 dark:text-white truncate">
                                    {{ upline.username }}
                                </div>
                                <div class="w-full text-sm text-surface-400 truncate">
                                    {{ $t('public.fund') }}: <span class="font-semibold text-primary">{{ formatAmount(upline.capital_fund_sum) }}</span>
                                </div>
                                <div class="w-full text-sm text-surface-400 truncate">
                                    {{ $t('public.team_capital') }}: <span class="font-semibold text-primary">{{ formatAmount(upline.total_downline_capital_fund) }}</span>
                                </div>
                            </div>
                            <div class="pb-2 px-3 rounded-b grid grid-cols-2 gap-3 w-full self-stretch text-sm">
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <span class="font-medium">{{ formatAmount(upline.total_directs, 0, '') }}</span>
                                    <span class="text-xs uppercase">{{ $t('public.directs') }}</span>
                                </div>
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <span class="font-medium">{{ formatAmount(upline.total_downlines, 0, '') }}</span>
                                    <span class="text-xs uppercase">{{ $t('public.networks') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Section -->
                <div  v-if="(parent.level === 0 && checked) || (parent.level !== 0 && parent)" class="flex flex-col items-center gap-6 w-full">
                    <div class="flex items-center self-stretch gap-3">
                        <span class="text-sm font-medium text-surface-400 dark:text-surface-500 uppercase">{{ $t('public.level' ) }} {{ parent.level ?? 0 }}</span>
                        <div class="h-[1px] flex-1 bg-surface-200 dark:bg-surface-700" />
                    </div>

                    <!-- loading state -->
                    <div v-if="loading" class="flex justify-center flex-wrap w-full">
                        <div
                            class="rounded flex flex-col items-center w-full md:max-w-[240px] shadow-card border-l-4 border border-surface-200 dark:border-surface-700 select-none cursor-pointer md:basis-1/2 bg-white dark:bg-surface-800"
                        >
                            <div class="pt-3 pb-2 px-3 rounded-t flex flex-col w-full self-stretch">
                                <Skeleton width="5rem" class="my-0.5" height="1rem"></Skeleton>
                                <Skeleton width="7rem" class="my-0.5" height="1rem"></Skeleton>
                                <Skeleton width="9rem" class="my-0.5" height="1rem"></Skeleton>
                            </div>
                            <div class="pb-2 px-3 rounded-b flex items-center gap-3 w-full self-stretch">
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <Skeleton width="3rem" class="my-0.5" height="1rem"></Skeleton>
                                    <span class="text-xs uppercase">{{ $t('public.directs') }}</span>
                                </div>
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <Skeleton width="3rem" class="my-0.5" height="1rem"></Skeleton>
                                    <span class="text-xs uppercase">{{ $t('public.networks') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex justify-center flex-wrap w-full relative">
                        <div class="absolute top-[-18px]">
                            <Button
                                type="button"
                                severity="secondary"
                                rounded
                                class="!px-2"
                                v-if="upline_id && !loading"
                                @click="backToUpline(parent.level)"
                            >
                                <IconChevronUp size="16" stroke-width="1.5"/>
                            </Button>
                        </div>
                        <div
                            class="rounded flex flex-col items-center md:max-w-[240px] shadow-card border-l-4 select-none cursor-pointer md:basis-1/2 bg-white dark:bg-surface-800 w-full border-primary border-t border-t-surface-200 dark:border-t-surface-700 border-b border-b-surface-200 dark:border-b-surface-700 border-r border-r-surface-200 dark:border-r-surface-700 hover:border-t hover:border-primary dark:hover:border-primary transition-all duration-200"
                        >
                            <div class="pt-3 pb-2 px-3 rounded-t flex flex-col items-center w-full self-stretch">
                                <div class="w-full text-sm font-semibold text-surface-950 dark:text-white truncate">
                                    {{ parent.username }}
                                </div>
                                <div class="w-full text-sm text-surface-400 truncate">
                                    {{ $t('public.fund') }}: <span class="font-semibold text-primary">{{ formatAmount(parent.capital_fund_sum) }}</span>
                                </div>
                                <div class="w-full text-sm text-surface-400 truncate">
                                    {{ $t('public.team_capital') }}: <span class="font-semibold text-primary">{{ formatAmount(parent.total_downline_capital_fund) }}</span>
                                </div>
                            </div>
                            <div class="pb-2 px-3 rounded-b grid grid-cols-2 gap-3 w-full self-stretch text-sm">
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <span class="font-medium">{{ formatAmount(parent.total_directs, 0, '') }}</span>
                                    <span class="text-xs uppercase">{{ $t('public.directs') }}</span>
                                </div>
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <span class="font-medium">{{ formatAmount(parent.total_downlines, 0, '') }}</span>
                                    <span class="text-xs uppercase">{{ $t('public.networks') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Children Section -->
                <div v-if="children.length" class="flex flex-col items-center gap-6 w-full">
                    <div class="flex items-center self-stretch gap-3">
                        <span class="text-sm font-medium text-surface-400 dark:text-surface-500 uppercase">{{ $t('public.level' ) }} {{ children[0].level ?? 0 }}</span>
                        <div class="h-[1px] flex-1 bg-surface-200 dark:bg-surface-700" />
                    </div>

                    <!-- loading state -->
                    <div v-if="loading" class="flex justify-center flex-wrap w-full">
                        <div
                            class="rounded flex flex-col items-center w-full md:max-w-[240px] shadow-card border-l-4 border border-surface-200 dark:border-surface-700 select-none cursor-pointer md:basis-1/2 bg-white dark:bg-surface-800"
                        >
                            <div class="pt-3 pb-2 px-3 rounded-t flex flex-col w-full self-stretch">
                                <Skeleton width="5rem" class="my-0.5" height="1rem"></Skeleton>
                                <Skeleton width="7rem" class="my-0.5" height="1rem"></Skeleton>
                                <Skeleton width="9rem" class="my-0.5" height="1rem"></Skeleton>
                            </div>
                            <div class="pb-2 px-3 rounded-b flex items-center gap-3 w-full self-stretch">
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <Skeleton width="3rem" class="my-0.5" height="1rem"></Skeleton>
                                    <span class="text-xs uppercase">{{ $t('public.directs') }}</span>
                                </div>
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <Skeleton width="3rem" class="my-0.5" height="1rem"></Skeleton>
                                    <span class="text-xs uppercase">{{ $t('public.networks') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex overflow-x-auto md:overflow-x-hidden md:flex gap-3 md:gap-5 md:justify-center md:flex-wrap w-full">
                        <div
                            v-for="downline in children"
                            :key="downline.id"
                            class="rounded flex flex-col items-center md:max-w-[240px] shadow-card border-l-4 select-none cursor-pointer md:basis-1/2 bg-white dark:bg-surface-800 w-full min-w-[240px] border-primary border-t border-t-surface-200 dark:border-t-surface-700 border-b border-b-surface-200 dark:border-b-surface-700 border-r border-r-surface-200 dark:border-r-surface-700 hover:border-t hover:border-primary dark:hover:border-primary transition-all duration-200"
                            @click="selectDownline(downline.id)"
                        >
                            <div class="pt-3 pb-2 px-3 rounded-t flex flex-col items-center w-full self-stretch">
                                <div class="w-full text-sm font-semibold text-surface-950 dark:text-white truncate">
                                    {{ downline.username }}
                                </div>
                                <div class="w-full text-sm text-surface-400 truncate">
                                    {{ $t('public.fund') }}: <span class="font-semibold text-primary">{{ formatAmount(downline.capital_fund_sum) }}</span>
                                </div>
                                <div class="w-full text-sm text-surface-400 truncate">
                                    {{ $t('public.team_capital') }}: <span class="font-semibold text-primary">{{ formatAmount(downline.total_downline_capital_fund) }}</span>
                                </div>
                            </div>
                            <div class="pb-2 px-3 rounded-b grid grid-cols-2 gap-3 w-full self-stretch text-sm">
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <span class="font-medium">{{ formatAmount(downline.total_directs, 0, '') }}</span>
                                    <span class="text-xs uppercase">{{ $t('public.directs') }}</span>
                                </div>
                                <div class="flex flex-col items-center w-full bg-surface-100 dark:bg-surface-700 p-2">
                                    <span class="font-medium">{{ formatAmount(downline.total_downlines, 0, '') }}</span>
                                    <span class="text-xs uppercase">{{ $t('public.networks') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Dialog>
</template>
