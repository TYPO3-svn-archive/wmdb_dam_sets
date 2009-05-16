<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('wmdb_dam_sets').'lib/class.user_damsets_tceforms.php');
$TCA["tx_wmdbdamsets_sets"] = array (
	"ctrl" => $TCA["tx_wmdbdamsets_sets"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,title,description,categories,assets"
	),
	"feInterface" => $TCA["tx_wmdbdamsets_sets"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		/*
		 * LANGUAGE
		 */
		'sys_language_uid' => array(
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dam',
				'foreign_table_where' => 'AND tx_dam.uid=###REC_FIELD_l18n_parent### AND tx_dam.sys_language_uid IN (-1,0)',
				'wizards' => array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,

					'edit' => array(
							'type' => 'popup',
							'title' => 'edit default language version of this record ',
							'script' => 'wizard_edit.php',
							'popup_onlyOpenIfSelected' => 1,
							'icon' => 'edit2.gif',
							'JSopenParams' => 'height=600,width=700,status=0,menubar=0,scrollbars=1,resizable=1',
					),
				),
			)
		),
		'l18n_diffsource' => array(
			'config'=>array(
				'type' => 'passthrough'
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
				'eval' => 'required',
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",	
				"rows" => "5",
			)
		),
		
		"categories" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.categories",		
			"config" => Array (
				"type" => "select",
				"form_type"=>"user",
				"userFunc"=>"EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_selectTree",
				"treeViewBrowseable"=>1,
				"treeViewClass"=>"EXT:dam/components/class.tx_dam_selectionCategory.php:&tx_dam_selectionCategory",
				"foreign_table"=>"tx_dam_cat",
				"size" => 6,	
				"autoSizeMax"=>20,
				"minitems" => 0,
				"maxitems" => 100,	
				"MM" => "tx_wmdbdamsets_sets_categories_mm",
			)
		),
		"assets" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.assets",		
			"config" => Array (
				'type'=>'select',
				'form_type'=>'user',
				'userFunc' => 'user_damsets_tceforms->user_damset_assets',
				'foreign_table'=>'tx_dam',
				"size" => 6,	
				"autoSizeMax"=>20,
				"minitems" => 0,
				"maxitems" => 100,	
				'MM'=>'tx_wmdbdamsets_sets_assets_mm'
			)
		),
		'language' => array(
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:dam/locallang_db.xml:tx_dam_item.language',
			'exclude' => '0',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('',''),
				),
				'size' => '1',
				'maxitems' => '1',
				'default' => 'DE',
				'itemsProcFunc' => 'tx_staticinfotables_div->selectItemsTCA',
				'itemsProcFunc_config' => array(
					'table' => 'static_languages',
					'indexField' => 'lg_iso_2',
					'prependHotlist' => 1,
					'hotlistApp' => 'dam',
				),
			)
		),
		"starttime" => $TCA['tt_content']['columns']['starttime'],
		"endtime" => $TCA['tt_content']['columns']['endtime'],
	),
	"types" => array (
		"0" => array("showitem" => "--div--;LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.assets,assets,--div--;LLL:EXT:wmdb_dam_sets/locallang_db.xml:tx_wmdbdamsets_sets.description,title;;1;;,description,language;;;;3-3-3, categories")
	),
	"palettes" => array (
		"1" => array("showitem" => "hidden,starttime,endtime")
	)
);

$TCA["tx_wmdbdamsets_sets_assets_mm"] = array (
	"ctrl" => $TCA["tx_wmdbdamsets_sets_assets_mm"]["ctrl"],
	'columns' => array(
		'uid_local'	=> array(
			'config'=>array(
				'type'=>'select',
				'foreign_table'=>'tx_wmdbdamsets_sets',
				'maxitems'=>1
			)
		),
		'uid_foreign' => array(
			'config'=>array(
				'type'=>'inline',
				'foreign_table'=>'tx_dam',
				'maxitems'=>1,
			)
		),
		'sorting' => array(
			'label'=>'',
			'config'=>array(
				'type'=>'passthrough'
			)
		)
	),
	"types" => array (
		"0" => array("showitem" => "setid,assetid")
	),
);

?>