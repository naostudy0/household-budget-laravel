<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  account: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  name: props.account.name,
});

const submit = () => {
  form.put(route('accounts.update', props.account.account_uuid));
};
</script>

<template>
  <Head :title="`${account.name}を編集`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">会計単位を編集</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <form class="space-y-6 bg-white p-6 shadow-sm sm:rounded-lg" @submit.prevent="submit">
          <div>
            <InputLabel for="name" value="名前" />
            <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div class="flex items-center gap-4">
            <PrimaryButton :disabled="form.processing">保存</PrimaryButton>
            <Link
              :href="route('accounts.show', account.account_uuid)"
              class="text-sm text-gray-600 underline"
            >
              戻る
            </Link>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
