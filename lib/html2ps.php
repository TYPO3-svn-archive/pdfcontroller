<?php

/***************************************************************
 *  Notice
 *
 *  2011-07-23 Dirk Wildt <http://wildt.at.die-netzmacher.de>
 * 
 * This is a copy of the file ../res/html2ps_v2043/public_html/demo/html2ps
 * It is adapted to the needs of TYPO3 pdfcontroller.
 * 
 * See comment string 'dwildt' in the code below.
 *  
 ***************************************************************/


// $Header: /cvsroot/html2ps/demo/html2ps.php,v 1.10 2007/05/17 13:55:13 Konstantin Exp $

// 110723, dwildt -
//error_reporting(E_ALL);
ini_set("display_errors","1");
if (ini_get("pcre.backtrack_limit") < 1000000) { ini_set("pcre.backtrack_limit",1000000); };
@set_time_limit(10000);



// 110723, dwildt -
//require_once('generic.param.php');
//require_once('../config.inc.php');
// 110723, dwildt -

// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is starting: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +


$path_to_html2ps = t3lib_extMgm::extPath('pdfcontroller') . 'res/html2ps_v2043/public_html/';
require_once($path_to_html2ps . 'demo/generic.param.php');
require_once($path_to_html2ps . 'config.inc.php');
// 110723, dwildt +



//var_dump(HTML2PS_DIR); // /home/www/htdocs/typo3/typo3conf/ext/pdfcontroller/res/html2ps_v2043/public_html/
require_once(HTML2PS_DIR.'pipeline.factory.class.php');

ini_set("user_agent", DEFAULT_USER_AGENT);

$g_baseurl = trim(get_var('URL', $_REQUEST));

if ($g_baseurl === "") {
  die("Please specify URL to process!");
}

// Add HTTP protocol if none specified
if (!preg_match("/^https?:/",$g_baseurl)) {
  $g_baseurl = 'http://'.$g_baseurl;
}



// 110723, dwildt +
if(parse_url ($g_baseurl, PHP_URL_HOST) != $_SERVER['HTTP_HOST'])
{
  die('SAFETY: Sorry, html2ps is allowed to handle documents of the host ' . $_SERVER['HTTP_HOST'] . ' only. ' .
         'But you like to handle a document of the host ' . parse_url ($g_baseurl, PHP_URL_HOST) . '.');
}
// 110723, dwildt +



$g_css_index = 0;

// Title of styleshee to use (empty if no preferences are set)
$g_stylesheet_title = "";

// dwildt, 110721
//var_dump($_GET);
//return;
$GLOBALS['g_config'] = array(
                             'compress'      => isset($_REQUEST['compress']),
                             'cssmedia'      => get_var('cssmedia', $_REQUEST, 255, "screen"),
                             'debugbox'      => isset($_REQUEST['debugbox']),
                             'debugnoclip'   => isset($_REQUEST['debugnoclip']),
                             'draw_page_border'        => isset($_REQUEST['pageborder']),
                             'encoding'      => get_var('encoding', $_REQUEST, 255, ""),
                             'html2xhtml'    => !isset($_REQUEST['html2xhtml']),
                             'imagequality_workaround' => isset($_REQUEST['imagequality_workaround']),
                             'landscape'     => isset($_REQUEST['landscape']),
                             'margins'       => array(
                                                      'left'    => (int)get_var('leftmargin',   $_REQUEST, 10, 0),
                                                      'right'   => (int)get_var('rightmargin',  $_REQUEST, 10, 0),
                                                      'top'     => (int)get_var('topmargin',    $_REQUEST, 10, 0),
                                                      'bottom'  => (int)get_var('bottommargin', $_REQUEST, 10, 0),
                                                      ),
                             'media'         => get_var('media', $_REQUEST, 255, "A4"),
                             'method'        => get_var('method', $_REQUEST, 255, "fpdf"),
                             'mode'          => 'html',
                             'output'        => get_var('output', $_REQUEST, 255, ""),
                             'pagewidth'     => (int)get_var('pixels', $_REQUEST, 10, 800),
                             'pdfversion'    => get_var('pdfversion', $_REQUEST, 255, "1.2"),
                             'ps2pdf'        => isset($_REQUEST['ps2pdf']),
                             'pslevel'       => (int)get_var('pslevel', $_REQUEST, 1, 3),
                             'renderfields'  => isset($_REQUEST['renderfields']),
                             'renderforms'   => isset($_REQUEST['renderforms']),
                             'renderimages'  => isset($_REQUEST['renderimages']),
                             'renderlinks'   => isset($_REQUEST['renderlinks']),
                             'scalepoints'   => isset($_REQUEST['scalepoints']),
                             'smartpagebreak' => isset($_REQUEST['smartpagebreak']),
                             'transparency_workaround' => isset($_REQUEST['transparency_workaround'])
                             );
// dwildt, 110725
//var_dump($GLOBALS['g_config']);
//return;

$proxy = get_var('proxy', $_REQUEST, 255, '');



// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is parsing the config file: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +



// ========== Entry point
// 110723, dwildt -
//parse_config_file('../html2ps.config');
// 110723, dwildt +
parse_config_file(HTML2PS_DIR.'html2ps.config');

// validate input data
if ($GLOBALS['g_config']['pagewidth'] == 0) {
  die("Please specify non-zero value for the pixel width!");
};



// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is starting the process: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +



// begin processing

$g_media = Media::predefined($GLOBALS['g_config']['media']);
$g_media->set_landscape($GLOBALS['g_config']['landscape']);
$g_media->set_margins($GLOBALS['g_config']['margins']);
$g_media->set_pixels($GLOBALS['g_config']['pagewidth']);

// Initialize the coversion pipeline
$pipeline = new Pipeline();
$pipeline->configure($GLOBALS['g_config']);

// Configure the fetchers
if (extension_loaded('curl')) {
  require_once(HTML2PS_DIR.'fetcher.url.curl.class.php');
  $pipeline->fetchers = array(new FetcherUrlCurl());
  if ($proxy != '') {
    $pipeline->fetchers[0]->set_proxy($proxy);
  };
} else {
  require_once(HTML2PS_DIR.'fetcher.url.class.php');
  $pipeline->fetchers[] = new FetcherURL();
};

// Configure the data filters
$pipeline->data_filters[] = new DataFilterDoctype();
$pipeline->data_filters[] = new DataFilterUTF8($GLOBALS['g_config']['encoding']);
if ($GLOBALS['g_config']['html2xhtml']) {
  $pipeline->data_filters[] = new DataFilterHTML2XHTML();
} else {
  $pipeline->data_filters[] = new DataFilterXHTML2XHTML();
};

$pipeline->parser = new ParserXHTML();

// "PRE" tree filters

$pipeline->pre_tree_filters = array();

$header_html    = get_var('headerhtml', $_REQUEST, 65535, "");
$footer_html    = get_var('footerhtml', $_REQUEST, 65535, "");
$filter = new PreTreeFilterHeaderFooter($header_html, $footer_html);
$pipeline->pre_tree_filters[] = $filter;
if ($GLOBALS['g_config']['renderfields']) {
  // 110723, dwildt +
  if ($this->b_drs_perform)
  {
    $endTime = $this->TT->getDifferenceToStarttime();
    t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is starting to render fields: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
  }
  // 110723, dwildt +
  $pipeline->pre_tree_filters[] = new PreTreeFilterHTML2PSFields();
  // 110723, dwildt +
  if ($this->b_drs_perform)
  {
    $endTime = $this->TT->getDifferenceToStarttime();
    t3lib_div::devLog('[INFO/PERFORMANCE] html2ps has finished to render fields: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
  }
  // 110723, dwildt +
};

// 

if ($GLOBALS['g_config']['method'] === 'ps') {
  $pipeline->layout_engine = new LayoutEnginePS();
} else {
  $pipeline->layout_engine = new LayoutEngineDefault();
};

$pipeline->post_tree_filters = array();

// Configure the output format
if ($GLOBALS['g_config']['pslevel'] == 3) {
  $image_encoder = new PSL3ImageEncoderStream();
} else {
  $image_encoder = new PSL2ImageEncoderStream();
};

switch ($GLOBALS['g_config']['method']) {
 case 'fastps':
   if ($GLOBALS['g_config']['pslevel'] == 3) {
     $pipeline->output_driver = new OutputDriverFastPS($image_encoder);
   } else {
     $pipeline->output_driver = new OutputDriverFastPSLevel2($image_encoder);
   };
   break;
 case 'pdflib':
   $pipeline->output_driver = new OutputDriverPDFLIB16($GLOBALS['g_config']['pdfversion']);
   break;
 case 'fpdf':
   $pipeline->output_driver = new OutputDriverFPDF();
   break;
 case 'png':
   $pipeline->output_driver = new OutputDriverPNG();
   break;
 case 'pcl':
   $pipeline->output_driver = new OutputDriverPCL();
   break;
 default:
   die("Unknown output method");
};

// Setup watermark
$watermark_text = trim(get_var('watermarkhtml', $_REQUEST, 65535, ""));
if ($watermark_text != '') {
  $pipeline->add_feature('watermark', array('text' => $watermark_text));
};

if ($GLOBALS['g_config']['debugbox']) {
  $pipeline->output_driver->set_debug_boxes(true);
}

if ($GLOBALS['g_config']['draw_page_border']) {
  $pipeline->output_driver->set_show_page_border(true);
}

if ($GLOBALS['g_config']['ps2pdf']) {
  $pipeline->output_filters[] = new OutputFilterPS2PDF($GLOBALS['g_config']['pdfversion']);
}

if ($GLOBALS['g_config']['compress'] && $GLOBALS['g_config']['method'] == 'fastps') {
  $pipeline->output_filters[] = new OutputFilterGZip();
}

if (get_var('process_mode', $_REQUEST) == 'batch') {
  $filename = "batch";
} else {
  $filename = $g_baseurl;
};



// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is configuring the output: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +



switch ($GLOBALS['g_config']['output']) {
 case 0:
   $pipeline->destination = new DestinationBrowser($filename);
   break;
 case 1:
   $pipeline->destination = new DestinationDownload($filename);
   break;
 case 2:
   $pipeline->destination = new DestinationFile($filename, 'File saved as: <a href="%link%">%name%</a>');
   break;
};

// Add additional requested features
if (isset($_REQUEST['toc'])) {
  $pipeline->add_feature('toc', array('location' => isset($_REQUEST['toc-location']) ? $_REQUEST['toc-location'] : 'after'));
};

if (isset($_REQUEST['automargins'])) {
  $pipeline->add_feature('automargins', array());
};



// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is starting conversion: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +



// Start the conversion

$time = time();
if (get_var('process_mode', $_REQUEST) == 'batch') {
  $batch = get_var('batch', $_REQUEST);

  for ($i=0; $i<count($batch); $i++) {
    if (trim($batch[$i]) != "") {
      if (!preg_match("/^https?:/",$batch[$i])) {
        $batch[$i] = "http://".$batch[$i];
      }
    };
  };

  $status = $pipeline->process_batch($batch, $g_media);
} else {
  $status = $pipeline->process($g_baseurl, $g_media);
};



// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps has finished the conversion: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +



error_log(sprintf("Processing of '%s' completed in %u seconds", $g_baseurl, time() - $time));

if ($status == null) {
  print($pipeline->error_message());
  error_log("Error in conversion pipeline");
  die();
}



// 110723, dwildt +
if ($this->b_drs_perform)
{
  $endTime = $this->TT->getDifferenceToStarttime();
  t3lib_div::devLog('[INFO/PERFORMANCE] html2ps is ending: '. ($endTime - $this->startTime).' ms', $this->extKey, 0);
}
// 110723, dwildt +



?>