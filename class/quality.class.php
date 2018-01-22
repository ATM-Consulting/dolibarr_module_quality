<?php

class TQuality extends TObjetStd {
		
	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'quality');
		
		parent::add_champs('code,type_object', array('length'=>30,'index'=>true));
		parent::add_champs('fk_object', array('type'=>'integer','index'=>true));
		
		parent::add_champs('qty', array('type'=>'float'));
		
		parent::_init_vars('comment');
		
		parent::start();
		
	}

	function loadByObjectCode(&$PDOdb, $fk_object, $type_object, $code) {
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."quality WHERE type_object='".$type_object."' AND fk_object=".(int)$fk_object;
		$sql.= " AND code = '".$code."'";
		
		$PDOdb->Execute($sql);
		if($obj = $PDOdb->Get_line()) {
			
			return $this->load($PDOdb, $obj->rowid);
			
		}
		
		return false;
	}

}



class TQualityControl extends TObjetStd
{
	static $TType=array( //TODO translate
			'text'=>'Texte libre'
			,'checkbox'=>'Réponse oui / non'
			,'num'=> 'Réponse numérique'
			,'checkboxmultiple'=>'Réponse multiple'
			
	);
	
	function __construct()
	{
		$this->set_table(MAIN_DB_PREFIX.'quality_control');
		$this->TChamps = array();
		$this->add_champs('label,type,question',array('type'=>'string'));
		
		$this->start();
		
		$this->setChild('TQualityControlMultiple','fk_control');
		$this->setChild('TQualityControlAnswer','fk_control');
		
	}
}

class TQualityControlMultiple extends TObjetStd
{
	function __construct()
	{
		$this->set_table(MAIN_DB_PREFIX.'quality_control_multiple');
		$this->TChamps = array();
		$this->add_champs('fk_control',array('type'=>'integer', 'index'=>true));
		$this->add_champs('value',array('type'=>'string'));
		
		$this->start();
		
	}
	
	function visu_select_control(&$PDOdb, $name)
	{
		$sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'quality_control WHERE type = "checkboxmultiple"';
		$resql = $PDOdb->Execute($sql);
		
		$res = '<select name="'.$name.'"><option value=""></option>';
		
		while($db->Get_line())
		{
			$fk_control = $db->Get_field('rowid');
			$res.= '<option '.($this->fk_control == $fk_control ? 'selected="selected"' : '').' value="'.$fk_control.'">'.$db->Get_field('libelle').'</option>';
		}
		
		$res.= '</select>';
		
		return $res;
	}
	
}

class TQualityControlAnswer extends TObjetStd
{
	function __construct()
	{
		$this->set_table(MAIN_DB_PREFIX.'quality_control_answer');
		$this->TChamps = array();
		$this->add_champs('fk_assetOf,fk_control',array('type'=>'integer', 'index'=>true));
		$this->add_champs('response',array('type'=>'string'));
		
		$this->errors = array();
		
		$this->start();
	}
	
	function save(&$PDOdb)
	{
		global $user,$langs,$conf,$db;
		
		parent::save($PDOdb);
		
	}
	
	function delete(&$PDOdb)
	{
		global $user,$langs,$conf,$db;
		
		
		parent::delete($PDOdb);
	}
	
}

class TC_quality extends TObjetStd {
	
	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'c_quality');
		
		parent::add_champs('code', array('length'=>30,'index'=>true));
		parent::add_champs('active', array('type'=>'integer','index'=>true));
		
		parent::_init_vars('label');
		
		parent::start();
		
		$this->active = 1;
	}
	
	static function getAll(&$PDOdb, $forCombo = false) {
		
		$Tmp = $PDOdb->ExecuteAsArray("SELECT rowid FROM ".MAIN_DB_PREFIX."c_quality WHERE active = 1");
		$Tab=array();
		foreach($Tmp as &$row) {
			
			$c=new TC_quality;
			$c->load($PDOdb, $row->rowid);
			
			if($forCombo) {
				$Tab[$c->code] = $c->label;
			}
			else{
				$Tab[] = $c;	
			}
			
			
			
		}
		
		return $Tab;
	}
	
}
