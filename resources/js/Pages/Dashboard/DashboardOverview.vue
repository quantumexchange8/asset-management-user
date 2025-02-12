<script setup>
import Card from "primevue/card";
import {generalFormat} from "@/Composables/format.js";
import {
    IconWallet,
    IconUserDollar
} from "@tabler/icons-vue";

const props = defineProps({
    currentAssetCapital: String,
    currentTeamCapital: String,
    totalBonus: String,
})

const {formatAmount} = generalFormat();
</script>

<template>
    <Card class="w-full">
        <template #content>
            <div class="flex flex-col gap-5 items-center self-stretch w-full">
                <div class="flex flex-col gap-1 items-center md:items-start self-stretch w-full">
                    <div class="text-4xl font-semibold">
                        ${{ formatAmount(currentAssetCapital) }}
                    </div>
                    <span class="text-surface-500 text-sm">{{ $t('public.current_asset_capital') }}</span>
                </div>

                <div class="flex flex-col md:flex-row gap-5 items-center md:justify-between w-full">
                    <div class="flex gap-2 items-center bg-surface-100 dark:bg-surface-800 p-3 rounded-md w-full">
                        <div class="flex justify-center items-center p-2 bg-surface-200 dark:bg-surface-900 rounded-md">
                            <IconWallet stroke-width="1.5" />
                        </div>
                        <div class="flex flex-col items-start self-stretch">
                            <div class="text-xl font-semibold">
                                ${{ formatAmount(totalBonus, 4) }}
                            </div>
                            <span class="text-surface-500 text-sm">{{ $t('public.total_bonus') }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2 items-center bg-surface-100 dark:bg-surface-800 p-3 rounded-md w-full">
                        <div class="flex justify-center items-center p-2 bg-surface-200 dark:bg-surface-900 rounded-md">
                            <IconUserDollar stroke-width="1.5" />
                        </div>
                        <div class="flex flex-col items-start self-stretch">
                            <div class="text-xl font-semibold">
                                ${{ formatAmount(currentTeamCapital) }}
                            </div>
                            <span class="text-surface-500 text-sm">{{ $t('public.current_team_capital') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Card>
</template>
