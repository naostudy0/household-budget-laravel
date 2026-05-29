<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
  account: {
    type: Object,
    required: true,
  },
});

const destroy = () => {
  if (!window.confirm('このアカウントを削除しますか？')) {
    return;
  }

  router.delete(route('accounts.destroy', props.account.account_uuid));
};
</script>

<template>
  <Head :title="account.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ account.name }}</h2>
        <Link
          :href="route('accounts.edit', account.account_uuid)"
          class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white"
        >
          編集
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="space-y-6 bg-white p-6 shadow-sm sm:rounded-lg">
          <div class="flex items-center gap-4">
            <Link :href="route('accounts.index')" class="text-sm text-gray-600 underline">
              一覧へ戻る
            </Link>
            <button type="button" class="text-sm text-red-600 underline" @click="destroy">
              削除
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
