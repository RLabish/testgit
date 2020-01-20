<?php

$fuelVolume = 0;
$fuelLevel = '';
$density = '';
$fuelMass = '';
$temperature = '';
$waterVolume = '';
$date = null;
$dateOk = false;

if ($data->visible == 2) {
	if (isset($data->realState)) {
		$fuelVolume = $data->realState->fuelVolume;
		$fuelLevel = $data->realState->fuelLevel;
		$density = $data->realState->density;
		$fuelMass = $data->realState->fuelMass;
		$temperature = $data->realState->temperature;
		$waterVolume = $data->realState->waterVolume;

		$date = new DateTime($data->realState->date);
		$dif = $date->diff(new DateTime(), true);
		$minutes = $dif->format('%a') * 24 * 60 + $dif->format('%h') * 60 + $dif->format('%I');
		$dateOk = ($minutes < 24*60);		
	}
}
else if ($data->visible == 1) {
	if (isset($data->bookState)) {
		$fuelVolume = $data->bookState->fuelVolume;
		$date = new DateTime();
		$dateOk = true;
	}
}

if (!isset($fuelVolume)) {
	$percent = 0;
	$fuelVolumeOk = false;
	$freeVolumeOk = false;	
}
else {
	$freeVolume = $data->capacity - $fuelVolume;
	$fuelVolumeOk = ($fuelVolume >= $data->minVolume) && (($fuelVolume <= $data->maxVolume));
	$freeVolumeOk = $freeVolume >= 0;

	if ($data->capacity > 0) {
		$percent = round($fuelVolume / $data->capacity * 100);
		if ($percent < 0)
			$percent = 0;
		else if ($percent > 100)
			$percent = 100;
	}
	else
		$percent = 0;
}
?>

<div class="tank-view">
	<div>
		<p><?php 
		 	echo mb_substr($data->name, 0, 16, 'UTF-8');
		?></p>
		<div>
			<?php
				echo '<div style="background-color:'.$data->fuel->color.';">';
				echo '<div style="height:'.(100-$percent).'%;"></div>';
				echo '</div>';
			?>
			<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/tank.png"  />
			<?php echo '<p>'.CHtml::encode($data->fuel->name).'<br>'.$percent.'%</p>'; ?>
		</div>
	</div>

	<table class="tank-view">
	<?php $errStyle = array('style'=>'background-color: #FCA7B1'); ?>
	<tr>
	  <th>Дата</th>
	  <?php echo Chtml::tag('td', $dateOk ? array(): $errStyle, isset($date) ? $date->format('d.m.y H:i') : '', true); ?>
	 </tr>
	<tr>
	  <th>Свободно</th>
	  <?php echo Chtml::tag('td', $freeVolumeOk ? array(): $errStyle, isset($freeVolume) ? number_format($freeVolume,0,'.',' '): '', true); ?>
	</tr>
	<tr>
	  <th>Объем</th>
	  <?php echo Chtml::tag('td', $fuelVolumeOk ? array(): $errStyle, isset($fuelVolume) ? number_format($fuelVolume,0,'.',' '): '', true); ?>
	</tr>
	<tr>
	  <th>Уровень</th>
	  <?php echo Chtml::tag('td', isset($fuelLevel)? array(): $errStyle, $fuelLevel, true); ?>
	</tr>
	<tr>
	  <th>Плотность</th>
	  <?php echo Chtml::tag('td', isset($density)? array(): $errStyle, $density, true); ?>
	</tr>
	<tr>
	  <th>Масса</th>
	  <?php echo Chtml::tag('td', isset($fuelMass)? array(): $errStyle, ($fuelMass == '') ? '': number_format($fuelMass,0,'.',' '), true);?> 
	</tr>	
	<tr>
	  <th>Темп.</th>
	  <?php echo Chtml::tag('td', isset($temperature) ? array(): $errStyle, $temperature, true); ?>
	</tr>
	<tr>
	  <th>Вода</th>
	  <?php echo Chtml::tag('td', isset($waterVolume) && ($waterVolume == 0) ? array(): $errStyle, $waterVolume, true); ?>
	</tr>
	</table>

</div>


