<template>
  <div class="p-4 bg-gray-100 min-h-screen">
    <!-- Filter and Search -->
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center space-x-2">
        <input
          type="text"
          placeholder="Search bots"
          class="px-4 py-2 border rounded-lg w-full sm:w-64 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button class="px-4 py-2 bg-gray-200 rounded-lg shadow hover:bg-gray-300">
          <i class="pi pi-filter"></i> Filter
        </button>
      </div>
      <select class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="latest">Latest</option>
        <option value="popular">Largest Fund</option>
        <option value="roi">Most Investors</option>
      </select>
    </div>

    <!-- Bot Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Card 1 -->
      <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Perceptrader EA Bot</h3>
          <span class="px-2 py-1 bg-blue-100 text-blue-600 text-sm font-semibold rounded">
            $500
          </span>
        </div>
        <p class="text-sm text-gray-600 mt-1">457312</p>
        <span class="text-sm text-blue-600 font-semibold">60% Profit Sharing</span>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
          <div>
            <p class="text-gray-500 text-sm font-medium">14 Days</p>
            <p class="text-xs text-gray-400">Settlement</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm font-medium">5% - 10%</p>
            <p class="text-xs text-gray-400">Estimated ROI</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm font-medium">10 - 20</p>
            <p class="text-xs text-gray-400">Lot Size</p>
          </div>
        </div>
        <hr class="my-4">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-sm text-gray-500">
              <i class="pi pi-users"></i> 3 Subscribers
            </p>
            <p class="text-sm text-gray-500">
              <i class="pi pi-dollar"></i> $72,000.00 Fund
            </p>
          </div>
        </div>
        <div class="flex justify-between mt-4">
          <button class="px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-sm">
            Subscribe
          </button>
          <button class="px-3 py-1 bg-white border border-gray-300 rounded-lg shadow hover:bg-gray-200 text-sm">
            View Details
          </button>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Titan X Robot</h3>
          <span class="px-2 py-1 bg-blue-100 text-blue-600 text-sm font-semibold rounded">
            $500
          </span>
        </div>
        <p class="text-sm text-gray-600 mt-1">457286</p>
        <span class="text-sm text-blue-600 font-semibold">60% Profit Sharing</span>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
          <div>
            <p class="text-gray-500 text-sm font-medium">7 Days</p>
            <p class="text-xs text-gray-400">Settlement</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm font-medium">3% - 8%</p>
            <p class="text-xs text-gray-400">Estimated ROI</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm font-medium">10 - 20</p>
            <p class="text-xs text-gray-400">Lot Size</p>
          </div>
        </div>
        <hr class="my-4">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-sm text-gray-500">
              <i class="pi pi-users"></i> 55 Subscribers
            </p>
            <p class="text-sm text-gray-500">
              <i class="pi pi-dollar"></i> $1,133,200.00 Fund
            </p>
          </div>
        </div>
        <div class="flex justify-between mt-4">
          <button class="px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-sm">
            Subscribe
          </button>
          <button class="px-3 py-1 bg-white border border-gray-300 rounded-lg shadow hover:bg-gray-200 text-sm">
            View Details
          </button>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-gray-800">Bullion AI-Trading</h3>
          <span class="px-2 py-1 bg-blue-100 text-blue-600 text-sm font-semibold rounded">
            $1,000
          </span>
        </div>
        <p class="text-sm text-gray-600 mt-1">457285</p>
        <span class="text-sm text-blue-600 font-semibold">60% Profit Sharing</span>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
          <div>
            <p class="text-gray-500 text-sm font-medium">30 Days</p>
            <p class="text-xs text-gray-400">Settlement</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm font-medium">5% - 15%</p>
            <p class="text-xs text-gray-400">Estimated ROI</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm font-medium">15 - 30</p>
            <p class="text-xs text-gray-400">Lot Size</p>
          </div>
        </div>
        <hr class="my-4">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-sm text-gray-500">
              <i class="pi pi-users"></i> 340 Subscribers
            </p>
            <p class="text-sm text-gray-500">
              <i class="pi pi-dollar"></i> $1,970,702.40 Fund
            </p>
          </div>
        </div>
        <div class="flex justify-between mt-4">
          <button class="px-3 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 text-sm">
            Subscribe
          </button>
          <button class="px-3 py-1 bg-white border border-gray-300 rounded-lg shadow hover:bg-gray-200 text-sm">
            View Details
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center space-x-2">
      <button class="px-4 py-2 bg-gray-200 rounded-lg shadow hover:bg-gray-300">
        &lt;
      </button>
      <button class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow">1</button>
      <button class="px-4 py-2 bg-gray-200 rounded-lg shadow hover:bg-gray-300">
        &gt;
      </button>
    </div>
  </div>
</template>

<script setup>
/* Placeholder script for dynamic functionality */
</script>

<style scoped>
/* Responsive adjustments */
.grid-cols-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
</style>
