<?php
$this->pageTitle=Yii::app()->name . ' - About';
$this->breadcrumbs=array(
	'About',
);
?>

<script text/javascript>
  jQuery(function(){
	  jQuery('.test')
	    .bind('mouseover', function(event) {this.style.color = 'red' })
	    .bind('mouseout', function(event) {this.style.color = 'green' })	  
  });
 
</script>

<h1>About</h1>

<p class="test">This is a "static" page. You may change the content of this page
by updating the file <tt><?php echo __FILE__; ?></tt>.</p>


 