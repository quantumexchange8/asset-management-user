<script setup>
import Avatar from 'primevue/avatar';
import {usePage, Link} from "@inertiajs/vue3";
import {generalFormat} from "@/Composables/format.js";
import Tag from "primevue/tag";

const user = usePage().props.auth.user;
const rank = usePage().props.auth.rank;
const {formatNameLabel} = generalFormat();
</script>

<template>
    <Link
        :href="route('profile')"
        :class="[
            'flex items-center gap-3 self-stretch group select-none cursor-pointer transition-colors py-3 px-7',
            {
                'text-surface-950 dark:text-white hover:text-primary hover:bg-primary-50 dark:hover:bg-surface-800 dark:hover:text-primary-500': !route().current('profile'),
                'text-primary bg-primary-100 dark:bg-surface-800': route().current('profile'),
            },
        ]"
    >
        <Avatar
            v-if="usePage().props.auth.profile_photo"
            :image="usePage().props.auth.profile_photo"
            shape="circle"
        />
        <Avatar
            v-else
            :label="formatNameLabel(user.name)"
            shape="circle"
        />
        <div class="flex flex-col text-sm">
            <div class="flex items-center gap-1 font-semibold">
                <span class="max-w-28 truncate">{{ user.name }}</span>
                <Tag
                    severity="primary"
                    :value="rank.rank_name === 'member' ? $t(`public.${rank.rank_name}`) : rank.rank_name"
                />
            </div>
            <span class="text-xs text-surface-500 max-w-36 truncate">{{ user.email }}</span>
        </div>
    </Link>
</template>
