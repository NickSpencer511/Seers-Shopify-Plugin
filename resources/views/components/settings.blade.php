<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <title>Settings</title>
</head>

<body>
    <div class="seers-cms-appearance-settings-container">
        <div class="seers-cms-appearance-settings-card">
            <div class="seers-cms-appearance-settings-menu">
                <div class="seers-cms-appearance-settings-setting">
                    <label for="child-privacy">Cookie Banner <span
                            class="seers-cms-appearance-settings-show-hide">(Show/Hide)</span>
                        <span class="tooltiphtml" style="font-size:20px;">
                            <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}"
                                    alt="info-icon"></span>
                            <span class="tooltiptext">
                                Allow Cookie Banner to be displayed on your website
                            </span>
                        </span></label>
                    <div class="seers-cms-appearance-settings-input-field">
                        <label class="seers-cms-settings-toggle" style="width: 0;">
                            <input class="seers-cms-settings-toggle-checkbox" type="checkbox" name="banner_check"
                                id="banner_check" checked>
                        </label>
                    </div>
                </div>

                <div class="seers-cms-appearance-settings-setting">
                    <label for="global-privacy-control">Show Badge
                        <span class="tooltiphtml" style="font-size:20px;">
                            <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}"
                                    alt="info-icon"></span>
                            <span class="tooltiptext">
                                Show a badge to enable cookie banner appear post consent
                            </span>
                        </span>
                    </label>
                    <div class="seers-cms-appearance-settings-input-field">
                        <label class="seers-cms-settings-toggle" style="width: 0;">
                            <input class="seers-cms-settings-toggle-checkbox" type="checkbox" name="show_badge"
                                id="show_badge" checked>
                        </label>
                    </div>
                </div>

                <div class="seers-cms-appearance-settings-setting">
                    <label for="manage-badge-customization">Manage Badge Customization
                        <span class="tooltiphtml" style="font-size:20px;">
                            <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}"
                                    alt="info-icon"></span>
                            <span class="tooltiptext ">
                                Manage Badge Customization
                            </span>
                        </span>
                        {{-- <span class="seers-cms-appearance-settings-premium">PREMIUM</span> --}}
                    </label>
                    <div class="seers-cms-appearance-settings-input-field">
                        {{-- <input type="checkbox" id="manage-badge-customization" class="seers-paid-feature-opener seers-get-premium" name="managebadgecustomization"> --}}
                        {{-- <div class="toggle-frameworks-switch seers-paid-feature-opener seers-get-premium" --}}
                        <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Visuals"
                            id="language-auto-regional-detection" name="languageautoregionaldetection"></div>
                    </div>
                </div>

                <div class="seers-cms-appearance-settings-setting">
                    <label for="record-consent">Record Consent
                        <span class="tooltiphtml" style="font-size:20px;">
                            <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}"
                                    alt="info-icon"></span>
                            <span class="tooltiptext">
                                Turn on to record the consent of users
                            </span>
                        </span>
                        {{-- <span class="seers-cms-appearance-settings-premium">PREMIUM</span> --}}
                    </label>
                    <div class="seers-cms-appearance-settings-input-field">
                        {{-- <input type="checkbox" id="record-consent" class="seers-paid-feature-opener seers-get-premium" name="recordconsent"> --}}
                        <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Reports" data-subtab="ConsentLog"
                            id="language-auto-regional-detection" name="languageautoregionaldetection"></div>
                    </div>
                </div>

                <div class="seers-cms-appearance-settings-setting">
                    <label for="sub-domain-setting">Sub-Domains Setting
                        <span class="tooltiphtml" style="font-size:20px;">
                            <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}"
                                    alt="info-icon"></span>
                            <span class="tooltiptext">
                                Covers all subdomain under the selected domain
                            </span>
                        </span>
                        {{-- <span class="seers-cms-appearance-settings-premium">PREMIUM</span> --}}
                    </label>
                    <div class="seers-cms-appearance-settings-input-field">
                        {{-- <input type="checkbox" id="sub-domain-setting" class="seers-paid-feature-opener seers-get-premium" name="subdomain"> --}}
                        <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences"
                            id="language-auto-regional-detection" name="languageautoregionaldetection"></div>
                    </div>
                </div>

                <div class="seers-cms-appearance-settings-setting">
                    <label for="google-additional-consent">Consent Frequency
                        <span class="tooltiphtml" style="font-size:20px;">
                            <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}"
                                    alt="info-icon"></span>
                            <span class="tooltiptext">
                                Choose how many times you want consent from user
                            </span>
                        </span>
                    </label>
                    <div class="seers-cms-appearance-settings-input-field">
                        <div class="seers-cms-appearance-settings-dropdown">
                            <select class="seers-cms-settings-cosent-frequency seers-cms-input fm" id="cookies_expiry"
                                name="cookies_expiry">
                                <option value=0>Always</option>
                                <option value=1 selected>Daily</option>
                                <option value=7>Weekly</option>
                                <option value=30>Monthly</option>
                                <option value=90>Quarterly</option>
                                <option value=365>Yearly</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="seers-cms-appearance-settings-hr">
            <button class="seers-cms-appearance-settings-save-btn" id="setting_save">Save Changes</button>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // ar currentUser = <?php echo json_encode($current_user); ?>;
            // console.log(currentUser['data_key']);
            // var data_key = currentUser['data_key'];
            // var token = currentUser['token'];
            // if (data_key) {
                $(document).on('userDataReady', function(event, data) {
                    // console.log("Data received in Settings Blade file:", data);

                    const isChecked = data.bannersettings.is_cookie_banner === 1;
                    $('#banner_check').prop('checked', isChecked);
                    $('#banner_check').on('change', function() {
                        const isChecked = $(this).is(':checked');
                        data.bannersettings.is_cookie_banner = isChecked ? 1 : 0;
                        // console.log('Updated is_cookie_banner:', data.bannersettings
                        //     .is_cookie_banner);
                    });

                    const isBadgeChecked = data.bannersettings.has_badge === 1;
                    $('#show_badge').prop('checked', isBadgeChecked);
                    $('#show_badge').on('change', function() {
                        const isBadgeChecked = $(this).is(':checked');
                        data.bannersettings.has_badge = isBadgeChecked ? 1 : 0;
                        // console.log('Updated has_badge:', data.bannersettings.has_badge);
                    });

                    const agreementExpire = data.bannersettings.agreement_expire || '';
                    $('#cookies_expiry').val(agreementExpire);

                    $('#cookies_expiry').on('change', function() {
                        const selectedCookieExpiry = $(this).val();
                        data.bannersettings.agreement_expire = selectedCookieExpiry;
                        // console.log('Updated agreement expiry:', data.bannersettings
                        //     .agreement_expire);
                    });

                    document.getElementById('setting_save').addEventListener('click', function() {
                        var currentUser = <?php echo json_encode($current_user); ?>;
                        var token = currentUser['token'];
                        var user_doamin = $('#user_doamin').val();
                        var user_email = $('#user_email').val();
                        var data_key = $('#user_key').val();
                        // console.log('setting tab');
                        updateUserData(switchStatus, user_doamin, user_email, data_key, data,
                        token);
                    });
                });
            // }
        });
    </script>



</body>

</html>
