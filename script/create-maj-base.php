<?php
/*
 * Script créant et vérifiant que les champs requis s'ajoutent bien
 */

if(!defined('INC_FROM_DOLIBARR')) {
	define('INC_FROM_CRON_SCRIPT', true);

	require('../config.php');

}

global $db;

dol_include_once('/quality/class/quality.class.php');

$PDOdb=new TPDOdb;

$o=new TC_quality($db);
$o->init_db_by_vars($PDOdb);

$Tab = $PDOdb->ExecuteAsArray("SELECT * FROM ".MAIN_DB_PREFIX."c_quality");
if(empty($Tab)) {
	
	$TInit = array('NORMAL'=>'Normal','ERROR'=>'Rebus', 'SURPROD'=>'Surproduction','OTHER'=>'Other');
	foreach($TInit as $code=>$label) {
		$o=new TC_quality;
		$o->code = $code;
		$o->label = $label;
		$o->save($PDOdb);
		
	}
	
	
}


$o=new TQuality($db);
$o->init_db_by_vars($PDOdb);

$o=new QualityControl($db);
$o->init_db_by_vars($PDOdb);

$o=new QualityControlMultiple($db);
$o->init_db_by_vars($PDOdb);

$o=new QualityControlAnswer($db);
$o->init_db_by_vars($PDOdb);

$o=new TQualityControlSheet($db);
$o->init_db_by_vars($PDOdb);

