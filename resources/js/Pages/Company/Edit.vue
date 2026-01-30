<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useToast } from '@/Composables/useToast';
import { useI18n } from 'vue-i18n';

const props = defineProps({
	company: {
		type: Object,
		required: true,
	},
});

const form = useForm({
	name: props.company.name ?? '',
	email: props.company.email ?? '',
	logo: null,
	website: props.company.website ?? '',
});

const { success, error: errorToast } = useToast();
const { t } = useI18n();

const isDragOver = ref(false);
const logoInput = ref(null);
const logoPreview = ref('');
const logoFile = ref(null);
const logoDisplay = computed(() => logoPreview.value || props.company.logo || '');
const MIN_LOGO_SIZE = 100;

const validateLogoFile = (file) => new Promise((resolve) => {
	const objectUrl = URL.createObjectURL(file);
	const img = new Image();
	img.onload = () => {
		const isValid = img.width >= MIN_LOGO_SIZE && img.height >= MIN_LOGO_SIZE;
		URL.revokeObjectURL(objectUrl);
		if (!isValid) {
			form.setError('logo', t('company.logoMinError'));
			logoFile.value = null;
			logoPreview.value = '';
			resolve(false);
			return;
		}
		resolve(true);
	};
	img.onerror = () => {
		URL.revokeObjectURL(objectUrl);
		form.setError('logo', t('company.logoInvalid'));
		logoFile.value = null;
		logoPreview.value = '';
		resolve(false);
	};
	img.src = objectUrl;
});

const handleLogoFiles = async (files) => {
	const file = files?.[0];
	if (!file) return;
	form.clearErrors('logo');
	const isValid = await validateLogoFile(file);
	if (!isValid) return;
	logoFile.value = file;
	const reader = new FileReader();
	reader.onload = () => {
		logoPreview.value = reader.result;
	};
	reader.readAsDataURL(file);
};

const onLogoChange = (event) => {
	void handleLogoFiles(event.target.files);
};

const onDrop = (event) => {
	event.preventDefault();
	isDragOver.value = false;
	void handleLogoFiles(event.dataTransfer.files);
};

const onDragOver = () => {
	isDragOver.value = true;
};

const onDragLeave = () => {
	isDragOver.value = false;
};

const openLogoPicker = () => {
	logoInput.value?.click();
};

const submit = async () => {
	if (form.errors.logo) {
		return;
	}
	form.clearErrors();
	try {
		const payload = new FormData();
		payload.append('name', form.name);
		payload.append('email', form.email || '');
		payload.append('website', form.website || '');
		if (logoFile.value) {
			payload.append('logo', logoFile.value, logoFile.value.name);
		}

		await window.axios.post(`/company/${props.company.id}`, payload, {
			params: { _method: 'PUT' },
		});
		success(t('company.updated'));
		router.visit(`/company/${props.company.id}`);
	} catch (error) {
		if (error?.response?.status === 422) {
			form.setError(error.response.data.errors || {});
			return;
		}
		errorToast(t('company.updateError'));
	}
};
</script>

<template>
	<Head :title="t('company.editTitle')" />

	<AuthenticatedLayout>
		<template #header>
			<div class="flex flex-wrap items-center justify-between gap-4">
				<h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
					{{ t('company.editTitle') }}
				</h2>
				<Link
					href="/company"
					class="text-sm text-indigo-200 hover:text-indigo-400"
				>
					{{ t('buttons.backToCompanies') }}
				</Link>
			</div>
		</template>

		<div class="py-12">
			<div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
				<div class="bg-white p-6 shadow sm:rounded-lg dark:bg-gray-800">
					<form class="space-y-6" novalidate @submit.prevent="submit">
						<div>
							<InputLabel for="name" :value="t('company.name')" />
							<TextInput
								id="name"
								type="text"
								class="mt-1 block w-full"
								v-model="form.name"
								autofocus
								autocomplete="organization"
							/>
							<InputError class="mt-2" :message="form.errors.name" />
						</div>

						<div class="grid gap-6 md:grid-cols-2">
							<div>
								<InputLabel for="email" :value="t('company.email')" />
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
								<InputLabel for="website" :value="t('company.website')" />
								<TextInput
									id="website"
									type="url"
									class="mt-1 block w-full"
									v-model="form.website"
									autocomplete="url"
								/>
								<InputError class="mt-2" :message="form.errors.website" />
							</div>
						</div>

						<div>
							<InputLabel for="logo" :value="t('company.logo')" />
							<div
								class="mt-1 flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-4 py-4 text-center transition"
								:class="isDragOver ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
								@dragover.prevent="onDragOver"
								@dragleave.prevent="onDragLeave"
								@drop="onDrop"
								@click="openLogoPicker"
							>
								<input
									id="logo"
									type="file"
									accept="image/*"
									class="hidden"
									ref="logoInput"
									@change="onLogoChange"
								/>
								<div class="flex w-full flex-wrap items-center justify-between gap-3">
									<div class="space-y-1 text-left">
										<p class="text-sm font-medium text-gray-700">{{ t('company.logo') }}</p>
										<p class="text-xs text-gray-500">{{ t('company.logoHint') }}</p>
										<p class="text-xs text-gray-400">{{ t('company.logoHelp') }}</p>
									</div>
									<div class="flex h-16 w-24 items-center justify-center rounded border border-gray-200 bg-gray-50">
										<img
											v-if="logoDisplay"
											:src="logoDisplay"
											alt="Logo preview"
											class="h-full w-full rounded object-cover"
										/>
										<span v-else class="text-xs text-gray-400">{{ t('company.logoPreview') }}</span>
									</div>
								</div>
							</div>
							<InputError class="mt-2" :message="form.errors.logo" />
						</div>

						<div class="flex items-center justify-end">
							<PrimaryButton :disabled="form.processing">
								{{ t('buttons.updateCompany') }}
							</PrimaryButton>
						</div>
					</form>
				</div>
			</div>
		</div>
	</AuthenticatedLayout>
</template>
