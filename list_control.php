<?php
	require('config.php');
	dol_include_once('/quality/class/quality.class.php');
	
	if(!$user->rights->quality->control->read) accessforbidden();
	
	dol_include_once("/core/class/html.formother.class.php");
	
	$langs->load('quality@quality');
	_liste();

	function _liste() {
		global $langs,$db,$user,$conf;
		
		llxHeader('',$langs->trans('ListControl'),'','');
		getStandartJS();

		if (isset($_SESSION['AssetMsg']))
		{
			print_r('<div class="info">'.$langs->trans($_SESSION['AssetMsg']).'</div>');
			unset($_SESSION['AssetMsg']);
		}
		
		$form=new TFormCore;
		$assetControl = new QualityControl($db);
		$l = new TListViewTBS('listControl');
	
		$sql = 'SELECT rowid as id, label, type, question FROM '.MAIN_DB_PREFIX.'quality_control';
		
		$THide = array('id');
	
		$formCore=new TFormCore($_SERVER['PHP_SELF'], 'form', 'GET');
	
		$PDOdb=new TPDOdb;
	
		echo $l->render($PDOdb, $sql, array(
			'limit'=>array(
				'nbLine'=>'30'
			)
			,'subQuery'=>array()
			,'link'=>array(
				'label'=>'<a href="control.php?id=@id@">'.img_picto('','object_generic.png', '', 0).'@val@</a>'
				,'question'=>'<a href="control.php?id=@id@">@val@</a>'
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
					,'type'=>$langs->trans('Type')
					,'nb_value'=>$langs->trans('NbAssociatedValue')
					,'question'=>$langs->trans('Question')
					,'action'=>$langs->trans('Action')
			)
			,'eval'=>array(
				'type'=>'QualityControl::$TType["@val@"]'
			)
		));
		
		$formCore->end();
		
	
		$PDOdb->close();		
		llxFooter('');
	}
