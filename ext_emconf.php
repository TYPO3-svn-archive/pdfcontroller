<?php

########################################################################
# Extension Manager/Repository config file for ext "pdfcontroller".
#
# Auto generated 21-02-2012 13:31
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'PDF Controller',
	'description' => 'Offer your HTML page for PDF download. Controll the conversion with the user interface.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '2.0.3',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dirk Wildt (Die Netzmacher)',
	'author_email' => 'http://wildt.at.die-netzmacher.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.0.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'pdfcontroller_fonts' => '',
		),
	),
	'suggests' => array(
		'0' => 'pdfcontroller_fonts',
	),
	'_md5_values_when_last_written' => '',
);

?>
