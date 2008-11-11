<?php
$debug = true;

require_once('Text/Wiki/Mediawiki.php');
require_once('Text/Wiki/BBCode.php');

$wigit_config = array();

include('config.php');
include('inc/class.base.php');
include('inc/class.git.php');
include('inc/class.theme.php');
include('inc/class.wigit.php');

$wigit = new Wigit($wigit_config);

?>
