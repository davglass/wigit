<?php
class WigitTheme extends Base {
    
    protected $cfg = array(
        'header' => 'header.php',
        'footer' => 'footer.php',
        'content' => 'view',
    );

    function __construct($config) {
        parent::__construct($config);
        $this->logger('theme config', $this->config);
        $this->getThemeDir();
        $this->getParts();
    }

    private function getParts() {
        $header = $this->get('themeDir').'/'.$this->get('header');
        if (!is_file($header)) {
            throw new ErrorException('WigitTheme failed to locate the header file: ('.$dir.'/'.$this->header.')', 0, E_ERROR);
        }
        $this->set('header', $header);

        $footer = $this->get('themeDir').'/'.$this->get('footer');
        if (!is_file($header)) {
            throw new ErrorException('WigitTheme failed to locate the footer file: ('.$dir.'/'.$this->footer.')', 0, E_ERROR);
        }
        $this->set('footer', $footer);
        
        $r = $this->get('resource');
        $content = $this->get('themeDir').'/'.$r['type'].'.php';
        if (!is_file($content)) {
            throw new ErrorException('WigitTheme failed to locate the content file: ('.$dir.'/'.$this->content.')', 0, E_ERROR);
        }
        $this->set('content', $content);
    }

    public function render($data='') {
        $this->data = $data;
        $this->logger('Render Theme');
        include($this->get('header'));
        include($this->get('content'));
        include($this->get('footer'));
    }

    public function getContent() {
        return $this->data;
    }

    private function getThemeDir() {
        $dir = $this->get('dir').$this->get('name');
        if (!is_dir($dir)) {
            throw new ErrorException('WigitTheme failed to locate the theme directory: ('.$dir.')', 0, E_ERROR);
        }
        $this->set('themeDir', $dir);
    }

}
?>
