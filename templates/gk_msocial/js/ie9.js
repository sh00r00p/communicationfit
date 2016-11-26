window.addEvent('domready', function() {
	document.getElements('*[placeholder]').each(function(el) {
		if(el.get('value') == '') {
			el.set('value', el.getProperty('placeholder'));
		}
		
		el.addEvents({
			'focus': function() {
				if(el.get('value') == el.getProperty('placeholder')) {
					el.set('value', '');
				}
			}, 
			'blur': function() {
				if(el.get('value') == '') {
					el.set('value', el.getProperty('placeholder'));
				}
			}
		});
	});
});