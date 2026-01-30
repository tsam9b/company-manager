import { computed, reactive } from 'vue';

const state = reactive({
	toasts: [],
});

const toasts = computed(() => state.toasts);

let counter = 0;

const removeToast = (id) => {
	state.toasts = state.toasts.filter((toast) => toast.id !== id);
};

const addToast = (message, type = 'info', timeout = 3000) => {
	const id = `${Date.now()}-${counter++}`;
	state.toasts = [
		...state.toasts,
		{
			id,
			message,
			type,
		},
	];

	if (timeout > 0) {
		setTimeout(() => removeToast(id), timeout);
	}

	return id;
};

const success = (message, timeout) => addToast(message, 'success', timeout);
const error = (message, timeout) => addToast(message, 'error', timeout);
const info = (message, timeout) => addToast(message, 'info', timeout);

export function useToast() {
	return {
		toasts,
		addToast,
		removeToast,
		success,
		error,
		info,
	};
}
