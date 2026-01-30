import { createI18n } from 'vue-i18n';
import en from '../../lang/en.json';
import el from '../../lang/el.json';

const messages = { en, el };

const savedLocale = localStorage.getItem('locale') || 'en';

export const i18n = createI18n({
	legacy: false,
	locale: savedLocale,
	fallbackLocale: 'en',
	messages,
});
