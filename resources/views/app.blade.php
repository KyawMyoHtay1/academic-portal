<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @include('partials.favicon')

        <title inertia>{{ config('app.name', 'University Academic Portal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Google reCAPTCHA v3 -->
        @if(config('recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
        @endif

        <!-- Google Translate -->
        <script type="text/javascript">
            window.googleTranslateElementInit = function() {
                // Wait for the element to exist (Inertia.js loads content dynamically)
                const checkElement = setInterval(function() {
                    const element = document.getElementById('google_translate_element');
                    if (element && window.google && window.google.translate) {
                        clearInterval(checkElement);
                        new google.translate.TranslateElement({
                            pageLanguage: 'en',
                            includedLanguages: 'ar,zh-CN,zh-TW,fr,de,hi,id,it,ja,ko,pt,ru,es,th,tr,vi',
                            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                            autoDisplay: false
                        }, 'google_translate_element');
                    }
                }, 100);
                
                // Stop checking after 5 seconds
                setTimeout(function() {
                    clearInterval(checkElement);
                }, 5000);
            };
        </script>
        <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
