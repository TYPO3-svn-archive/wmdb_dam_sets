<?php

require_once(t3lib_extMgm::extPath('wmdb_dam_sets').'modfunc_list/class.tx_wmdbdamsets_listrecords.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db.php');
require_once(PATH_txdam.'lib/class.tx_dam_iterator_db_lang_ovl.php');
require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

$LANG->includeLLFile('EXT:wmdb_dam_sets/locallang_db.xml');

class tx_wmdbdamsets_listfunc extends t3lib_extobjbase {
	
	var $pObj;
	var $extKey='tx_wmdbdamsets_listfunc';
	
	function modMenu()    {
		global $LANG;
		return array(
			'tx_wmdbdamsets_buildSet' => '',
			'tx_wmdbdamsets_listMode' => array(
				'assets' => $LANG->getLL('tx_wmdbdamsets_sets.assets'),
				'sets'	=> $LANG->getLL('tx_wmdbdamsets_sets'),
			),
			'tx_wmdbdamsets_currentSet'=>''
		);
	}
	
	function head(){
		$this->pObj->guiItems->registerFunc('getResultInfoBar', 'header');
		$this->pObj->guiItems->registerFunc('getCurrentSelectionBox', 'footer');
	}
	
	
	/**
	 * 
	 * @todo class="txdam-dia" make sortable
	 *
	 */
	function main()	{
		global $BACK_PATH, $LANG;
		$inVars = t3lib_div::_GP('tx_wmdbdamsets_listfunc');
		if($inVars['createSet'] || $inVars['addToCurrent']){
			$this->redirectToForm($inVars);
			exit;
		}else{
			switch ($this->pObj->MOD_SETTINGS["tx_wmdbdamsets_listMode"]) {
				case 'assets':
				$this->pObj->doc->JScodeArray[$this->extKey]=t3lib_div::getURL(t3lib_extMgm::extPath('wmdb_dam_sets').'res/wmdb_dam_sets_list_assets.js')."
						var wmdb_dam_set_lang = {
							'select_all':".$LANG->JScharCode($LANG->getLL('tx_wmdbdamsets_sets.select_all')).",
							'select_none':".$LANG->JScharCode($LANG->getLL('tx_wmdbdamsets_sets.select_none')).",
							'invert_selected':".$LANG->JScharCode($LANG->getLL('tx_wmdbdamsets_sets.invert_selected')).",
							'hide_unselected':".$LANG->JScharCode($LANG->getLL('tx_wmdbdamsets_sets.hide_unselected')).",
							'show_hidden':".$LANG->JScharCode($LANG->getLL('tx_wmdbdamsets_sets.show_hidden'))."
						}
					";
					$content .= $this->renderAssetList();
				break;
				case 'sets':
					$this->pObj->doc->JScodeArray[$this->extKey]=t3lib_div::getURL(t3lib_extMgm::extPath('wmdb_dam_sets').'res/wmdb_dam_sets_list_sets.js');
					$content .= $this->renderSetList();
				break;
			}
			return $content;
		}
	}
	
	function redirectToForm($vars){
		global $BACK_PATH;
		//edit:
		//http://fpg42.dus.wmdb.de/typo3/alt_doc.php?returnUrl=%2Ftypo3conf%2Fext%2Fdam%2Fmod_list%2Findex.php%3FSET%5Bfunction%5D%3Dtx_dam_list_list&edit[tx_dam][16523]=edit
		$docVars['returnUrl']=t3lib_div::getIndpEnv('REQUEST_URI');
		if($this->pObj->MOD_SETTINGS['tx_wmdbdamsets_currentSet'] && $vars['addToCurrent']){
			$docVars['edit']=array(
				'tx_wmdbdamsets_sets' => array(
					$this->pObj->MOD_SETTINGS['tx_wmdbdamsets_currentSet'] => 'edit',
				)
			);
			$docVars['defVals'] = array(
				'tx_wmdbdamsets_sets' => array(
					'assets' => implode(',',$vars['selected'])
				)
			);
		}else{
			$docVars['edit']=array(
				'tx_wmdbdamsets_sets' => array(
					'1' => 'new',
				)
			);
			$docVars['defVals'] = array(
				'tx_wmdbdamsets_sets' => array(
					'assets' => implode(',',$vars['selected'])
				)
			);
		}
		header('Location: '.$BACK_PATH.'alt_doc.php?'.t3lib_div::implodeArrayForUrl('',$docVars));
	}
	
	function renderSetList(){
		
		//only use categories (folders make no sense... anything else?)
		$sel = $this->pObj->selection->sl->sel;
		$tempSel = array();
		foreach (array('SELECT','OR','AND','NOT','SEARCH') as $queryType) {
			if(is_array($sel[$queryType])) {
				foreach ($sel[$queryType] as $selectionRuleName => $items) {
					//only use categories (folders make no sense... anything else?)
					if($selectionRuleName=='txdamCat'){
						foreach ($items as $iKey => $iVal){
							$tempSel[$queryType]['txdamCat'][$iKey]=$iVal;
						}
					}
				}
			}
		}
		$this->pObj->selection->sl->sel=$tempSel;
		$this->pObj->selection->qg->initBESelect('tx_wmdbdamsets_sets', tx_dam_db::getPidList());
		$this->pObj->selection->addSelectionToQuery();
		
		$this->pObj->selection->qg->query['LEFT_JOIN'] = array(
			'tx_wmdbdamsets_sets_categories_mm' => 'tx_wmdbdamsets_sets_categories_mm.uid_local = tx_wmdbdamsets_sets.uid',
			'tx_dam_cat'=>'tx_wmdbdamsets_sets_categories_mm.uid_foreign = tx_dam_cat.uid'
		);
		$this->pObj->selection->qg->query['GROUPBY'] = array(
			'tx_wmdbdamsets_sets.uid'=>'tx_wmdbdamsets_sets.uid'
		);
		$this->pObj->selection->execSelectionQuery(true);
		
		if($this->pObj->selection->pointer->countTotal) {
			$res = $this->pObj->selection->execSelectionQuery();
			$conf = array(	'table' => 'tx_wmdbdamsets_sets',
							'countTotal' => $this->pObj->selection->pointer->countTotal	);
			$dbIterator =& new tx_dam_iterator_db($res, $conf);
			//
			// init db list object
			//
			$dblist = t3lib_div::makeInstance('tx_wmdbdamsets_listrecords');
			$dblist->setParameterName('form', $this->pObj->formName);
			$dblist->init('tx_wmdbdamsets_sets');
			$this->pObj->selection->addLimitToQuery();
			$res = $this->pObj->selection->execSelectionQuery();
			$conf = array(	'table' => 'tx_wmdbdamsets_sets',
							'countTotal' => $this->pObj->selection->pointer->countTotal	);
			$dbIterator =& new tx_dam_iterator_db($res, $conf);
			$dblist->setDataObject($dbIterator);

				// add columns to list
			$dblist->clearColumns();
				// enable display of action column
			$dblist->showActions = true;
			$dblist->iconNotExists=true;
			$dblist->showIcon = true;
			$this->enableContextMenus=true;
			
			$dblist->addColumn('title', 'Title');
			$dblist->addColumn('_CONTROL_', '');
			
				// Enable/disable display of AlternateBgColors
			$dblist->showAlternateBgColors = $this->pObj->config_checkValueEnabled('alternateBgColors', true);
			$dblist->setPointer($this->pObj->selection->pointer);
			$this->pObj->doc->JScodeArray['dblist-JsCode'] = $dblist->getJsCode();
			
			$content.=$this->getControls();
			$content.= $this->pObj->guiItems->getOutput('header');
			
			$content.= $dblist->getListTable();
		}else{ //no records found
			$content.=$this->getControls();
			$content.= $this->pObj->guiItems->getOutput('header');
		}
		return $content;
	}

	function renderAssetList(){
		global $LANG,$BACK_PATH;
		
		$content=$this->getControls();
		
		//macht das irgendeinen sinn????? ich fürchte nein => tobtested
		$this->pObj->guiItems->registerFunc('getAssetsetControl', 'footer');
		
		
		//initialize currently selected records
		$this->pObj->selection->addSelectionToQuery();
		//count selected records
		$this->pObj->selection->execSelectionQuery(TRUE);
		
		$diaSize=115;
		$diaMargin=0;
		
		if($this->pObj->selection->pointer->countTotal) {
			$this->pObj->doc->inDocStylesArray['tx_dam_SCbase_dia'] = tx_dam_guiFunc::getDiaStyles($diaSize, $diaMargin, 5);
			$this->pObj->doc->inDocStylesArray['tx_wmdbdamsets_dia'] = t3lib_div::getURL(t3lib_extMgm::extPath('wmdb_dam_sets').'res/wmdb_dam_sets_list.css');
			$code = '';
			//
			// init iterator for query
			//
			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery=1;
			
			$this->pObj->selection->qg->addOrderBy('file_name');
			$res = $this->pObj->selection->execSelectionQuery();
			
			$conf = array(	'table' => 'tx_dam',
							'countTotal' => $this->pObj->selection->pointer->countTotal	);
			if ($this->langCurrent>0 AND $this->pObj->MOD_SETTINGS['tx_dam_list_langOverlay']!=='exclusive') {
				$dbIterator =& new tx_dam_iterator_db_lang_ovl($res, $conf);
				$dbIterator->initLanguageOverlay($table, $this->pObj->MOD_SETTINGS['tx_dam_list_langSelector']);
			} else {
				$dbIterator =& new tx_dam_iterator_db($res, $conf);
			}			
			if ($dbIterator->count())	{
				while ($dbIterator->valid()) {
					$row = $dbIterator->current();
					$rows[$row['uid']]=$row;
					$rows[$row['uid']][$this->extKey.'_dia'] = tx_dam_guiFunc::getDia($row, $diaSize, $diaMargin, array('title','info'));
					$rows[$row['uid']][$this->extKey.'_btn_remove'] = tx_dam_SCbase::btn_removeRecFromSel('tx_dam', $row['uid']);
					$content.='
						<div class="tx_wmdbdamsets_dia">
							<div>
								<input class="select_check" type="checkbox" name="'.$this->extKey.'[selected][]" value="'.$row['uid'].'" checked>
								<a href="'.$this->getAddToCurrentSetUrl($row).'"><img src="'.$BACK_PATH.t3lib_extMgm::extRelPath('dam').'i/add_to_lightbox.gif"></a>
							</div>
							<div>'.$rows[$row['uid']][$this->extKey.'_dia'].'</div>
						</div>
					';
					$dbIterator->next();
				}
			}
			$content.= $this->pObj->doc->spacer(5);
			$content.= $this->pObj->doc->section('','<div style="line-height:'.($this->diaSize +7+8).'px;">'.$code.'</div><br style="clear:left" />',0,1);
		}
		return $content;
	}
	
	function getAddToCurrentSetUrl($row){
		global $BACK_PATH;
		$params=array(
			'returnUrl'=>t3lib_div::getIndpEnv('REQUEST_URI'),
			'edit[tx_wmdbdamsets_sets]['.$this->pObj->MOD_SETTINGS['tx_wmdbdamsets_currentSet'].']'=>'edit',
			'defVals[tx_wmdbdamsets_sets][assets]'=>$row['uid']
		);
		$url = $BACK_PATH.'alt_doc.php?'.t3lib_div::implodeArrayForUrl('',$params);
		return $url;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 * @todo labels $LANG based
	 */
	function getControls(){
		global $LANG;
		$currentSet = $this->pObj->MOD_SETTINGS['tx_wmdbdamsets_currentSet'];
		if($currentSet){
			$row=t3lib_BEfunc::getRecord('tx_wmdbdamsets_sets',$currentSet);
			$label_currentSet = t3lib_BEfunc::getRecordTitle('tx_wmdbdamsets_sets',$row,false,true);
		}else{
			$label_currentSet = $LANG->getLL('tx_wmdbdamsets_sets.no_set_selected');
		}
		$left = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], t3lib_extMgm::extRelPath('wmdb_dam_sets').'icon_tx_wmdbdamsets_sets.gif', 'width="18" height="16"').' '.$titleNotExists.' alt="" style="vertical-align:middle;"/>
				<span id="tx_wmdbdamsets_currentset">'.$label_currentSet.'</span>'; //left part of the headerbar
		if($this->pObj->MOD_SETTINGS['tx_wmdbdamsets_listMode']=='assets'){
			//$right = $this->getControls_toolsIcon();
		}
		$right .= t3lib_BEfunc::getFuncMenu($this->id,"SET[tx_wmdbdamsets_listMode]",$this->pObj->MOD_SETTINGS["tx_wmdbdamsets_listMode"],$this->pObj->MOD_MENU["tx_wmdbdamsets_listMode"]);			//right part of the headerbar
		$content.=$this->pObj->getHeaderBar($left,$right);
		if($this->pObj->MOD_SETTINGS['tx_wmdbdamsets_listMode']=='assets'){
			$left='';
			/*
			$left = '<p>[already in set][not in set][find]</p>
			';
					ToDo:<br>
					- disable createset if no checkbox checked<br>
					- add "hide unchecked/show unchecked<br>
					- add "already used in ...."<br>

			*/
			$right ='<input id="tx_wmdbdamsets_sets_create_new_set" type="submit" name="'.$this->extKey.'[createSet]" value="'.$LANG->getLL('tx_wmdbdamsets_sets.create_new_set').'">';
			if($currentSet){
				$right.='<input id="tx_wmdbdamsets_sets_add_to_current" type="submit" name="'.$this->extKey.'[addToCurrent]" value="'.$LANG->getLL('tx_wmdbdamsets_sets.add_to_current').'">';
			}
			$content.=$this->pObj->getHeaderBar($left,$right);
		}
		return $content;
	}
	
	function getControls_toolsIcon(){
		$tools = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], t3lib_extMgm::extRelPath('dam').'mod_tools/moduleicon.gif', 'width="18" height="16"').' '.$titleNotExists.' alt="" style="vertical-align:middle;"/>';
		return $tools;
	}
	
}

?>