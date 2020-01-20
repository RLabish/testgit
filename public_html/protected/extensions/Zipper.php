<?php

class Zipper extends ZipArchive {
	private $sub_path;
   
public function _addDir($path) {
	$p = pathinfo($path,  PATHINFO_BASENAME);
	if (($p != '.') and ($p != '..')) {
		$this->addEmptyDir(substr($path, strlen($this->sub_path) + 1));			
	}
    $nodes1 = glob($path . '/*');	
	$nodes2 = glob($path . '/.*');	
	$nodes = array();
	if (is_array($nodes1)) $nodes = array_merge($nodes, $nodes1);
	if (is_array($nodes2)) $nodes = array_merge($nodes, $nodes2);

    foreach ($nodes as $node) {
		$p = pathinfo($node);

		if (array_key_exists('extension', $p) and ($p['extension'] == '') and (
			 ($p['filename'] == '') or ($p['filename'] == '.'))) {
			continue;			
		}
		
		
        if (is_dir($node)) {
            $this->_addDir($node);
        } else if (is_file($node))  {
            $this->addFile($node, substr($node, strlen($this->sub_path) + 1));
        }
    }

}

public function addDir($path) {
	$this->sub_path = $path;
	$this->_addDir($path);
}

   
} // class Zipper 