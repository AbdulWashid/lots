<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="title" content="Admin" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <meta name="supported-color-schemes" content="light dark" />

    <link rel="preload" href="{{ asset('css/adminlte.css') }}" as="style" />
    <link rel="stylesheet" href="{{ asset('css/fontsource.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/overlayscrollbars.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />
    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        @include('admin.layouts.header')
        @include('admin.layouts.sidenav')
        <main class="app-main">
            @yield('content')
        </main>
        @include('admin.layouts.footer')
    </div>
    <script src="{{ asset('js/overlayscrollbars.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/adminlte.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(function() {
            'use strict';

            const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
            const Default = {
                scrollbarTheme: 'os-theme-light',
                scrollbarAutoHide: 'leave',
                scrollbarClickScroll: true,
            };

            const $sidebarWrapper = $(SELECTOR_SIDEBAR_WRAPPER);

            if ($sidebarWrapper.length && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
                OverlayScrollbarsGlobal.OverlayScrollbars($sidebarWrapper[0], {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script>
    <script>
        $(function() {
            'use strict';

            const storedTheme = localStorage.getItem("theme");

            const getPreferredTheme = () => {
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            };

            const setTheme = (theme) => {
                const themeToSet = (theme === "auto" && window.matchMedia("(prefers-color-scheme: dark)")
                        .matches) ?
                    "dark" :
                    theme;
                $('html').attr("data-bs-theme", themeToSet);
            };

            const showActiveTheme = (theme, focus = false) => {
                const $themeSwitcher = $("#bd-theme");
                if (!$themeSwitcher.length) {
                    return;
                }

                const $themeSwitcherText = $("#bd-theme-text");
                const $activeThemeIcon = $(".theme-icon-active i");
                const $btnToActive = $(`[data-bs-theme-value="${theme}"]`);
                const svgOfActiveBtn = $btnToActive.find("i").attr("class");

                $("[data-bs-theme-value]").removeClass("active").attr("aria-pressed", "false");

                $btnToActive.addClass("active").attr("aria-pressed", "true");
                $activeThemeIcon.attr("class", svgOfActiveBtn);
                const themeSwitcherLabel =
                    `${$themeSwitcherText.text()} (${$btnToActive.data("bs-theme-value")})`;
                $themeSwitcher.attr("aria-label", themeSwitcherLabel);

                if (focus) {
                    $themeSwitcher.trigger('focus');
                }
            };

            setTheme(getPreferredTheme());
            showActiveTheme(getPreferredTheme());

            $(window).on('change', function(e) {
                if (e.originalEvent.matches && (storedTheme !== "light" && storedTheme !== "dark")) {
                    setTheme(getPreferredTheme());
                }
            });

            $("[data-bs-theme-value]").on('click', function() {
                const theme = $(this).data("bs-theme-value");
                localStorage.setItem("theme", theme);
                setTheme(theme);
                showActiveTheme(theme, true);
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
