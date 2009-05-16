<?php

class tx_wmdbdamsets_ajax {
	
	var $CMD;
	var $uid;
	var $params;
	var $ajaxObj; //instance of typo3/classes/class.typo3ajax.php:TYPO3AJAX
	
	function main($params,$ajaxObj){
		$this->params = $params;
		$this->ajaxObj = $ajaxObj;
		$this->CMD = t3lib_div::_GP('CMD');
		$this->uid = t3lib_div::_GP('uid');
		switch ($this->CMD) {
			case 'getRow':
				$this->ajax_getRow($this->uid);
			break;
			case 'setCurrent':
				$this->ajax_setCurrent();
			break;
			default:
				//needed????
			break;
		}
	}
	function ajax_setCurrent(){
		$this->ajax_getRow($this->uid,'tx_wmdbdamsets_sets','uid,title,description');
		if(!$this->ajaxObj->isError()){
			$moduleData = $GLOBALS['BE_USER']->getModuleData('txdamM1_list');
			$moduleData['tx_wmdbdamsets_currentSet']=$this->uid;
			$GLOBALS['BE_USER']->pushModuleData('txdamM1_list',$moduleData);
		}
	}
	
	function ajax_getRow($uid,$table='tx_dam',$fields='title,description')	{
		
		$row = t3lib_BEfunc::getRecord($table,$uid,$fields);
		
		$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_dam_cat.uid,tx_dam_cat.title',
													'tx_dam_mm_cat
													 LEFT JOIN tx_dam_cat on tx_dam_mm_cat.uid_foreign=tx_dam_cat.uid',
													'tx_dam_mm_cat.uid_local = '.$uid,
													'','tx_dam_mm_cat.sorting');
		while ($cat=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$row['categories'][]=$cat;
		}
		
		if(is_array($row)){
			$this->ajaxObj->setContentFormat('jsonhead');
			$this->ajaxObj->setContent($row);
		}else{
			$this->ajaxObj->setError('no record found');
		}
	}
}

?>