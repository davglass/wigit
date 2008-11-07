<?php
/**
* Test file for git access
*/
include('class.git.php');
$git = new Git('./data/');

$git->add('Home');
$git->commit('Dav Changed Something', 'Dav Glass <dav.glass@yahoo.com>');

$history = $git->history('Home');

echo('<pre>'.print_r($history, 1).'</pre>');

?>
