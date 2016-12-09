function gkclick(tag){	
	jInsertEditorText(tag, $GKEditor);
	jQuery('#sbox-btn-close').trigger('click');	
}

function gkhideTab(val) {	
	if(jQuery('#sbox-window .gkTypoTable')) {
		jQuery('#sbox-window .gkTypoTable').each(function(i, el){	
			el = jQuery(el);	
			if(i==val){	
				el.css('display', 'block');	
			} else {	
				el.css('display', 'none');	
			}	
		});	
	
		jQuery('#sbox-window .gkTypoMenu li').attr('class', '');
		if(jQuery('#sbox-window .gkTypoMenu li')[val])jQuery(jQuery('#sbox-window .gkTypoMenu li')[val]).attr('class', 'active');
	}
}

jQuery(document).ready(function() {	
	jQuery('#editor-xtd-buttons').find('a.modal-button').find('i.gk-typo').parent().addClass('btn-info');
	jQuery('#editor-xtd-buttons').find('a.modal-button').find('i.gk-typo').parent().click( function() {
		
		(function() {
			gkhideTab(0); 
			jQuery('#sbox-window').css('padding', '0px');
			jQuery('#sbox-window .gkTypoMenu li').first().attr('class', 'active');
			jQuery('#sbox-window .gkTypoContent').first().css('height', jQuery('#sbox-window .gkTypoMenu').first().height() + 'px');
		}).delay(500);
	});
});