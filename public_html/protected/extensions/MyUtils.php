<?php 

class MyUtils
{
	public static function datetimeFormat($pattern, $time = null)
	{ /*	
		echo "-> datetimeFormat <br>";
		echo "___pattern: ".$pattern."<br>";
		echo "___time   : ".print_r($time, true)."<br>";
		echo "___gettype(time): ".gettype($time)."<br>";
		*/
		if ($time == null)	
			$time = date_create();		
				
		if (gettype($time) == 'object')
			$time = $time->getTimestamp();
		/*
		else if (gettype($time) == 'string') {
			$pos = strpos($time, '-');
			echo "___pos : ".$pos."<br>";
			if ($pos !== FALSE) {
				$time = strtotime($time);				
			}
			else {
				
			}			
		}
		*/
//		echo "__res: ".Yii::app()->DateFormatter->format($pattern, $time)."<br>";			
//		echo "<- datetimeFormat <br>";
		return Yii::app()->DateFormatter->format($pattern, $time);
/*		
		
		//			$criteria->compare('date',"<=".date("Y-m-d 23:59:59", strtotime(Yii::app()->DateFormatter->format('yyyy-MM-dd', $this->dateFrom))));
		
		
		if (gettype($time) == 'object')
			return $time->format($pattern); 
		else return Yii::app()->DateFormatter->format($pattern, $time);
		*/
	}
}
?>