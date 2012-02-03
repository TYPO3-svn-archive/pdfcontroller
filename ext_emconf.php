<?php

########################################################################
# Extension Manager/Repository config file for ext "pdfcontroller".
#
# Auto generated 23-07-2011 03:46
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
  'version' => '1.1.0',
  'dependencies' => '',
  'conflicts' => '',
  'priority' => '',
  'loadOrder' => '',
  'module' => '',
  'state' => 'alpha',
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
      'php' => '5.1.0-0.0.0',
      'typo3' => '4.4.6-0.0.0',
    ),
    'conflicts' => array(
    ),
    'suggests' => array(
      'pdfcontroller_fonts' => '1.0.0-0.0.0',
    ),
  ),
  'suggests' => array(
    'pdfcontroller_fonts',
  ),
);

?>