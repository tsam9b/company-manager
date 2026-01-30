import { computed, onMounted, ref } from 'vue';

export function usePaginatedList({
	endpoint,
	defaultPerPage = 10,
	initialPage = 1,
	initialSortBy = null,
	initialSortDir = 'asc',
} = {}) {
	const items = ref([]);
	const pagination = ref(null);
	const loading = ref(false);
	const sortBy = ref(initialSortBy);
	const sortDir = ref(initialSortDir);

	const rows = computed(() => pagination.value?.data ?? items.value);

	const getQueryParams = () => {
		const searchParams = new URLSearchParams(window.location.search);
		return {
			page: Number(searchParams.get('page')) || initialPage,
			per_page: Number(searchParams.get('per_page')) || defaultPerPage,
			sort_by: searchParams.get('sort_by') || sortBy.value,
			sort_dir: searchParams.get('sort_dir') || sortDir.value,
		};
	};

	const updateQuery = (params) => {
		const searchParams = new URLSearchParams(window.location.search);
		Object.entries(params).forEach(([key, value]) => {
			if (value === null || value === undefined) {
				searchParams.delete(key);
			} else {
				searchParams.set(key, value);
			}
		});

		const newUrl = `${window.location.pathname}?${searchParams.toString()}`;
		window.history.replaceState({}, '', newUrl);
	};

	const fetchList = async () => {
		if (!endpoint) return;
		loading.value = true;
		try {
			const params = getQueryParams();
			const response = await window.axios.get(endpoint, { params });
			pagination.value = response.data;
			items.value = response.data?.data ?? [];
		} finally {
			loading.value = false;
		}
	};

	const handlePageChange = (page) => {
		updateQuery({ page });
		fetchList();
	};

	const handlePerPageChange = (perPage) => {
		updateQuery({ per_page: perPage, page: 1 });
		fetchList();
	};

	const handleSortChange = ({ key, direction }) => {
		sortBy.value = key;
		sortDir.value = direction;
		updateQuery({ sort_by: key, sort_dir: direction, page: 1 });
		fetchList();
	};

	onMounted(fetchList);

	return {
		items,
		pagination,
		loading,
		rows,
		sortBy,
		sortDir,
		fetchList,
		handlePageChange,
		handlePerPageChange,
		handleSortChange,
	};
}
