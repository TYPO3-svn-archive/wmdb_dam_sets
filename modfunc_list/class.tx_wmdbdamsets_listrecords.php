<?php
require_once(PATH_txdam.'lib/class.tx_dam_listrecords.php');


class tx_wmdbdamsets_listrecords extends tx_dam_listrecords {
	
	var $currentSet;
	
	function __construct(){
		parent::__construct();
		$this->MOD_SETTINGS=$GLOBALS['BE_USER']->getModuleData('txdamM1_list');
		$this->currentSet = $this->MOD_SETTINGS['tx_wmdbdamsets_currentSet'];
	}
	
	
	/**
	 * Renders the item icon
	 *
	 * @param	array		$item item array
	 * @return	string
	 */
	function getItemIcon (&$item) {
		static $iconNotExists;
		if($item['__table']=='tx_wmdbdamsets_sets'){
			$itemIcon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], t3lib_iconWorks::getIcon('tx_wmdbdamsets_sets',$item), 'width="18" height="16"').' '.$titleNotExists.' alt="" />';
			if ($this->enableContextMenus) $itemIcon = $GLOBALS['SOBE']->doc->wrapClickMenuOnIcon($itemIcon, $this->table, $item['uid']);
			return $itemIcon;
		}else{
			return parent::getItemIcon(&$item);
		}
	}
	
	function getItemAction ($item) {
		global $BACK_PATH;
		$icon = $item['uid']==$this->currentSet ? 'unmarkstate.gif' : 'markstate.gif';
		return '<a href="javascript:void(0);" onClick="this.blur();return setCurrent('.$item['uid'].',this);" title="'.$GLOBALS['LANG']->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.set_as_current').'"><img src="'.$BACK_PATH.'gfx/'.$icon.'" width="14" height="14"></a>';
	}
	
}

?>