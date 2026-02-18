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
    <?php echo !empty($current_user['host']) ? '<script src="https://unpkg.com/@shopify/app-bridge@3"></script>' : ''; ?>
    <script type="text/javascript">
        var hostval = "<?php echo !empty($current_user['host']) ? $current_user['host'] : ''; ?>";

        if (hostval) {

            var AppBridge = window['app-bridge'];

            var createApp = AppBridge.createApp;
            var TitleBar = AppBridge.TitleBar;
            var Button = AppBridge.Button;
            var actions = AppBridge.actions;
            var Loading = actions.Loading;

            var app = "";
            var loading = "";


            app = createApp({
                apiKey: '<?php echo $SHOPIFY_API_KEY; ?>',
                host: '<?php echo !empty($current_user['host']) ? $current_user['host'] : ''; ?>',
                forceRedirect: true,
            });

            loading = Loading.create(app);
        }
    </script>
    <script type="text/javascript">
        var query_output = '';

        if (hostval) {

            const dashboardButton = Button.create(app, {
                label: 'Dashboard',
                "href": "index.php?shop=<?php echo $shopvar; ?>"
            });
            const titleBarOptions = {
                buttons: {
                    secondary: dashboardButton,
                },
            };
            const myTitleBar = TitleBar.create(app, titleBarOptions);

            loading.dispatch(Loading.Action.STOP);

        }
    </script>
    <?php } ?>

    <script type="text/javascript" src="{{ secure_asset('js/custom.js') }}"></script>
</head>
@include('components.header')

<body class="antialiased">
    <div class="seers-cms-new-main-container">
        <div class="loadingoverlay">
            <div class="loader"></div>
        </div>
        <!-- Upgrade banner for embed -->
<div id="embed-setup-banner" class="Polaris-Banner Polaris-Banner--statusWarning" style="display: none;">
    <div class="Polaris-Banner__Content">
        <h2 class="Polaris-Heading">Upgrade to faster synchronous loading</h2>
        <p>Enable the new app embed to ensure our script runs before any third-party scripts, improving consent management.</p>
        <button id="enable-embed-btn" class="Polaris-Button Polaris-Button--primary">
            Enable now in theme editor
        </button>
        <p class="note">After enabling, refresh this page.</p>
    </div>
</div>

<!-- Optional cleanup banner -->
<div id="cleanup-scripts-banner" class="Polaris-Banner Polaris-Banner--statusInfo" style="display: none;">
    <div class="Polaris-Banner__Content">
        <p>The embed is active, but we detected old script tags. You can remove them for a cleaner setup.</p>
        <button id="cleanup-scripts-btn" class="Polaris-Button">Remove old scripts</button>
    </div>
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
                    {{-- <div class="Polaris-Banner-head"><span>Banner Settings</span>
                        <p class="grey-text">Enable/disable banner in just one click.</p>
                    </div> --}}
                    <div class="Polaris-Banner">
                        <label class="mt-4">Domain URL:</label>
                        <input class="input-text" type = "text" name="user_doamin" id="user_doamin" readonly
                            value="<?php if (!empty($current_user['user_domain'])) {
                                echo $current_user['user_domain'];
                            } else {
                                echo $current_user['domain'];
                            } ?>">
                        <label>Email: *</label>
                        <input class="input-text" type = "email" name="user_email" id="user_email"
                            value="<?php if (!empty($current_user['user_email'])) {
                                echo $current_user['user_email'];
                            } else {
                                echo $current_user['email'];
                            } ?>">
                        <label>Domain Group ID:</label>
                        <input class="input-text" type = "text" name="data_key" id="user_key"
                            value="<?php echo @$current_user['data_key']; ?>" readonly>
                        <p class="cooloes-text" style="font-weight: bold;">CONSENT </p>
                        <p class="cooloes-text">
                            By using this plugin, you agree to the <a
                                href='https://seers.ai/terms-conditions/' target='_blank'>terms and
                                condition</a> and <br> <a href='https://seers.ai/privacy-policy/'
                                target='_blank'>privacy policy</a>, and also agree Seers to use my email and url to <br>
                            create an account and power the cookie banner.
                        </p>
                        <button class="customizeSeersBtn get-more-features-account" id="customizeBtn" data-tab="Preferences">
                            <span>Get More Features</span>
                        </button>
                        <hr style="margin:0 auto 10px; border-bottom:.5px dotted #c1c1c1; width:95%">
                        {{-- <p class="cooloes-text">You must enter Domain Url and Email to get a Consent Banner.</p>
                        <?php  if($current_user['toggle_status']==1){ ?>
                        <p class="enable-banner"><span class ="banner-tick"></span> Banner is enabled on your
                            store.<br><span style="margin-left:18px;"></span>Please refresh your store home page to see
                            the effect. </p>
                        <?php }else{?>
                        <p class="enable-banner">Banner is disabled on your store.</p>
                        <?php } ?>
                        <div class="onoffswitch">
                            <?php  if($current_user['toggle_status']==1){ ?>

                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"
                                checked tabindex="0">
                            <?php }else{ ?>
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"
                                tabindex="0">
                            <?php } ?>
                            <label class="onoffswitch-label" for="myonoffswitch">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div> --}}


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
        if ($(this).is(':checked')) {
            switchStatus = $(this).is(':checked');
            var user_doamin = $('#user_doamin').val();
            var user_email = $('#user_email').val();
            var data_key = $('#user_key').val();
            toggleCheckedVal(switchStatus, user_doamin, user_email, data_key);
        } else {
            switchStatus = $(this).is(':checked');
            var user_doamin = $('#user_doamin').val();
            var user_email = $('#user_email').val();
            var data_key = $('#user_key').val();
            toggleCheckedVal(switchStatus, user_doamin, user_email, data_key);
        }
    });

    $('.seers-get-premium').click(function() {
        window.open('https://seers.ai/price-plan', '_blank');
    });

    $(document).ready(function() {
        var currentUser = <?php echo json_encode($current_user); ?>;
        var user_doamin = $('#user_doamin').val();
        var user_email = $('#user_email').val();
        var data_key = $('#user_key').val();
        var token = currentUser['token'];

        // Call existing function (preserve it)
        if (typeof getUserData === 'function') {
            getUserData(switchStatus, user_doamin, user_email, data_key, token);
        }

        // --- New embed checks ---
        // Check embed status on page load
        $.ajax({
            url: siteapiactionurl,
            method: 'POST',
            data: {
                method_name: 'check_embed_enabled',   // changed from 'action'
                shop: shop,
                token: token
            },
            success: function(response) {
                if (response.enabled) {
                    $('#embed-setup-banner').hide();
                    // If embed is enabled but database flag is 0, show cleanup option
                    if (currentUser.embed_enabled == 0) {
                        $('#cleanup-scripts-banner').show();
                    }
                } else {
                    $('#embed-setup-banner').show();
                }
            }
        });

        // Handle "Enable now" button
        $(document).on('click', '#enable-embed-btn', function() {
            $.ajax({
                url: siteapiactionurl,
                method: 'POST',
                data: {
                    method_name: 'get_theme_editor_url',   // changed
                    shop: shop,
                    token: token
                },
                success: function(response) {
                    if (response.url) {
                        window.open(response.url, '_blank');
                        alert('After enabling the embed in the theme editor, please refresh this page.');
                    } else {
                        alert('Could not generate editor URL.');
                    }
                }
            });
        });

        // Handle cleanup button
        $(document).on('click', '#cleanup-scripts-btn', function() {
            $.ajax({
                url: siteapiactionurl,
                method: 'POST',
                data: {
                    method_name: 'remove_old_scripts',   // changed
                    shop: shop,
                    token: token
                },
                success: function(response) {
                    if (response.success) {
                        alert('Old scripts removed successfully.');
                        $('#cleanup-scripts-banner').hide();
                    }
                }
            });
        });
    });
</script>



</html>
