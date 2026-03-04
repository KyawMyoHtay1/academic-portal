<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const showDropdown = ref(false);
const currentLanguage = ref('English');
const containerRef = ref(null);

const languages = [
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'ar', name: 'العربية', flag: '🇸🇦' },
    { code: 'zh-CN', name: '中文 (简体)', flag: '🇨🇳' },
    { code: 'zh-TW', name: '中文 (繁體)', flag: '🇹🇼' },
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
    { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
    { code: 'hi', name: 'हिन्दी', flag: '🇮🇳' },
    { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩' },
    { code: 'it', name: 'Italiano', flag: '🇮🇹' },
    { code: 'ja', name: '日本語', flag: '🇯🇵' },
    { code: 'ko', name: '한국어', flag: '🇰🇷' },
    { code: 'pt', name: 'Português', flag: '🇵🇹' },
    { code: 'ru', name: 'Русский', flag: '🇷🇺' },
    { code: 'es', name: 'Español', flag: '🇪🇸' },
    { code: 'th', name: 'ไทย', flag: '🇹🇭' },
    { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
    { code: 'vi', name: 'Tiếng Việt', flag: '🇻🇳' },
];

onMounted(() => {
    // Initialize Google Translate if not already done
    const initGoogleTranslate = () => {
        const element = document.getElementById('google_translate_element');
        if (!element) return;

        // Check if already initialized
        if (document.querySelector('.goog-te-combo')) {
            return;
        }

        // Wait for Google Translate API to be available
        if (window.google && window.google.translate) {
            try {
                new google.translate.TranslateElement({
                    pageLanguage: 'en',
                    includedLanguages: 'ar,zh-CN,zh-TW,fr,de,hi,id,it,ja,ko,pt,ru,es,th,tr,vi',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false
                }, 'google_translate_element');
            } catch (e) {
                console.warn('Google Translate initialization error:', e);
            }
        }
    };

    // Try to initialize immediately
    if (window.googleTranslateElementInit) {
        window.googleTranslateElementInit();
    }

    // Also try after delays (Google Translate loads asynchronously)
    setTimeout(initGoogleTranslate, 500);
    setTimeout(initGoogleTranslate, 1500);
    setTimeout(initGoogleTranslate, 3000);

    // Check if Google Translate has set a language
    const checkLanguage = () => {
        const cookie = document.cookie.match(/(?:^|; )googtrans=([^;]*)/);
        if (cookie) {
            const langCode = cookie[1].split('/').pop();
            const lang = languages.find(l => l.code === langCode);
            if (lang) {
                currentLanguage.value = lang.name;
            }
        }
    };

    // Check immediately and periodically
    checkLanguage();
    setInterval(checkLanguage, 1000);
});

const selectLanguage = (langCode) => {
    showDropdown.value = false;
    
    // Update current language display immediately
    const lang = languages.find(l => l.code === langCode);
    if (lang) {
        currentLanguage.value = lang.name;
    }
    
    // Method 1: Try using the select element (preferred)
    const select = document.querySelector('.goog-te-combo');
    if (select && select.options) {
        // Find the option with matching value
        for (let i = 0; i < select.options.length; i++) {
            const option = select.options[i];
            if (option.value === langCode || option.value === `/en/${langCode}`) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
        }
    }

    // Method 2: Direct cookie manipulation (fallback - requires page reload)
    const setCookie = (name, value, days = 365) => {
        const expires = new Date();
        expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    };

    if (langCode === 'en') {
        // Remove translation cookie to reset to English
        document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    } else {
        // Set translation cookie
        setCookie('googtrans', `/en/${langCode}`);
    }
    
    // Reload page to apply translation
    window.location.reload();
};

const resetLanguage = () => {
    selectLanguage('en');
};

// Handle clicks outside dropdown
const handleClickOutside = (event) => {
    if (showDropdown.value && containerRef.value && !containerRef.value.contains(event.target)) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="containerRef" class="relative">
        <!-- Google Translate Element (hidden visually but present in DOM) -->
        <div id="google_translate_element" style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;"></div>

        <!-- Custom Language Selector -->
        <div class="relative">
            <button
                @click.stop="showDropdown = !showDropdown"
                type="button"
                class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-portal-navy focus:ring-offset-2"
                :class="{ 'ring-2 ring-portal-navy': showDropdown }"
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
                <span class="md:hidden">🌐</span>
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

            <!-- Dropdown Menu -->
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
                            v-for="lang in languages"
                            :key="lang.code"
                            @click.stop="selectLanguage(lang.code)"
                            type="button"
                            class="flex w-full items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"
                            :class="{
                                'bg-slate-50 font-medium': currentLanguage === lang.name
                            }"
                        >
                            <span class="text-lg">{{ lang.flag }}</span>
                            <span>{{ lang.name }}</span>
                        </button>
                    </div>
                    <div class="border-t border-slate-200 px-4 py-2">
                        <button
                            @click.stop="resetLanguage"
                            type="button"
                            class="w-full text-left text-xs text-slate-500 hover:text-slate-700"
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
/* Hide Google Translate's default UI */
.goog-te-banner-frame,
.goog-te-menu-value,
.goog-te-menu-frame {
    display: none !important;
}

body {
    top: 0 !important;
}

/* Style the Google Translate select that appears in the hidden element */
#google_translate_element select.goog-te-combo {
    display: none;
}
</style>
