import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useRecaptcha() {
    const isLoading = ref(false);
    const page = usePage();
    const siteKey = page.props.recaptchaSiteKey || null;

    /**
     * Execute reCAPTCHA and get token
     * @param {string} action - Action name (e.g., 'login', 'register')
     * @returns {Promise<string|null>} - reCAPTCHA token or null
     */
    const execute = async (action = 'submit') => {
        if (!siteKey || !window.grecaptcha) {
            console.warn('reCAPTCHA not loaded or site key not configured');
            return null;
        }

        isLoading.value = true;

        try {
            const token = await new Promise((resolve, reject) => {
                window.grecaptcha.ready(() => {
                    window.grecaptcha
                        .execute(siteKey, { action })
                        .then((token) => {
                            resolve(token);
                        })
                        .catch((error) => {
                            console.error('reCAPTCHA error:', error);
                            reject(error);
                        });
                });
            });

            return token;
        } catch (error) {
            console.error('reCAPTCHA execution failed:', error);
            return null;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        execute,
        isLoading,
        isAvailable: !!siteKey && !!window.grecaptcha,
    };
}
