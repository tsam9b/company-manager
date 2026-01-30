<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
	columns: {
		type: Array,
		required: true,
	},
	rows: {
		type: Array,
		required: true,
	},
	rowKey: {
		type: String,
		default: 'id',
	},
	loading: {
		type: Boolean,
		default: false,
	},
	emptyText: {
		type: String,
		default: 'No records found.',
	},
	pagination: {
		type: Object,
		default: null,
	},
	perPageOptions: {
		type: Array,
		default: () => [10, 15, 25, 50],
	},
	sortKey: {
		type: String,
		default: null,
	},
	sortDir: {
		type: String,
		default: 'asc',
	},
});

const emit = defineEmits(['page-change', 'per-page-change', 'row-click', 'sort-change']);
const { t } = useI18n();

const hasPagination = computed(() => Boolean(props.pagination));
const links = computed(() => {
	const rawLinks = props.pagination?.links || [];
	return rawLinks.filter((link) => {
		const label = typeof link.label === 'string' ? link.label.toLowerCase() : '';
		return label !== 'previous' && label !== 'next' && label !== '&laquo; previous' && label !== 'next &raquo;';
	});
});

const canGoPrev = computed(() => props.pagination?.current_page > 1);
const canGoNext = computed(
	() => props.pagination?.current_page < props.pagination?.last_page,
);

const displayFrom = computed(() => props.pagination?.from ?? 0);
const displayTo = computed(() => props.pagination?.to ?? 0);
const displayTotal = computed(() => props.pagination?.total ?? 0);

const handlePage = (page) => {
	if (!page || page === props.pagination?.current_page) return;
	emit('page-change', page);
};

const handleLink = (link) => {
	if (!link || link.active || !link.url) return;
	const page = Number(new URL(link.url).searchParams.get('page')) || 1;
	emit('page-change', page);
};

const handlePerPage = (event) => {
	const value = Number(event.target.value);
	if (!Number.isNaN(value)) {
		emit('per-page-change', value);
	}
};

const getCellValue = (row, column) => {
	if (typeof column.format === 'function') {
		return column.format(row);
	}
	return row?.[column.key] ?? '';
};

const handleSort = (column) => {
	if (!column.sortable) return;
	const isActive = props.sortKey === column.key;
	const nextDir = isActive && props.sortDir === 'asc' ? 'desc' : 'asc';
	emit('sort-change', { key: column.key, direction: nextDir });
};
</script>

<template>
	<div class="space-y-4 data-table-component">
		<div class="overflow-hidden rounded-lg border border-slate-700 bg-slate-900 shadow-sm">
			<table class="min-w-full divide-y divide-slate-700">
				<thead class="bg-slate-800">
					<tr>
						<th
							v-for="column in columns"
							:key="column.key"
							scope="col"
							class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-100"
						>
							<button
								v-if="column.sortable"
								type="button"
								class="inline-flex items-center gap-1 text-left text-xs font-semibold uppercase tracking-wide text-slate-100 hover:text-white"
								@click="handleSort(column)"
							>
								<span>{{ column.label }}</span>
								<span class="text-[10px]">
									<span v-if="sortKey === column.key">
										{{ sortDir === 'asc' ? '▲' : '▼' }}
									</span>
									<span v-else>⇅</span>
								</span>
							</button>
							<span v-else>{{ column.label }}</span>
						</th>
						<th v-if="$slots.actions" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-100">
							
						</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-slate-800">
					<tr v-if="loading">
						<td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-4 text-sm text-slate-200">
							{{ t('table.loading') }}
						</td>
					</tr>
					<tr v-else-if="rows.length === 0">
						<td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-4 text-sm text-slate-200">
							{{ emptyText || t('table.empty') }}
						</td>
					</tr>
					<tr
						v-else
						v-for="row in rows"
						:key="row[rowKey] ?? row"
						class="hover:bg-slate-800"
						@click="emit('row-click', row)"
					>
						<td
							v-for="column in columns"
							:key="column.key"
							class="px-6 py-4 text-sm text-left text-slate-100"
						>
							<slot :name="`cell-${column.key}`" :row="row">
								{{ getCellValue(row, column) }}
							</slot>
						</td>
						<td v-if="$slots.actions" class="px-6 py-4 text-right">
							<slot name="actions" :row="row" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div v-if="hasPagination" class="flex flex-col gap-3 text-slate-200 sm:flex-row sm:items-center sm:justify-between">
			<div class="text-sm text-slate-300">
				{{ t('table.showing', { from: displayFrom, to: displayTo, total: displayTotal }) }}
			</div>

			<div class="flex flex-wrap items-center gap-2">
				<label class="text-sm text-slate-300">
					{{ t('table.rowsPerPage') }}
					<select
						class="ml-2 rounded-md border-slate-600 bg-slate-800 text-sm text-slate-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
						:value="pagination?.per_page"
						@change="handlePerPage"
					>
						<option v-for="option in perPageOptions" :key="option" :value="option">
							{{ option }}
						</option>
					</select>
				</label>

				<div class="flex items-center gap-1">
					<button
						class="rounded-md border border-slate-600 px-3 py-1 text-sm text-slate-100 disabled:opacity-50"
						:disabled="!canGoPrev"
						@click="handlePage(pagination.current_page - 1)"
					>
						{{ t('table.prev') }}
					</button>
					<button
						v-for="link in links"
						:key="link.label + String(link.url)"
						class="rounded-md border border-slate-600 px-3 py-1 text-sm"
						:class="link.active ? 'bg-indigo-500 text-white' : 'text-slate-100'"
						:disabled="!link.url"
						v-html="link.label"
						@click="handleLink(link)"
					/>
					<button
						class="rounded-md border border-slate-600 px-3 py-1 text-sm text-slate-100 disabled:opacity-50"
						:disabled="!canGoNext"
						@click="handlePage(pagination.current_page + 1)"
					>
						{{ t('table.next') }}
					</button>
				</div>
			</div>
		</div>
	</div>
</template>
