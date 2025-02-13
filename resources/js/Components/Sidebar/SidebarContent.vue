<script setup>
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import {
    IconLayoutDashboard,
    IconWallet,
    IconCoin,
    IconDatabaseDollar,
    IconReport,
} from '@tabler/icons-vue';
import SidebarCategoryLabel from "@/Components/Sidebar/SidebarCategoryLabel.vue";

</script>

<template>
    <!-- Dashboard -->
    <SidebarLink
        title="dashboard"
        :href="route('dashboard')"
        :active="route().current('dashboard')"
    >
        <template #icon>
            <IconLayoutDashboard :size="20" stroke-width="1.5" />
        </template>
    </SidebarLink>

    <!--        <SidebarLink-->
    <!--            title="broker"-->
    <!--            :href="route('wallet.getWalletHistory')"-->
    <!--            :active="route().current('wallet.getWalletHistory')"-->
    <!--        >-->
    <!--            <template #icon>-->
    <!--                <IconWallet :size="20" stroke-width="1.5"/>-->
    <!--            </template>-->
    <!--        </SidebarLink>-->

    <!-- Analytics -->
    <SidebarCategoryLabel title="analytics" />

    <SidebarLink
        title="standard_bonus"
        :href="route('report.standard_bonus')"
        :active="route().current('report.standard_bonus')"
    >
        <template #icon>
            <IconCoin :size="20" stroke-width="1.5"/>
        </template>
    </SidebarLink>

    <SidebarLink
        title="rebate_bonus"
        :href="route('report.rebate_bonus')"
        :active="route().current('report.rebate_bonus')"
    >
        <template #icon>
            <IconDatabaseDollar :size="20" stroke-width="1.5"/>
        </template>
    </SidebarLink>
</template>
