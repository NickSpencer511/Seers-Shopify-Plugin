"use strict";
/****************************
*  SOME COMMON SVG CONSTANT *
****************************/
var SVG_LOADER = '<svg viewBox="0 0 20 20" class="Polaris-Spinner Polaris-Spinner--colorInkLightest Polaris-Spinner--sizeSmall" aria-label="Loading" role="status"><path d="M7.229 1.173a9.25 9.25 0 1 0 11.655 11.412 1.25 1.25 0 1 0-2.4-.698 6.75 6.75 0 1 1-8.506-8.329 1.25 1.25 0 1 0-.75-2.385z"></path></svg>';
/****************************
 *  SOME COMMON SVG CONSTANT*
 ****************************/

/*
 * 
 * @param string $message
 * @returns {undefined} show flash message
 */
function flashNotice($message, $class) {
    $class = ($class != undefined) ? $class : '';
    var flashMsgHtml = '<div class="inline-flash-wrapper animated bounceInUp inline-flash-wrapper--is-visible ourFlashMsg"><div class="inline-flash ' + $class + '  "><p class="inline-flash__message">' + $message + '</p></div></div>';
    if ($('.ourFlashMsg').length) {
        $('.ourFlashMsg').remove();
    }
    $("body").append(flashMsgHtml);
    setTimeout(function () {
        if ($('.ourFlashMsg').length) {
            $('.ourFlashMsg').remove();
        }
    }, 3000);
}

/*
 * @param {string} $className
 * @returns {undefined} show loader
 */
// function loading_show($selector) {
//     $($selector).addClass("Polaris-Button--loading").html('<span class="Polaris-Button__Content"><span class="Polaris-Button__Spinner">' + SVG_LOADER + '</span><span>Loading</span></span>').fadeIn('fast').attr('disabled', 'disabled');
// }

function loading_show($selector, loadingText = '') {
    const showSpinner = loadingText.trim().toLowerCase() !== 'please wait...';

    $($selector)
        .addClass("Polaris-Button--loading")
        .html(`
            <span class="Polaris-Button__Content"
                  style="display: flex; align-items: center; justify-content: center; gap: 8px; min-width: 140px; padding: 5px;">
                ${showSpinner ? `
                    <span class="Polaris-Button__Spinner" style="display: inline-flex; align-items: center;">
                        ${SVG_LOADER}
                    </span>` : ''}
                ${loadingText ? `<span style="color: #000;">${loadingText}</span>` : ''}
            </span>
        `)
        .fadeIn('fast')
        .attr('disabled', 'disabled');
}

/**
 * @param {string} $className
 * @param {string} $buttonName
 * @returns {undefined} hide loader
 */
// function loading_hide($selector, $buttonName, $buttonIcon) {
//     if ($buttonIcon != undefined) {
//         $buttonIcon = '<span class="Polaris-Button__Icon"><span class="Polaris-Icon">' + $buttonIcon + '</span></span>'
//     } else {
//         $buttonIcon = '';
//     }

//     $($selector).removeClass("Polaris-Button--loading").html('<span class="Polaris-Button__Content">' + $buttonIcon + '<span>' + $buttonName + '</span></span>').removeAttr("disabled");
// }
function loading_hide($selector) {
    var originalHtml = $($selector).data('original-html');
    $($selector)
        .removeClass("Polaris-Button--loading")
        .html(originalHtml)
        .removeAttr("disabled");
}


$(document).on('click', '.close-message', function () {
    $('.remove-sucees-message').hide();
});



function removeCode(thisObj, data_key) {
    var current = $(thisObj);
    var btnText = current.html();
    loading_show(current);
    var deleteAjax = function deleteAjax(){
        loading_show(thisObj);
        $.ajax({
            url: siteapiactionurl,
            type: "post",
            dataType: "json",
            data: {method_name: 'remove_code', data_key: data_key, shop: shop},
            success: function (response) {
                if (response['result'] == 'success') {
                    flashNotice(response['msg']);
                    $('.remove-sucees-message').show();
                    $('.remove-sucees-message').css({'display': 'flex'});
                }
                loading_hide(current, btnText);
            },
            error: function () {
                flashNotice('Please try again!','error');
            }
        });
    }
    
    if(mode == 'live'){
        ShopifyApp.Modal.confirm({
            title: 'Uninstall',
            message: 'Are you sure you want to remove? This action cannot be reversed.',
            okButton: 'Delete',
            cancelButton: 'Cancel',
            style: "danger"
        }, function (result) {
            if (result) {
                $('.ui-button.close-modal.btn-destroy-no-hover').addClass("ui-button ui-button--destructive js-btn-loadable is-loading disabled");
                deleteAjax();
            }
        });
    }else{
        var r = confirm('Are you sure you want to remove? This action cannot be reversed.');
        if (r == true) {
            deleteAjax();
        }
    }
}

function toggleCheckedVal(switchval,userdomain,useremail,datakey){
    $(".loadingoverlay").css("display", "flex");
        $.ajax({
            url: siteapiactionurl,
            type: "post",
            dataType: "json",
            data: {method_name: 'change_appStatus', data_status: switchval, user_name:userdomain, user_email:useremail, data_key:datakey, shop: shop},
            beforeSend: function(){
                $('#myonoffswitch').prop('disabled', true);
            },
            complete: function(){
                $(".loadingoverlay").hide();
                $('#myonoffswitch').prop('disabled', false);
            },
            success: function (response) {
                if (response['result'] == 'success') {
                     $(".enable-banner").html(response['msg']); 
                     $('#user_key').val(response['key']);

                     if (response["errormsg"] !== undefined){
                        flashNotice(response["errormsg"],'error');
                     }
                }
            },
            error: function () {
                flashNotice('Please try again!','error');
            }
        });

}
window.userData = {};

function getUserData(switchval, userdomain, useremail, datakey, token) {
    $(".loadingoverlay").css("display", "flex");

    const payload = {
        method_name: 'get_user_data',
        domain: userdomain, 
        email: useremail,
        platform: 'shopify',
        lang: 'en_US',
        data_key: datakey,
        shop: 'seers-cookie-consent.myshopify.com',
        token: token
    };

    // console.log("Payload Sent to API:", payload);

    $.ajax({
        url: siteapiactionurl,
        type: "post",
        dataType: "json", 
        data: payload,
        success: function(response) {
            if (response.status === 'success') {
                // console.log("API Data: ", response.data);

                window.userData = response.data;

                $(document).trigger('userDataReady', [window.userData]);
            } else {
                // console.log("Error: ", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error: ", xhr.responseText);
            console.log("Status: ", status);
            console.log("Error: ", error);
        },
        complete: function() {
            $(".loadingoverlay").css("display", "none");
        }
    });
}


function updateUserData(switchval, userdomain, useremail, datakey, data, token) {
    $(".loadingoverlay").css("display", "flex");

    const payload = {
        method_name: 'update_user_data',
        domain: userdomain, 
        email: useremail,
        platform: 'shopify',
        lang: 'en_US',
        data_key: datakey,
        shop: 'seers-cookie-consent.myshopify.com',
        data: data,
        token: token
    };

    // console.log("Payload Sent to API:", payload);

    $.ajax({
        url: siteapiactionurl,
        type: "post",
        dataType: "json", 
        data: payload,
        beforeSend: function() {
            // console.log("AJAX request started...");
        },
        complete: function() {
            $(".loadingoverlay").hide();
        },
        success: function(response) {
            // console.log("AJAX success:", response);

            if (response.status === 'success') {
                // window.userData = response.data; 
                // $(document).trigger('userDataReady', [window.userData]);
                flashNotice(response.message || "Data updated successfully!");
            } else {
                flashNotice(response.message || "An error occurred while updating data", "error");
            }
        },
        error: function(xhr, status, error) {
            console.log("AJAX Error:", error);
            flashNotice("An error occurred while updating data", "error");
        },
       
    });

}


//  $(document).off("click", ".customizeSeersBtn").on("click", ".customizeSeersBtn", function (e) {
//     e.preventDefault();
    
//     var domain = $("#user_doamin").val();
//     var email = $("#user_email").val();
//     var $btn = $(this);
//     var shop = window.shop || '';
    
//     loading_show($btn);

//     $.ajax({
//         url: siteapiactionurl,
//         type: "POST",
//         dataType: "json",
//         data: {
//             method_name: "get_customize_redirect_url",
//             domain: domain,
//             email: email,
//             shop: shop
//         },
//         success: function (res) {
//             if (res.status === 'success') {
//                 window.open(res.redirect_url, '_blank');
//             } else {
//                 flashNotice(res.message, "error");
//             }
//         },
//         error: function () {
//             flashNotice("Something went wrong", "error");
//         },
//         complete: function () {
//             loading_hide($btn, "Customize in CMP");
//         }
//     });
// });
$(document).off("click", ".customizeSeersBtn").on("click", ".customizeSeersBtn", function (e) {
    e.preventDefault();

    var domain = $("#user_doamin").val();
    var email = $("#user_email").val();
    var $btn = $(this);
    var shop = window.shop || '';
    var tabName = $btn.data('tab') || '';
    var subTabName = $btn.data('subtab') || '';

    $btn.data('original-html', $btn.html());

    var loadingText = $btn.data('loading-text') || ''; 
    loading_show($btn, loadingText);

    $.ajax({
        url: siteapiactionurl,
        type: "POST",
        dataType: "json",
        data: {
            method_name: "get_customize_redirect_url",
            domain: domain,
            email: email,
            shop: shop,
            tab_name: tabName,
            sub_tab_name: subTabName
        },
        success: function (res) {
            if (res.status === 'success') {
                window.open(res.redirect_url, '_blank');
            } else {
                flashNotice(res.message, "error");
            }
        },
        error: function () {
            flashNotice("Something went wrong", "error");
        },
        complete: function () {
            loading_hide($btn); 
        }
    });
});



