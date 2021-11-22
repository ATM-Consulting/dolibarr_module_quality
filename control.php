<?php


	require('config.php');

	dol_include_once('/quality/class/quality.class.php');
	if(!$user->rights->quality->control->read) accessforbidden();

	$langs->load('quality@quality');

	$action=__get('action','view');
	$id = __get('id', 0);

	$control=new QualityControl($db);


	switch($action) {
		case 'view':
			if ($id <= 0) header('Location: '.dol_buildpath('/quality/list_control.php',1));

			$control->fetch($id);

			_fiche( $control, 'view');

			break;

		case 'new':

			_fiche( $control, 'edit');

			break;

		case 'edit':
			$control->fetch($id);

			_fiche( $control, 'edit');

			break;

		case 'save':
			$control->fetch($id);
			$control->setValues($_REQUEST);
			$control->update($user);

			setEventMessage($langs->trans('QualitySaveControlEvent'));

			_fiche( $control, 'view');

			break;

		case 'delete':
			$control->fetch($id);
			$control->delete($PDOdb);

			$_SESSION['AssetMsg'] = 'QualityDeleteControlEvent';
			header('Location: '.dol_buildpath('/quality/list_control.php',1));

			break;

		case 'editValue':
			$control->fetch($id);

			_fiche( $control, 'view', 1);

			break;

		case 'editValueConfirm':
			$res = $control->fetch($id);

			$k=$control->addChild('QualityControlMultiple', __get('id_value', 0, 'int')); //TODO in class
			$control->TQualityControlMultiple[$k]->fk_control = $control->id;
			$control->TQualityControlMultiple[$k]->value = __get('value');

			if ($control->TQualityControlMultiple[$k]->update($user)) setEventMessage($langs->trans('QualityMsgSaveControlValue'));
			else setEventMessage($langs->trans('QualityErrSaveControlValue'));

			_fiche( $control, 'view');

			break;

		case 'deleteValue':
			$control->fetch($id);

			if ($control->removeChild($user, 'QualityControlMultiple', __get('id_value',0,'integer'))) //TODO in class
			{
				setEventMessage($langs->trans('AssetMsgDeleteControlValue'));
			}
			else setEventMessage($langs->trans('AssetErrDeleteControlValue'));

			_fiche( $control, 'view');

			break;


		default:
			if ($id <= 0) header('Location: '.DOL_MAIN_URL_ROOT.'/custom/asset/list_control.php');

			$control->fetch($id);

			_fiche( $control, 'view');

			break;
	}


function _fiche(&$control, $mode='view', $editValue=false) {
	global $db,$langs;

	llxHeader('',$langs->trans('AddControl'),'','');
	$TBS=new TTemplateTBS;

	$formCore=new TFormCore();
	$formCore->Set_typeaff($mode);

	$TForm=array(
			'id'=>$control->id
			,'label'=>$formCore->texte('', 'label', $control->label,50,255)
			,'type'=>$formCore->combo('', 'type', QualityControl::$TType, $control->type)
			,'question'=>$formCore->texte('', 'question', $control->question,120,255)
	);

	$TFormVal = _fiche_value( $editValue);
	$TVal = _liste_valeur( $control->id, $control->type);

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

function _fiche_value( $editValue)
{
	global $db;

	$res = array();

	if (!$editValue) return $res;

	$id_value = __get('id_value', 0, 'int');
	$res['id_value'] = $id_value;

	if ($id_value > 0)
	{
		$val = new QualityControlMultiple($db);
		$val->fetch( $id_value);
		$res['value'] = $val->value;
	}
	else
	{
		$res['value'] = '';
	}

	return $res;
}

function _liste_valeur( $fk_control, $type)
{
	global $langs,$db;

	$res = array();

	if ($type != 'checkboxmultiple') return $res;

	$sql = 'SELECT rowid, value
			FROM '.MAIN_DB_PREFIX.'quality_control_multiple cm
			WHERE cm.fk_control = '.(int) $fk_control;

	$resql = $db->query($sql);
	while ($obj = $db->fetch_object($resql))
	{
		// TODO: check if a CSRF token is required here
		$res[] = array(
				'value' => $obj->value
				,'action' => '<a title="'.$langs->trans('Modify').'" href="?id='.(int) $fk_control.'&action=editValue&id_value='.$obj->rowid.'">'
							.img_picto('','edit.png', '', 0)
						.'</a>&nbsp;&nbsp;&nbsp;
					<a title="Supprimer" onclick="if (!window.confirm(\'Confirmez-vous la suppression ?\')) return false;" href="?id='
				.(int) $fk_control.'&action=deleteValue&id_value='.(int)$obj->rowid.'">'.img_picto('','delete.png', '', 0).'</a>'
		);
	}

	return $res;
}
