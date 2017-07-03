<?php


	require('config.php');
	
	dol_include_once('/quality/class/quality.class.php');
	if(!$user->rights->quality->control->read) accessforbidden();
	
	$langs->load('quality@quality');
	
	$action=__get('action','view');
	$id = __get('id', 0);
	$PDOdb=new TPDOdb;
	$control=new TQualityControl;
	

	switch($action) {
		case 'view':
			if ($id <= 0) header('Location: '.dol_buildpath('/quality/list_control.php',1));
		
			$control->load($PDOdb, $id);
			
			_fiche($PDOdb, $control, 'view');
			
			break;
		
		case 'new':
			
			_fiche($PDOdb, $control, 'edit');
			
			break;
	
		case 'edit':
			$control->load($PDOdb, $id);
			
			_fiche($PDOdb, $control, 'edit');
			
			break;
			
		case 'save':
			$control->load($PDOdb, $id);
			$control->set_values($_REQUEST);
			$control->save($PDOdb);
		
			setEventMessage($langs->trans('QualitySaveControlEvent'));
		
			_fiche($PDOdb, $control, 'view');
			
			break;
		
		case 'delete':				
			$control->load($PDOdb, $id);
			$control->delete($PDOdb);
			
			$_SESSION['AssetMsg'] = 'QualityDeleteControlEvent';
			header('Location: '.dol_buildpath('/quality/list_control.php',1));
			
			break;
			
		case 'editValue':
			$control->load($PDOdb, $id);
			
			_fiche($PDOdb, $control, 'view', 1);	
			
			break;
			
		case 'editValueConfirm':
			$control->load($PDOdb, $id);
								
			$k=$control->addChild($PDOdb,'TAssetControlMultiple', __get('id_value', 0, 'int'));
			$control->TAssetControlMultiple[$k]->fk_control = $control->getId();
			$control->TAssetControlMultiple[$k]->value = __get('value');
				
			if ($control->TAssetControlMultiple[$k]->save($PDOdb)) setEventMessage($langs->trans('AssetMsgSaveControlValue'));
			else setEventMessage($langs->trans('AssetErrSaveControlValue'));
			
			_fiche($PDOdb, $control, 'view');
			
			break;
			
		case 'deleteValue':
			$control->load($PDOdb, $id);
			
			if ($control->removeChild('TAssetControlMultiple', __get('id_value',0,'integer'))) 
			{
				$control->save($PDOdb);
				setEventMessage($langs->trans('AssetMsgDeleteControlValue'));
			}
			else setEventMessage($langs->trans('AssetErrDeleteControlValue'));
			
			_fiche($PDOdb, $control, 'view');
			
			break;

			
		default:
			if ($id <= 0) header('Location: '.DOL_MAIN_URL_ROOT.'/custom/asset/list_control.php');

			$control->load($PDOdb, $id);
			
			_fiche($PDOdb, $control, 'view');
			
			break;
	}
	

function _fiche(&$PDOdb, &$control, $mode='view', $editValue=false) {
	global $db,$langs;

	llxHeader('',$langs->trans('AddControl'),'','');
	$TBS=new TTemplateTBS;
	
	$formCore=new TFormCore();
	$formCore->Set_typeaff($mode);
	
	$TForm=array(
			'id'=>$control->getId()
			,'label'=>$formCore->texte('', 'label', $control->label,50,255)
			,'type'=>$formCore->combo('', 'type', TQualityControl::$TType, $control->type)
			,'question'=>$formCore->texte('', 'question', $control->question,120,255)
	);
	
	$TFormVal = _fiche_value($PDOdb, $editValue);
	$TVal = _liste_valeur($PDOdb, $control->getId(), $control->type);
	
	print $TBS->render('./tpl/control.tpl.php', 
		array(
			'TVal'=>$TVal
		)
		,array(
			'co'=>$TForm
			,'FormVal'=>$TFormVal
			,'view'=>array(
				'mode'=>$mode
				,'editValue'=>$editValue
				,'type'=>$control->type
				,'url'=>dol_buildpath('/quality/control.php', 1)
				,'langs'=>$langs
			)
		)
	);
	
	
	
	llxFooter();
}

function _fiche_value(&$PDOdb, $editValue)
{
	$res = array();
	
	if (!$editValue) return $res;
	
	$id_value = __get('id_value', 0, 'int');
	$res['id_value'] = $id_value;
	
	if ($id_value > 0)
	{
		$val = new TQualityControlMultiple;
		$val->load($PDOdb, $id_value);
		$res['value'] = $val->value;
	}
	else 
	{
		$res['value'] = '';
	}
	
	return $res;
}

function _liste_valeur(&$PDOdb, $fk_control, $type)
{
	global $langs;
	
	$res = array();
	
	if ($type != 'checkboxmultiple') return $res;
	
	$sql = 'SELECT rowid, value 
			FROM '.MAIN_DB_PREFIX.'quality_control_multiple cm
			WHERE cm.fk_control = '.(int) $fk_control;
	
	$PDOdb->Execute($sql);
	while ($PDOdb->Get_line())
	{
		$res[] = array(
			'value' => $PDOdb->Get_field('value')
			,'action' => '<a title="'.$langs->trans('Modify').'" href="?id='.(int) $fk_control.'&action=editValue&id_value='.(int)$PDOdb->Get_field('rowid').'">'.img_picto('','edit.png', '', 0).'</a>&nbsp;&nbsp;&nbsp;<a title="Supprimer" onclick="if (!window.confirm(\'Confirmez-vous la suppression ?\')) return false;" href="?id='.(int) $fk_control.'&action=deleteValue&id_value='.(int)$PDOdb->Get_field('rowid').'">'.img_picto('','delete.png', '', 0).'</a>'
		);
	}

	return $res;
}
