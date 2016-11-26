/**
 * jQuery Cookie plugin
 *
 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
jQuery.noConflict();
jQuery.cookie = function (key, value, options) {

    // key and at least value given, set cookie...
    if (arguments.length > 1 && String(value) !== "[object Object]") {
        options = jQuery.extend({}, options);

        if (value === null || value === undefined) {
            options.expires = -1;
        }

        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }

        value = String(value);

        return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? value : encodeURIComponent(value),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }

    // key and possibly options given, get cookie...
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};

jQuery(window).load( function() {
	// add the loaded class to <body> with small delay to avoid low framerate	
	setTimeout(function() {
		jQuery('body').addClass('loaded');
	}, 500);
	
	// style area
	if(jQuery('#gkStyleArea')){
		jQuery('#gkStyleArea').find('a').each(function(i,element){
			jQuery(element).click(function(e){
	            e.preventDefault();
	            e.stopPropagation();
				changeStyle(i+1);
			});
		});
	}
	
	// font-size switcher
	if(jQuery('#gkTools') && jQuery('#gkMainbody')) {
		var current_fs = 100;
		
		jQuery('#gkMainbody').css('font-size', current_fs+"%");
		
		jQuery('#gkToolsInc').click(function(e){ 
			e.stopPropagation();
			e.preventDefault(); 
			if(current_fs < 150) {  
				jQuery('#gkMainbody').animate({ 'font-size': (current_fs + 10) + "%"}, 200); 
				current_fs += 10; 
			} 
		});
		jQuery('#gkToolsReset').click(function(e){ 
			e.stopPropagation();
			e.preventDefault(); 
			jQuery('#gkMainbody').animate({ 'font-size' : "100%"}, 200); 
			current_fs = 100; 
		});
		jQuery('#gkToolsDec').click(function(e){ 
			e.stopPropagation();
			e.preventDefault(); 
			if(current_fs > 70) { 
				jQuery('#gkMainbody').animate({ 'font-size': (current_fs - 10) + "%"}, 200); 
				current_fs -= 10; 
			} 
		});
	}
	if(jQuery('#system-message-container').length != 0){
		  jQuery('#system-message-container').each(function(i, element){
		  		(function() {
		  		     jQuery(element).fadeOut('slow');
		  		}).delay(3500);
	      });
	}
	
	// K2 font-size switcher fix
	if(jQuery('#fontIncrease') && jQuery('.itemIntroText')) {
		jQuery('#fontIncrease').click(function() {
			jQuery('.itemIntroText').attr('class', 'itemIntroText largerFontSize');
		});
		
		jQuery('#fontDecrease').click( function() {
			jQuery('.itemIntroText').attr('class', 'itemIntroText smallerFontSize');
		});
	}
	
	// login popup
	if(jQuery('#gkPopupLogin').length > 0) {
		var popup_overlay = jQuery('#gkPopupOverlay');
		popup_overlay.css({'display': 'none', 'opacity' : 0});
		popup_overlay.fadeOut();
		
		jQuery('#gkPopupLogin').css({'display': 'block', 'opacity': 0, 'height' : 0});
		var opened_popup = null;
		var popup_login = null;
		var popup_login_h = null;
		var popup_login_fx = null;
		
		if(jQuery('#gkPopupLogin')) {

			popup_login = jQuery('#gkPopupLogin');
			popup_login.css('display', 'block');
			popup_login_h = popup_login.find('.gkPopupWrap').outerHeight();
			 
			jQuery('#gkLogin').click( function(e) {
				e.preventDefault();
				e.stopPropagation();
				popup_overlay.css({'opacity' : 0.98});
				popup_overlay.fadeIn('medium');
				
				popup_login.animate({'opacity':1, 'height': popup_login_h},200, 'swing');
				opened_popup = 'login';
				
				(function() {
					if(jQuery('#modlgn-username')) {
						jQuery('#modlgn-username').focus();
					}
				}).delay(600);
			});
		}
		
		popup_overlay.click( function() {
			if(opened_popup == 'login')	{
				popup_overlay.fadeOut('slow');
				popup_login.css({
					'opacity' : 0,
					'height' : 0
				});
			}
		});
		
		jQuery('.gkPopupWrap').each(function(i, wrap) {
			wrap = jQuery(wrap);
			if(wrap.find('.gk-icon-cross')) {
				wrap.find('.gk-icon-cross').click(function() {
					popup_overlay.trigger('click');
				});
			}
		});
	}
	
});
// Function to change styles
function changeStyle(style){
	var file1 = $GK_TMPL_URL+'/css/style'+style+'.css';
	var file2 = $GK_TMPL_URL+'/css/typography/typography.style'+style+'.css';
	jQuery('head').append('<link rel="stylesheet" href="'+file1+'" type="text/css" />');
	jQuery('head').append('<link rel="stylesheet" href="'+file2+'" type="text/css" />');
	jQuery.cookie('gk_msocial_j25_style', style, { expires: 365, path: '/' });
}
// Function to change styles
/*function changeStyle(style){
	//var file1 = $GK_TMPL_URL+'/css/style'+style+'.css';
	//var file2 = $GK_TMPL_URL+'/css/typography/typography.style'+style+'.css';
	//new Asset.css(file1);
	//new Asset.css(file2);
	Cookie.write('gk_msocial_j25_style', style, { duration:365, path: '/' });
	document.location.reload(true);
}
*/

