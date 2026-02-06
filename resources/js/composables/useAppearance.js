import { ref, watch } from 'vue';

const STORAGE_KEY = 'psrefer_appearance';
const DEFAULT_APPEARANCE = 'light';

const appearance = ref(DEFAULT_APPEARANCE);

const applyAppearance = (value) => {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.classList.toggle('dark', value === 'dark');
};

const loadAppearance = () => {
    if (typeof window === 'undefined') {
        return DEFAULT_APPEARANCE;
    }

    const stored = window.localStorage.getItem(STORAGE_KEY);
    return stored || DEFAULT_APPEARANCE;
};

appearance.value = loadAppearance();
applyAppearance(appearance.value);

watch(appearance, (value) => {
    if (typeof window !== 'undefined') {
        window.localStorage.setItem(STORAGE_KEY, value);
    }
    applyAppearance(value);
});

export function useAppearance() {
    const setAppearance = (value) => {
        appearance.value = value;
    };

    return {
        appearance,
        setAppearance,
        options: [
            { value: 'light', label: 'Claro' },
            { value: 'dark', label: 'Oscuro' },
        ],
    };
}