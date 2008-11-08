<?php

#echo('<pre>'.print_r($this, 1).'</pre>');

$url = $this->config['resource']['navurl'].'/edit';
$page = $this->config['resource']['page'];

echo('<p>Describe <a href="'.$url.'">'.$page.'</a> here.</p>');

?>
