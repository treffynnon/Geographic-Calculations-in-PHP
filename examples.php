<?php
require './geography.class.php';
?>
<head>
	<title>Geography.class.php Examples</title>
</head>
<html>
<h1>Geography.class.php Examples</h1>
<h2>Distance between two points (defaults to Vincenty's Formula)</h2>
<? $Geography = new Geography();
highlight_string('<?=$Geography->getDistance(-30, 150, -31, 160);?>'); ?>
<p><strong>Result:</strong> <?=$Geography->getDistance(-30, 150, -31, 160);?></p>
<h2>Distance between two points specifying formula</h2>
<? $Geography = new Geography();
highlight_string('<?=$Geography->getDistance(-30, 150, -31, 160, \'gc\');?>'); ?>
<p><strong>Result:</strong> <?=$Geography->getDistance(-30, 150, -31, 160, 'gc');?></p>
<h2>Convert decimals to degrees, minutes and seconds</h2>
<? $Geography = new Geography();
highlight_string('<?=$Geography->convertLatToDMS(-31.567);?>
<?=$Geography->convertLongToDMS(134.678, \'%d&deg; %d\\\' %d"%s\');?>');?>
<p><strong>Result 1:</strong> <?=$Geography->convertLatToDMS(-31.567);?><br />
<strong>Result 2:</strong> <?=$Geography->convertLongToDMS(134.678, '%d&deg; %d\' %d"%s');?></p>
<h2>Convert DMS to decimals</h2>
<? $Geography = new Geography();
highlight_string('<?=$Geography->convertToDecimal(\'31 34 1.200000S\');?>
<?=$Geography->convertToDecimal(\'134° 40\\\' 40"E\', \'([0-9]{1,3})° ([0-9]{1,2})\\\' ([0-9]{1,2}[.]{0,1}[0-9]*)"([N,S,E,W])\');?>'); ?>
<p><strong>Result 1:</strong> <?=$Geography->convertToDecimal('31 34 1.200000S');?><br />
<strong>Result 2:</strong> <?=$Geography->convertToDecimal('134° 40\' 40"E', '([0-9]{1,3})° ([0-9]{1,2})\' ([0-9]{1,2}[.]{0,1}[0-9]*)"([N,S,E,W])');?></p>
<h2>Conversion between units</h2>
<? $Geography = new Geography();
highlight_string('<?=$Geography->mToKm($Geography->getDistance(-30, 150, -31, 160));?>
<?=$Geography->mToNM($Geography->getDistance(-30, 150, -31, 160));?>
<?=$Geography->mToM($Geography->getDistance(-30, 150, -31, 160));?>');?>
<p><strong>Result 1:</strong> <?=$Geography->mToKm($Geography->getDistance(-30, 150, -31, 160));?>Km<br />
<strong>Result 2:</strong> <?=$Geography->mToNM($Geography->getDistance(-30, 150, -31, 160));?>NM<br />
<strong>Result 3:</strong> <?=$Geography->mToM($Geography->getDistance(-30, 150, -31, 160));?>M</p>
</html>