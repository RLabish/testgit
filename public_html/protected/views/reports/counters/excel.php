<?php 
Yii::import('application.extensions.XLReport');

 $xl = new XLReport();
 $xl->start('counters', array());
 
 foreach ($model->findall($criteria) as $azs) {
 	foreach($azs->terminals as $term) {
 		foreach ($term->pumps as $pump) {
 			$xl->add('data', array(
 				'azs' => $azs->name,
 				'terminal' => $term->name,
 				'date' => Yii::app()->DateFormatter->format('dd.MM.yy HH:mm',$term->syncDate),
 				'pump' => $pump->pumpNo.'-'.$pump->nozzleNo,
 				'fuel' => $pump->tank->fuel->name,
 				'counter' => array (
 					'format' => 'numeric',
 					'value' => $pump->counter,
 				),
 			));
 		}
 	}
 }
 $xl->complete();
