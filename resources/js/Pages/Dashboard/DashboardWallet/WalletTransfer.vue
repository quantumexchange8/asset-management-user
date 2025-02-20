<script setup>
import { ref, computed, watch } from "vue";
import Card from "primevue/card";
import Button from "primevue/button";
import Dialog from "primevue/dialog";

const showWithdrawDialog = ref(false);
const showDepositDialog = ref(false);
const withdrawAmount = ref("");
const depositAmount = ref("");
const selectedWallet = ref("");
const selectedAccount = ref("");
const selectedDepositType = ref("bank");
const isSubmitting = ref(false);
const errorMessage = ref("");
const uploadedPhotos = ref([]); // Stores image previews
const fileInput = ref(null); // Reference to file input

// Fetch Wallet Balances from Backend
const fetchWallets = async () => {
  try {
    const userId = 2; // 🔥 Change this dynamically if needed
    const response = await axios.get(`http://localhost:3000/api/wallets?userId=${userId}`);
    wallets.value = response.data;
  } catch (error) {
    console.error("Error fetching wallets:", error);
  }
};

const walletOptions = [
    { label: "Bonus Wallet", value: "bonus_wallet" },
    { label: "Cash Wallet", value: "cash_wallet" }
];

const accountOptions = [
    { label: "中国农业银行 (Test User 278)", value: "agricultural_bank" },
    { label: "Maybank (Maybank User)", value: "maybank" },
    { label: "USDT (TRC20) (TEST278-2)", value: "usdt_trc20" }
];

const openWithdrawDialog = () => {
    showWithdrawDialog.value = true;
    withdrawAmount.value = "";
    selectedWallet.value = "";
    selectedAccount.value = "";
    errorMessage.value = "";
};

const openDepositDialog = () => {
    showDepositDialog.value = true;
    depositAmount.value = "";
    selectedDepositType.value = "bank";
};

// Compute live balance
const displayedBalance = computed(() => {
    if (!selectedWallet.value) return "US$0.00";
    const currentBalance = walletBalances.value[selectedWallet.value] || 0;
    return `US$${currentBalance.toFixed(2)}`;
});

// Calculate withdrawal fee
const withdrawFee = computed(() => {
    const amount = parseFloat(withdrawAmount.value) || 0;
    return Math.max(amount * 0.03, 5.00);
});

// Calculate amount received after withdrawal fee
const receiveAmount = computed(() => {
    const amount = parseFloat(withdrawAmount.value) || 0;
    return Math.max(amount - withdrawFee.value, 0);
});




// Handle file selection and preview
const handleFileUpload = (event) => {
    const files = event.target.files;
    processFiles(files);
};

// Handle drag & drop
const handleDrop = (event) => {
  event.preventDefault(); // ✅ Prevents browser from opening the file
  event.stopPropagation(); // ✅ Ensures event does not propagate further
  const files = event.dataTransfer.files;
  processFiles(files);
};

// Process selected files
const processFiles = (files) => {
    for (let file of files) {
        const reader = new FileReader();
        reader.onload = (e) => {
            uploadedPhotos.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    }
};

// Remove uploaded image
const removeImage = (index) => {
    uploadedPhotos.value.splice(index, 1);
};


// Open file picker when clicking "browse"
const openFilePicker = () => {
    fileInput.value.click();
};



// Validate withdrawal amount
watch([withdrawAmount, selectedWallet, selectedAccount], () => {
    const amount = parseFloat(withdrawAmount.value) || 0;
    const balance = walletBalances.value[selectedWallet.value] || 0;

    if (
        selectedWallet.value &&
        selectedAccount.value &&
        amount > 0 &&
        amount <= balance
    ) {
        errorMessage.value = "";
    } else if (amount > balance) {
        errorMessage.value = "You do not have enough balance.";
    } else {
        errorMessage.value = "Please fill in all fields.";
    }
});

// Submit withdrawal
const submitWithdrawal = () => {
    const amount = parseFloat(withdrawAmount.value);
    if (!amount || !selectedWallet.value || !selectedAccount.value) {
        errorMessage.value = "Please fill in all fields.";
        return;
    }

    const currentBalance = walletBalances.value[selectedWallet.value] || 0;
    if (amount > currentBalance) {
        errorMessage.value = "You do not have enough balance.";
        return;
    }

    walletBalances.value[selectedWallet.value] -= amount;
    isSubmitting.value = true;

    setTimeout(() => {
        alert(`Withdrawal successful! New balance: US$${walletBalances.value[selectedWallet.value].toFixed(2)}`);
        isSubmitting.value = false;
        showWithdrawDialog.value = false;
    }, 2000);
};

// Submit deposit
const submitDeposit = () => {
    const amount = parseFloat(depositAmount.value);
    if (!amount || amount <= 0) {
        alert("Please enter a valid deposit amount.");
        return;
    }

    // Assume all deposits go to the cash wallet
    walletBalances.value.cash_wallet += amount;
    alert(`Deposit successful! New balance: US$${walletBalances.value.cash_wallet.toFixed(2)}`);
    showDepositDialog.value = false;
};

// Input handler for numeric fields
const handleInput = (event, targetRef) => {
    let value = event.target.value;
    value = value.replace(/[^0-9.]/g, "");
    value = value.replace(/(\..*)\./g, "$1");
    targetRef.value = value;
};
</script>

<template>
    <Card class="bg-white shadow-md rounded-lg">
        <template #title>
            <span class="text-gray-700 font-semibold">Transfer Funds</span>
        </template>
        <template #content>
            <div class="flex flex-col gap-2 items-center">
                <Button
                    severity="success"
                    size="small"
                    label="Deposit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white"
                    @click="openDepositDialog"
                />
                <Button
                    severity="danger"
                    size="small"
                    label="Withdraw"
                    class="w-full bg-red-600 hover:bg-red-700 text-white"
                    @click="openWithdrawDialog"
                />
            </div>
        </template>
    </Card>

    <!-- Withdraw Dialog -->
    <Dialog v-model:visible="showWithdrawDialog" modal header="Withdrawal" :style="{ width: '30rem' }" class="rounded-lg shadow-lg">
        <div class="flex flex-col gap-4 p-4">
            <div class="text-lg font-bold text-center bg-gray-100 p-3 rounded-md">
                Available balance: <span class="text-primary">{{ displayedBalance }}</span>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-gray-600 font-medium">Select Wallet</label>
                <select v-model="selectedWallet" class="w-full border rounded-md px-2 py-1 text-gray-700">
                    <option disabled value="">Please select a wallet</option>
                    <option v-for="wallet in walletOptions" :key="wallet.value" :value="wallet.value">
                        {{ wallet.label }}
                    </option>
                </select>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-gray-600 font-medium">Withdraw Amount</label>
                <input
                    v-model="withdrawAmount"
                    placeholder="Enter amount"
                    class="w-full border rounded-md px-3 py-2"
                    @input="e => handleInput(e, withdrawAmount)"
                />
                <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-gray-600 font-medium">To Account</label>
                <select v-model="selectedAccount" class="w-full border rounded-md px-2 py-1 text-gray-700">
                    <option disabled value="">Please select an account</option>
                    <option v-for="account in accountOptions" :key="account.value" :value="account.value">
                        {{ account.label }}
                    </option>
                </select>
            </div>

            <div class="text-sm text-gray-600">
                <p>Withdrawal Charges (3%): <strong>${{ withdrawFee.toFixed(2) }}</strong></p>
                <p>Receive Amount: <strong>${{ receiveAmount.toFixed(2) }}</strong></p>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <Button label="Cancel" class="p-button-text text-gray-700" @click="showWithdrawDialog = false" />
                <Button 
                    label="Confirm" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md"
                    :disabled="isSubmitting || !!errorMessage"
                    @click="submitWithdrawal"
                />
            </div>
        </div>
    </Dialog>

    <!-- Deposit Dialog -->
    <Dialog v-model:visible="showDepositDialog" modal header="Deposit" :style="{ width: '30rem' }" class="rounded-lg shadow-lg">
        <div class="flex flex-col gap-4 p-4">
            <div class="text-lg font-bold text-center bg-gray-100 p-3 rounded-md">
                Balance: US${{ walletBalances.cash_wallet.toFixed(2) }}
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-gray-600 font-medium">Select Type</label>
                <div class="flex gap-4">
                   
                    <button 
                        class="w-1/2 py-2 rounded-md text-center"
                        :class="selectedDepositType === 'usdt' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                        @click="selectedDepositType = 'usdt'"
                    >Tether (USDT)</button>
                </div>
            </div>

             <!-- Network ID -->
             <label class="text-gray-600 font-medium"
            >Network--------------------------------------------****</label>

          
            <!-- Token Address -->
            <label class="text-gray-600 font-medium"
            >Token Address--------------------------------------****</label>
        


             <!-- Deposit Amount -->
            <div class="mt-4">
            <label class="text-gray-700 font-medium">Deposit Amount</label>
            <div class="relative mt-2">
                <input 
                type="number" 
                placeholder="Enter amount" 
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300"
                />
            </div>
            </div>

        
            <!-- Upload Payment Slip -->
            <div class="mt-4">
                <label class="text-gray-700 font-medium">Upload Payment Slip</label>
                <div 
                    class="border-dashed border-2 border-gray-300 p-4 rounded-lg text-center cursor-pointer hover:bg-gray-100 mt-2"
                    @dragover.prevent
                    @drop="handleDrop"
                    @click="fileInput.click()"
                >
                    <input 
                        ref="fileInput"
                        type="file"
                        multiple
                        accept="image/*"
                        class="hidden"
                        @change="handleFileUpload"
                    />
                    <p class="text-gray-600 text-sm">Drag & drop files here or 
                        <span class="text-blue-500 font-semibold cursor-pointer">browse</span>
                    </p>
                </div>

                <!-- Image Previews -->
                <div v-if="uploadedPhotos.length" class="flex gap-2 mt-2">
                    <div v-for="(photo, index) in uploadedPhotos" :key="index" class="relative">
                        <img :src="photo" class="w-16 h-16 object-cover rounded-md shadow-md" />
                        <button 
                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full px-2 py-1 text-xs"
                            @click="removeImage(index)"
                        >X</button>
                    </div>
                </div>
            </div>
                
            <!-- Confirm and cancel button-->
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" class="p-button-text text-gray-700" @click="showDepositDialog = false" />
                    <Button 
                        label="Confirm" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
                        @click="submitDeposit"
                />
            </div>
        </div>
    </Dialog>
</template>
