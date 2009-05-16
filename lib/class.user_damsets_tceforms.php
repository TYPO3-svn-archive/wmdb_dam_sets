<?php

require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam.php');
require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_scbase.php');


class user_damsets_tceforms {
	
	var $diaSize=115;
	var $diaMargin=0;
	var $PA;
	
	/**
	 * user defined form field
	 *
	 * @param unknown_type $PA
	 * @param object $fObj t3lib_TCEforms obj
	 * @return unknown
	 */
	function user_damset_assets($PA,$fObj){
		global $LANG;
		$this->PA=$PA;
		$res=false;
		$rows=array();
		
		$defVals=t3lib_div::_GP('defVals');
		$idList=$this->checkIdList($defVals['tx_wmdbdamsets_sets']['assets']);
		
		$fObj->loadJavascriptLib('contrib/scriptaculous/scriptaculous.js');
		$fObj->additionalJS_pre['wmdb_dam_sets'] = "
			var wmdb_dam_set_id = '".$PA['row']['uid']."';
			".t3lib_div::getURL(t3lib_extMgm::extPath('wmdb_dam_sets').'res/wmdb_dam_sets_form.js'
		);
		
		
		$rows=array();
		$storedRows=0;
		if($idList){
			$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_dam','uid in ('.$idList.')');
			$rows=$this->getRows($res);
			$storedRows=count($rows);
		}
		
		if(t3lib_div::testInt($PA['row']['uid'])){
			$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_dam.*,tx_wmdbdamsets_sets.description as damset_description',
											'tx_wmdbdamsets_sets
											 LEFT JOIN tx_wmdbdamsets_sets_assets_mm on tx_wmdbdamsets_sets.uid=tx_wmdbdamsets_sets_assets_mm.uid_local
											 LEFT JOIN tx_dam on tx_wmdbdamsets_sets_assets_mm.uid_foreign=tx_dam.uid',
											 'tx_wmdbdamsets_sets.uid='.$PA['row']['uid'],'','tx_wmdbdamsets_sets_assets_mm.sorting');
			$additionalRows=$this->getRows($res);
			if(is_array($additionalRows)){
				$rows=t3lib_div::array_merge_recursive_overrule($rows,$additionalRows);
			}
		}
		
		if($storedRows && count($rows)>$storedRows){ //new rows added via defVals?
			$fObj->additionalJS_pre['wmdb_dam_sets'] .= '
			Event.observe(window,\'load\',function(){
					TBE_EDITOR.fieldChanged(\'tx_wmdbdamsets_sets\','.$PA['row']['uid'].',\'assets\',\'data[tx_wmdbdamsets_sets]['.$PA['row']['uid'].'][assets]\');
			});
			';
		}
		
		$content.=$this->renderFormElements($rows);
		return $content;
	}
	
	function getRows($res){
		while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$rows[$row['uid']]=$row;
		}
		return $rows;
	}
	
	
	
	function renderFormElements($rows){
		global $BACK_PATH, $LANG;
		
			// extra CSS code for HTML header
		if(is_object($GLOBALS['SOBE']) AND !isset($GLOBALS['SOBE']->doc->inDocStylesArray['wmdb_dam_sets'])) {
			$GLOBALS['SOBE']->doc->inDocStylesArray['wmdb_dam_sets'] = t3lib_div::getURL(t3lib_extMgm::extPath('wmdb_dam_sets').'res/wmdb_dam_sets_form.css');
		}

		
		foreach ($rows as $row){
			$cats=array();
			$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_dam_cat.title',
														'tx_dam_cat,tx_dam_mm_cat',
														'tx_dam_mm_cat.uid_foreign=tx_dam_cat.uid
														AND tx_dam_mm_cat.uid_local='.$row['uid'].'
														AND tx_dam_cat.deleted=0',
														'','tx_dam_mm_cat.sorting');
			while($cat=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$cats[]='<li>'.$cat['title'].'</li>';
			}
			
			$uids[]=$row['uid'];
			$lines[]='
				<div id="wmdb_dam_sets_sortable_row_'.$row['uid'].'" class="wmdb_dam_sets_sortable_row">
					<div class="bgColor5">
						<div style="float:left;">
							<strong>'.tx_dam::icon_getFileTypeImgTag($row, 'style="vertical-align:middle;" title="'.$row['title'].'"').$row['title'].'</strong>
							'.strtoupper($row['file_type']).', '.t3lib_div::formatSize($row['file_size']).($row['hpixels']&&$row['vpixels']?', '.$row['hpixels'].'x'.$row['vpixels'].'px':'').($row['color_space']?', '.$row['color_space']:'').'
						</div>
						<div style="float:right;">
							<img src="'.$BACK_PATH.'gfx/move.gif" style="vertical-align:middle;" class="movehandle">
							<input type="button" value="'.strtoupper(substr($LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.title'),0,1)).'" onClick="copyValue('.$row['uid'].',\'title\');this.blur();" title="'.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.use').' '.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.title').'">
							<input type="button" value="'.strtoupper(substr($LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.description'),0,1)).'" onClick="copyValue('.$row['uid'].',\'description\');this.blur();" title="'.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.use').' '.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.description').'">
							<input type="button" value="'.strtoupper(substr($LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.categories'),0,1)).'" onClick="copyValue('.$row['uid'].',\'categories\');this.blur();" title="'.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.use').' '.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.categories').'">
							<input type="button" value="'.strtoupper(substr($LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.all'),0,1)).'" onClick="copyValue('.$row['uid'].',\'all\');this.blur();" title="'.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.use').' '.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.all').'">
							<a href="javascript:void(0);" onClick="removeEntry(\''.$row['uid'].'\')"><img style="vertical-align:text-top;" src="'.$BACK_PATH.'gfx/delete_record.gif"></a>
							<a href="javascript:void(0);" onClick="toggleDetails(\''.$row['uid'].'\')"><img id="icon_show_details_'.$row['uid'].'" style="vertical-align:middle;" src="'.$BACK_PATH.'gfx/arrowleft.png"></a>
						</div>
						<div class="clearer">&nbsp;</div>
					</div>
					<div id="wmdb_dam_sets_sortable_row_'.$row['uid'].'_details" style="display:none;">
						<div class="wmdb_dam_sets_dia">'.tx_dam_guiFunc::getDia($row, $this->diaSize, $this->diaMargin).'</div>
						<div style="float:left;width:290px;">
							<b>'.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.description').':</b>
							<br><br>
							<p>'.$row['description'].'</p><br>
							<b>'.$LANG->sL('LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.categories').':</b>
							<ul>'.implode('',$cats).'</ul>
						</div>
					</div>
					<div class="clearer">&nbsp;</div>
				</div>
			';
		}
		return '<div id="wmdb_dam_sets_sortable" style="display:none;">'.implode('',$lines).'</div>
				<input type="hidden" id="wmdb_dam_sets_sortable_values" name="'.$this->PA['itemFormElName'].'" value="'.implode(',',$uids).'">';
	}
	
	function checkIdList($idList){
		$ids=t3lib_div::trimExplode(',',$idList,1);
		foreach ($ids as $id){
			if(!t3lib_div::testInt($id)) return false;
		}
		return implode(',',$ids);
	}
	

}
?>