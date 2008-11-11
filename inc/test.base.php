<?php
include('class.base.php');

class Bar extends Base {
    
    protected $cfg = array(
        'one' => 'one'
    );

    function __construct($config) {
        parent::__construct($config);
    }
    
}
class Foo extends Bar {

    function __construct($config) {
        parent::__construct($config);
    }
    
}

$cfg = array();
$cfg['one'] = array();
$cfg['two'] = 'two';
$cfg['three'] = 'three';

$foo = &new Foo($cfg);

echo('two: '.$foo->get('two')."\n");
echo('foo: '.$foo->get('foo')."\n");

?>
