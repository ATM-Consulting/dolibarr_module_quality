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

class TC_quality extends TObjetStd {
	
	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'c_quality');
		
		parent::add_champs('code', array('length'=>30,'index'=>true));
		parent::add_champs('active', array('type'=>'integer','index'=>true));
		
		parent::_init_vars('label');
		
		parent::start();
		
		$this->active = 1;
	}
	
	static function getAll(&$PDOdb) {
		
		$Tmp = $PDOdb->ExecuteAsArray("SELECT rowid FROM ".MAIN_DB_PREFIX."c_quality WHERE active = 1");
		$Tab=array();
		foreach($Tmp as &$row) {
			
			$c=new TC_quality;
			$c->load($PDOdb, $row->rowid);
			
			$Tab[] = $c;
			
		}
		
		return $Tab;
	}
	
}
