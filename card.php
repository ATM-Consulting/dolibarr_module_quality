<?php 

	require 'config.php';
	dol_include_once('/of/class/ordre_fabrication_asset.class.php');
	dol_include_once('/quality/class/quality.class.php');
	dol_include_once('/of/lib/of.lib.php');
	
$action = GETPOST('action');
$fk_of = GETPOST('fk_of');

$PDOdb=new TPDOdb;
$assetOf=new TAssetOF;
$assetOf->load($PDOdb,$fk_of);

switch($action) {
	
	case 'update':
		$TControlDelete = __get('TControlDelete', array());
		$TResponse = __get('TControlResponse', false);
		QualityControl::updateControl($TResponse,$TControlDelete);
		
		header("location:".$_SERVER['PHP_SELF']."?fk_of=".$fk_of);
		exit;
		
		break;
	
	case 'add':
		$TControl= __get('TControl', array());
		
		QualityControl::addControl($TControl,$assetOf->id, $assetOf->element);
		
		header("location:".$_SERVER['PHP_SELF']."?fk_of=".$fk_of);
		exit;
		break;
	
	default:
		
		_fiche_control($assetOf);
		
		break;
	
}


function _fiche_ligne_control($fk_assetOf, $assetOf=-1)
{
	global $db;
	
	$Tab = array();
	
	if ($assetOf == -1)
	{
		$sql = 'SELECT c.rowid as id, c.label, c.question, c.type, "" as response, "" as id_assetOf_control 
			FROM '.MAIN_DB_PREFIX.'quality_control c
				WHERE c.rowid NOT IN (
					SELECT fk_control 
						FROM '.MAIN_DB_PREFIX.'quality_control_answer 
							WHERE fk_object='.(int) $fk_assetOf.' AND type_object="of"
				)';
	}
	else
	{
		$sql = 'SELECT c.rowid as id, c.label, c.question, c.type, ofc.response, ofc.rowid as id_assetOf_control 
					FROM '.MAIN_DB_PREFIX.'quality_control c';
		$sql.= ' INNER JOIN '.MAIN_DB_PREFIX.'quality_control_answer ofc ON (ofc.fk_control = c.rowid)';
		$sql.= ' WHERE ofc.fk_object='.$fk_assetOf.' AND type_object="of"';
		
	}
	
	$res = $db->query($sql);
	if($res=== false) {
		var_dump($db);exit;
		
	}
	
	while ($obj = $db->fetch_object($res))
	{
		$Tab[] = array(
				'id' => $obj->id
				,'libelle' => '<a href="'.dol_buildpath('/quality/control.php',1).'?id='.$obj->id.'">'.$obj->label.'</a>'
				,'type' => QualityControl::$TType[$obj->type]
				,'action' => '<input type="checkbox" value="'.$obj->id.'" name="TControl[]" />'
				,'question' => $obj->question
				,'response' => ($assetOf == -1 ? '' : QualityControl::generate_visu_control_value($obj->id, $obj->type, $obj->response, 'TControlResponse['.$obj->id_assetOf_control.'][]'))
				,'delete' => '<input type="checkbox" value="1" name="TControlDelete['.$obj->id_assetOf_control.']" />'
		);
	}
	
	return $Tab;
}

function _fiche_control(&$assetOf)
{
	global $langs,$db,$conf;
	
	llxHeader('',$langs->trans('OFAsset'),'','');
	print dol_get_fiche_head(ofPrepareHead( $assetOf, 'assetOF') , 'quality', $langs->trans('OFAsset'));
	
	$TBS=new TTemplateTBS();
	$TBS->TBS->protect=false;
	$TBS->TBS->noerr=true;
	
	$TControl = _fiche_ligne_control($assetOf->getId());
	$TQualityControlAnswer = _fiche_ligne_control( $assetOf->getId(), $assetOf);
	
	print $TBS->render('tpl/fiche_of_control.tpl.php'
			,array(
					'TControl'=>$TControl
					,'TQualityControlAnswer'=>$TQualityControlAnswer
			)
			,array(
					'assetOf'=>array(
							'id'=>(int) $assetOf->getId()
					)
					,'view'=>array(
							'nbTControl'=>count($TControl)
							,'nbTQualityControlAnswer'=>count($TQualityControlAnswer)
							,'url'=>$_SERVER['PHP_SELF']
					)
			)
			);
	
	llxFooter();
}
