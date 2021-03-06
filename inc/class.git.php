<?php
/**
* Git Abstraction class
*/

class Git extends Base {
    
    private $_init = false;

    protected $cfg = array(
        'bin' => '/usr/bin/git'
    );
    
    /**
    * 
    */
    function __construct($path) {
        $cfg = array('path' => $path);
        parent::__construct($cfg);
        $this->_getBin();
        $this->_getWorkingTree($path);
    }
    
    private function _getBin() {
        $cmd = `which git`;
        //Kill the new line
        $cmd = trim($cmd);
        if (is_executable($cmd)) {
            $this->set('bin', $cmd);
        } else {
            throw new ErrorException('Git failed to locate a valid git executable', 0, E_ERROR);
        }
    }

    private function _getWorkingTree($path) {
        $path = realpath($path);
        if (is_dir($path)) {
            if (is_writable($path)) {
                $this->set('tree', $path);
                $repo = $path.'/.git';
                $this->set('repo', $repo);
                if (is_dir($repo)) {
                    $this->_init = true;
                } else {
                    //Init the tree?
                    $this->init();
                }
            } else {
                throw new ErrorException('Git repository path not writable', 0, E_ERROR);
            }
        } else {
            throw new ErrorException('Git failed to locate a the repository path', 0, E_ERROR);
        }
    }

    public function init() {
        if ($this->_init) {
            return false;
        }
        $this->_init = true;
        $this->git('init');
        return 1;
    }
    
    public function add($file, &$output = '') {
        $this->git('add '.$file, $output);
        return $output;
    }

    public function rm($file, &$output = '') {
        $this->git('rm '.$file, $output);
        return $output;
    }

    public function log($cmd, &$output = '') {
        $this->git('log '.$cmd, $output);
        return $output;
    }

    public function gc(&$output = '') {
        $this->git('gc', $output);
        return $output;
    }

    public function commit($msg, $author, $file='', &$output = '') {
        $this->git('commit --allow-empty --no-verify --message="'.addslashes($msg).'" --author="'.$author.'" '.$file, $output);
        return $output;
    }

    public function historyCount($file, $max=false) {
        return count($this->historyList($file, $max));
    }

    public function historyList($file, $max=false) {
        $output = array();
        $max = "";
        if ($max !== false) {
            $max = " --max-count=$max ";
        }
        $this->log("--pretty=format:'%H' -- ".$max.$file, $output);
        return $output;
    }

    public function historyFile($sha, $file, &$output = '') {
        $this->git('cat-file -p '.$sha.':'.$file, $output);
        return join("\n", $output);
    }


	public function history($file = "", $num=10) {
        if ($num === false) {
            //TODO Make this a config option?
            //Maxing out at 50 items..
            $num = 50;
        }
		$output = array();
		// FIXME: Find a better way to find the files that changed than --name-only
		$this->log("--name-only --pretty=format:'%H@|@%T@|@%an@|@%ae@|@%aD@|@%s' --max-count=$num -- $file", $output);
		$history = array();
		$historyItem = array();
		foreach ($output as $line) {
            $logEntry = explode("@|@", $line, 6);
            if (sizeof($logEntry) > 1) {
                // Populate history structure
                $historyItem = array(
                        "author" => $logEntry[2], 
                        "email" => $logEntry[3],
                        "linked-author" => (
                                $logEntry[3] == "" ? 
                                    $logEntry[2] 
                                    : "<a href=\"mailto:$logEntry[3]\">$logEntry[2]</a>"),
                        "date" => $logEntry[4], 
                        "message" => $logEntry[5],
                        "commit" => $logEntry[0]
                    );
            } else if (!isset($historyItem["file"])) {
                $historyItem["file"] = $line;
                $history[] = $historyItem;
            }
		}
		return $history;
	}


    private function git($cmd, &$output = "") {
        $gitCmd = $this->get('bin').' --git-dir='.$this->get('repo').' --work-tree='.$this->get('tree').' '.$cmd;

        //echo('cmd: '.$gitCmd."<br>");

        $output = array();
        $result;

		$oldUMask = umask(0022);
		exec($gitCmd . " 2>&1", $output, $result);
		umask($oldUMask);
        if ($result != 0) {
            throw new ErrorException('Git action failed: '.$gitCmd.'<br>'.(join("\n", $output)), 0, E_ERROR);
        }
        return 1;
    }

}

?>
