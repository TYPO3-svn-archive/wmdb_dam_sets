<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
if (TYPO3_MODE == 'BE')	{
	// insert module functions into DAM list module
	t3lib_extMgm::insertModuleFunction(
		'txdamM1_list',
		'tx_wmdbdamsets_listfunc',
		t3lib_extMgm::extPath($_EXTKEY).'modfunc_list/class.tx_wmdbdamsets_listfunc.php',
		'LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets'
	);
	#tx_dam::register_action ('tx_wmdbdamsets_actions', 'EXT:wmdb_dam_sets/modfunc_list/class.tx_wmdbdamsets_actions.php:&tx_wmdbdamsets_actions');
	
}

$TCA["tx_wmdbdamsets_sets"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wmdbdamsets_sets.gif',
		'dividers2tabs'=>1
	),
);
$TCA["tx_wmdbdamsets_sets_assets_mm"] = array (
	"ctrl" => array (
		'title'     => 'Sets <=> Assets',		
		'label'     => 'uid_local',
		'label_alt' => 'uid_foreign',
		'label_alt_force'=>1,
		'sortby'=>'sorting',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath('dam').'icon_tx_dam.gif',
	),
);
/*
tx_dam::register_action ('tx_wmdbdamsets_action_setCurrent',
						 'EXT:wmdb_dam_sets/modfunc_list/class.tx_wmdbdamsets_actions.php:&tx_wmdbdamsets_actions','before:tx_dam_action_editRec');
*/
?>