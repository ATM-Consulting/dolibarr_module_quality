<?php
if (!defined("NOCSRFCHECK")) define('NOCSRFCHECK', 1);
if (!defined("NOTOKENRENEWAL")) define('NOTOKENRENEWAL', 1);

	require '../config.php';

	dol_include_once('/quality/class/quality.class.php');

	$get=GETPOST('get', 'alphanohtml');
	$put=GETPOST('put', 'alphanohtml');


	switch($get) {
		case 'quality_definition':

			echo _get_quality_definition_form(GETPOST('fk_object', 'int'),GETPOST('type_object', 'alphanohtml'),GETPOST('qty', 'int'));

			break;


	}

	switch($put){

		case 'quality_definition':

			$TQuality = (Array)$_POST['TQuality'];
			$PDOdb=new TPDOdb;//$PDOdb->debug=true;
			foreach($TQuality as $code=>$data) {

				$q=new TQuality;
				$q->loadByObjectCode($PDOdb, (int)GETPOST('fk_object', 'int'), GETPOST('type_object', 'alphanohtml'), $code);
				$q->set_values($data);
				$q->type_object = GETPOST('type_object', 'alphanohtml');
				$q->fk_object = GETPOST('fk_object', 'int');
				$q->code = $code;
				$q->save($PDOdb);

			}

			break;

	}


function _get_quality_definition_form($fk_object, $type_object,$qty) {

	global $langs;

	$PDOdb=new TPDOdb;

	$formCore = new TFormCore('auto','formQuality','post');
	echo $formCore->hidden('put', 'quality_definition');
	echo $formCore->hidden('fk_object', $fk_object);
	echo $formCore->hidden('type_object', $type_object);

	echo '<table class="border" width="100%"><tr class="titre"><th>'.$langs->trans('Quality').'</th><th>'.$langs->trans('Qty').'</th><th>'.$langs->trans('Motif').'</th></tr>';

	$TC_Quality = TC_quality::getAll($PDOdb);

	foreach($TC_Quality as &$c_quality) {

		$q=new TQuality;
		$q->loadByObjectCode($PDOdb, $fk_object, $type_object, $c_quality->code);

		if($c_quality->code === 'NORMAL' && empty($q->qty) ) $q->qty = $qty;

		echo '<tr><td style="font-weight:bold; text-align:right;">'.$c_quality->label.'</td><td style="text-align:right;">'.$formCore->texte('', 'TQuality['.$c_quality->code.'][qty]', $q->qty, 3,10).'</td>
		<td>'.$formCore->texte('','TQuality['.$c_quality->code.'][comment]', $q->comment, 30,255).'</td>
		</tr>';

	}

	echo '</table>';

	echo '<div class="tabsAction">'.$formCore->btsubmit($langs->trans('Save'), 'bt_save').'</div>';

	$formCore->end();

}
