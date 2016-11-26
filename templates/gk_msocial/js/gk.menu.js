jQuery(document).ready(function() {
    if(jQuery('#gkExtraMenu')  && jQuery('#gkMainMenu').hasClass('gkMenuClassic')) {
        // fix for the iOS devices     
        jQuery('#gkExtraMenu ul li span').each(function(el) {
            el.attr('onmouseover', '');
        });

        jQuery('#gkExtraMenu ul li a').each(function(el) {
            el = jQuery(el);
            el.attr('onmouseover', '');

            if(el.parent().hasClass('haschild') && jQuery(document.body).attr('data-tablet') != null) {
                el.click(function(e) {
                    if(el.attr("dblclick") == undefined) {
                        e.preventDefault();
                        e.stopPropagation();
                        el.attr("dblclick", new Date().getTime());
                    } else {
                    	if(el.parent().find('div.childcontent').eq(0).css('overflow') == 'visible') {
    	 					window.location = el.attr('href');
 	                    }
                        var now = new Date().getTime();
                        if(now - attr("dblclick", 0) < 500) {
                            window.location = el.attr('href');
                        } else {
                           e.preventDefault();
                           e.stopPropagation();
                           el.attr("dblclick", new Date().getTime());
                        }
                    }
                });
            }
        });

        var base = jQuery('#gkExtraMenu');

        if($GKMenu && ($GKMenu.height || $GKMenu.width)) {    
      	  	 
            base.find('li.haschild').each(function(i, el){   
            	el = jQuery(el);  
               
                if(el.children('.childcontent').length > 0) {
                    var content = el.children('.childcontent').first();
                    var prevh = content.height();
                    var prevw = content.width();
					var duration = $GKMenu.duration;
					var heightAnim = $GKMenu.height;
					var widthAnim = $GKMenu.width;
					

                    var fxStart = { 
						'height' : heightAnim ? 0 : prevh, 
						'width' : widthAnim ? 0 : prevw, 
						'opacity' : 0 
					};
					var fxEnd = { 
						'height' : prevh, 
						'width' : prevw, 
						'opacity' : 1 
					};	
					
					
                    content.css(fxStart);
                    content.css({'left' : 'auto', 'overflow' : 'hidden' });
											
                    el.mouseenter(function(){
                			                    
                        var content = el.children('.childcontent').first();
                        content.css('display', 'block');
						
						if(content.attr('data-base-margin') != null) {
							content.css({
								'margin-left': content.attr('data-base-margin') + "px"
							});
						}
							
						var pos = content.offset();
						var winWidth = jQuery(window).outerWidth();
						var winScroll = jQuery(window).scrollLeft();
							
						if(pos.left + prevw > (winWidth + winScroll)) {
							var diff = (winWidth + winScroll) - (pos.left + prevw) - 5;
							var base = parseInt(content.css('margin-left'));
							var margin = base + diff;
							
							if(base > 0) {
								margin = -prevw + 10;	
							}
							content.css('margin-left', margin + "px");
							
							if(content.attr('data-base-margin') == null) {
								content.attr('data-base-margin', base);
							}
						}
						//
						content.animate(
							fxEnd, 
							duration, 
							function() { 
								if(content.outerHeight() == 0){ 
									content.css('overflow', 'hidden'); 
								} else if(
									content.outerHeight(true) - prevh < 30 && 
									content.outerHeight(true) - prevh >= 0
								) {
									content.css('overflow', 'visible');
								}
							}
						);
					});
				el.mouseleave(function(){
				
						content.css({
							'overflow': 'hidden'
						});
						//
						content.animate(
							fxStart, 
							duration, 
							function() { 
								if(content.outerHeight() == 0){ 
									content.css('overflow', 'hidden'); 
								} else if(
									content.outerHeight(true) - prevh < 30 && 
									content.outerHeight(true) - prevh >= 0
								) {
									content.css('overflow', 'visible');
								}
								
								content.css('display', 'none');
							}
						);
					});
				}
			});
            
            base.find('li.haschild').each(function(i, el) {
				el = jQuery(el);
				content = jQuery(el.children('.childcontent').first());
				content.css({ 'display': 'none' });
			});       
        }
	} else if(jQuery('#gkExtraMenu').length > 0 && jQuery('#gkMainMenu').hasClass('gkMenuOverlay')) {
    	var overlay = new jQuery('<div id="gkMenuOverlay"></div>');
    	
    	jQuery('body').append(overlay);
    	overlay.fadeOut();
    	    	
    	var overlaywrapper = new jQuery('<div id="gkMenuOverlayWrap"><div><i id="gkMenuOverlayClose" class="gk-icon-cross"></i><h3 id="gkMenuOverlayHeader"></h3><div id="gkMenuOverlayContent"></div></div></div>');
    	
    	jQuery('body').append(overlaywrapper);
    	overlay.fadeOut();
    	overlaywrapper.fadeOut();
    	
    	var overlaywrap = overlaywrapper.find('div');
 		overlaywrap.fadeOut();
    	var header = jQuery('#gkMenuOverlayHeader');
    	var content = jQuery('#gkMenuOverlayContent');
    	header.css('margin-top', '-100px');
    	var submenus = [];
    	
    	jQuery('#gkMenuOverlayClose').click(function() {
    		overlay.fadeOut();
    		overlaywrapper.fadeOut();
    		overlaywrap.fadeOut();
    		header.animate({marginTop: '-100px'}, 500); 
    		setTimeout(function() {
    			overlay.removeClass('open');
    			overlaywrapper.removeClass('open');
    			header.html('');
    			content.html('');
    		}, 500);
    	});
    	
    	overlay.click(function(e) {
    		e.stopPropagation();
    		if(e.target == overlay) {
    			jQuery('#gkMenuOverlayClose').trigger('click');	
    		}
    	});
    	
    	jQuery('#gkExtraMenu').find('.haschild').each(function(i, el) {
    		el = jQuery(el);
    		if(el.parent().hasClass('level0')) {
    			var link = el.find('> a');
    			submenus[link.attr('id')] = {
    				"link": link,
    				"submenu": el.find('.childcontent')
    			};
    			
    			link.click(function(e) {
    				e.preventDefault();
    				overlay.css('height', jQuery('body').height());
    				var menuID = jQuery(e.target).attr('id');
    				header.html('');
    				header.append(submenus[menuID].link.clone());
    				content.html('');
    				content.append(submenus[menuID].submenu.clone());
    				overlay.addClass('open');
    				overlaywrapper.addClass('open');
    				overlay.css('opacity', '1').fadeIn();
    				overlaywrapper.css('opacity', '1').fadeIn();
    				
    				setTimeout(function() {
    					overlaywrap.css('opacity', '1').fadeIn();
    					header.animate({marginTop: '0px'}, 500);
    				}, 500);
    			});
    		}
    	});
    }
}); 