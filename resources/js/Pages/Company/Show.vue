<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useToast } from '@/Composables/useToast';

const props = defineProps({
	company: {
		type: Object,
		required: true,
	},
});

const { success, error: errorToast } = useToast();

const handleDelete = async () => {
	if (!window.confirm('Delete this company?')) return;
	try {
		await window.axios.delete(`/company/${props.company.id}`);
		success('Company deleted.');
		router.visit('/company');
	} catch (error) {
		errorToast('Failed to delete company.');
	}
};
</script>

<template>
	<Head title="Company Details" />

	<AuthenticatedLayout>
		<template #header>
			<div class="flex flex-wrap items-center justify-between gap-4">
				<h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
					Company Details
				</h2>
				<div class="flex items-center gap-2">
					<Link
						:href="`/company/${company.id}/edit`"
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
					<div class="flex flex-col gap-6 sm:flex-row sm:items-start">
						<div class="flex h-28 w-28 items-center justify-center rounded-lg border border-gray-200 bg-gray-50">
							<img
								v-if="company.logo"
								:src="company.logo"
								alt="logo"
								class="h-full w-full rounded-lg object-cover"
							/>
							<span v-else class="text-sm text-gray-400">No logo</span>
						</div>
						<div class="grid flex-1 gap-4 sm:grid-cols-2">
							<div>
								<p class="text-xs font-semibold uppercase text-gray-500">Name</p>
								<p class="text-sm text-gray-900 dark:text-gray-100">{{ company.name }}</p>
							</div>
							<div>
								<p class="text-xs font-semibold uppercase text-gray-500">Email</p>
								<p class="text-sm text-gray-900 dark:text-gray-100">{{ company.email || '—' }}</p>
							</div>
							<div>
								<p class="text-xs font-semibold uppercase text-gray-500">Website</p>
								<a
									v-if="company.website"
									:href="company.website"
									class="text-sm text-indigo-200 hover:text-indigo-600"
									target="_blank"
									rel="noreferrer"
								>
									{{ company.website }}
								</a>
								<span v-else class="text-sm text-gray-400">—</span>
							</div>
							<div>
								<p class="text-xs font-semibold uppercase text-gray-500">ID</p>
								<p class="text-sm text-gray-900 dark:text-gray-100">{{ company.id }}</p>
							</div>
						</div>
					</div>
					<div class="mt-6">
						<Link href="/company" class="text-sm text-indigo-200 hover:text-indigo-600">
							Back to Companies
						</Link>
					</div>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
