<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { usePaginatedList } from '@/Composables/usePaginatedList';
import { useI18n } from 'vue-i18n';
import { useToast } from '@/Composables/useToast';

const { t } = useI18n();

const columns = computed(() => [
	{ key: 'name', label: t('company.name'), sortable: true },
	{ key: 'email', label: t('company.email'), sortable: true },
	{ key: 'logo', label: t('company.logo') },
	{ key: 'website', label: t('company.website'), sortable: true },
]);

const {
	pagination,
	loading,
	rows,
	sortBy,
	sortDir,
	fetchList,
	handlePageChange,
	handlePerPageChange,
	handleSortChange,
} = usePaginatedList({ endpoint: '/company/list', defaultPerPage: 10 });

const { success, error: errorToast } = useToast();

const showDeleteModal = ref(false);
const deleteTarget = ref(null);

const handleView = (row) => {
	router.visit(`/company/${row.id}`);
};

const handleEdit = (row) => {
	router.visit(`/company/${row.id}/edit`);
};

const openDeleteModal = (row) => {
	deleteTarget.value = row;
	showDeleteModal.value = true;
};

const closeDeleteModal = () => {
	showDeleteModal.value = false;
	deleteTarget.value = null;
};

const handleDelete = async () => {
	if (!deleteTarget.value) return;
	try {
		await window.axios.delete(`/company/${deleteTarget.value.id}`);
		await fetchList();
		success('Company deleted.');
		closeDeleteModal();
	} catch (error) {
		errorToast('Failed to delete company.');
	}
};
</script>

<template>
	<Head :title="t('company.title')" />

	<AuthenticatedLayout>
		<template #header>
			<h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
				{{ t('company.title') }}
			</h2>
		</template>

		<div class="py-12">
			<div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
				<div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
					<div class="mb-4 flex items-center justify-end">
						<Link
							href="/company/create"
							class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
						>
							{{ t('buttons.addCompany') }}
						</Link>
					</div>
                    <DataTable
						:columns="columns"
						:rows="rows"
						:pagination="pagination"
						:loading="loading"
						:sort-key="sortBy"
						:sort-dir="sortDir"
						:empty-text="t('table.empty')"
						@page-change="handlePageChange"
						@per-page-change="handlePerPageChange"
						@sort-change="handleSortChange"
					>
						<template #actions="{ row }">
							<div class="flex items-center justify-end gap-2">
								<button
									type="button"
									class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-indigo-600"
									:title="t('buttons.view')"
									@click.stop="handleView(row)"
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 24 24"
										fill="none"
										stroke="currentColor"
										stroke-width="1.5"
										class="h-5 w-5"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12 18 19.5 12 19.5 2.25 12 2.25 12Zm9.75 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"
										/>
									</svg>
								</button>
								<button
									type="button"
									class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-indigo-600"
									:title="t('buttons.edit')"
									@click.stop="handleEdit(row)"
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 24 24"
										fill="none"
										stroke="currentColor"
										stroke-width="1.5"
										class="h-5 w-5"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L9.832 16.62a4.5 4.5 0 0 1-1.897 1.13l-2.685.896.896-2.685a4.5 4.5 0 0 1 1.13-1.897L16.862 4.487Z"
										/>
										<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
									</svg>
								</button>
								<button
									type="button"
									class="rounded-md p-2 text-gray-500 hover:bg-red-50 hover:text-red-600"
									:title="t('buttons.delete')"
									@click.stop="openDeleteModal(row)"
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 24 24"
										fill="none"
										stroke="currentColor"
										stroke-width="1.5"
										class="h-5 w-5"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.11 48.11 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
										/>
									</svg>
								</button>
							</div>
						</template>
						<template #cell-name="{ row }">
							<Link
								:href="`/company/${row.id}/edit`"
								class="text-indigo-200 hover:text-indigo-600"
							>
								{{ row.name }}
							</Link>
						</template>
						<template #cell-logo="{ row }">
							<img
								v-if="row.logo"
								:src="row.logo"
								alt="logo"
								class="h-10 w-10 rounded border border-gray-200 object-cover"
							/>
							<span v-else class="text-sm text-gray-400">—</span>
						</template>
						<template #cell-website="{ row }">
							<a
								v-if="row.website"
								:href="row.website"
								class="text-sm text-indigo-200 hover:text-indigo-600"
								target="_blank"
								rel="noreferrer"
							>
								{{ row.website }}
							</a>
							<span v-else class="text-sm text-gray-400">—</span>
						</template>
					</DataTable>
				</div>
			</div>
		</div>

		<Modal :show="showDeleteModal" max-width="md" @close="closeDeleteModal">
			<div class="p-6">
				<h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
					{{ t('company.deleteTitle') }}
				</h2>
				<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
					{{
						t('company.deleteConfirm', {
							name: deleteTarget?.name ?? '',
						})
					}}
				</p>
				<div class="mt-6 flex justify-end gap-3">
					<SecondaryButton type="button" @click="closeDeleteModal">
						{{ t('buttons.cancel') }}
					</SecondaryButton>
					<DangerButton type="button" @click="handleDelete">
						{{ t('buttons.delete') }}
					</DangerButton>
				</div>
			</div>
		</Modal>
	</AuthenticatedLayout>
</template>
