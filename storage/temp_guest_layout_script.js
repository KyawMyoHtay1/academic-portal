
        // Reading progress indicator for long guest pages.
        document.addEventListener('DOMContentLoaded', function () {
            var progressBar = document.getElementById('guest-scroll-progress');
            if (!progressBar) return;

            function updateProgress() {
                var doc = document.documentElement;
                var scrollTop = doc.scrollTop || document.body.scrollTop;
                var scrollHeight = Math.max(1, doc.scrollHeight - doc.clientHeight);
                var percent = Math.min(100, Math.max(0, (scrollTop / scrollHeight) * 100));
                progressBar.style.width = percent + '%';
            }

            updateProgress();
            window.addEventListener('scroll', updateProgress, { passive: true });
            window.addEventListener('resize', updateProgress);
        });

        // Top-right loading status for guest Blade pages.
        document.addEventListener('DOMContentLoaded', function () {
            var pageLoading = document.getElementById('guest-page-loading');
            if (!pageLoading) return;

            var showTimer = null;
            var isVisible = false;
            var SHOW_DELAY_MS = 120;

            function showLoading() {
                if (isVisible) return;
                if (showTimer) clearTimeout(showTimer);
                showTimer = setTimeout(function () {
                    pageLoading.classList.add('is-visible');
                    isVisible = true;
                }, SHOW_DELAY_MS);
            }

            function hideLoading() {
                if (showTimer) {
                    clearTimeout(showTimer);
                    showTimer = null;
                }
                isVisible = false;
                pageLoading.classList.remove('is-visible');
            }

            function isInternalNavigationLink(link) {
                if (!link) return false;
                if (link.hasAttribute('download')) return false;

                var target = (link.getAttribute('target') || '').toLowerCase();
                if (target === '_blank') return false;

                var href = link.getAttribute('href') || '';
                if (!href || href.charAt(0) === '#') return false;
                if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0 || href.indexOf('javascript:') === 0) {
                    return false;
                }

                var url;
                try {
                    url = new URL(href, window.location.href);
                } catch (e) {
                    return false;
                }

                if (url.origin !== window.location.origin) return false;
                if (url.href === window.location.href) return false;

                return true;
            }

            document.addEventListener('click', function (event) {
                if (event.defaultPrevented) return;
                if (event.button !== 0) return;
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

                var link = event.target.closest('a[href]');
                if (!isInternalNavigationLink(link)) return;

                showLoading();
            }, true);

            document.addEventListener('submit', function (event) {
                if (event.defaultPrevented) return;
                var form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                var method = (form.getAttribute('method') || 'get').toLowerCase();
                var target = (form.getAttribute('target') || '').toLowerCase();
                if (target === '_blank') return;
                if (method !== 'get' && method !== 'post') return;

                showLoading();
            }, true);

            window.addEventListener('beforeunload', showLoading);
            window.addEventListener('pageshow', hideLoading);
        });

        // Auto-dismiss and close handlers for guest flash toasts.
        document.addEventListener('DOMContentLoaded', function () {
            var toastNodes = document.querySelectorAll('[data-guest-toast]');
            if (!toastNodes.length) return;

            toastNodes.forEach(function (toastNode) {
                var timeoutMs = parseInt(toastNode.getAttribute('data-timeout') || '5000', 10);
                var closeButton = toastNode.querySelector('[data-guest-toast-close]');

                function dismissToast() {
                    if (!toastNode || !toastNode.parentNode) return;
                    toastNode.parentNode.removeChild(toastNode);
                }

                if (closeButton) {
                    closeButton.addEventListener('click', dismissToast);
                }

                window.setTimeout(dismissToast, timeoutMs);
            });
        });

        // Guest search: search everything (courses, news, pages)
        document.addEventListener('DOMContentLoaded', function () {
            var guestSearchWrapper = document.getElementById('guest-search-wrapper');
            if (!guestSearchWrapper) return;
            var searchUrl = guestSearchWrapper.getAttribute('data-search-url');
            var input = document.getElementById('guest-search-input');
            var dropdown = document.getElementById('guest-search-dropdown');
            var minLen = 2;
            var debounceMs = 300;
            var debounceTimer = null;
            var results = [];
            var highlightIndex = -1;
            var typeLabels = { course: 'Course', announcement: 'News', page: 'Page' };

            function fetchResults() {
                var q = (input.value || '').trim();
                if (q.length < minLen) {
                    results = [];
                    dropdown.classList.add('hidden');
                    dropdown.innerHTML = '';
                    return;
                }
                dropdown.classList.remove('hidden');
                dropdown.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500">Searching...</div>';
                fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                    method: 'GET',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        results = data.results || [];
                        highlightIndex = -1;
                        renderResults(q);
                    })
                    .catch(function () {
                        results = [];
                        renderResults(q);
                    });
            }

            function renderResults(q) {
                if (results.length === 0) {
                    dropdown.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500">No results found for "' + (q || '').replace(/</g, '&lt;') + '"</div>';
                    return;
                }
                var html = '<div class="space-y-1" role="listbox">';
                results.forEach(function (r, i) {
                    var typeLabel = typeLabels[r.type] || r.type;
                    var subtitle = r.subtitle ? '<span class="truncate text-xs text-slate-500">' + (r.subtitle || '').replace(/</g, '&lt;') + '</span>' : '';
                    var bg = i === highlightIndex ? ' bg-[color:var(--portal-navy)]/5' : '';
                    html += '<button type="button" class="guest-search-result flex w-full flex-col items-start gap-0.5 px-4 py-2.5 text-left transition hover:bg-slate-50' + bg + '" data-index="' + i + '" data-url="' + (r.url || '').replace(/"/g, '&quot;') + '">';
                    html += '<div class="flex w-full items-center justify-between gap-2"><span class="truncate text-sm font-medium text-slate-900">' + (r.title || '').replace(/</g, '&lt;') + '</span>';
                    html += '<span class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">' + typeLabel + '</span></div>';
                    if (subtitle) html += subtitle;
                    html += '</button>';
                });
                html += '</div>';
                dropdown.innerHTML = html;
                dropdown.querySelectorAll('.guest-search-result').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var url = btn.getAttribute('data-url');
                        if (url) window.location.href = url;
                    });
                });
            }

            function openDropdown() {
                var q = (input.value || '').trim();
                if (q.length >= minLen && results.length > 0) {
                    dropdown.classList.remove('hidden');
                    renderResults(q);
                } else if (q.length >= minLen) {
                    fetchResults();
                }
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                highlightIndex = -1;
            }

            function selectHighlighted() {
                if (highlightIndex >= 0 && results[highlightIndex] && results[highlightIndex].url) {
                    window.location.href = results[highlightIndex].url;
                }
            }

            input.addEventListener('input', function () {
                if (debounceTimer) clearTimeout(debounceTimer);
                var q = (input.value || '').trim();
                if (q.length < minLen) {
                    results = [];
                    dropdown.classList.add('hidden');
                    dropdown.innerHTML = '';
                    return;
                }
                debounceTimer = setTimeout(fetchResults, debounceMs);
            });

            input.addEventListener('focus', openDropdown);
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDropdown();
                    input.blur();
                    return;
                }
                if (!dropdown.classList.contains('hidden') && results.length > 0) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        highlightIndex = Math.min(highlightIndex + 1, results.length - 1);
                        renderResults((input.value || '').trim());
                        return;
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        highlightIndex = Math.max(highlightIndex - 1, 0);
                        renderResults((input.value || '').trim());
                        return;
                    }
                    if (e.key === 'Enter' && highlightIndex >= 0) {
                        e.preventDefault();
                        selectHighlighted();
                    }
                }
            });

            document.addEventListener('click', function (e) {
                if (guestSearchWrapper && !guestSearchWrapper.contains(e.target)) closeDropdown();
            });

            document.addEventListener('keydown', function (e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    input.focus();
                    openDropdown();
                }
            });
        });

        // Lightweight, reusable slider for guest pages
        document.addEventListener('DOMContentLoaded', function () {
            const sliders = document.querySelectorAll('[data-portal-slider]');
            sliders.forEach((slider) => {
                const slides = Array.from(slider.querySelectorAll('[data-portal-slide]'));
                if (slides.length === 0) return;

                let current = 0;
                const interval = parseInt(slider.dataset.interval || '7000', 10);
                const autoplay = slider.dataset.autoplay === 'true';
                let timer = null;

                const dots = Array.from(slider.querySelectorAll('[data-portal-slider-dot]'));

                function goTo(index) {
                    current = (index + slides.length) % slides.length;
                    slides.forEach((s, i) => {
                        if (i === current) {
                            s.classList.add('is-active');
                        } else {
                            s.classList.remove('is-active');
                        }
                    });
                    dots.forEach((d, i) => {
                        if (i === current) {
                            d.classList.add('is-active');
                        } else {
                            d.classList.remove('is-active');
                        }
                    });
                }

                function next() {
                    goTo(current + 1);
                }

                function prev() {
                    goTo(current - 1);
                }

                const nextBtn = slider.querySelector('[data-portal-slider-next]');
                const prevBtn = slider.querySelector('[data-portal-slider-prev]');

                if (nextBtn) nextBtn.addEventListener('click', () => { next(); restart(); });
                if (prevBtn) prevBtn.addEventListener('click', () => { prev(); restart(); });
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        goTo(index);
                        restart();
                    });
                });

                function start() {
                    if (!autoplay || interval <= 0) return;
                    timer = window.setInterval(next, interval);
                }

                function stop() {
                    if (timer) {
                        window.clearInterval(timer);
                        timer = null;
                    }
                }

                function restart() {
                    stop();
                    start();
                }

                slider.addEventListener('mouseenter', stop);
                slider.addEventListener('mouseleave', start);

                goTo(0);
                start();
            });
        });

        // Reveal major content blocks with a small stagger on scroll.
        document.addEventListener('DOMContentLoaded', function () {
            const revealTargets = Array.from(
                document.querySelectorAll('main section, main article, main .portal-card, main [data-guest-reveal]'),
            );

            revealTargets.forEach((target, index) => {
                target.classList.add('guest-reveal');
                target.style.transitionDelay = `${Math.min(index * 35, 320)}ms`;
            });

            if (!('IntersectionObserver' in window)) {
                revealTargets.forEach((target) => target.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
            );

            revealTargets.forEach((target) => observer.observe(target));
        });

        // Google Translate Functions
        function toggleTranslateDropdown() {
            const dropdown = document.getElementById('translate-dropdown');
            dropdown.classList.toggle('hidden');
        }

        function changeLanguage(langCode) {
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                // Find the option with matching value
                for (let i = 0; i < select.options.length; i++) {
                    const option = select.options[i];
                    if (option.value === langCode || option.value === `/en/${langCode}`) {
                        select.selectedIndex = i;
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                        document.getElementById('translate-dropdown').classList.add('hidden');
                        updateCurrentLanguage(langCode);
                        return;
                    }
                }
            }

            // Fallback: Set cookie and reload
            if (langCode === 'en') {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            } else {
                const expires = new Date();
                expires.setTime(expires.getTime() + 365 * 24 * 60 * 60 * 1000);
                document.cookie = `googtrans=/en/${langCode};expires=${expires.toUTCString()};path=/`;
            }
            window.location.reload();
        }

        function updateCurrentLanguage(langCode) {
            const langNames = {
                'en': 'English',
                'ar': 'العربية',
                'zh-CN': '中文 (简体)',
                'zh-TW': '中文 (繁體)',
                'fr': 'Français',
                'de': 'Deutsch',
                'hi': 'हिन्दी',
                'id': 'Bahasa Indonesia',
                'it': 'Italiano',
                'ja': '日本語',
                'ko': '한국어',
                'pt': 'Português',
                'ru': 'Русский',
                'es': 'Español',
                'th': 'ไทย',
                'tr': 'Türkçe',
                'vi': 'Tiếng Việt'
            };
            const langName = langNames[langCode] || 'Language';
            document.getElementById('current-lang').textContent = langName;
        }

        // Check current language on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cookie = document.cookie.match(/(?:^|; )googtrans=([^;]*)/);
            if (cookie) {
                const langCode = cookie[1].split('/').pop();
                updateCurrentLanguage(langCode);
            } else {
                updateCurrentLanguage('en');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const container = document.getElementById('google-translate-container');
                const dropdown = document.getElementById('translate-dropdown');
                if (container && dropdown && !container.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        // reCAPTCHA v3 for guest forms (contact/feedback).
        document.addEventListener('DOMContentLoaded', function () {
            var recaptchaSiteKey = "SITE_KEY";
            if (!recaptchaSiteKey) return;

            var forms = Array.prototype.slice.call(
                document.querySelectorAll('form[data-recaptcha-action]')
            );
            if (!forms.length) return;

            function hideLoadingOverlay() {
                var loadingNode = document.getElementById('guest-page-loading');
                if (loadingNode) {
                    loadingNode.classList.remove('is-visible');
                }
            }

            function clearRecaptchaError(form) {
                var errorNode = form.querySelector('[data-recaptcha-client-error]');
                if (errorNode) {
                    errorNode.remove();
                }
            }

            function showRecaptchaError(form, message) {
                clearRecaptchaError(form);
                var errorNode = document.createElement('div');
                errorNode.setAttribute('data-recaptcha-client-error', '1');
                errorNode.className = 'rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800';
                errorNode.textContent = message;
                form.prepend(errorNode);
            }

            function setSubmitButtonsState(form, isBusy) {
                var submitButtons = form.querySelectorAll(
                    'button[type="submit"], input[type="submit"]'
                );
                submitButtons.forEach(function (button) {
                    if (isBusy) {
                        button.setAttribute('data-recaptcha-was-disabled', button.disabled ? '1' : '0');
                        button.disabled = true;
                    } else {
                        if (button.getAttribute('data-recaptcha-was-disabled') !== '1') {
                            button.disabled = false;
                        }
                        button.removeAttribute('data-recaptcha-was-disabled');
                    }
                });
            }

            forms.forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.getAttribute('data-recaptcha-submitting') === '1') {
                        return;
                    }

                    var tokenInput = form.querySelector('input[name="recaptcha_token"]');
                    if (!tokenInput) {
                        return;
                    }

                    event.preventDefault();
                    clearRecaptchaError(form);
                    setSubmitButtonsState(form, true);

                    var action = form.getAttribute('data-recaptcha-action') || 'submit';

                    function fail(message) {
                        tokenInput.value = '';
                        setSubmitButtonsState(form, false);
                        hideLoadingOverlay();
                        showRecaptchaError(
                            form,
                            message || 'reCAPTCHA verification failed. Please refresh and try again.'
                        );
                    }

                    if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
                        fail('reCAPTCHA failed to load. Please refresh the page and try again.');
                        return;
                    }

                    window.grecaptcha.ready(function () {
                        window.grecaptcha
                            .execute(recaptchaSiteKey, { action: action })
                            .then(function (token) {
                                if (!token) {
                                    fail('reCAPTCHA verification failed. Please try again.');
                                    return;
                                }

                                tokenInput.value = token;
                                form.setAttribute('data-recaptcha-submitting', '1');
                                form.submit();
                            })
                            .catch(function () {
                                fail('reCAPTCHA verification failed. Please try again.');
                            });
                    });
                });
            });
        });
    
