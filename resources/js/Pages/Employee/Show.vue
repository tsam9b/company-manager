<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
	employee: {
		type: Object,
		required: true,
	},
});

const { success, error: errorToast } = useToast();

const handleDelete = async () => {
	if (!window.confirm('Delete this employee?')) return;
	try {
		await window.axios.delete(`/employee/${props.employee.id}`);
		success('Employee deleted.');
		router.visit('/employee');
	} catch (error) {
		errorToast('Failed to delete employee.');
	}
};
</script>

<template>
	<Head title="Employee Details" />

	<AuthenticatedLayout>
		<template #header>
			<div class="flex flex-wrap items-center justify-between gap-4">
				<h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
					Employee Details
				</h2>
				<div class="flex items-center gap-2">
					<Link
						:href="`/employee/${employee.id}/edit`"
						class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
					>
						Edit
					</Link>
					<button
						type="button"
						class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
						@click="handleDelete"
					>
						Delete
					</button>
				</div>
			</div>
		</template>

		<div class="py-12">
			<div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
				<div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
					<div class="grid gap-4 sm:grid-cols-2">
						<div>
							<p class="text-xs font-semibold uppercase text-gray-500">First name</p>
							<p class="text-sm text-gray-900 dark:text-gray-100">{{ employee.first_name }}</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase text-gray-500">Last name</p>
							<p class="text-sm text-gray-900 dark:text-gray-100">{{ employee.last_name }}</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase text-gray-500">Email</p>
							<p class="text-sm text-gray-900 dark:text-gray-100">{{ employee.email || '—' }}</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase text-gray-500">Phone</p>
							<p class="text-sm text-gray-900 dark:text-gray-100">{{ employee.phone || '—' }}</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase text-gray-500">Company</p>
							<p class="text-sm text-gray-900 dark:text-gray-100">
								{{ employee.get_company?.name || '—' }}
							</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase text-gray-500">ID</p>
							<p class="text-sm text-gray-900 dark:text-gray-100">{{ employee.id }}</p>
						</div>
					</div>
					<div class="mt-6">
						<Link href="/employee" class="text-sm text-indigo-200 hover:text-indigo-600">
							Back to Employees
						</Link>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
