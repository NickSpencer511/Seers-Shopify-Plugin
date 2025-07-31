<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/general.css') }}">
    <title>Consent Settings</title>
</head>
<body>
    
    <div class="seers-cms-consent-banner-general-container">
        <div class="seers-cms-consent-banner-upper-general-container">
            <div class="seers-cms-consent-banner-general-settings-section">
                <label class="seers-cms-consent-banner-general-regulation" for="seers-cms-consent-banner-general-regulation">REGULATION</label>
                <div class="seers-cms-consent-banner-general-custom-dropdown">
                    <input type="checkbox" id="dropdown-regulation-toggle">
                    <label for="dropdown-regulation-toggle" class="seers-cms-consent-banner-general-custom-select" id="selected-option">Consent Type</label>
                    <ul class="seers-cms-consent-banner-general-dropdown-options">
                        <li>
                            <input type="radio" id="gdpr" name="consent" value="gdpr" checked>
                            <label class="seers-cms--general-default" for="gdpr">GDPR<span class="seers-cms-general-tick"></span></label>
                            </li>
                        <li class="seers-paid-feature-opener" name="regulation">
                            <input type="radio" id="ccpa" name="consent" value="ccpa" class="seers-paid-feature-opener" name="regulation">
                            <label for="ccpa" class="seers-paid-feature-opener" name="regulation">Global Privacy Law<span class="seers-cms-consent-banner-general-premium">PREMIUM</span></label>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="seers-cms-consent-banner-general-settings-section">
                <label class="seers-cms-consent-banner-general-regulation" for="seers-cms-consent-banner-general-regulation">LANGUAGE</label>
                <div class="seers-cms-consent-banner-general-custom-dropdown">
                    <input type="checkbox" id="dropdown-language-toggle">
                    <label for="dropdown-language-toggle" class="seers-cms-consent-banner-general-custom-select" id="selected-language">Choose</label>
                    <ul class="seers-cms-consent-banner-general-dropdown-options">
                        <li>
                            <input type="radio" id="english" name="language" value="english" checked>
                            <label class="seers-cms--general-default" for="english">English<span class="seers-cms-general-tick"></span></label>
                        </li>
                        <li  class="seers-paid-feature-opener" name="regulation">
                            <input type="radio" id="add-other" name="language" value="add-other"  class="seers-paid-feature-opener" name="regulation">
                            <label for="add-other" class="seers-paid-feature-opener" name="regulation">Add Other<span class="seers-cms-consent-banner-general-premium">PREMIUM</span></label>
                        </li>
                        <!-- <li class="seers-paid-feature-opener" name="regulation">
                            <input type="radio" id="auto-detect" name="language" value="auto-detect" class="seers-paid-feature-opener" name="regulation">
                            <label for="auto-detect" class="seers-paid-feature-opener" name="regulation">Auto Detect <span class="seers-cms-consent-banner-general-premium">PREMIUM</span></label>
                        </li> -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="seers-cms-consent-banner-upper-general-container">
        <div class="seers-cms-general-geo-target-and-language-auto">
        <div class="seers-cms-consent-banner-general-geo-target-section">
            <label class="seers-cms-consent-banner-general-geo-target-section-heading">Geo-target Location (Regional)/label>
            <div class="seers-cms-consent-banner-general-radio-group">
                <label class="seers-paid-feature-opener" name="regulation">
                    <input class="seers-paid-feature-opener" name="regulation" type="radio" name="geo-target" value="worldwide">Worldwide<span class="seers-cms-consent-banner-general-premium">PREMIUM</span>
                </label>
                <label class="seers-paid-feature-opener" name="regulation">
                    <input class="seers-paid-feature-opener" name="regulation" type="radio" name="geo-target" value="uk-eu">UK & EU Countries<span class="seers-cms-consent-banner-general-premium">PREMIUM</span>
                </label>
                <label class="seers-paid-feature-opener" name="regulation">
                    <input class="seers-paid-feature-opener" name="regulation" type="radio" name="geo-target" value="auto">Auto Regional Detection<span class="seers-cms-consent-banner-general-premium">PREMIUM</span>
                </label>
                <label class="seers-paid-feature-opener" name="regulation">
                    <input class="seers-paid-feature-opener" name="regulation" type="radio" name="geo-target" value="other-countries">Other Countries<span class="seers-cms-consent-banner-general-premium">PREMIUM</span>
                </label>
            </div>
        </div>
        <div class="seers-cms-appearance-settings-setting seers-cms-general-language-auto">
                <label for="language-auto-regional-detection">Language Auto Regional Detection<span class="tooltiphtml" style="font-size:20px;">
                        <span><img class="seers-cms-frameworks-info-icon" src="{{ asset('images/info icon.png') }}" alt="info-icon"></span>
                        <span class="tooltiptext">
                            Turn on to automatically detect the language of banner according to region
                        </span>
                    </span><span class="seers-cms-appearance-settings-premium">PREMIUM</span></label>
                <div class="seers-cms-appearance-settings-input-field ">
                <input type="checkbox" id="language-auto-regional-detection" class="seers-paid-feature-opener seers-cms-general-language-auto-toggle" name="languageautoregionaldetection">
            </div>
            </div>
            <div class="seers-cms-consent-banner-general-buttons">
            <a class="seers-cms-consent-banner-general-save-button  s-save" href="#" target="_blank">Preview</a>
            <!-- <button class="seers-cms-consent-banner-general-save-button">Save Changes</button> -->
        </div>
        </div>
        <div class="seers-cms-consent-banner-general-world-laws">
    <label class="seers-cms-consent-banner-general-geo-target-section-heading">Global Privacy Laws</label>
    <div class="seers-cms-consent-banner-general-radio-group">
        <div>
            <table>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(GDPR) General Data Protection Regulation</td>
                    <td class="seers-cms-general-global-privacy-law-country">European Union</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(CCPA) California Consumer Privacy Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">United States</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(CPRA) California Privacy Rights Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">United States</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(LGPD) Brazilian General Data Protection Law</td>
                    <td class="seers-cms-general-global-privacy-law-country">Brazil</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(PIPL) Personal Information Protection Law</td>
                    <td class="seers-cms-general-global-privacy-law-country">China</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(PDPA) Personal Data Protection Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">Singapore</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Privacy Act 1988</td>
                    <td class="seers-cms-general-global-privacy-law-country">Australia</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Data Protection Act 2018</td>
                    <td class="seers-cms-general-global-privacy-law-country">United Kingdom</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(POPIA) Protection of Personal Information Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">South Africa</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(PIPEDA) Personal Information Protection and Electronic Documents Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">Canada</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(EU Cookie Law) ePrivacy Directive</td>
                    <td class="seers-cms-general-global-privacy-law-country">European Union</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(COPPA) Children's Online Privacy Protection Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">United States</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Personal Data (Privacy) Ordinance</td>
                    <td class="seers-cms-general-global-privacy-law-country">Hong Kong</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Data Protection Law</td>
                    <td class="seers-cms-general-global-privacy-law-country">UAE</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(APPI) Act on the Protection of Personal Information</td>
                    <td class="seers-cms-general-global-privacy-law-country">Japan</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">New Zealand Privacy Act 2020</td>
                    <td class="seers-cms-general-global-privacy-law-country">New Zealand</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(PDPA) Thailand Personal Data Protection Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">Thailand</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Data Privacy Act of 2012</td>
                    <td class="seers-cms-general-global-privacy-law-country">Philippines</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Indian Personal Data Protection Bill (Draft)</td>
                    <td class="seers-cms-general-global-privacy-law-country">India</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(PIPA) South Korean Personal Information Protection Act</td>
                    <td class="seers-cms-general-global-privacy-law-country">South Korea</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(LFPDPPP) Mexican Federal Law on the Protection of Personal Data Held by Private Parties</td>
                    <td class="seers-cms-general-global-privacy-law-country">Mexico</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Kenya Data Protection Act 2019</td>
                    <td class="seers-cms-general-global-privacy-law-country">Kenya</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(LPPD) Law on Protection of Personal Data</td>
                    <td class="seers-cms-general-global-privacy-law-country">Turkey</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">General Data Protection Law</td>
                    <td class="seers-cms-general-global-privacy-law-country">Argentina</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">(FADP) Swiss Federal Act on Data Protection</td>
                    <td class="seers-cms-general-global-privacy-law-country">Switzerland</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Israel Protection of Privacy Law 5741-1981</td>
                    <td class="seers-cms-general-global-privacy-law-country">Israel</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Colombian Data Protection Law (Law 1581 of 2012)</td>
                    <td class="seers-cms-general-global-privacy-law-country">Colombia</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Russian Federal Law on Personal Data</td>
                    <td class="seers-cms-general-global-privacy-law-country">Russia</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Data Privacy Law</td>
                    <td class="seers-cms-general-global-privacy-law-country">Indonesia</td>
                </tr>
                <tr>
                    <td class="seers-cms-general-global-privacy-law" style="width: 82%;">Law on Cybersecurity</td>
                    <td class="seers-cms-general-global-privacy-law-country">Vietnam</td>
                </tr>
            </table>
        </div>
    </div>
</div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regulationDropdown = document.getElementById('dropdown-regulation-toggle');
            const regulationOptions = document.querySelectorAll('input[name="consent"]');

            regulationDropdown.addEventListener('change', function() {
                regulationOptions.forEach(option => {
                    if (option.checked) {
                        document.getElementById('selected-option').innerText = option.nextElementSibling.innerText;
                    }
                });
            });
            
            const languageDropdown = document.getElementById('dropdown-language-toggle');
            const languageOptions = document.querySelectorAll('input[name="language"]');

            languageDropdown.addEventListener('change', function() {
                languageOptions.forEach(option => {
                    if (option.checked) {
                        document.getElementById('selected-language').innerText = option.nextElementSibling.innerText;
                    }
                });
            });
        });
    </script>
</body>
</html>
