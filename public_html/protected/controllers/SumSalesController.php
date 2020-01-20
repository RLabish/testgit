<?php

class SumSalesController extends Controller
{
	public $layout='//layouts/column2';
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionExcel()
	{
		$this->render('excel');
	}		
}