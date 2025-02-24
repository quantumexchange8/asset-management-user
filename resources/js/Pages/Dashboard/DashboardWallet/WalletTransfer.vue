<script setup>
import { ref, computed, watch } from "vue";
import Card from "primevue/card";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Deposit from "@/Pages/Dashboard/DashboardWallet/Deposit.vue";

const props = defineProps({
    wallets: Array
})

const visible = ref(false);
const dialogType = ref('');

const openDialog = (type) => {
    visible.value = true;
    dialogType.value = type;
}
</script>

<template>
    <Card class="bg-white shadow-md rounded-lg">
        <template #title>
            <span class="font-semibold">{{ $t('public.transfer') }}</span>
        </template>
        <template #content>
            <div class="flex flex-col gap-2 items-center">
                <Button
                    severity="success"
                    size="small"
                    :label="$t('public.deposit')"
                    class="w-full"
                    :disabled="!wallets.length"
                    @click="openDialog('deposit')"
                />
                <Button
                    severity="danger"
                    size="small"
                    :label="$t('public.withdraw')"
                    class="w-full"
                    disabled
                    @click="openDialog('withdrawal')"
                />
            </div>
        </template>
    </Card>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t(`public.${dialogType}`)"
        class="dialog-xs md:dialog-md"
    >
        <template v-if="dialogType === 'deposit'">
            <Deposit
                :wallets="wallets"
                @update:visible="visible = $event"
            />
        </template>
    </Dialog>
</template>
