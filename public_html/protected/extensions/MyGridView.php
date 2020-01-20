<?php
Yii::import('zii.widgets.grid.CGridView');

class HeaderNode {
	public $owner;
	public $name;
	public $column;
	public $items;
	public function __construct($name, $owner, $column) {
		$this->owner = $owner;
		$this->name = $name;
		$this->column = $column;
		$this->items = array();		
	}
	
	public function child($name, $autocreate = false) {
		if (array_key_exists($name,$this->items))
			return $this->items[$name];						
		if (!$autocreate)
			return null;
		$x =  new HeaderNode($name, $this, $this->column);
		$this->items[$name] = $x;
		return $x;
	}
	
	public function childByDepth($depth) {
		if ($depth <= 1)
			return $this->items;
		$res = array();
		$depth--;
		foreach($this->items as $i) 				
	 		$res = array_merge($res, $i->childByDepth($depth));
		return $res;
	}	
}

class MyGridView extends CGridView {
	public $actions = array();
	public function init() {
		$cssUrl = Yii::app()->baseUrl.'/css/';		
		$this->cssFile = $cssUrl.'gridview.css';
		$cs = Yii::app()->getClientScript();
		if ($this->dataProvider->getPagination() !== false)
			$this->dataProvider->getPagination()->pageSize = 50;
		$this->template = "{items}\n{summary}\n{pager}"; 
		$this->summaryCssClass = 'grid-summary';
		$this->summaryText = '{start}-{end} из {count}';
		parent::init();
	}
	
	private $nheaders;
	private $nheaderrowcount;	
	private function  buildmheaders(){
		$this->nheaders = new HeaderNode('root', null, null);
		$this->nheaderrowcount = 0;
		foreach($this->columns as $column) {
			if(isSet($column->name) && $column->name !== null && $column->header===null)
				$header = $this->dataProvider->model->getAttributeLabel($column->name);
			else	
				$header = $column->header;
			if ($header === '')
			 	$header = '&nbsp;';
		
			$cnt = 0;
			$parent = $this->nheaders;		
			foreach(explode('|',$header) as $i) {
				$item =  $parent->child($i, true);	
				$item->column = $column;
				$cnt++;
				$parent = $item;				
			}
			if ($cnt > $this->nheaderrowcount)
				$this->nheaderrowcount = $cnt;					
			
		}
	}
	
		
	public function renderTableHeader()	{		
		if(!$this->hideHeader)	{			
			$this->buildmheaders();	
			echo "<thead>\n";

			if($this->filterPosition===self::FILTER_POS_HEADER)
				$this->renderFilter();

				
			for ($i = 1; $i <= $this->nheaderrowcount; $i++) {
				echo "<tr>\n";
				$headers = $this->nheaders->childByDepth($i);
				foreach($headers as $row ) {
					$htmlattr = array(); 
					if (count($row->items) == 0) 
						$htmlattr['rowspan'] = $this->nheaderrowcount - $i + 1;
					else
						$htmlattr['colspan'] = count($row->items);

				    $column = $row->column;
					$column_headerHtmlOptions = $column->headerHtmlOptions;
					$column_header = $column->header;				
					$column->headerHtmlOptions = array_merge($row->column->headerHtmlOptions, $htmlattr);
					$column->header = $row->name;
					$column->renderHeaderCell();
					$column->headerHtmlOptions = $column_headerHtmlOptions;
					$column->header = $column_header;									
				}
				echo "</tr>\n";
			}
		
			if($this->filterPosition===self::FILTER_POS_BODY)
				$this->renderFilter();
			echo "</thead>\n";			
		}
		else if($this->filter!==null && ($this->filterPosition===self::FILTER_POS_HEADER || $this->filterPosition===self::FILTER_POS_BODY))
		{
			echo "<thead>\n";
			$this->renderFilter();
			echo "</thead>\n";
		}
	}
	
	public function renderSummary()
	{
		if(($count=$this->dataProvider->getItemCount())<=0)
			return;

		echo '<div class="'.$this->summaryCssClass.'">';
		if($this->enablePagination)
		{
			if(($summaryText=$this->summaryText)===null)
				$summaryText=Yii::t('zii','Displaying {start}-{end} of {count} result(s).');
			$pagination=$this->dataProvider->getPagination();
			$total=$this->dataProvider->getTotalItemCount();
			$start=$pagination->currentPage*$pagination->pageSize+1;
			$end=$start+$count-1;
			if($end>$total)
			{
				$end=$total;
				$start=$end-$count+1;
			}
			echo strtr($summaryText,array(
				'{start}'=>$start,
				'{end}'=>$end,
				'{count}'=>$total,
				'{page}'=>$pagination->currentPage+1,
				'{pages}'=>$pagination->pageCount,
			));
		}
		else
		{
			if(($summaryText=$this->summaryText)===null)
				$summaryText=Yii::t('zii','Total {count} result(s).');
			echo strtr($summaryText,array('{count}'=>$count));
		}
		echo '</div>';
		
		if (count($this->actions) > 0) {
			echo '<div class="grid-toolbar">';
			if (in_array('excel', $this->actions)) {
				$this->widget('zii.widgets.jui.CJuiButton',
						array(
								'name'=>'btn-xls',
								'buttonType'=>'button',
								'htmlOptions'=>array('class'=>'tb-btn xls-tb-btn', 'title'=>'excel'),
								'onclick'=>'js:function(){gridtoexcel("'.$this->id.'","'.$this->controller->createUrl('excel').'");}',
						)
				);				
			}
			echo '</div>';			
		}
		
		
//		echo '<div class="grid-actions">';
//		echo CHtml::link('Excel', '#&grid-action=excel', array('id'=>'grid-action-excel'));
//		echo CHtml::link('Excel', '#', array('id'=>'grid-action-excel',));
//		echo '</div>';
	}
	
}
