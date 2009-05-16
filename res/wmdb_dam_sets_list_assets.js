/** 
 * @description	  prototype.js based context menu
 * @author        Juriy Zaytsev; kangax [at] gmail [dot] com; http://thinkweb2.com/projects/prototype/
 * @version       0.6
 * @date          12/03/07
 * @requires      prototype.js 1.6
*/

if (Object.isUndefined(Proto)) { var Proto = { } }

Proto.Menu = Class.create({
	initialize: function() {
		var e = Prototype.emptyFunction;
		this.ie = Prototype.Browser.IE;
		this.options = Object.extend({
			selector: '.contextmenu',
			className: 'protoMenu',
			pageOffset: 25,
			fade: false,
			zIndex: 100,
			beforeShow: e,
			beforeHide: e,
			beforeSelect: e
		}, arguments[0] || { });
		
		this.shim = new Element('iframe', {
			style: 'position:absolute;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);display:none',
			src: 'javascript:false;',
			frameborder: 0
		});
		
		this.options.fade = this.options.fade && !Object.isUndefined(Effect);
		this.container = new Element('div', {className: this.options.className, style: 'display:none'});
		var list = new Element('ul');
		this.options.menuItems.each(function(item) {
			list.insert(
				new Element('li', {className: item.separator ? 'separator' : ''}).insert(
					item.separator 
						? '' 
						: Object.extend(new Element('a', {
							href: '#',
							title: item.name,
							className: (item.className || '') + (item.disabled ? ' disabled' : ' enabled')
						}), { _callback: item.callback })
						.observe('click', this.onClick.bind(this))
						.observe('contextmenu', Event.stop)
						.update(item.name)
				)
			)
		}.bind(this));
		$(document.body).insert(this.container.insert(list).observe('contextmenu', Event.stop));
		if (this.ie) { $(document.body).insert(this.shim) }
		
		document.observe('click', function(e) {
			if (this.container.visible() && !e.isRightClick()) {
				this.options.beforeHide(e);
				if (this.ie) this.shim.hide();
				this.container.hide();
			}
		}.bind(this));
		
		$$(this.options.selector).invoke('observe', Prototype.Browser.Opera ? 'click' : 'contextmenu', function(e){
			if (Prototype.Browser.Opera && !e.ctrlKey) {
				return;
			}
			this.show(e);
		}.bind(this));
	},
	show: function(e) {
		e.stop();
		this.options.beforeShow(e);
		var x = Event.pointer(e).x,
			y = Event.pointer(e).y,
			vpDim = document.viewport.getDimensions(),
			vpOff = document.viewport.getScrollOffsets(),
			elDim = this.container.getDimensions(),
			elOff = {
				left: ((x + elDim.width + this.options.pageOffset) > vpDim.width 
					? (vpDim.width - elDim.width - this.options.pageOffset) : x) + 'px',
				top: ((y - vpOff.top + elDim.height) > vpDim.height && (y - vpOff.top) > elDim.height 
					? (y - elDim.height) : y) + 'px'
			};
		this.container.setStyle(elOff).setStyle({zIndex: this.options.zIndex});
		if (this.ie) { 
			this.shim.setStyle(Object.extend(Object.extend(elDim, elOff), {zIndex: this.options.zIndex - 1})).show();
		}
		this.options.fade ? Effect.Appear(this.container, {duration: 0.25}) : this.container.show();
		this.event = e;
	},
	onClick: function(e) {
		e.stop();
		if (e.target._callback && !e.target.hasClassName('disabled')) {
			this.options.beforeSelect(e);
			if (this.ie) this.shim.hide();
			this.container.hide();
			e.target._callback(this.event);
		}
	}
})


selectOnClick=function(set){
	var el=$$('input.select_check');
	var cCnt=0;
	for(var i=0;i<el.length;i++){
		if(diaIsVisible(el[i])){
			switch(set){
				case true:
				case false:
					el[i].checked=set;
				break;
				case 'revert':
					el[i].checked=(el[i].checked==false);
				break;
			}
		}
		if(el[i].checked)cCnt++;
	}
	cm_set_states(el.length,cCnt);
}

checkboxClicked = function(e){
	selectOnClick('just_count');
}
diaIsVisible = function(el){
	var a = el.ancestors();
	var visible = false;
	for(var i=0;i<a.length;i++){
		if(a[i].hasClassName('tx_wmdbdamsets_dia')){
			visible = a[i].visible();
			break;
		}
	}
	return visible;
}

cm_set_states = function(bCnt,cCnt){
	submitsEnabled(true);
	cm_enable('cm_select_all');
	cm_enable('cm_select_none');
	//all selected
	if(cCnt==bCnt){
		cm_disable('cm_hide_unselected');
		cm_disable('cm_select_all');
	//none selected
	}else if(cCnt==0){
		submitsEnabled(false);
		cm_disable('cm_hide_unselected');
		cm_disable('cm_select_none');
	}else if(cCnt > 0){
		cm_enable('cm_hide_unselected');
	}
}

submitsEnabled = function(state){
	if(state==true) {
		$('tx_wmdbdamsets_sets_create_new_set').enable();
		$('tx_wmdbdamsets_sets_create_new_set').removeClassName('bgColor5');
		if($('tx_wmdbdamsets_sets_add_to_current')){
			$('tx_wmdbdamsets_sets_add_to_current').enable();
			$('tx_wmdbdamsets_sets_add_to_current').removeClassName('bgColor5');
		}
	}else{
		$('tx_wmdbdamsets_sets_create_new_set').disable();
		$('tx_wmdbdamsets_sets_create_new_set').addClassName('bgColor5');
		if($('tx_wmdbdamsets_sets_add_to_current')){
			$('tx_wmdbdamsets_sets_add_to_current').disable();
			$('tx_wmdbdamsets_sets_add_to_current').addClassName('bgColor5');
		}
	}
}

cm_enable = function(sel){
	$$('a.'+sel)[0].removeClassName('disabled');
	$$('a.'+sel)[0].addClassName('enabled');
}

cm_disable = function(sel){
	$$('a.'+sel)[0].addClassName('disabled');
	$$('a.'+sel)[0].removeClassName('enabled');
}

hideUnselected = function(){
	cm_enable('cm_show_hidden');
	$$('div.tx_wmdbdamsets_dia').each(function(el){
		el.select('input.select_check')[0].checked ? el.show():el.hide();
	});
}
showHidden = function(){
	cm_disable('cm_show_hidden');
	$$('div.tx_wmdbdamsets_dia').each(function(el){
		el.show();
	});	
}

var pM;
document.observe('dom:loaded',function(){
	pM = new Proto.Menu({
	  selector: '#ext-dam-mod-list-index-php', // context menu will be shown when element with id of "contextArea" is clicked
	  className: 'menu desktop', // this is a class which will be attached to menu container (used for css styling)
	  menuItems: [
				  {
				    name: wmdb_dam_set_lang.select_all,
				    className: 'cm_select_all', 
				    disabled: true,
				    callback: function(e){
				    	selectOnClick(true);
				    	
				    }
				  },{
				    name: wmdb_dam_set_lang.select_none,
				    className: 'cm_select_none', 
				    callback: function() {
				      selectOnClick(false);
				    }
				  },{
				    name: wmdb_dam_set_lang.invert_selected, 
				    className: 'cm_select_revert',
				    callback: function() {
				    	selectOnClick('revert');
				    }
				  },{
				    separator: true
				  },{
				    name: wmdb_dam_set_lang.hide_unselected,
				    className: 'cm_hide_unselected',
				    disabled: true,
				    callback: function() {
				      hideUnselected();
				    }
				  },{
				    name: wmdb_dam_set_lang.show_hidden,
				    className: 'cm_show_hidden',
				    disabled: true,
				    callback: function() {
				      showHidden();
				    }
				  }
				] // array of menu items
	});
	$$('input.select_check').each(function(c){
		Event.observe(c,'click',checkboxClicked.bindAsEventListener());
	});
});