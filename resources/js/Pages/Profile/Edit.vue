<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {h, onMounted, ref} from 'vue';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import AccountPreferences from "@/Pages/Profile/Account/AccountPreferences.vue";
import PaymentAccount from "@/Pages/Profile/Payment/PaymentAccount.vue";

const props = defineProps({
    front_identity_image: String,
    back_identity_image: String,
})

const tabs = ref([
    {
        title: 'account',
        component: h(AccountPreferences, {
            title: 'account',
            front_identity_image: props.front_identity_image,
            back_identity_image: props.back_identity_image
        }),
        value: '0'
    },
    // {
    //     title: 'credentials',
    //     value: '1'
    // },
    {
        title: 'finance',
        component: h(PaymentAccount, {
            title: 'finance',
        }),
        value: '2'
    }
]);

const selectedType = ref('account');
const activeIndex = ref('0');

onMounted(() => {
    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    if (params.tab === 'finance') {
        selectedType.value = 'finance';
        activeIndex.value = '2';
    }
});
</script>

<template>
    <AuthenticatedLayout title="profile">
        <div class="flex flex-col">
            <Tabs v-model:value="activeIndex">
                <TabList>
                    <Tab
                        v-for="tab in tabs"
                        :key="tab.title"
                        :value="tab.value"
                    >
                        {{ $t(`public.${tab.title}`) }}
                    </Tab>
                </TabList>
                <TabPanels>
                    <TabPanel
                        v-for="tab in tabs"
                        :key="tab.value"
                        :value="tab.value"
                    >
                        <component :is="tab.component" />
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </AuthenticatedLayout>
</template>
