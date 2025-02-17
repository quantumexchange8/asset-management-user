<script setup>
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import SidebarCategoryLabel from "@/Components/Sidebar/SidebarCategoryLabel.vue";

// Import Tabler icons
import {
    IconLayoutDashboard,
    IconHomeDollar,
    IconCoin,
    IconDatabaseDollar,
    IconHomeShare,
    IconChartBar
} from '@tabler/icons-vue';

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

    <!-- Market -->
    <SidebarCategoryLabel title="market" />

    <SidebarLink
        title="broker"
        :href="route('broker')"
        :active="route().current('broker')"
    >
        <template #icon>
            <IconHomeDollar :size="20" stroke-width="1.5"/>
        </template>
    </SidebarLink>

    <SidebarLink
        title="connections"
        :href="route('connections')"
        :active="route().current('connections')"
    >
        <template #icon>
            <IconHomeShare :size="20" stroke-width="1.5"/>
        </template>
    </SidebarLink>

    <!-- Analytics -->
    <SidebarCategoryLabel title="analytics" />

    <SidebarLink
        title="profit_sharing"
        :href="route('report.profit_sharing')"
        :active="route().current('report.profit_sharing')"
    >
        <template #icon>
            <IconChartBar :size="20" stroke-width="1.5"/>
        </template>
    </SidebarLink>

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
