<?php

//include_once('pclzip.lib.php'); 
include_once('Zipper.php'); 

class XLReport extends CComponent {
	public $name;
	public $workDir;
	public $workBookDoc;
	public $calcChainDoc;
	private $ranges;
	
	function __destruct() {
		$this->clear();
	}
		
	public function start($template, $vars = null) {
		$this->clear();
//		$this->workDir = YiiBase::getPathOfAlias('application.runtime.xls.'.str_replace('.', '_', microtime(true))).'/';
		$this->workDir = Yii::app()->runtimePath.'/xls/'.str_replace('.', '_', microtime(true)).'/';

		$i = strpos($template, '.');
		if ($i === FALSE)
			$this->name = $template;
		else
			$this->name = substr($template, 0, $i);
		
		if (isset(Yii::app()->theme))
			$templateFileName = Yii::app()->theme->basePath.'/templates/excel/'.$template.'.xlsx';
		if (!isset($templateFileName) || !file_exists($templateFileName))
			$templateFileName = Yii::app()->basePath.'/templates/excel/'.$template.'.xlsx';

		$zip = new ZipArchive();
		$zip->open($templateFileName);
		if (!$zip->extractTo($this->workDir.'xlsx'))
			throw new Exception($zip->errorInfo(true));
		
//		$zip = new PclZip($templateFileName);
//		if($zip->extract(PCLZIP_OPT_PATH, $this->workDir.'xlsx') == 0)
//			throw new Exception($zip->errorInfo(true));

		$this->ranges = array();
		$this->workBookDoc = new DOMDocument();
		$this->workBookDoc->load(realpath($this->workDir.'xlsx/xl/workbook.xml'));
		$this->calcChainDoc = new DOMDocument();
		$this->calcChainDoc->load(realpath($this->workDir.'xlsx/xl/calcChain.xml'));

		if ($vars && (count($vars) > 0)) {
			$sheetFN = realpath($this->workDir.'xlsx/xl/worksheets/sheet1.xml');
			$sheetDoc = new DOMDocument();
			$sheetDoc->load($sheetFN);
			foreach ($this->calcChainDoc->documentElement->childNodes as $n) {
				$r = $n->attributes->getNamedItem('r')->nodeValue;
				$vnode = null;				
				foreach ($sheetDoc->documentElement->getElementsByTagName('sheetData')->item(0)->childNodes as $nrow) {
					foreach ($nrow->childNodes as $ncol) {
						if ($ncol->attributes->getNamedItem('r')->nodeValue == $r) {
							if ($ncol->getElementsByTagName('f') != null) {
								$vnode = $ncol;
								break;
							}
						}
					}
					if ($vnode != null)
						break;
				}
				$varName = $ncol->getElementsByTagName('f')->item(0)->nodeValue;
				if (isset($vars[$varName])) { 
					$ncol->getElementsByTagName('v')->item(0)->nodeValue = $vars[$varName];
					$ncol->attributes->getNamedItem('t')->nodeValue = 'str';
				}  
			}
			$sheetDoc->save($sheetFN);
		}
		
		foreach ($this->workBookDoc->getElementsByTagName('definedName') as $definedName) {
			if (isset($definedName->nodeValue) && (strpos($definedName->nodeValue, 'REF!') === FALSE)) {
				$r = new XLRange($this, $definedName);
				$this->ranges[$r->name] = $r;
			}
		}
	}
	
	public function add($range, $data) {
		if (!isset($this->ranges[$range]))
			throw new Exception("Range \"$range\" not found");
		$this->ranges[$range]->add($data);
	}
		
		
	public function complete() {
		foreach ($this->ranges as $range)
			$range->complete();	
		$n = $this->calcChainDoc->documentElement; 
		$fn = realpath($this->workDir.'xlsx/xl/calcChain.xml');
		if ($n->hasChildNodes()) {
			$n = $n->firstChild->setAttribute('i', 3);
			$this->calcChainDoc->save($fn);
		}	
		else
			unlink($fn);							
		$reportFileName = Yii::app()->runtimePath.'/xls/'.$this->name.'-'.str_replace('.', '_', microtime(true)).'.xlsx';

		$zip = new Zipper();
		$zip->open($reportFileName);
		if ($zip->open($reportFileName, ZipArchive::CREATE)!== TRUE) 
			throw new Exception("Ошибка открытия файла: ".$reportFileName);
		chdir($this->workDir.'/xlsx');
		
		$zip->addDir('.');
		$zip->close();

		ob_end_clean();
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'.$this->name.'.xlsx"');
		header('Cache-Control: max-age=0');
		echo file_get_contents($reportFileName);
		Yii::app()->end();
		unlink($reportFileName);
		
		$this->clear();
	}
	
	private function clear() {
		return;
		if ($this->workDir == NULL)
			return;		
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->workDir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) 
			$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
		rmdir($this->workDir);
		$this->workDir = NULL; 
	}
	
	public function make($template, $range, $provider, $columns)
	{
		$this->start($template);		
		$provider->pagination = false;
		$offset = 0;
		while (true) {
			$crit = $provider->criteria;
			$crit->offset = $offset;
			$crit->limit = 1000;
			$provider->setCriteria($crit);
			$data = $provider->getData(true);			
			if (count($data) == 0)
				break;
			foreach ($data as $row) {
				$offset++;
				$band = array();
				foreach ($columns as $col) {
					$name = null; 
					$label = null;
					$value = null;
					$item = array();
					if (gettype($col) == "array" ) {
						$name = $col['name'];
						if (isset($col['label']))
							$item['label'] = $col['label']; 						
						if (isset($col['format']))
							$item['format'] = $col['format']; 						
						if (isset($col['value']))
							$value = $col['value']; 						
					}
					else $name = $col;
					if ($label == null)
						$label = $name;
					if ($value == null) 
						$value = '$data->'.$name;	
					$item['value'] = $this->evaluateExpression($value,array('data'=>$row));
					$band[$label] = $item;
				}
				$this->add($range, $band);
			}
		}
		$this->complete();			
	}

	public function test() {
		$this->start('talons');		
		$this->add('data', array('BarCode'=>'#bc-1', ));
		$this->add('data', array('BarCode'=>'#bc-2', ));
		$this->add('data', array('BarCode'=>'#bc-3', ));
		$this->add('data', array('BarCode'=>'#bc-4', ));
		$this->complete();
		echo '<br>TEST: OK<br>';											
	}
}

class XLRange {
	private $owner;
	public  $name;
	private $shDoc;
	private $shHnd;
	private $rectCells;
	private $firstDataRowNo;
	private $dataRowCount;
	private $tmplNode;
	private $valueIndexes;

	function __construct ($owner, $domDefine) {
		$this->owner = $owner;
		$this->name = $domDefine->attributes->getNamedItem('name')->nodeValue;
		$this->rectCells = array();
		list($shName, $cells) = explode('!', $domDefine->nodeValue);
		foreach ($owner->workBookDoc->getElementsByTagName('sheets')->item(0)->childNodes as $node) {
			if ($node->attributes->getNamedItem('name')->nodeValue == $shName) {
				foreach ($node->attributes as $attr) {
					if ($attr->nodeName == 'r:id') {
						$s = realpath($owner->workDir.'xlsx/xl/worksheets/sheet'.substr($attr->nodeValue, 3).'.xml');
						$this->shDoc = new DOMDocument();
						$this->shDoc->load($s);
						$this->shHnd = fopen($s, 'w');
						break;
					}
				}
				break;
			}
		}
		if (isset($cells)) {
			list($a, $b) = explode(':', $cells);
			if (isset($a) && isset($b)) {
				$c = explode('$', $a);
				$d = explode('$', $b);
				$this->rectCells = array('c1' => $c[1], 'r1' => $c[2], 'c2' => $d[1], 'r2' => $d[2],);
			}
		}
		fwrite($this->shHnd, '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'."\n\r");
		fwrite($this->shHnd, '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'."\n\r");
		$cancel = false;
		foreach ($this->shDoc->documentElement->childNodes as $node) {
			if ($node->nodeName == 'sheetData') {
				fwrite($this->shHnd, '<sheetData>');
				foreach ($node->childNodes as $node2) {
					if (($node2->nodeName == 'row') && ($node2->attributes->getNamedItem('r')->nodeValue == $this->rectCells['r1'])) {
						$this->firstDataRowNo = $this->rectCells['r1'];
						$this->dataRowCount = 0;
						$this->tmplNode = $node2;
						$cancel = true;
						break;
					}
					else fwrite($this->shHnd, $node2->ownerDocument->saveXML($node2));
				}
			}
			if ($cancel)
				break;
			fwrite($this->shHnd, $node->ownerDocument->saveXML($node)."\n\r");
		}
		$this->tmplNode->attributes->getNamedItem('r')->nodeValue = '';
		$this->valueIndexes = array();
		$idx = -1;
		foreach ($this->tmplNode->childNodes as $col) {
			$colName = $col->attributes->getNamedItem('r')->nodeValue;
			$col->attributes->getNamedItem('r')->nodeValue = substr($colName, 0, 1);
			if (!$col->hasChildNodes())
				continue;
			$n = $col->childNodes->item(0);
			if ($n->nodeName != 'f')
				continue;
			$this->valueIndexes[$n->nodeValue] = ++$idx;
			while (isset($col->firstChild))
				$col->removeChild($col->firstChild);
			$col->appendChild($n->ownerDocument->createElement('v', ''));
			$col->attributes->getNamedItem('t')->nodeValue = 'str';

			foreach ($owner->calcChainDoc->documentElement->getElementsByTagName('*') as $n) {
				$attr = $n->attributes->getNamedItem('r');
				if ($attr->nodeValue == $colName) {
					$this->owner->calcChainDoc->documentElement->removeChild($n);
					break;
				}
			}
		}
	}

	function __destruct() {
		if ($this->shHnd != NULL)
			fclose($this->shHnd);
	}

	public function add($data) {
		$rowNo = $this->firstDataRowNo + $this->dataRowCount;
		$this->dataRowCount++;
		$doc = new DOMDocument();
		$doc->loadXML('<?xml version="1.0"?><root xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"/>');
		$row = $doc->documentElement->appendChild($doc->importNode($this->tmplNode, true));
		$row->attributes->getNamedItem('r')->nodeValue = $rowNo;
		foreach ($row->childNodes as $n) {
			$attr = $n->attributes->getNamedItem('r');
			$attr->nodeValue = $attr->nodeValue.$rowNo;
		}
		foreach ($data as $name => $val) {
			if (!isset($this->valueIndexes[$name]))
				break;
			$n = $row->childNodes->item($this->valueIndexes[$name]);
			$v = $val;
			if (gettype($val) == "array" ) {
				if (isset($val['value']))
					$v = $val['value'];
				else
					$v = '';
				if (isset($val['format'])) {
					if ($val['format'] == 'numeric')
						$n->removeAttribute('t');
				}
			}
			$n->childNodes->item(0)->nodeValue = $v;
		}
		fwrite($this->shHnd, $doc->saveXML($row)."\n\r");
	}

	public function complete() {
		$doc = new DOMDocument();
		$doc->loadXML('<?xml version="1.0"?><root xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"/>');
		$nodes = $this->shDoc->getElementsByTagName('sheetData')->item(0)->childNodes;
		for ($i = $this->firstDataRowNo; $i < $nodes->length; $i++) {
			$node = $nodes->item($i);
			$row = $doc->documentElement->appendChild($doc->importNode($node, true));
			$rowNo = $row->attributes->getNamedItem('r')->nodeValue + $this->dataRowCount;
			$row->attributes->getNamedItem('r')->nodeValue = $rowNo;
			foreach ($row->childNodes as $col) {
				$attr = $col->attributes->getNamedItem('r');
				$attr->nodeValue = substr($attr->nodeValue, 0, 1).$rowNo;
			}
			fwrite($this->shHnd, $doc->saveXML($row)."\n\r");
		}
		fwrite($this->shHnd, '</sheetData>'."\n\r");
		$needSave = false;
		foreach ($this->shDoc->documentElement->childNodes as $node) {
			if ($needSave) {
				$node = $doc->documentElement->appendChild($doc->importNode($node, true));
				fwrite($this->shHnd, $doc->saveXML($node)."\n\r");
			}
			else if ($node->nodeName == 'sheetData')
				$needSave = true;
		}
		fwrite($this->shHnd, '</worksheet>'."\n\r");
		fclose($this->shHnd);
		$this->shHnd = NULL;
	}
}