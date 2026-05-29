<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
  accounts: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <Head title="会計単位" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">会計単位</h2>
        <Link
          :href="route('accounts.create')"
          class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white"
        >
          追加
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="divide-y divide-gray-100">
            <div v-if="accounts.length === 0" class="p-6 text-gray-600">
              会計単位はまだありません。
            </div>
            <div
              v-for="account in accounts"
              :key="account.account_uuid"
              class="flex items-center justify-between p-6"
            >
              <Link
                :href="route('accounts.show', account.account_uuid)"
                class="font-medium text-gray-900"
              >
                {{ account.name }}
              </Link>
              <Link
                :href="route('accounts.edit', account.account_uuid)"
                class="text-sm text-gray-600 underline"
              >
                編集
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
