<?php 

require 'config.php';
dol_include_once('/quality/class/quality.class.php');

$action = GETPOST('action');
$id = GETPOST('id');

$sheet = new TQualityControlSheet();

$PDOdb = new TPDOdb();

switch ($action) {
	
	case 'edit':
		$sheet->load($PDOdb, $id);
		_card($sheet,'edit');
		
		break;
	
	
	default:
		
		if($id>0) {
			$sheet->load($PDOdb, $id);
			_card($sheet);
		}
		else{
			_list($PDOdb);
		}
		
}


function _card(&$sheet, $mode = 'view') {
	global $db,$conf,$user,$langs;
	
	
}

function _list(&$PDOdb) {
	
	llxHeader('',$langs->trans('ListSheet'),'','');
	
	$form=new TFormCore;
	$l = new TListViewTBS('list1');
	
	$sql = 'SELECT rowid as id, label, controls FROM '.MAIN_DB_PREFIX.'quality_control_sheet';
	
	$THide = array('id');
	
	$formCore=new TFormCore($_SERVER['PHP_SELF'], 'form', 'GET');
	
	$PDOdb=new TPDOdb;
	
	echo $l->render($PDOdb, $sql, array(
			'link'=>array(
					'label'=>'<a href="?id=@id@">'.img_picto('','object_generic.png', '', 0).'@val@</a>'
			)
			,'search'=>array(
					'label'=>array('recherche'=>true, 'table'=>'')
			)
			,'translate'=>array()
			,'hide'=>$THide
			,'liste'=>array(
					'titre'=>$langs->trans('ListControl')
			)
			,'title'=>array(
					'label'=>$langs->trans('Label')
					,'controls'=>$langs->trans('Controls')
					
			)
			,'eval'=>array(
					'type'=>'TQualityControl::$TType["@val@"]'
			)
	));
	
	$formCore->end();
	
	llxFooter();
	
}