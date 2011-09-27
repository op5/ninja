<?php
if (!isset($object_data) || empty($object_data)) {
	die('no data');
}
$create_pdf = !isset($create_pdf) ? false : $create_pdf;
?>
<br />
<div id="trend_event_display"></div>
<?php if (!isset($is_avail)) { ?>
<h1 style="margin-top: 0px"><?php echo $title ?></h1>
<p style="margin-top: -13px;">
	<?php echo $label_report_period ?>: <?php echo $rpttimeperiod	?>
	<?php //echo $label_duration.': '.$duration ?>
	(<?php echo $str_start_date.' '.$this->translate->_('to').' '.$str_end_date ?>)
</p>

<?php
}

$cell_height = isset($avail_height) ? $avail_height: 50;
$title_str = $this->translate->_('Start: %s, End: %s, Duration: %s, Output: %s');

$graph->draw();
$graph->display();

?>
<div style="clear:both"></div>
<?php echo (isset($avail_template) && !empty($avail_template)) ? $avail_template : ''; ?>
