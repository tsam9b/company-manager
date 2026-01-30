<script setup>
import { useToast } from '@/Composables/useToast';

const { toasts, removeToast } = useToast();

const typeClasses = {
	success: 'bg-emerald-600 text-white',
	error: 'bg-red-600 text-white',
	info: 'bg-slate-800 text-white',
};
</script>

<template>
	<div class="pointer-events-none fixed right-4 top-4 z-50 flex w-full max-w-sm flex-col gap-2">
		<div
			v-for="toast in toasts"
			:key="toast.id"
			class="pointer-events-auto flex items-start justify-between gap-3 rounded-lg px-4 py-3 text-sm shadow-lg"
			:class="typeClasses[toast.type] || typeClasses.info"
		>
			<span>{{ toast.message }}</span>
			<button
				type="button"
				class="text-white/80 hover:text-white"
				@click="removeToast(toast.id)"
			>
				×
			</button>
		</div>
	</div>
</template>
