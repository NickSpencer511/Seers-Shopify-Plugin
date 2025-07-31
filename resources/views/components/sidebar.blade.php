{{-- <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sidebar</title>
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>
<body>
<div class="seers-cms-sidebar">
<div class="seers-cms-sidebar-header">
<button class="seers-cms-sidebar-close-button">&times;</button>
</div>
<ul class="seers-cms-sidebar-menu">
<li class="seers-cms-sidebar-menu-options"><a href="#"><img class="seers-cms-sidebar-icon" src="{{ asset('images/dashboard.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Dashboard</span></a></li>
<li class="seers-cms-sidebar-menu-options"><a href="#" id="consent-banner-toggle"><img class="seers-cms-sidebar-icon" src="{{ asset('images/consent-banner.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Consent Banner <img class="seers-cms-sidebar-dropdown-blue" src="{{ asset('images/dropdown-blue.png') }}"></span></a></li>
<div id="consent-banner-submenu" class="seers-cms-sidebar-consent-banner-expand">
<li class="seers-cms-sidebar-menu-optionss"><a href="#" id="general-toggle"><img id="general-icon" class="seers-cms-sidebar-icon-expand" src="{{ asset('images/select-app.png') }}" alt="icon"><span class="seers-cms-sidebar-options">General</span></a></li>
<li class="seers-cms-sidebar-menu-optionss"><a href="#" id="appearance-toggle"><img id="appearance-icon" class="seers-cms-sidebar-icon-expand" src="{{ asset('images/no-select-app.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Appearance</span></a></li>
<div id="appearance-submenu" class="seers-cms-sidebar-appearance-expand">
<li class="seers-cms-sidebar-menu-optionss"><a href="#" id="settings-item"><img class="seers-cms-sidebar-icon-expand" src="{{ asset('images/select-option.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Settings</span></a></li>
<li class="seers-cms-sidebar-menu-optionss"><a href="#" id="visuals-item"><img class="seers-cms-sidebar-icon-expand" src="{{ asset('images/option.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Visuals</span></a></li>
</div>
</div>
<li class="seers-cms-sidebar-menu-options"><a href="#"><img class="seers-cms-sidebar-icon" src="{{ asset('images/tracking-manager.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Tracking Manager</span></a></li>
<li class="seers-cms-sidebar-menu-options"><a href="#"><img class="seers-cms-sidebar-icon" src="{{ asset('images/framework.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Frameworks</span></a></li>
<li class="seers-cms-sidebar-menu-options"><a href="#"><img class="seers-cms-sidebar-icon" src="{{ asset('images/policy.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Privacy Policy</span></a></li>
<li class="seers-cms-sidebar-menu-options"><a href="#"><img class="seers-cms-sidebar-icon" src="{{ asset('images/reports.png') }}" alt="icon"><span class="seers-cms-sidebar-options">Reports</span></a></li>
<li class="seers-cms-sidebar-menu-options"><a href="#"><img class="seers-cms-sidebar-icon" src="{{ asset('images/user-guide.png') }}" alt="icon"><span class="seers-cms-sidebar-options">User Guide</span></a></li>
</ul>
<div class="seers-cms-sidebar-borders">
<ul class="seers-cms-sidebar-menu seers-cms-sidebar-menu-extras">
<li class="seers-cms-sidebar-links"><a href="#">Pricing Plan</a></li>
<li class="seers-cms-sidebar-links"><a href="#">Support</a></li>
<li class="seers-cms-sidebar-links"><a href="#">Need any help?</a></li>
</ul>
</div>
<div class="seers-cms-sidebar-footer">
<img class="seers-cms-social-links" src="{{ asset('images/facebook.png') }}">
<img class="seers-cms-social-links" src="{{ asset('images/twitter.png') }}">
<img class="seers-cms-social-links" src="{{ asset('images/linkedin.png') }}">
</div>
</div>
<button class="seers-cms-toggle-button">
<span></span>
<span></span>
<span></span>
</button>
    <script>
        const toggleButton = document.querySelector('.seers-cms-toggle-button');
        const sidebar = document.querySelector('.seers-cms-sidebar');
        const closeButton = document.querySelector('.seers-cms-sidebar-close-button');
        const menuItems = document.querySelectorAll('.seers-cms-sidebar-menu-options');
        const consentBannerToggle = document.getElementById('consent-banner-toggle');
        const consentBannerSubmenu = document.getElementById('consent-banner-submenu');
        const appearanceToggle = document.getElementById('appearance-toggle');
        const appearanceSubmenu = document.getElementById('appearance-submenu');
        const generalToggle = document.getElementById('general-toggle');
        const generalIcon = document.getElementById('general-icon');
        const appearanceIcon = document.getElementById('appearance-icon');
        const settingsItem = document.getElementById('settings-item').querySelector('img');
        const visualsItem = document.getElementById('visuals-item').querySelector('img');
    
        window.addEventListener('load', () => {
            menuItems[0].classList.add('active');
        });
    
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            toggleButton.classList.toggle('active');
        });
    
        closeButton.addEventListener('click', () => {
            sidebar.classList.remove('active');
            toggleButton.classList.remove('active');
        });
    
        consentBannerToggle.addEventListener('click', (e) => {
            e.preventDefault();
            consentBannerSubmenu.classList.toggle('active');
            generalToggle.click();
        });
    
        appearanceToggle.addEventListener('click', (e) => {
            e.preventDefault();
            appearanceSubmenu.classList.toggle('active');
            generalIcon.src = '{{ asset('images/no-select-app.png') }}';
            appearanceIcon.src = '{{ asset('images/select-app.png') }}';
            settingsItem.src = '{{ asset('images/select-option.png') }}';
            visualsItem.src = '{{ asset('images/option.png') }}';
        });
    
        generalToggle.addEventListener('click', (e) => {
            e.preventDefault();
            generalIcon.src = '{{ asset('images/select-app.png') }}';
            appearanceIcon.src = '{{ asset('images/no-select-app.png') }}';
            appearanceSubmenu.classList.remove('active');
        });
    
        function switchIcons(selectedItem, unselectedItem, selectedIcon, unselectedIcon) {
            selectedItem.src = selectedIcon;
            unselectedItem.src = unselectedIcon;
        }
    
        settingsItem.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            switchIcons(settingsItem, visualsItem, '{{ asset('images/select-option.png') }}', '{{ asset('images/option.png') }}');
        });
    
        visualsItem.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            switchIcons(visualsItem, settingsItem, '{{ asset('images/select-option.png') }}', '{{ asset('images/option.png') }}');
        });
        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                const clickedMenu = e.currentTarget;
                menuItems.forEach(menu => menu.classList.remove('active'));
                clickedMenu.classList.add('active');
            });
        });
    </script>
</body>
</html> --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>

<body>
    <div class="seers-cms-sidebar">
        <div class="seers-cms-sidebar-header">
            <button class="seers-cms-sidebar-close-button">&times;</button>
        </div>
        <ul class="seers-cms-sidebar-menu">
            <li class="seers-cms-sidebar-menu-options active" data-page="Dashboard"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/dashboard.png') }}" alt="icon"><span
                        class="seers-cms-sidebar-options">{{ __('Dashboard') }}</span></a></li>
            <li class="seers-cms-sidebar-menu-options" data-page="Account"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/policy.png') }}" alt="icon"><span
                        class="seers-cms-sidebar-options">{{ __('Account Setup') }}</span></a></li>
            <li class="seers-cms-sidebar-menu-options" data-page="Consent-Banner"><a href="javascript:void(0);"
                    id="consent-banner-toggle"><img class="seers-cms-sidebar-icon"
                        src="{{ asset('images/consent-banner.png') }}" alt="icon"><span
                        class="seers-cms-sidebar-options">{{ __('Consent Banner') }} <img
                            class="seers-cms-sidebar-dropdown-blue"
                            src="{{ asset('images/dropdown-blue.png') }}"></span></a></li>
            <div id="consent-banner-submenu" class="seers-cms-sidebar-consent-banner-expand">
                <li class="seers-cms-sidebar-menu-optionss" data-page="General"><a href="javascript:void(0);"
                        id="general-toggle"><img id="general-icon" class="seers-cms-sidebar-icon-expand"
                            src="{{ asset('images/select-app.png') }}" alt="icon"><span
                            class="seers-cms-sidebar-options">{{ __('General') }}</span></a></li>
                <li class="seers-cms-sidebar-menu-optionss" data-page="Appearance"><a href="javascript:void(0);"
                        id="appearance-toggle"><img id="appearance-icon" class="seers-cms-sidebar-icon-expand"
                            src="{{ asset('images/no-select-app.png') }}" alt="icon"><span
                            class="seers-cms-sidebar-options">{{ __('Appearance') }}</span></a></li>
                <div id="appearance-submenu" class="seers-cms-sidebar-appearance-expand">
                    <li class="seers-cms-sidebar-menu-optionss" data-page="Settings"><a href="javascript:void(0);"
                            id="settings-item"><img class="seers-cms-sidebar-icon-expand"
                                src="{{ asset('images/select-option.png') }}" alt="icon"><span
                                class="seers-cms-sidebar-options">{{ __('Settings') }}</span></a></li>
                    <li class="seers-cms-sidebar-menu-optionss" data-page="Visuals"><a href="javascript:void(0);"
                            id="visuals-item"><img class="seers-cms-sidebar-icon-expand"
                                src="{{ asset('images/option.png') }}" alt="icon"><span
                                class="seers-cms-sidebar-options">{{ __('Visuals') }}</span></a></li>
                </div>
            </div>
            <li class="seers-cms-sidebar-menu-options" data-page="Tracking-Manager"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/tracking-manager.png') }}"
                        alt="icon"><span class="seers-cms-sidebar-options">{{ __('Tracking Manager') }}</span></a>
            </li>
            <li class="seers-cms-sidebar-menu-options" data-page="Frameworks"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/framework.png') }}" alt="icon"><span
                        class="seers-cms-sidebar-options">{{ __('Frameworks') }}</span></a></li>
            <li class="seers-cms-sidebar-menu-options" data-page="Privacy-Policy"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/policy.png') }}" alt="icon"><span
                        class="seers-cms-sidebar-options">{{ __('Privacy Policy') }}</span></a></li>
            <li class="seers-cms-sidebar-menu-options" data-page="Reports"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/reports.png') }}" alt="icon"><span
                        class="seers-cms-sidebar-options">{{ __('Reports') }}</span></a></li>
            <li class="seers-cms-sidebar-menu-options" data-page="UserGuide"><a href="javascript:void(0);"><img
                        class="seers-cms-sidebar-icon" src="{{ asset('images/user-guide.png') }}"
                        alt="icon"><span class="seers-cms-sidebar-options">{{ __('User Guide') }}</span></a></li>
        </ul>
        <div class="seers-cms-sidebar-borders">
            <ul class="seers-cms-sidebar-menu seers-cms-sidebar-menu-extras">
                <li class="seers-cms-sidebar-links"><a href="https://seers.ai/price-plan"
                        target="_blank">{{ __('Pricing Plan') }}</a></li>
                <li class="seers-cms-sidebar-links"><a href="mailto:support@seersco.com">{{ __('Support') }}</a></li>
                <li class="seers-cms-sidebar-links"><a href="https://seers.ai/contact/"
                        target="_blank">{{ __('Need any help?') }}</a></li>
            </ul>
        </div>
        <div class="seers-cms-sidebar-footer">
            <a href="https://www.facebook.com/seersgroupltd?mibextid=ZbWKwL" target="_blank">
                <img class="seers-cms-social-links" src="{{ asset('images/facebook.png') }}">
            </a>
            <a href="https://x.com/seersco" target="_blank">
                <img class="seers-cms-social-links" src="{{ asset('images/twitter.png') }}">
            </a>
            <a href="https://www.linkedin.com/company/seersco/" target="_blank">
                <img class="seers-cms-social-links" src="{{ asset('images/linkedin.png') }}">
            </a>
        </div>
    </div>
    <script>
        const toggleButton = document.querySelector('.seers-cms-toggle-button');
        const sidebar = document.querySelector('.seers-cms-sidebar');
        const closeButton = document.querySelector('.seers-cms-sidebar-close-button');
        const menuItems = document.querySelectorAll('.seers-cms-sidebar-menu-options');
        const consentBannerToggle = document.getElementById('consent-banner-toggle');
        const consentBannerSubmenu = document.getElementById('consent-banner-submenu');
        const appearanceToggle = document.getElementById('appearance-toggle');
        const appearanceSubmenu = document.getElementById('appearance-submenu');
        const generalToggle = document.getElementById('general-toggle');
        const generalIcon = document.getElementById('general-icon');
        const appearanceIcon = document.getElementById('appearance-icon');
        const settingsItem = document.getElementById('settings-item').querySelector('img');
        const visualsItem = document.getElementById('visuals-item').querySelector('img');

        // window.addEventListener('load', () => {
        //     menuItems[0].classList.add('active');
        // });

        toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        // console.log("Click");
        toggleButton.classList.toggle('active');
        });
        closeButton.addEventListener('click', () => {
            sidebar.classList.remove('active');
            // console.log("Close Click");
            toggleButton.classList.remove('active');
        });

        consentBannerToggle.addEventListener('click', (e) => {
            e.preventDefault();
            consentBannerSubmenu.classList.toggle('active');
            // console.log("General Testing");
            generalToggle.click();
        });
        appearanceToggle.addEventListener('click', (e) => {
            e.preventDefault();
            appearanceSubmenu.classList.toggle('active');
            generalIcon.src = '{{ asset('images/no-select-app.png') }}';
            appearanceIcon.src = '{{ asset('images/select-app.png') }}';
            settingsItem.src = '{{ asset('images/select-option.png') }}';
            visualsItem.src = '{{ asset('images/option.png') }}';
        });
        generalToggle.addEventListener('click', (e) => {
            e.preventDefault();
            generalIcon.src = '{{ asset('images/select-app.png') }}';
            appearanceIcon.src = '{{ asset('images/no-select-app.png') }}';
            appearanceSubmenu.classList.remove('active');
        });

        document.getElementById('visuals-item').addEventListener('click', () => {
            settingsItem.src = '{{ asset('images/option.png') }}';
            visualsItem.src = '{{ asset('images/select-option.png') }}';
        });

        function switchIcons(selectedItem, unselectedItem, selectedIcon, unselectedIcon) {
            selectedItem.src = selectedIcon;
            unselectedItem.src = unselectedIcon;
        }

        settingsItem.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            switchIcons(settingsItem, visualsItem, '{{ asset('images/select-option.png') }}',
                '{{ asset('images/option.png') }}');
        });

        visualsItem.parentElement.addEventListener('click', (e) => {
            e.preventDefault();
            switchIcons(visualsItem, settingsItem, '{{ asset('images/select-option.png') }}',
                '{{ asset('images/option.png') }}');
        });

        menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                const clickedMenu = e.currentTarget.querySelector('.seers-cms-sidebar-options').textContent
                    .trim();
                menuItems.forEach(i => i.classList.remove('active'));
                e.currentTarget.classList.add('active');
                if (clickedMenu !== 'Consent Banner') {
                    consentBannerSubmenu.classList.remove('active');
                    appearanceSubmenu.classList.remove('active');
                    generalIcon.src = '{{ asset('images/no-select-app.png') }}';
                    appearanceIcon.src = '{{ asset('images/no-select-app.png') }}';
                }
            });
        });

        window.addEventListener('load', () => {
            const defaultPage = 'Dashboard';
            const contentSections = document.querySelectorAll('.content-section');
            contentSections.forEach(section => {
                // console.log('Hello');
                if (section.getAttribute('data-page') !== defaultPage) {
                    section.style.display = 'none';
                }
            });

            const menuItems = document.querySelectorAll('.seers-cms-sidebar-menu-options');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    const page = this.getAttribute('data-page');
                    contentSections.forEach(section => {
                        if (section.getAttribute('data-page') === page) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });
            });
            const submenuItems = document.querySelectorAll('.seers-cms-sidebar-menu-optionss');
            submenuItems.forEach(item => {
                item.addEventListener('click', function() {
                    const page = this.getAttribute('data-page');
                    contentSections.forEach(section => {
                        if (section.getAttribute('data-page') === page) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });
            });
        });
        
    </script>
</body>

</html>
