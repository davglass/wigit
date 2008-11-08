<?php
class WigitTheme {
    
    public $config = array();
    private $themeDir = '';
    private $header = 'header.php';
    private $footer = 'footer.php';
    private $content = 'view.php';
    private $data = '';

    function __construct($config) {
        $this->config = $config;
        $this->log('theme config', $this->config);
        $this->getThemeDir();
        $this->getParts();
    }

    private function getParts() {
        $header = $this->themeDir.'/'.$this->header;
        if (!is_file($header)) {
            throw new ErrorException('WigitTheme failed to locate the header file: ('.$dir.'/'.$this->header.')', 0, E_ERROR);
        }
        $this->header = $header;

        $footer = $this->themeDir.'/'.$this->footer;
        if (!is_file($header)) {
            throw new ErrorException('WigitTheme failed to locate the footer file: ('.$dir.'/'.$this->footer.')', 0, E_ERROR);
        }
        $this->footer = $footer;

        $content = $this->themeDir.'/'.$this->config['resource']['type'].'.php';
        if (!is_file($content)) {
            throw new ErrorException('WigitTheme failed to locate the content file: ('.$dir.'/'.$this->content.')', 0, E_ERROR);
        }
        $this->content = $content;
    }

    public function render($data='') {
        $this->data = $data;
        $this->log('Render Theme');
        include($this->header);
        include($this->content);
        include($this->footer);
    }

    public function getContent() {
        return $this->data;
    }

    private function getThemeDir() {
        $dir = $this->config['dir'].$this->config['name'];
        if (!is_dir($dir)) {
            throw new ErrorException('WigitTheme failed to locate the theme directory: ('.$dir.')', 0, E_ERROR);
        }
        $this->themeDir = $dir;
    }

    public function log($title, $str='') {
        if (!$GLOBALS['debug']) {
            return;
        }
        if (!$str) {
            echo('<strong>'.$title.'</strong><br>');
        } else {
            echo('<strong>'.$title.'</strong><br><pre>'.print_r($str, 1).'</pre>');
        }
    }
}
?>
