<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
if (TYPO3_MODE == 'BE')	{
	$TYPO3_CONF_VARS['BE']['AJAX']['tx_wmdbdamsets_ajax'] = t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_wmdbdamsets_ajax.php:tx_wmdbdamsets_ajax->main';
	//used anywhere? depricated?
	tx_dam::register_mediaTable('tx_wmdbdamsets_sets');
}
?>