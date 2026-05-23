<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const GOOGLE_TRANSLATE_SRC = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';

const showDropdown = ref(false);
const currentLanguage = ref('English');
const containerRef = ref(null);

const languages = [
    { code: 'en', name: 'English' },
    { code: 'ar', name: 'Arabic' },
    { code: 'zh-CN', name: 'Chinese (Simplified)' },
    { code: 'zh-TW', name: 'Chinese (Traditional)' },
    { code: 'fr', name: 'French' },
    { code: 'de', name: 'German' },
    { code: 'hi', name: 'Hindi' },
    { code: 'id', name: 'Indonesian' },
    { code: 'it', name: 'Italian' },
    { code: 'ja', name: 'Japanese' },
    { code: 'ko', name: 'Korean' },
    { code: 'pt', name: 'Portuguese' },
    { code: 'ru', name: 'Russian' },
    { code: 'es', name: 'Spanish' },
    { code: 'th', name: 'Thai' },
    { code: 'tr', name: 'Turkish' },
    { code: 'vi', name: 'Vietnamese' },
];

let translateLoader = null;

const updateCurrentLanguageFromCookie = () => {
    const cookie = document.cookie.match(/(?:^|; )googtrans=([^;]*)/);
    const langCode = cookie?.[1]?.split('/').pop() ?? 'en';
    const language = languages.find((item) => item.code === langCode);
    currentLanguage.value = language?.name ?? 'English';
};

const initGoogleTranslate = () => {
    const element = document.getElementById('google_translate_element');
    if (!element || !window.google?.translate) {
        return;
    }

    if (document.querySelector('.goog-te-combo')) {
        return;
    }

    element.innerHTML = '';

    try {
        new window.google.translate.TranslateElement(
            {
                pageLanguage: 'en',
                includedLanguages: languages
                    .filter((language) => language.code !== 'en')
                    .map((language) => language.code)
                    .join(','),
                layout: window.google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false,
            },
            'google_translate_element',
        );
    } catch (error) {
        console.warn('Google Translate initialization error:', error);
    }
};

const ensureGoogleTranslateLoaded = async () => {
    if (window.google?.translate) {
        initGoogleTranslate();
        return;
    }

    if (!translateLoader) {
        translateLoader = new Promise((resolve, reject) => {
            window.googleTranslateElementInit = () => {
                initGoogleTranslate();
                resolve();
            };

            const script = document.createElement('script');
            script.src = GOOGLE_TRANSLATE_SRC;
            script.async = true;
            script.defer = true;
            script.onerror = () => {
                translateLoader = null;
                reject(new Error('Failed to load Google Translate.'));
            };

            document.head.appendChild(script);
        }).catch((error) => {
            console.warn(error.message);
        });
    }

    await translateLoader;
};

const toggleDropdown = async () => {
    showDropdown.value = !showDropdown.value;

    if (showDropdown.value) {
        await ensureGoogleTranslateLoaded();
    }
};

const selectLanguage = async (langCode) => {
    showDropdown.value = false;

    const language = languages.find((item) => item.code === langCode);
    if (language) {
        currentLanguage.value = language.name;
    }

    await ensureGoogleTranslateLoaded();

    const select = document.querySelector('.goog-te-combo');
    if (select?.options) {
        for (let index = 0; index < select.options.length; index += 1) {
            const option = select.options[index];

            if (option.value === langCode || option.value === `/en/${langCode}`) {
                select.selectedIndex = index;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
        }
    }

    const expires = new Date();
    expires.setTime(expires.getTime() + 365 * 24 * 60 * 60 * 1000);

    if (langCode === 'en') {
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    } else {
        document.cookie = `googtrans=/en/${langCode};expires=${expires.toUTCString()};path=/`;
    }

    window.location.reload();
};

const resetLanguage = () => {
    selectLanguage('en');
};

const handleClickOutside = (event) => {
    if (showDropdown.value && containerRef.value && !containerRef.value.contains(event.target)) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    updateCurrentLanguageFromCookie();
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="containerRef" class="relative">
        <div id="google_translate_element" style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;"></div>

        <div class="relative">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                :class="{ 'ring-2 ring-portal-navy': showDropdown }"
                @click.stop="toggleDropdown"
            >
                <svg
                    class="h-4 w-4 text-slate-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"
                    />
                </svg>
                <span class="hidden md:inline">{{ currentLanguage }}</span>
                <span class="md:hidden">Lang</span>
                <svg
                    class="h-4 w-4 text-slate-400 transition-transform"
                    :class="{ 'rotate-180': showDropdown }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </button>

            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="showDropdown"
                    class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <div class="max-h-64 overflow-y-auto py-1">
                        <button
                            v-for="language in languages"
                            :key="language.code"
                            type="button"
                            class="flex w-full items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"
                            :class="{ 'bg-slate-50 font-medium': currentLanguage === language.name }"
                            @click.stop="selectLanguage(language.code)"
                        >
                            <span>{{ language.name }}</span>
                        </button>
                    </div>
                    <div class="border-t border-slate-200 px-4 py-2">
                        <button
                            type="button"
                            class="w-full text-left text-xs text-slate-500 hover:text-slate-700"
                            @click.stop="resetLanguage"
                        >
                            Reset to English
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style>
.goog-te-banner-frame,
.goog-te-menu-value,
.goog-te-menu-frame {
    display: none !important;
}

body {
    top: 0 !important;
}

#google_translate_element select.goog-te-combo {
    display: none;
}
</style>
