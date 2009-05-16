var rows = [];
assetGuiInit = function(){
	Sortable.create('wmdb_dam_sets_sortable',{
		tag:'div',
		only:'wmdb_dam_sets_sortable_row',
		handle:'movehandle',
		onUpdate: function(){
			var rows = $$('div.wmdb_dam_sets_sortable_row');
			var valuelist='';
			for(i=0;i<rows.length;i++){
				valuelist += (i>0?',':'')+rows[i].id.replace(/wmdb_dam_sets_sortable_row_/g,'');
			}
			TBE_EDITOR.fieldChanged('tx_wmdbdamsets_sets',wmdb_dam_set_id,'assets','data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][assets]');
			$('wmdb_dam_sets_sortable_values').value=valuelist;
		}
	});
	$('wmdb_dam_sets_sortable').show();
}
toggleDetails = function(id){
	$('wmdb_dam_sets_sortable_row_'+id+'_details').toggle();
	$('icon_show_details_'+id).src=$('wmdb_dam_sets_sortable_row_'+id+'_details').visible() ? 'gfx/arrowdown.png':'gfx/arrowleft.png';
}
copyValue = function(id,toCopy){
	if(!rows[id]){
		new Ajax.Request('ajax.php?ajaxID=tx_wmdbdamsets_ajax&CMD=getRow&uid='+id,{
			method: 'get',
			onSuccess: copyValue_success.bind(this,id,toCopy)
		});
	}else{
		copyValue_success(id,toCopy,false);
	}
}
copyValue_success = function(id,toCopy,transport){
	if(transport){
		rows[id]=transport.headerJSON;
	}
	switch(toCopy){
		case 'title':
			copyValue_updateField('data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][title]_hr',rows[id][toCopy],toCopy);
		break;
		case 'description':
			copyValue_updateField('data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][description]',rows[id][toCopy],toCopy);
		break;
		case 'categories':
			copyValue_updateCategories(id);
		break;
		case 'all':
			copyValue_updateField('data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][title]_hr',rows[id]['title'],'title');
			copyValue_updateField('data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][description]',rows[id]['description'],'description');
			copyValue_updateCategories(id);
		break;
	}
	new Effect.Highlight($$('.tab')[0]);
}
copyValue_updateField = function(fName,value,toCopy){
	var f = document.getElementsByName(fName)[0];
	f.value=value;
	typo3form.fieldGet('data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][title]','required','',0,'');
	TBE_EDITOR.fieldChanged('tx_wmdbdamsets_sets',wmdb_dam_set_id,toCopy,'data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+']['+toCopy+']');
}
copyValue_updateCategories = function(id){
	var fName = 'data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][categories]';
	var f = document.getElementsByName(fName+'_list')[0];
	f.length=null;
	var fHidden = document.getElementsByName(fName)[0];
	fHidden.value='';
	for(var i=0;i < rows[id].categories.length;i++){
		var nE = new Option(rows[id].categories[i].title,rows[id].categories[i].uid,false,false);
		f.options[i]=nE;
		fHidden.value != '' ? fHidden.value += ','+rows[id].categories[i].uid : fHidden.value = rows[id].categories[i].uid;
	}
	TBE_EDITOR.fieldChanged('tx_wmdbdamsets_sets',wmdb_dam_set_id,'categories','data[tx_wmdbdamsets_sets]['+wmdb_dam_set_id+'][categories]_list');
}

removeEntry = function(id){
	if(confirm('are you sure?')){
		$('wmdb_dam_sets_sortable_row_'+id).remove();
		var newValuelist = $F('wmdb_dam_sets_sortable_values').split(',').without(id.toString());
		$('wmdb_dam_sets_sortable_values').value=newValuelist.join(',');
	}
}
Event.observe(window, 'load', assetGuiInit );