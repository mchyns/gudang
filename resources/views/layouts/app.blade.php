<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        $currentUser = Auth::user();
        $role = $currentUser?->role ?? 'guest';
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased app-shell role-{{ $role }}">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const nominalInputs = document.querySelectorAll('.js-nominal');
                if (!nominalInputs.length) return;

                const normalizeInitialValueToDigits = (rawValue) => {
                    const raw = String(rawValue || '').trim();
                    if (!raw) return '';

                    // 5.000 or 12.500.000 -> thousand separator format
                    if (/^\d{1,3}(\.\d{3})+$/.test(raw)) {
                        return raw.replace(/\./g, '');
                    }

                    // 5,000 or 12,500,000 -> thousand separator format
                    if (/^\d{1,3}(,\d{3})+$/.test(raw)) {
                        return raw.replace(/,/g, '');
                    }

                    // 5000.00 or 5000,00 -> decimal-like numeric
                    if (/^\d+[\.,]\d+$/.test(raw)) {
                        const asNumber = Number(raw.replace(',', '.'));
                        if (Number.isFinite(asNumber)) {
                            return String(Math.round(asNumber));
                        }
                    }

                    return raw.replace(/\D/g, '');
                };

                // While typing, always trust digits only so value can grow naturally (5 -> 50 -> 500 -> 5.000 ...)
                const normalizeTypingValueToDigits = (rawValue) => {
                    return String(rawValue || '').replace(/\D/g, '');
                };

                const formatNominal = (rawValue, isTyping = false) => {
                    const digitsOnly = isTyping
                        ? normalizeTypingValueToDigits(rawValue)
                        : normalizeInitialValueToDigits(rawValue);
                    if (!digitsOnly) return '';
                    return digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                };

                nominalInputs.forEach((input) => {
                    input.value = formatNominal(input.value);

                    input.addEventListener('input', function () {
                        this.value = formatNominal(this.value, true);
                    });
                });

                const forms = document.querySelectorAll('form');
                forms.forEach((form) => {
                    form.addEventListener('submit', function () {
                        const scopedNominals = form.querySelectorAll('.js-nominal');
                        scopedNominals.forEach((input) => {
                            input.value = normalizeTypingValueToDigits(input.value);
                        });
                    });
                });
            });
        </script>
    </body>
</html>
