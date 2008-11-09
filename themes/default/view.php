<div id="view">
<?php echo($this->getContent()); ?>
</div>
<div id="history">
<ul>
<?php
$history = $this->config['parent']->git->historyList($this->config['resource']['page']);
foreach ($history as $k => $v) {
    echo('<li'.(($_GET['history'] == $v) ? ' class="on"' : '').'><a href="'.$this->config['resource']['navurl'].'?history='.$v.'">'.($k + 1).'</a></li>');
}
?>
</ul>
</div>
