<?php

class TQuality extends TObjetStd {
		
	function __construct() {
		parent::set_table(MAIN_DB_PREFIX.'quality');
		
		parent::add_champs('code,type_object', array('length'=>30,'index'=>true));
		parent::add_champs('fk_object', array('type'=>'integer','index'=>true));
		
		parent::_init_vars('comment');
		
		parent::start();
		
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
	
	
}
