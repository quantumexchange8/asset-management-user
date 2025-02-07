<script setup>
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import {
    IconLayoutDashboard,
    IconWallet,
    IconClockDollar,
    IconCreditCardPay,
    IconReport,
} from '@tabler/icons-vue';
import SidebarCategoryLabel from "@/Components/Sidebar/SidebarCategoryLabel.vue";
import ScrollPanel from 'primevue/scrollpanel';

</script>

<template>
    <ScrollPanel style="flex: 1; width: 100%;" class="h-full">
        <!-- Dashboard -->
        <SidebarLink 
            :title="'Dashboard'" 
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            <template #icon>
                <IconLayoutDashboard :size="20" stroke-width="1.5" />
            </template>
        </SidebarLink>

        <SidebarLink
            :title="'Broker'"
            :href="route('wallet.getWalletHistory')"
            :active="route().current('wallet.getWalletHistory')"
        >
        <template #icon>
            <IconWallet :size="20" stroke-width="1.5"/>
        </template>
    </SidebarLink>

    <!-- Analytics -->
    <SidebarCategoryLabel :title="'Analytics'"/>

    <SidebarCollapsible
        :title="'Report'"
    >
        <template #icon>
            <IconReport :size="20" stroke-width="1.5"/>
        </template>


        <SidebarCollapsibleItem
            :title="'Lot Commission'"
            :href="route('report.lot_comission')"
        />
    </SidebarCollapsible>
    </ScrollPanel>
</template>
