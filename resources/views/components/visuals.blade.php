
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Banner Settings</title>
    <link rel="stylesheet" href="{{ asset('css/visuals.css') }}">
    <script type="text/javascript" src="{{ secure_asset('js/custom.js') }}"></script>
</head>

<body>
    <div class="seers-cms-visual-container">
        <div class="seers-cms-visual-settings">
            <p class="seers-cms-visual-font-setting seers-cms-visual-heading">Accept Button &nbsp; <span class="tooltip"
                    data-title="This text will appear on the button that the user can click to “accept” the storage of their terminal equipment."
                    style="font-size:20px;"><img class="seers-cms-visuals-info-icon"
                        src="{{ asset('images/info icon.png') }}" alt="info-icon"></span></p>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-font-setting">Text & Colour&nbsp; <span class="tooltip"
                        data-title="Edit the text & its colour of “Accept” button" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <input class="seers-cms-visual-btn seers-cms-visual-disable seers-cms-visual-white-color"
                        type="text" name="accept_btn_text" id="accept_btn_text" placeholder="Allow All"
                        value="" {{-- onchange="setColorDummyButton(this, 'value', 'accept_all')" --}} style="text-align: center;">

                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="agree_text_color" id="agree_text_color" value=""
                            class="seers-banner-custom-color" {{-- onchange="setColorDummyButton(this, 'text', 'accept_all')" --}}
                            style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}" alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>
            </div>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-font-setting">Button Colour&nbsp; <span class="tooltip"
                        data-title="Change the “Accept” button colour" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">

                    <button class="seers-cms-visual-btn seers-cms-visual-color seers-cms-visual-blue-color"
                        id="accept_btn" style="background-color: #0061fe; color: #fff">
                    </button>



                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="agree_btn_color" id="agree_btn_color" value=""
                            class="seers-banner-custom-color" {{-- onchange="setColorDummyButton(this, 'background', 'accept_all')" --}}
                            style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}" alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>
            </div>


            <div class="seers-cms-visuals-settings-setting">
                <p class="seers-cms-visual-font-setting seers-cms-visual-heading">Reject Button&nbsp; <span
                        class="tooltip"
                        data-title="This text will appear on the button that the user can click to “decline” the storage of cookies on their terminal equipment."
                        style="font-size:20px;"><img class="seers-cms-visuals-info-icon"
                            src="{{ asset('images/info icon.png') }}" alt="info-icon"></span></p>
                <div class="seers-cms-visual-options seers-cms-visuals-settings-input-field">
                    <input type="checkbox" class="seers-paid-feature-opener" id="allow_reject_button"
                        name="allow_reject_button">
                </div>
            </div>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-font-setting">Text & Colour&nbsp; <span class="tooltip"
                        data-title="Edit the text & its colour of “Reject” button" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <!-- <button class="seers-cms-visual-btn seers-cms-visual-disable seers-cms-visual-white-color">Disable All</button> -->
                    <input class="seers-cms-visual-btn seers-cms-visual-disable seers-cms-visual-white-color"
                        type="text" name="reject_btn_text" id="reject_btn_text" placeholder="Disable All"
                        value="" {{-- onchange="setColorDummyButton(this, 'value', 'reject_all')" --}} style="text-align: center;">
                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="disagree_text_color" id="disagree_text_color" value=""
                            class="seers-banner-custom-color" {{-- onchange="setColorDummyButton(this, 'text', 'reject_all')" --}}
                            style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}"
                            alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>

            </div>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-font-setting">Button Colour&nbsp; <span class="tooltip"
                        data-title="Change the “Reject” button colour" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <!-- <button class="seers-cms-visual-btn seers-cms-visual-color seers-cms-visual-blue-color">Disable All</button> -->
                    <?php
                    $disagree_text_color = '#fff';
                    $disagree_button_color = '#3b6ef8';
                    $disagree_button_text = 'Disable All';
                    ?>

                    <button class="seers-cms-visual-btn seers-cms-visual-color seers-cms-visual-blue-color"
                        style="background-color: #0061fe; color: #fff;" id="reject_btn">
                        Disable All
                    </button>
                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="disagree_btn_color" id="disagree_btn_color" value=""
                            class="seers-banner-custom-color" {{-- onchange="setColorDummyButton(this, 'background', 'reject_all')" --}}
                            style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}"
                            alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>
            </div>

            <p class="seers-cms-visual-heading seers-cms-visual-font-setting">Cookie Settings Button&nbsp; <span
                    class="tooltip"
                    data-title="This text will expand the cookie banner downward, showing the categories of cookies and details of each cookie within the respective category."
                    style="font-size:20px;"><img class="seers-cms-visuals-info-icon"
                        src="{{ asset('images/info icon.png') }}" alt="info-icon"></span></p>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-font-setting">Text & Colour&nbsp; <span class="tooltip"
                        data-title="Edit the text & its colour of “Cookie Setting”" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <!-- <button class="seers-cms-visual-btn seers-cms-visual-color seers-cms-visual-white-color">Preference</button> -->
                    <input class="seers-cms-visual-btn seers-cms-visual-disable seers-cms-visual-white-color"
                        type="text" name="setting_btn_text" id="setting_btn_text" placeholder="Preference"
                        value="" {{-- onchange="setColorDummyButton(this, 'value', 'setting_pref')" --}} style="text-align: center;">
                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="preferences_text_color" id="preferences_text_color"
                            value="" class="seers-banner-custom-color" {{-- onchange="setColorDummyButton(this, 'text', 'setting_pref')" --}}
                            style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}"
                            alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>
            </div>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-font-setting">Button Colour&nbsp; <span class="tooltip"
                        data-title="Change the “Cookie Setting” button colour" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <!-- <button class="seers-cms-visual-btn seers-cms-visual-color seers-cms-visual-blue-color">Preference</button> -->
                    <?php
                    $setting_text_color = '#3b6ef8';
                    $setting_button_color = '#fff';
                    $setting_button_text = 'Preference';
                    ?>

                    <button class="seers-cms-visual-btn seers-cms-visual-color seers-cms-visual-blue-color"
                        style="background-color: #fff color: #0061fe" id="setting_btn">
                    </button>
                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="setting_btn_color" id="setting_btn_color" value=""
                            class="seers-banner-custom-color" {{-- onchange="setColorDummyButton(this, 'background', 'setting_pref')" --}}
                            style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}"
                            alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>
            </div>


            <div class="seers-cms-visual-setting-group-res">
                <p class="seers-cms-visual-heading seers-cms-visual-font-setting">Banner Text&nbsp; <span
                        class="tooltip" data-title="This text will appear on the banner" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></p>
                <div class="seers-cms-visual-options-res">
                    <textarea class="seers-cms-visual-banner-text" id="banner-text-body" rows="6"></textarea>
                </div>
            </div>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-heading seers-cms-visual-font-setting">Banner Text Colour &nbsp; <span
                        class="tooltip" data-title="Change the banner text colour" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="body_color" id="body_color" value=""
                            class="seers-banner-custom-color" style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}"
                            alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>
                </div>
            </div>
            <div class="seers-cms-visual-setting-group">
                <label class="seers-cms-visual-heading seers-cms-visual-font-setting">Banner Background Colour&nbsp;
                    <span class="tooltip" data-title="Change the banner background colour"
                        style="font-size:20px;"><img class="seers-cms-visuals-info-icon"
                            src="{{ asset('images/info icon.png') }}" alt="info-icon"></span></label>
                <div class="seers-cms-visual-options">
                    <div style="position: relative; display: inline-block;">
                        <input type="color" name="banner_bg_color" id="banner_bg_color" value=""
                            class="seers-banner-custom-color" style="margin: 0px; width: 70px; height: 35px;">
                        <img class="seers-cms-visual-img-edit" src="{{ asset('images/edit-grey.png') }}"
                            alt="edit"
                            style="position: absolute; right: 26px; top: 45%; transform: translateY(-50%); pointer-events: none;">
                    </div>

                </div>
            </div>
            <div class="seers-cms-visual-setting-group-res">
                <p class="seers-cms-visual-font-setting">Button Style&nbsp; <span class="tooltip"
                        data-title="Select the shape of the buttons that appear on banner"
                        style="font-size:20px;"><img class="seers-cms-visuals-info-icon"
                            src="{{ asset('images/info icon.png') }}" alt="info-icon"></span></p>
                <div class="seers-cms-visual-options-res seers-cms-visual-button-style">
                    <!-- <button class="seers-cms-visual-btn seers-cms-visual-style seers-cms-visual-default">Default</button>
                <button class="seers-cms-visual-btn seers-cms-visual-style seers-cms-visual-flat">Flat</button>
                <button class="seers-cms-visual-btn seers-cms-visual-style seers-cms-visual-rounded">Rounded</button>
                <button class="seers-cms-visual-btn seers-cms-visual-style seers-cms-visual-stroke">Stroke</button> -->
                    <div class="seers-pr btn-group" style="flex-basis: 100%;" role="group">
                        <button class="seers-select-btn btn-default" style="width:100px !important;" type="button"
                            id="cbtn_default" name="btn_style_default">Default</button>
                        <button class="seers-select-btn btn-flat" style="width:100px !important;" type="button"
                            id="cbtn_flat">Flat</button>
                        <button class="seers-select-btn btn-round" style="width:100px !important;" type="button"
                            id="cbtn_rounded">Rounded</button>
                        <button class="seers-select-btn btn-stroke" style="width:100px !important;" type="button"
                            id="cbtn_stroke">Stroke</button>
                        <?php ?>
                    </div>
                </div>
            </div>
            <div class="seers-cms-visual-setting-group-res">
                <p class=" seers-cms-visual-font-setting">Display Style&nbsp; <span class="tooltip"
                        data-title="Choose the display style of your banner" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></p>
                <div class="seers-cms-visual-options-res seers-cms-visual-button-style">

                    <div class="seers-pr btn-group-display" style="flex-basis: 100%;" role="group">
                        <button class="seers-select-display-btn btn-banner" style="width:136px !important;"
                            type="button" id="bar_banners" name="btn_style_default"><img class="seers-banner-icons"
                                src="{{ asset('images/BannerNotActive.svg') }}" />Banner</button>
                        <button class="seers-select-display-btn btn-modal" style="width:136px !important;"
                            type="button" id="modal_banners"><img class="seers-banner-icons"
                                src="{{ asset('images/ModalNotActive.svg') }}" />Modal</button>
                        <button class="seers-select-display-btn btn-tooltip" style="width:136px !important;"
                            type="button" id="tooltip_banners"><img class="seers-banner-icons"
                                src="{{ asset('images/tooltipNotActive.svg') }}" /></span>Tooltip</button>
                        <?php ?>

                    </div>
                </div>
            </div>
            <div class="seers-cms-visual-setting-group-res display-position">
                <p class="seers-cms-visual-font-setting">Position&nbsp; <span class="tooltip"
                        data-title="Choose  the position or placement of the banner" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></p>
                <div class="seers-cms-visual-options-res seers-cms-visual-button-style">

                    <div class="seers-pr btn-group-position" style="display:ruby; flex-basis: 100%;" role="group">
                        <button class="seers-select-display-btn btn-top" style="width:136px !important;"
                            type="button" id="seers-cmp-top-bar" name="btn_style_default"><img
                                class="seers-banner-icons"
                                src="{{ asset('images/BannerNotActive.svg') }}" />Top</button>
                        <button class="seers-select-display-btn btn-bottom" style="width:136px !important;"
                            type="button" id="seers-cmp-banner-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/BannerNotActive.svg') }}" />Bottom</button>
                        <button class="seers-select-display-btn btn-bottomleft" style="width:136px !important;"
                            type="button" id="seers-cmp-left-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/tooltipNotActive.svg') }}" />Bottom Left</button>
                        <button class="seers-select-display-btn btn-bottomright" style="width:136px !important;"
                            type="button" id="seers-cmp-right-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/tooltipNotActive.svg') }}" />Bottom Right</button>
                        <button class="seers-select-display-btn btn-topleft" type="button"
                            id="seers-cmp-top-left-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/tooltipNotActive.svg') }}" />Top Left</button>
                        <button class="seers-select-display-btn btn-topright" type="button"
                            id="seers-cmp-top-right-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/tooltipNotActive.svg') }}" />Top Right</button>
                        <button class="seers-select-display-btn btn-hangingtop" style="width:136px !important;"
                            type="button" id="seers-cmp-top-hanging-bar" name="btn_style_default"><img
                                class="seers-banner-icons" src="{{ asset('images/BannerNotActive.svg') }}" />Top
                            Hanging</button>
                        <button class="seers-select-display-btn btn-hangingbottom" style="width:136px !important;"
                            type="button" id="seers-cmp-banner-hanging-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/BannerNotActive.svg') }}" />Bottom Hanging</button>
                        <button class="seers-select-display-btn btn-preference" style="width:136px !important;"
                            type="button" id="seers-cmp-banner-preference-bar" name="btn_style_default"><img
                                class="seers-banner-icons" src="{{ asset('images/BannerNotActive.svg') }}" />First
                            Preference</button>
                        <button class="seers-select-display-btn btn-hangingpreference" style="width:136px !important;"
                            type="button" id="seers-cmp-preference-bottom-hanging-bar"><img
                                class="seers-banner-icons" src="{{ asset('images/BannerNotActive.svg') }}" />Hang
                            Preference</button>
                        {{-- <button
                                                class="seers-select-display-btn btn-universal"
                                                style="width:136px !important;" type="button" id="seers-cmp-preference-universal-bar"><img class="seers-banner-icons" src="{{ asset('images/ModalNotActive.svg') }}"/>Universal</button> --}}
                        <button class="seers-select-display-btn btn-modal-bar" style="width:136px !important;"
                            type="button" id="seers-cmp-middle-bar"><img class="seers-banner-icons"
                                src="{{ asset('images/ModalNotActive.svg') }}" />Standard</button>

                    </div>
                </div>
            </div>

            <div class="seers-cms-visual-setting-group-res">
                <p class="seers-cms-visual-font-setting">Cookie Preferences Logo&nbsp; <span class="tooltip"
                        data-title="Add logo of your business" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></p>
                <div class="seers-cms-visual-options-res customizeSeersBtn"  data-tab="Visuals">
                    <div class="seers-cms-visual-logo">Logo will show here</div>
                    <button
                        class="seers-cms-visual-btn seers-cms-visual-upload"
                        name="seersbannerlogo">Upload</button>
                </div>
            </div>


            <div class="seers-cms-visual-setting-group-res">
                <p class="seers-cms-visual-font-setting">Choose Font & Size&nbsp; <span class="tooltip"
                        data-title="Change the font style and size of your text" style="font-size:20px;"><img
                            class="seers-cms-visuals-info-icon" src="{{ asset('images/info icon.png') }}"
                            alt="info-icon"></span></p>
                <div class="seers-cms-visual-options-res">

                    <div class="seers-pr">
                        <select class="seers-input fm  seers-cms-visuals-fonts" id="seers_fonts_fm"
                            name="seers_fonts_fm">
                            <option value="arial">
                                Arial</option>
                            <option value="cursive">
                                Cursive</option>
                            <option value="fantasy">
                                Fantasy</option>
                            <option value="monospace">
                                Monospace</option>
                            <option value="sans-serif">
                                Sans Serif</option>
                            <option value="serif">
                                Serif</option>
                            <option value="none">
                                None</option>
                            <option value="inherit">
                                Default</option>
                        </select>
                        <select class="seers-input fs  seers-cms-visuals-fonts-size" id="seers_fonts_fs"
                            name="seers_fonts_fs">
                            <option value="8">
                                8</option>
                            <option value="10">
                                10</option>
                            <option value="12">
                                12</option>
                            <option value="14">
                                14</option>
                            <option value="16">
                                16</option>
                        </select>
                    </div>
                </div>
            </div>


            <div class="seers-cms-visual-powered-by">
                <p class="seers-cms-visual-font-setting">Powered by Seers &nbsp;<span class="tooltip"
                        data-title="Show or hide powered by Seers on preference centre of banner"
                        style="font-size:20px;"><img class="seers-cms-visuals-info-icon"
                            src="{{ asset('images/info icon.png') }}" alt="info-icon"></span></p>
                <div class="seers-cms-visual-options customizeSeersBtn" style="cursor: pointer"  data-tab="Visuals">
                    <span class="seers-cms-visual-premium"
                        name="seerspoweredby">Apply</span>
                </div>
            </div>
        </div>
        <hr class="seers-cms-visual-space">
        <button class="seers-cms-visual-save-btn" id="setting_save_new">
            <span id="save_changes_data">Save Changes</span>
        </button>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function() {
            // var currentUser = <?php echo json_encode($current_user); ?>;
            // console.log(currentUser['data_key']);
            // var data_key = currentUser['data_key'];
            // var token = currentUser['token'];
            const bannerPositions = [
                'seers-cmp-top-bar',
                'seers-cmp-banner-bar',
                'seers-cmp-top-hanging-bar',
                'seers-cmp-banner-hanging-bar',
                'seers-cmp-banner-preference-bar',
                'seers-cmp-preference-bottom-hanging-bar'
            ];
            const modalPositions = ['seers-cmp-middle-bar'];
            const tooltipPositions = [
                'seers-cmp-left-bar',
                'seers-cmp-right-bar',
                'seers-cmp-top-left-bar',
                'seers-cmp-top-right-bar'
            ];

            function initializeButtons() {
                [...bannerPositions, ...modalPositions, ...tooltipPositions].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        if (bannerPositions.includes(id)) element.setAttribute('data-group', 'banner');
                        if (modalPositions.includes(id)) element.setAttribute('data-group', 'modal');
                        if (tooltipPositions.includes(id)) element.setAttribute('data-group', 'tooltip');
                        element.style.display = 'none';
                    }
                });
            }
            initializeButtons();


            $('.btn-group-position button').on('click', function() {
                const selectedButtonId = $(this).attr('id');
                $('.btn-group-position button').removeClass('selected');
                $(this).addClass('selected');

                if (bannerPositions.includes(selectedButtonId)) showElements(bannerPositions);
                else if (modalPositions.includes(selectedButtonId)) showElements(modalPositions);
                else if (tooltipPositions.includes(selectedButtonId)) showElements(tooltipPositions);

                showSelectedGroupButtons();
            });

            function showSelectedGroupButtons() {
                const selectedButton = document.querySelector('.selected[data-group]');
                if (selectedButton) {
                    const selectedGroup = selectedButton.getAttribute('data-group');
                    document.querySelectorAll('[data-group]').forEach(button => {
                        if (button.getAttribute('data-group') === selectedGroup) {
                            button.style.display = 'inline-block';
                        } else {
                            button.style.display = 'none';
                        }
                    });
                }
            }


            document.getElementById('bar_banners').addEventListener('click', function() {
                showElements(bannerPositions);
                selectButton('bar_banners');
            });

            document.getElementById('modal_banners').addEventListener('click', function() {
                showElements(modalPositions);
                selectButton('modal_banners');
            });

            document.getElementById('tooltip_banners').addEventListener('click', function() {
                showElements(tooltipPositions);
                selectButton('tooltip_banners');
            });

            function selectButton(selectedButtonId) {
                document.querySelectorAll('#bar_banners, #modal_banners, #tooltip_banners').forEach(btn => {
                    btn.classList.remove('selected');
                });

                const selectedButton = document.getElementById(selectedButtonId);
                if (selectedButton) {
                    selectedButton.classList.add('selected');
                }
            }


            function showElements(positionArray) {
                const allPositions = [...bannerPositions, ...modalPositions, ...tooltipPositions];
                allPositions.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.style.display = 'none';
                    }
                });

                positionArray.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.style.display = 'block';
                    }
                });
            }

            function hideElements(positionArray) {
                positionArray.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.style.display = 'none';
                    }
                });
            }

            function checkAndSelectParent(positionArray, parentId) {
                let isSelected = false;
                positionArray.forEach(id => {
                    const element = document.getElementById(id);
                    if (element && element.classList.contains('selected')) {
                        isSelected = true;
                    }
                });

                const parentElement = document.getElementById(parentId);
                if (parentElement) {
                    if (isSelected) {
                        parentElement.classList.add('selected');
                        hideElements([...modalPositions, ...tooltipPositions]);
                    } else {
                        parentElement.classList.remove('selected');
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const allPositions = [...bannerPositions, ...modalPositions, ...tooltipPositions];
                allPositions.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.style.display = 'none';
                    }
                });
            });
            // if (data_key) {
                $(document).on('userDataReady', function(event, data) {
                    // console.log("Data received in Blade file:", data);


                    $('#accept_btn_text').val(data.bannersettings.btn_agree_title || 'Accept All');
                    $('#accept_btn').text(data.bannersettings.btn_agree_title || 'Accept All');
                    $('#agree_text_color').val(data.bannersettingsbanners.agree_text_color || '#000');
                    $('#agree_btn_color').val(data.bannersettingsbanners.agree_btn_color || '#ffffff');
                    $('#accept_btn_text').on('input', function() {
                        const newText = $(this).val();
                        data.bannersettings.btn_agree_title = newText;
                        $('#accept_btn').text(newText);
                        // console.log('Updated accept button text:', data.bannersettings
                        //     .btn_agree_title);
                    });

                    $('#agree_text_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.agree_text_color = newColor;
                        $('#accept_btn').css('color', newColor);
                        // console.log('Updated accept button text color:', data.bannersettingsbanners
                        //     .agree_text_color);
                    });

                    $('#agree_btn_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.agree_btn_color = newColor;
                        $('#accept_btn').css('background-color', newColor);
                        // console.log('Updated accept button background color:', data
                        //     .bannersettingsbanners.agree_btn_color);
                    });
                    document.getElementById('accept_btn').style.setProperty('background-color', data
                        .bannersettingsbanners.agree_btn_color, 'important');
                    document.getElementById('accept_btn').style.setProperty('color', data
                        .bannersettingsbanners
                        .agree_text_color, 'important');


                    $('#reject_btn_text').val(data.bannersettings.btn_disagree_title || 'Accept All');
                    $('#reject_btn').text(data.bannersettings.btn_disagree_title || 'Accept All');
                    $('#disagree_text_color').val(data.bannersettingsbanners.disagree_text_color || '#000');
                    $('#disagree_btn_color').val(data.bannersettingsbanners.disagree_btn_color || '#fff');

                    $('#reject_btn_text').on('input', function() {
                        const newText = $(this).val();
                        data.bannersettings.btn_disagree_title = newText;
                        $('#reject_btn').text(newText);
                        // console.log('Updated reject button text:', data.bannersettings
                        //     .btn_disagree_title);
                    });

                    $('#disagree_text_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.disagree_text_color = newColor;
                        $('#reject_btn').css('color', newColor);
                        // console.log('Updated reject button text color:', data.bannersettingsbanners
                        //     .disagree_text_color);
                    });

                    $('#disagree_btn_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.disagree_btn_color = newColor;
                        $('#reject_btn').css('background-color', newColor);
                        // console.log('Updated reject button background color:', data
                        //     .bannersettingsbanners.disagree_btn_color);
                    });
                    document.getElementById('reject_btn').style.setProperty('background-color', data
                        .bannersettingsbanners.disagree_btn_color, 'important');
                    document.getElementById('reject_btn').style.setProperty('color', data
                        .bannersettingsbanners
                        .disagree_text_color, 'important');


                    $('#setting_btn_text').val(data.bannersettings.btn_preference_title || 'Preference');
                    $('#setting_btn').text(data.bannersettings.btn_preference_title || 'Preference');
                    $('#preferences_text_color').val(data.bannersettingsbanners.preferences_text_color ||
                        '#000');
                    $('#setting_btn_color').val(data.bannersettingsbanners.preferences_btn_color || '#fff');
                    $('#setting_btn_text').on('input', function() {
                        const newText = $(this).val();
                        data.bannersettings.btn_preference_title = newText;
                        $('#setting_btn').text(newText);
                        // console.log('Updated settings button text:', data.bannersettings
                        //     .btn_preference_title);
                    });

                    $('#preferences_text_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.preferences_text_color = newColor;
                        $('#setting_btn').css('color', newColor);
                        // console.log('Updated settings button text color:', data
                        //     .bannersettingsbanners
                        //     .preferences_text_color);
                    });

                    $('#setting_btn_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.preferences_btn_color = newColor;
                        $('#setting_btn').css('background-color', newColor);
                        // console.log('Updated settings button background color:', data
                        //     .bannersettingsbanners.preferences_btn_color);
                    });
                    document.getElementById('setting_btn').style.setProperty('background-color', data
                        .bannersettingsbanners.preferences_btn_color, 'important');
                    document.getElementById('setting_btn').style.setProperty('color', data
                        .bannersettingsbanners
                        .preferences_text_color, 'important');

                    $('#banner-text-body').val(data.bannersettings.body ||
                        'We use cookies to ensure you get the best experience')

                    $('#body_color').val(data.bannersettingsbanners.body_text_color || '#000');
                    $('#banner_bg_color').val(data.bannersettingsbanners.banner_bg_color || '#fff');
                    $('#banner-text-body').on('input', function() {
                        const newText = $(this).val();
                        data.bannersettings.body = newText;
                        $('#banner-text').text(newText);
                        // console.log('Updated banner text:', data.bannersettings.body);
                    });

                    $('#body_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.body_text_color = newColor;
                        $('#banner-text').css('color', newColor);
                        // console.log('Updated banner body text color:', data.bannersettingsbanners
                        //     .body_text_color);
                    });

                    $('#banner_bg_color').on('change', function() {
                        const newColor = $(this).val();
                        data.bannersettingsbanners.banner_bg_color = newColor;
                        $('#banner').css('background-color', newColor);
                        // console.log('Updated banner background color:', data.bannersettingsbanners
                        //     .banner_bg_color);
                    });

                    const button = document.getElementById(data.bannersettingsbanners.button_type);
                    if (button) {
                        // console.log(button);
                        button.style.setProperty('background-color', '#6cc04a', 'important');
                    }


                    $('.seers-select-btn').on('click', function() {
                        const selectedButtonId = $(this).attr('id');
                        data.bannersettingsbanners.button_type = selectedButtonId;
                        $('.seers-select-btn').each(function() {
                            this.style.removeProperty('background-color');
                        });
                        document.getElementById(selectedButtonId).style.setProperty(
                            'background-color',
                            '#6cc04a', 'important');
                        // console.log('Updated button_type:', data.bannersettingsbanners.button_type);
                    });

                    const fontSize = data.bannersettingsbanners.font_size;
                    $('#seers_fonts_fs').val(fontSize);
                    $('#seers_fonts_fs').on('change', function() {
                        const selectedFontSize = $(this).val();
                        data.bannersettingsbanners.font_size = selectedFontSize;
                        // console.log('Updated font size:', data.bannersettingsbanners.font_size);
                    });

                    const fontStyle = data.bannersettingsbanners.font_style;
                    $('#seers_fonts_fm').val(fontStyle);
                    $('#seers_fonts_fm').on('change', function() {
                        const selectedFontStyle = $(this).val();
                        data.bannersettingsbanners.font_style = selectedFontStyle;
                        // console.log('Updated font style:', data.bannersettingsbanners.font_style);
                    });

                    const isAllowRejectChecked = data.bannersettings.allow_reject === 1;
                    $('#allow_reject_button').prop('checked', isAllowRejectChecked);
                    $('#allow_reject_button').on('change', function() {
                        const isAllowRejectChecked = $(this).is(':checked');
                        data.bannersettings.allow_reject = isAllowRejectChecked ? 1 : 0;
                        // console.log('Updated allow reject:', data.bannersettings.allow_reject);
                    });

                    const position = data.bannersettingsbanners.position;
                    if (position) {

                        const selectedButton = $('#' + position);
                        selectedButton.addClass('selected');
                        // console.log('hello');

                        const selectedGroup = selectedButton.attr('data-group');

                        const groupToParentButtonId = {
                            banner: 'bar_banners',
                            modal: 'modal_banners',
                            tooltip: 'tooltip_banners'
                        };

                        const parentButtonId = groupToParentButtonId[selectedGroup];
                        if (parentButtonId) {
                            $('#bar_banners, #modal_banners, #tooltip_banners').removeClass('selected');
                            $('#' + parentButtonId).addClass('selected');
                        }
                    }

                    $('.btn-group-position button').on('click', function() {
                        $('.btn-group-position button').removeClass('selected');
                        $(this).addClass('selected');
                        const selectedButtonId = $(this).attr('id');
                        data.bannersettingsbanners.position = selectedButtonId;
                        // console.log('Updated position:', data.bannersettingsbanners.position);
                    });

                    document.getElementById('bar_banners').addEventListener('click', function() {
                        updateSelection(bannerPositions, 'seers-cmp-banner-bar');
                        data.bannersettingsbanners.position = 'seers-cmp-banner-bar';
                    });

                    document.getElementById('modal_banners').addEventListener('click', function() {
                        updateSelection(modalPositions, 'seers-cmp-middle-bar');
                        data.bannersettingsbanners.position = 'seers-cmp-middle-bar';
                    });

                    document.getElementById('tooltip_banners').addEventListener('click', function() {
                        updateSelection(tooltipPositions, 'seers-cmp-left-bar');
                        data.bannersettingsbanners.position = 'seers-cmp-left-bar';
                    });

                    function updateSelection(positionArray, selectedId) {
                        // console.log('updated');
                        const allPositions = [...bannerPositions, ...modalPositions, ...tooltipPositions];
                        allPositions.forEach(id => {
                            const element = document.getElementById(id);
                            if (element) {
                                element.classList.remove('selected');
                            }
                        });

                        const selectedElement = document.getElementById(selectedId);
                        if (selectedElement) {
                            selectedElement.classList.add('selected');
                        }
                    }
                    document.addEventListener('DOMContentLoaded', function() {
                        // console.log("Loaded");
                        const allPositions = [...bannerPositions, ...modalPositions, ...
                            tooltipPositions
                        ];
                        allPositions.forEach(id => {
                            const element = document.getElementById(id);
                            if (element) {
                                element.style.display = 'none';
                            }
                        });
                    });


                    document.getElementById('save_changes_data').addEventListener('click', function() {
                        var currentUser = <?php echo json_encode($current_user); ?>;
                        var token = currentUser['token'];
                        var user_doamin = $('#user_doamin').val();
                        var user_email = $('#user_email').val();
                        var data_key = $('#user_key').val();

                        updateUserData(switchStatus, user_doamin, user_email, data_key, data,
                        token);

                    });

                    const selectedButton = document.querySelector('.selected[data-group]');
                    if (selectedButton) {
                        const selectedGroup = selectedButton.getAttribute('data-group');
                        document.querySelectorAll('[data-group]').forEach(button => {
                            if (button.getAttribute('data-group') === selectedGroup) {
                                button.style.display = 'inline-block';
                            } else {
                                button.style.display = 'none';
                            }
                        });
                    }

                });
            // }

        });
    </script>




</body>

</html>
