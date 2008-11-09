<?php
class Wigit {
    
    public $config = array(
        'title' => 'Wigit',
        'defaultPage' => 'Home',
        'baseURL' => '/wigit/',
        'dataDir' => './data/',
        'theme' => 'default'
    );
    
    private $modes = array('edit', 'view', 'history');

    public $git = null;
    private $resource = null;
    public $theme = null;

    function __construct($userConfig) {
        $this->config['baseDir'] = realpath('.').'/';

        $this->parseConfig($userConfig);

        $this->connectGit();

        $this->fetchRequest();

        $this->initTheme();
    }

    private function initTheme() {
        $cfg = array(
            'dir' => $this->config['baseDir'].'themes/',
            'name' => $this->config['theme'],
            'title' => $this->config['title'],
            'resource' => $this->resource,
            'parent' => $this
        );
        $this->theme = new WigitTheme($cfg);
        $this->theme->render($this->getContent());
    }

    public function getContent() {
        if ($_GET['history']) {
            $data = $this->git->historyFile($_GET['history'], $this->resource['page']);
        } else {
            $data = @file_get_contents($this->resource['file']);
        }
        if ($this->resource['type'] == 'view') {
            //TODO put some logic here for which parser to use.
            $parser = &new Text_Wiki_Mediawiki();
            $parser->setRenderConf('xhtml', 'wikilink', 'view_url', $this->resource['baseURL']);
            $data = $parser->transform($data, 'Xhtml');
        }
        return $data;
    }

    private function fetchRequest() {
        $req = $_GET['r'];
        $this->log('fetching request', $req);

		$matches = array();
		$page = $req;
        $url = $req;
		$type = 'view';
        $file = null;
        
        $pos = strrpos($page, '/') + 1;
        $mode = strtolower(substr($page, $pos));
        $this->log('mode: ', $mode);
        if (in_array($mode, $this->modes)) {
            $page = substr($page, 0, $pos);
            $this->log('valid mode: ', $mode);
            $type = $mode;
        }
        $len = strlen($page);
        if (substr($page, ($len - 1)) == '/') {
            $page = substr($page, 0, ($len - 1));
        }


		if ($page == '') {
			$page = $this->config['defaultPage'];
		}
		if ($type == '') {
			$type = 'view';
            $mode = 'view';
		}
        
        if (strpos($page, '/') === true) {
            $title = stripslashes(substr($page, (strrpos($page, '/') + 1)));
        } else {
            $title = stripslashes($page);
        }
		$page = $this->sanitizeName($page);


        $this->resource = array(
            'page' => $page,
            'url' => $this->config['baseURL'].$url,
            'navurl' => $this->config['baseURL'].$page,
            'type' => $type,
            'file' => $file,
            'title' => $title
        );

        $file = $this->getFile($page);
        $this->resource['file'] = $file;

        if (($this->resource['type'] == 'edit') && ($_POST)) {
            $this->log('Edit mode..');
            $this->saveEdit();
        }
        
        $this->log('request', $this->resource);
    }

    private function saveEdit() {
        //TODO Add security call here when I write the auth class..

        $content = stripslashes($_POST['content']);

        $file = $this->resource['file'];
        
        if (!is_file($file)) {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            touch($file);
        }
        $fp = @file_put_contents($file, $content);
        if ($fp === false) {
            throw new ErrorException('Wigit failed to write the file, check permissions on the data dir: ('.realpath($this->config['dataDir']).')', 0, E_ERROR);
        }

		$msg = addslashes('Changed '.$this->resource['title']);
		$author = 'User <user@user.com>';
        $this->git->add($file);
        $this->git->commit($msg, $author, $file);
        
        $this->go($this->resource['navurl']);


    }

    public function go($url) {
        //echo('redirect: '.$url);
        //exit;
        header('Location: '.$url);
    }

    private function getFile($page) {
        $file = $this->config['dataDir'].'/'.$page;
        if (!is_file($file) && ($this->resource['type'] !== 'edit')) {
            $this->resource['type'] = 'none';
        }
        return $file;
    }

	public function sanitizeName($name) {
        $name = stripslashes($name);
		return ereg_replace("[^A-Za-z0-9\/]", "_", $name);
	}

    private function connectGit() {
        $this->log('before connect git');
        $this->git = new Git($this->config['dataDir']);
        $this->log('after connect git');
    }

    private function parseConfig($userConfig) {
        $this->log('parseConfig');
        $this->log('preConfig', $this->config);
        foreach($userConfig as $k => $v) {
            $this->config[$k] = $v;
        }
        $this->config['dataDir'] = realpath($this->config['dataDir']);
        $this->log('postConfig', $this->config);
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
