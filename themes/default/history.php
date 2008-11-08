<?php


$history = $this->config['parent']->git->history($this->config['resource']['page']);

echo('<table>');
$count = 1;
foreach($history as $k => $item) {
    echo('<tr>');
    echo('  <td><a href="'.$item['commit'].'">#'.$count.'</a></td>');
    echo('  <td>'.$item['linked-author'].'</td>');
    echo('  <td>'.$item['date'].'</td>');
    echo('  <td>'.$item['message'].'</td>');
    echo('</tr>');
    $count++;
}
echo('</table>');

#echo('<pre>'.print_r($history, 1).'</pre>');

?>
