<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $sitename; ?> | <?php echo $shopvar; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" media="screen" href="{{ secure_asset('css/polaris.css') }}" />
    <link rel="stylesheet" media="screen" href="{{ secure_asset('css/custom.css') }}" />

    <!-- Js -->
    <script type="text/javascript">
        var shop = '<?php echo $shopvar; ?>';
        var siteapiactionurl = '<?php echo url('api/ajax_actions'); ?>';
        var mode = '<?php echo $MODE; ?>';
    </script>
    <script type="text/javascript" src="{{ secure_asset('js/jquery-2.1.1.min.js') }}"></script>

    <?php if ($MODE == 'live') { ?>
    <?php if (!empty($current_user['host'])) { ?>
    <script>
        // Dynamically load App Bridge and initialize AFTER it's fully loaded
        (function() {
            var hostval = "<?php echo $current_user['host']; ?>";
            if (!hostval) return;

            var script = document.createElement('script');
            script.src = 'https://unpkg.com/@shopify/app-bridge@3';
            script.onload = function() {
                var AppBridge = window['app-bridge'];
                if (!AppBridge) {
                    console.error('App Bridge failed to load');
                    return;
                }

                var createApp = AppBridge.createApp;
                var actions = AppBridge.actions;
                var Loading = actions.Loading;
                var Button = actions.Button;
                var TitleBar = actions.TitleBar;

                var app = createApp({
                    apiKey: '<?php echo $SHOPIFY_API_KEY; ?>',
                    host: hostval,
                    forceRedirect: true,
                });

                var loading = Loading.create(app);

                var dashboardButton = Button.create(app, {
                    label: 'Dashboard',
                    href: 'index.php?shop=<?php echo $shopvar; ?>'
                });

                TitleBar.create(app, {
                    buttons: {
                        secondary: dashboardButton,
                    },
                });

                loading.dispatch(Loading.Action.STOP);
            };

            script.onerror = function() {
                console.error('Failed to load App Bridge script');
            };

            document.head.appendChild(script);
        })();
    </script>
    <?php } ?>
    <?php } ?>

    <script type="text/javascript" src="{{ secure_asset('js/custom.js') }}"></script>
</head>
@include('components.header')

<body class="antialiased">
    <div class="seers-cms-new-main-container">
        <div class="loadingoverlay">
            <div class="loader"></div>
        </div>
        <div>
            @include('components.sidebar')
        </div>
        <div class="seers-cms-new-main-content-container content-section" data-page="Dashboard">
            @include('components.dashboard')
        </div>

        <div class="Polaris-Page Polaris-Page--fullWidth content-section" style="display: none" data-page="Account">
            <div class="Polaris-Page__Content">

                <div class="Polaris-Banner">
                    <p class="Polaris-Heading">Need any other help?</p>
                    <p>We are always here to help you. Please <a class="Polaris-Link" href="mailto:<?php echo $sitemail; ?>"
                            target="_blank">email us</a></p>
                </div>

                <br>
                <div class="Polaris-Banner-hol">
                    <div class="Polaris-Banner">

                        <label class="mt-4">Domain URL:</label>
                        <input class="input-text" type="text" name="user_doamin" id="user_doamin" readonly
                            style="background:#f4f6f8; cursor:not-allowed; color:#637381;" value="<?php if (!empty($current_user['user_domain'])) {
                                echo $current_user['user_domain'];
                            } else {
                                echo $current_user['domain'];
                            } ?>">

                        <label>Email: *</label>
                        <input class="input-text" type="email" name="user_email" id="user_email" readonly
                            style="background:#f4f6f8; cursor:not-allowed; color:#637381;" value="<?php if (!empty($current_user['user_email'])) {
                                echo $current_user['user_email'];
                            } else {
                                echo $current_user['email'];
                            } ?>">

                        <label>Domain Group ID:</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <input class="input-text" type="text" name="data_key" id="user_key" style="flex: 1;"
                                value="<?php echo @$current_user['data_key']; ?>">
                            <button id="lookupKeyBtn" class="customizeSeersBtn"
                                style="margin:0; padding: 8px 8px; margin-bottom: 10px; background:#0061FE; border: none; border-radius:6px;
                                    font-weight: 700 !important; white-space:nowrap; width:100px; height:30px; display:inline-flex; align-items:center; justify-content:center; overflow:hidden;">
                                <span style="color: #fff">Check Key</span>
                            </button>
                        </div>

                        <!-- Lookup status message -->
                        {{-- <p id="keyLookupMsg" style="display:none; margin-top:8px; font-size:13px;"></p>

                            <!-- Update Data Button (hidden by default) -->
                            <button class="customizeSeersBtn" id="updateStoreDataBtn"
                                style="display:none; background:#28a745;">
                                <span>Update Data</span>
                            </button> --}}
                        <!-- Keep but always hidden — triggered by modal button -->
                        <button id="updateStoreDataBtn" style="display:none;"></button>

                        <p class="cooloes-text" style="font-weight: bold;">CONSENT </p>
                        <p class="cooloes-text">
                            By using this plugin, you agree to the
                            href='https://seers.ai/terms-conditions/' target='_blank'>terms and
                            condition</a> and <br> <a href='https://seers.ai/privacy-policy/' target='_blank'>privacy
                                policy</a>, and also agree Seers to use my email and url to <br>
                            create an account and power the cookie banner.
                        </p>
                        <button class="customizeSeersBtn get-more-features-account" id="customizeBtn"
                            data-tab="Preferences">
                            <span>Get More Features</span>
                        </button>
                        <hr style="margin:0 auto 10px; border-bottom:.5px dotted #c1c1c1; width:95%">
                    </div>
                </div>
                <br>

            </div>
        </div>
        <div class="content-section" data-page="Consent-Banner" style="width: 100%; display: none;">
            @include('components.general')
        </div>
        <div class="content-section" data-page="General" style="width: 100%; display: none;">
            @include('components.general')
        </div>
        <div class="content-section" data-page="Appearance" style="width: 100%; display: none;">
            @include('components.settings')
        </div>
        <div class="content-section" data-page="Settings" style="width: 100%; display: none;">
            @include('components.settings')
        </div>
        <div class="content-section" data-page="Visuals" style="width: 100%; display: none;">
            @include('components.visuals')
        </div>
        <div class="content-section" data-page="Tracking-Manager" style="width: 100%; display: none;">
            @include('components.trackingManager')
        </div>
        <div class="content-section" data-page="Frameworks" style="width: 100%; display: none;">
            @include('components.frameworks')
        </div>
        <div class="content-section" data-page="Reports" style="width: 100%; display: none;">
            @include('components.reports')
        </div>
        <div class="content-section" data-page="UserGuide" style="width: 100%; display: none;">
            @include('components.userGuide')
        </div>
        <div class="content-section" data-page="Privacy-Policy" style="width: 100%; display: none;">
            @include('components.privacy-policy')
        </div>
    </div>
    </div>
    <!-- Key Lookup Result Modal -->
    <div id="keyLookupModal"
        style="
    display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div
            style="
        background:#fff; border-radius:12px; padding:30px; max-width:480px; width:90%;
        box-shadow:0 4px 20px rgba(0,0,0,0.15); position:relative; text-align:center;">

            <!-- Close Button -->
            <button id="closeKeyModal"
                style="
            position:absolute; top:12px; right:16px; background:none; border:none;
            font-size:20px; cursor:pointer; color:#637381; line-height:1;">
                &times;
            </button>

            <!-- Icon -->
            <div id="modalIcon" style="font-size:40px; margin-bottom:12px;"></div>

            <!-- Title -->
            <h3 id="modalTitle" style="margin:0 0 10px; font-size:18px; font-weight:700;"></h3>

            <!-- Message -->
            <p id="modalMessage" style="margin:0 0 20px; font-size:14px; color:#637381;"></p>

            <!-- Update Data Button (only shown on success) -->
            <button id="modalUpdateBtn"
                style="
            display:none; background:#6CC04A; color:#fff; border:none; border-radius:8px;
            padding:10px 30px; font-size:14px; font-weight:700; cursor:pointer; width:100%;
            margin-bottom:10px;">
                Update Data
            </button>

            <!-- Close Button -->
            <button id="modalCloseBtn"
                style="
            background:#f4f6f8; color:#637381; border:1px solid #ddd; border-radius:8px;
            padding:10px 30px; font-size:14px; font-weight:600; cursor:pointer; width:100%;">
                Close
            </button>
        </div>
    </div>
</body>

<style>
    .get-more-features-account {
        text-align: center;
        justify-content: center;
        margin-left: 10px;
        margin-top: 20px;
        margin-bottom: 20px;
        background: #0061fe;
        border-radius: 10px;
        font-weight: 700 !important;
        font-size: 14px;
        padding: 10px;
        color: #fff;
        border: none;
        width: max-content;
    }
</style>

<script>
    var switchStatus = false;

    $("#myonoffswitch").on('change', function() {
        switchStatus = $(this).is(':checked');
        var user_doamin = $('#user_doamin').val();
        var user_email = $('#user_email').val();
        var data_key = $('#user_key').val();
        toggleCheckedVal(switchStatus, user_doamin, user_email, data_key);
    });

    $('.seers-get-premium').click(function() {
        window.open('https://seers.ai/price-plan', '_blank');
    });

    $(document).ready(function() {
        var currentUser = <?php echo json_encode($current_user); ?>;

        // Always use PHP-rendered values — never live field values
        var originalDomain = '<?php echo addslashes(!empty($current_user['user_domain']) ? $current_user['user_domain'] : $current_user['domain']); ?>';
        var originalEmail = '<?php echo addslashes(!empty($current_user['user_email']) ? $current_user['user_email'] : $current_user['email']); ?>';
        var originalDataKey = '<?php echo addslashes(@$current_user['data_key']); ?>';
        var token = currentUser['token'];

        // getUserData always uses PHP-rendered original values
        getUserData(switchStatus, originalDomain, originalEmail, originalDataKey, token);

        // Modal helper function
        function showKeyModal(type, icon, title, message) {
            $('#modalIcon').html(icon);
            $('#modalTitle').text(title);
            $('#modalMessage').html(message);

            // Show update button only on success
            if (type === 'success') {
                $('#modalUpdateBtn').show();
                $('#modalTitle').css('color', '#6CC04A');
            } else {
                $('#modalUpdateBtn').hide();
                $('#modalTitle').css('color', '#de3618');
            }

            $('#keyLookupModal').css('display', 'flex');
        }

        // Close modal handlers
        $('#closeKeyModal, #modalCloseBtn').on('click', function() {
            $('#keyLookupModal').hide();
            // On close — reset fields if error
            if ($('#modalUpdateBtn').is(':hidden')) {
                $('#user_doamin').val(originalDomain);
                $('#user_email').val(originalEmail);
            }
        });

        // Close modal on outside click
        $('#keyLookupModal').on('click', function(e) {
            if ($(e.target).is('#keyLookupModal')) {
                $('#keyLookupModal').hide();
            }
        });

        // When user types in Domain Group ID field — reset UI only, NO ajax call
        $('#user_key').on('input', function() {
            var newDataKey = $(this).val().trim();

            // Hide update button and message while typing
            $('#updateStoreDataBtn').hide();
            $('#keyLookupMsg').hide();

            // If cleared or same as original — reset fields
            if (!newDataKey || newDataKey === originalDataKey.trim()) {
                $('#user_doamin').val(originalDomain);
                $('#user_email').val(originalEmail);
            }
        });

        // Lookup fires ONLY when user clicks "Check Key" button
        $('#lookupKeyBtn').on('click', function() {
            var newDataKey = $('#user_key').val().trim();

            if (!newDataKey) {
                showKeyModal('error', '', 'Missing Key', 'Please enter a Domain Group ID.');
                return;
            }

            // If same as original — no need to lookup
            if (newDataKey === originalDataKey.trim()) {
                showKeyModal('error', '', 'Already Active',
                    'This is already your current Domain Group ID.');
                return;
            }

            var $btn = $(this);
            $btn.data('original-html', $btn.html());
            loading_show($btn, 'Checking...');

            $.ajax({
                url: siteapiactionurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    method_name: 'lookup_domain_by_key',
                    new_data_key: newDataKey,
                    shop: shop,
                    token: token,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Auto-fill Domain URL and Email from lookup response
                        $('#user_doamin').val(response.domain_name);
                        $('#user_email').val(response.user_email);

                        showKeyModal('success', '', 'Key Verified',
                            'Domain: <strong>' + response.domain_name +
                            '</strong><br>' +
                            'Email: <strong>' + response.user_email +
                            '</strong><br><br>' +
                            'Click "Update Data" to save changes.');

                    } else {
                        // Reset fields back to original on failure
                        $('#user_doamin').val(originalDomain);
                        $('#user_email').val(originalEmail);

                        showKeyModal('error', '', 'Invalid Key',
                            response.message ||
                            'Invalid Domain Group ID. Please check and try again.');
                    }
                },
                error: function() {
                    $('#user_doamin').val(originalDomain);
                    $('#user_email').val(originalEmail);
                    showKeyModal('error', '', 'Error',
                        'Something went wrong. Please try again.');
                },
                complete: function() {
                    loading_hide($btn);
                }
            });
        });
        // Handle Update Data from modal
        $('#modalUpdateBtn').on('click', function() {
            $('#keyLookupModal').hide();
            $('#updateStoreDataBtn').trigger('click');
        });

        // Handle Update Data button click
        $('#updateStoreDataBtn').on('click', function() {
            var $btn = $(this);
            var new_domain = $('#user_doamin').val().trim();
            var new_data_key = $('#user_key').val().trim();
            var new_email = $('#user_email').val().trim();

            if (!new_domain || !new_data_key || !new_email) {
                flashNotice('Domain URL, Email and Domain Group ID are required.', 'error');
                return;
            }

            $btn.data('original-html', $btn.html());
            loading_show($btn, 'Saving...');

            $.ajax({
                url: siteapiactionurl,
                type: 'POST',
                timeout: 120000,
                dataType: 'json',
                data: {
                    method_name: 'update_store_data',
                    shop: shop,
                    new_domain: new_domain,
                    new_data_key: new_data_key,
                    new_email: new_email,
                },
                success: function(response) {
                    if (response.status === 'success') {
                        flashNotice(response.message || 'Data updated successfully!');
                        // Reset all originals to new values
                        originalDomain = new_domain;
                        originalDataKey = new_data_key;
                        originalEmail = new_email;
                        $('#updateStoreDataBtn').hide();
                        $('#keyLookupMsg').hide();

                        // Reload page after 1.5s so user sees success message
                        // This refreshes all banner settings for the new Domain Group ID
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);

                    } else {
                        flashNotice(response.message || 'Update failed. Please try again.',
                            'error');
                    }
                },
                error: function(xhr, status, error) {
                    if (status === 'timeout') {
                        flashNotice(
                            'The request is taking longer than expected. The update may still complete successfully. Please check your store in a few minutes.',
                            'warning');
                    } else if (xhr.status === 504) {
                        flashNotice(
                            'Gateway timeout. The server is taking too long to respond. Please check your store to verify the update completed.',
                            'warning');
                    } else {
                        flashNotice('Something went wrong. Please try again.', 'error');
                    }
                    console.error('AJAX Error:', status, error, xhr.responseText);
                },
                complete: function() {
                    loading_hide($btn);
                }
            });
        });
    });
</script>

</html>
