<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useToast } from '@/Composables/useToast';
import { useI18n } from 'vue-i18n';

const props = defineProps({
	employee: {
		type: Object,
		required: true,
	},
});

const companies = ref([]);
const loadingCompanies = ref(false);

const form = useForm({
	first_name: props.employee.first_name ?? '',
	last_name: props.employee.last_name ?? '',
	company_id: props.employee.company_id ?? '',
	email: props.employee.email ?? '',
	phone: props.employee.phone ?? '',
});

const { success, error: errorToast } = useToast();
const { t } = useI18n();

const fetchCompanies = async () => {
	loadingCompanies.value = true;
	try {
		const response = await window.axios.get('/company/list', {
			params: { per_page: 1000 },
		});
		companies.value = response.data?.data ?? [];
	} finally {
		loadingCompanies.value = false;
	}
};

const submit = async () => {
	form.clearErrors();
	try {
		await window.axios.put(`/employee/${props.employee.id}`, form.data());
		success(t('employee.updated'));
		router.visit(`/employee/${props.employee.id}`);
	} catch (error) {
		if (error?.response?.status === 422) {
			form.setError(error.response.data.errors || {});
		}
		errorToast(t('employee.updateError'));
	}
};

onMounted(fetchCompanies);
</script>

<template>
	<Head :title="t('employee.editTitle')" />

	<AuthenticatedLayout>
		<template #header>
			<div class="flex flex-wrap items-center justify-between gap-4">
				<h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
					{{ t('employee.editTitle') }}
				</h2>
				<Link
					href="/employee"
					class="text-sm text-indigo-200 hover:text-indigo-600"
				>
					{{ t('buttons.backToEmployees') }}
				</Link>
			</div>
		</template>

		<div class="py-12">
			<div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
				<div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
					<form class="space-y-6" novalidate @submit.prevent="submit">
						<div class="grid gap-6 md:grid-cols-2">
							<div>
								<InputLabel for="first_name" :value="t('employee.firstName')" />
								<TextInput
									id="first_name"
									type="text"
									class="mt-1 block w-full"
									v-model="form.first_name"
									autofocus
									autocomplete="given-name"
								/>
								<InputError class="mt-2" :message="form.errors.first_name" />
							</div>

							<div>
								<InputLabel for="last_name" :value="t('employee.lastName')" />
								<TextInput
									id="last_name"
									type="text"
									class="mt-1 block w-full"
									v-model="form.last_name"
									autocomplete="family-name"
								/>
								<InputError class="mt-2" :message="form.errors.last_name" />
							</div>
						</div>

						<div>
							<InputLabel for="company_id" :value="t('employee.company')" />
							<select
								id="company_id"
								v-model="form.company_id"
								class="bg-gray-900 text-white mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
							>
								<option value="" disabled>
									{{ loadingCompanies ? t('table.loading') : t('employee.company') }}
								</option>
								<option v-for="company in companies" :key="company.id" :value="company.id">
									{{ company.name }}
								</option>
							</select>
							<InputError class="mt-2" :message="form.errors.company_id" />
						</div>

						<div class="grid gap-6 md:grid-cols-2">
							<div>
								<InputLabel for="email" :value="t('employee.email')" />
								<TextInput
									id="email"
									type="email"
									class="mt-1 block w-full"
									v-model="form.email"
									autocomplete="email"
								/>
								<InputError class="mt-2" :message="form.errors.email" />
							</div>

							<div>
								<InputLabel for="phone" :value="t('employee.phone')" />
								<TextInput
									id="phone"
									type="text"
									class="mt-1 block w-full"
									v-model="form.phone"
									autocomplete="tel"
								/>
								<InputError class="mt-2" :message="form.errors.phone" />
							</div>
						</div>

						<div class="flex items-center justify-end">
							<PrimaryButton :disabled="form.processing">
								{{ t('buttons.updateEmployee') }}
							</PrimaryButton>
						</div>
					</form>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
