<script setup>
import { ref, computed, watch } from "vue";
import Card from "primevue/card";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Deposit from "@/Pages/Dashboard/DashboardWallet/Deposit.vue";
import LinkAccount from "@/Pages/Dashboard/LinkAccount.vue";
import Withdrawal from "./Withdrawal.vue";
import {router, usePage} from "@inertiajs/vue3";
import {useConfirm} from "primevue/useconfirm";
import {trans} from "laravel-vue-i18n";

const props = defineProps({
    wallets: Array
})

const visible = ref(false);
const dialogType = ref('');

const openDialog = (type) => {
    if (usePage().props.auth.paymentAccountsCount === 0) {
        requireConfirmation('add_payment_account')
    } else {
        visible.value = true;
        dialogType.value = type;
    }
}

const confirm = useConfirm();

const requireConfirmation = (action_type) => {
    const messages = {
        add_payment_account: {
            group: 'headless-info',
            header: trans('public.missing_payment_account'),
            text: trans('public.missing_payment_account_caption'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.proceed'),
            action: () => {
                window.location.href = route('profile', {tab: 'finance'});
            }
        },
    };

    const { group, header, text, dynamicText, suffix, actionType, cancelButton, acceptButton, action } = messages[action_type];

    confirm.require({
        group,
        header,
        actionType,
        text,
        dynamicText,
        suffix,
        cancelButton,
        acceptButton,
        accept: action
    });
};
</script>

<template>
    <Card class="bg-white shadow-md rounded-lg">
        <template #title>
            <span class="font-semibold">{{ $t('public.account') }}</span>
        </template>
        <template #content>
            <div class="flex flex-col gap-2 items-center">
                <Button
                    severity="secondary"
                    size="small"
                    :label="$t('public.link_account')"
                    class="w-full"
                    :disabled="!wallets.length"
                    @click="openDialog('link_account')"
                />
                <Button
                    severity="danger"
                    size="small"
                    :label="$t('public.withdraw')"
                    class="w-full"
                    :disabled="!wallets.length"
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
<!--        <template v-if="dialogType === 'deposit'">-->
<!--            <Deposit-->
<!--                :wallets="wallets"-->
<!--                @update:visible="visible = $event"-->
<!--            />-->
<!--        </template>-->

        <template v-if="dialogType === 'link_account'">
            <LinkAccount
                @update:visible="visible = $event"
            />
        </template>

        <template v-if="dialogType === 'withdrawal'">
            <Withdrawal
                :wallets="wallets"
                @update:visible="visible = $event"
            />
        </template>

    </Dialog>
</template>
