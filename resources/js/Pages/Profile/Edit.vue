<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {h, ref} from 'vue';
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
    // {
    //     title: 'payments',
    //     value: '2'
    // }
]);
</script>

<template>
    <AuthenticatedLayout title="profile">
        <div class="flex flex-col">
            <Tabs value="0">
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
