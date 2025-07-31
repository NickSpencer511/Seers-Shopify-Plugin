<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/frameworks.css') }}">
    <title>Framework Settings</title>
</head>
<body>
    <div class="seers-cms-frameworks-container">
        <div class="seers-cms-frameworks-card">
            <h2 class="seers-cms-frameworks-heading">Framework</h2>
            <hr>
            <div class="seers-cms-frameworks-menu">
            <div class="seers-cms-frameworks-setting">
                <label for="google-consent-mode">
                    Google consent mode V2  
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, Google Consent Mode V2 for Google Analytics is supported. GCM must be implemented via your Google configuration for this option to function.
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="googleconsent"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="global-privacy-control">
                    Global Privacy Control (GPC)  
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, visitors who are using a browser or extension that is GPC compliant will have their signal automatically applied to their consent settings. 
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="globalprivacycontrol"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="do-not-sell">
                    Do Not Sell (CPRA)
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, visitors who are using a browser that sends a Do Not Sell Signal will have their signal automatically applied to their consent settings
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="donotsell"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="Do-Not-Track">
                    Do Not Track  
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, visitors who are using a browser that sends a Do Not Track signal will have their signal automatically applied to their consent settings. 
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="donottrack"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="iab-tcf-v2">
                    IAB TCF V2 
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, Seers CMP will implement the IAB Global Privacy Platform and IAB EU TCF 2.x Frameworks along with their respective APIs.  
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="iabtcf"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="facebook-consent-mode">
                    Facebook Consent Mode
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            Activate FCM API to pause sending pixel signals to Facebook.
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="facebookconsent"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="microsoft-consent-mode">
                    Microsoft Consent Mode
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, Microsoft Consent Mode V2 for Microsoft Advertising (UET) is supported.
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="microsoftconsent"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="shopify-consent-mode">
                    Shopify Privacy API Integration
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            When enabled, your CMP will send consent signals to Shopify's customerPrivacy API after user interaction.
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="shopifyconsent"></div>
                </div>
            </div>
            <div class="seers-cms-frameworks-setting">
                <label for="Child-privacy">
                    Child Privacy 
                    
                    <span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            The Child Privacy mode will show layers of banners for age-appropriate child consent.
                            <!-- <a href="https://support.seersco.com/en/child-privacy" target="_blank">
                                Learn more...'
                            </a> -->
                        </span>
                    </span>
                    {{-- <span class="seers-cms-frameworks-premium">PREMIUM</span> --}}
                </label>
                
                <div class="toggle-frameworks">
                    <div class="toggle-frameworks-switch customizeSeersBtn" data-tab="Preferences" name="childprivacy"></div>
                </div>
            </div>
            </div>
            <hr>
            <!-- <button class="seers-cms-frameworks-save-btn">Save Changes</button> -->
        </div>
    </div>
</body>
</html>
