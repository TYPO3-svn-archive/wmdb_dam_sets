/* assetset functions */
var marked = '../../../../typo3/gfx/unmarkstate.gif';
var unmarked = '../../../../typo3/gfx/markstate.gif';
setCurrent = function(id,el){
	var icon = el.firstChild;
	icon.src = '../../../../typo3/gfx/spinner.gif';
	new Ajax.Request('../../../../typo3/ajax.php?ajaxID=tx_wmdbdamsets_ajax&CMD=setCurrent&uid='+id,{
	  method: 'get',
	  onSuccess: setCurrent_onSuccess.bind(this,icon),
	  onFailure: setCurrent_onFailure.bind(this,icon)
	});
	return false;
}
setCurrent_onSuccess = function(icon,t){
	$$('img[src="'+marked+'"]').each(function(i){
		i.src=unmarked;
	});
	$('tx_wmdbdamsets_currentset').update(t.headerJSON.title);
	icon.src=marked;
}
setCurrent_onFailure = function(icon,t){
	icon.src=unmarked;
	alert('Application ERROR\nCould not set assetset as current :(');
}
