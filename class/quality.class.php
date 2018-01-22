<?php

class TQuality extends TObjetStd { //TODO seedObject
		
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



class QualityControl extends SeedObject
{
	static $TType=array( //TODO translate
			'text'=>'Texte libre'
			,'checkbox'=>'Réponse oui / non'
			,'num'=> 'Réponse numérique'
			,'checkboxmultiple'=>'Réponse multiple'
			
	);
	
	public $element = 'quality_control';
	
	public $table_element= 'quality_control';
	
	public $childtables=array('QualityControlMultiple','QualityControlAnswer');
	
	function __construct(&$db)
	{
		$this->db = $db;
		
		$this->fields=array(
				'label'=>array('type'=>'string')
				,'type'=>array('type'=>'string','length'=>50,'index'=>true)
				,'question'=>array('type'=>'string')
		);
		
		$this->init();
		
	}
	
	
	static function addControl( $TControl, $fk_object, $element) {
		
		global $db,$user;
		
		if(empty($TControl)) return false;
		
		foreach ($TControl as $fk_control)
		{
			$ofControl = new QualityControlAnswer($db);
			$ofControl->fk_object = $fk_object;
			$ofControl->type_object = $element;
			$ofControl->fk_control = $fk_control;
			$ofControl->response = '';
			
			if($ofControl->update($user)<0) {
				
				var_dump($ofControl);exit;
			}
			
		}
		
		setEventMessage("Contrôle ajouté");
		
	}
	
	static function updateControl( $TControl, $TControlDelete)
	{
		
		global $db,$user;
	
		if(empty($TControl)) return false;
		
		foreach ($TControl as $fk_control_answer=>$response)
		{
			
			$ofControl = new QualityControlAnswer($db);
			if($ofControl->fetch($fk_control_answer)>0) {
					
				
				//var_dump($TControl,$TControlDelete,$ofControl->id,$fk_control_answer);exit;
				//Si la ligne est marqué à supprimer alors on delete l'info et on passe à la suite
				if (isset($TControlDelete[(int)$ofControl->id]))
				{
					$ofControl->delete($user);
					continue;
				}
				
				//Toutes les valeurs sont envoyées sous forme de tableau
				$val = !empty($response) ? implode(',', $response) : '';
				$ofControl->response = $val;
				$ofControl->update($user);
				
			}
		}
		
		setEventMessage("Modifications enregistrées");
		
	}
	
	static function generate_visu_control_value($fk_control, $type, $value, $name)
	{
		$res = '';
		switch ($type) {
			case 'text':
				$res = '<input name="'.$name.'" type="text" style="width:99%;" maxlength="255" value="'.$value.'" />';
				break;
				
			case 'num':
				$res = '<input name="'.$name.'" type="number" style="width:55px" value="'.$value.'" min="0" />';
				break;
				
			case 'checkbox':
				$res = '<input name="'.$name.'" type="checkbox" '.($value ? 'checked="checked"' : '').' value="1" />&nbsp;&nbsp;';
				break;
				
			case 'checkboxmultiple':
				
				$values = explode(',', $value);
				$control = new TQualityControl($db);
				$control->fetch($fk_control);
				
				foreach ($control->TQualityControlMultiple as &$controlValue)
				{
					$res.= '<span style="border:1px solid #A4B2C3;padding:0 4px 0 2px;">';
					$res.= '<input name="'.$name.'" style="vertical-align:middle" '.(in_array($controlValue->id, $values) ? 'checked="checked"' : '').' type="checkbox" value="'.$controlValue->id.'" />';
					$res.= '&nbsp;'.$controlValue->value.'</span>&nbsp;&nbsp;&nbsp;';
				}
				
				$res = trim($res);
				break;
		}
		
		return $res;
	}
	
	static function getControlPDF($fk_object, $element)
	{
		
		global $db, $langs;
		
		$Tab = array();
		
		$sql = 'SELECT ofc.rowid
					FROM '.MAIN_DB_PREFIX.'quality_control_answer ofc 
					 WHERE ofc.fk_object='.$fk_object.' AND type_object="'.$element.'"';
		
		$res = $db->query($sql);
		if($res=== false) {
			var_dump($db);exit;
			
		}
		
		while($obj = $db->fetch_object($res))
		{
			$controlAnswer= new QualityControlAnswer($db);
			$controlAnswer->fetch($obj->rowid);
			
			$control = new QualityControl($db);
			$control->fetch($controlAnswer->fk_control);
			
			switch ($control->type) {
				case 'text':
				case 'num':
					$Tab[] = array(
					'question'=>utf8_decode($control->question)
					,'response'=>$controlAnswer->response
					);
					break;
					
				case 'checkbox':
					$Tab[] = array(
					'question'=>utf8_decode($control->question)
					,'response'=>$controlAnswer->response ? $langs->trans('Yes') : $langs->trans('No')
							);
					break;
					
				case 'checkboxmultiple': //TODO debug
					$res2 = '';
					foreach ($control->TQualityControlMultiple as $controlVal)
					{
						$res2 .= $controlVal->value.', ';
					}
					
					$Tab[] = array(
							'question'=>utf8_decode($control->question)
							,'response'=>rtrim($res2, ', ')
					);
					break;
			}
		}
		
		return $Tab;
	}
	
	
}

class TQualityControlSheet extends TObjetStd
{
	function __construct()
	{
		$this->set_table(MAIN_DB_PREFIX.'quality_control_sheet');
		$this->add_champs('label,controls',array('type'=>'string'));
		
		$this->errors = array();
		
		$this->start();
	}
	
}
class QualityControlMultiple extends SeedObject
{
	
	public $element = 'quality_control_multiple';
	
	public $table_element= 'quality_control_multiple';
	
	function __construct(&$db)
	{
		$this->db = $db;
		
		$this->fields=array(
				'fk_control'=>array('type'=>'integer', 'index'=>true)
				,'value'=>array('type'=>'string')
		);
		
		$this->init();
		
	}
	
	function visu_select_control($name)
	{
		$db = $this->db;
		
		$sql = 'SELECT rowid, label FROM '.MAIN_DB_PREFIX.'quality_control WHERE type = "checkboxmultiple"';
		$resql = $db->query($sql);
		
		$res = '<select name="'.$name.'"><option value=""></option>';
		
		while($obj = $db->fetch_object($resql))
		{
			$fk_control = $obj->rowid;
			$res.= '<option '.($this->fk_control == $fk_control ? 'selected="selected"' : '').' value="'.$fk_control.'">'.$obj->label.'</option>';
		}
		
		$res.= '</select>';
		
		return $res;
	}
	
}

class QualityControlAnswer extends SeedObject
{
	
	public $element = 'quality_control_answer';
	
	public $table_element= 'quality_control_answer';
	
	function __construct(&$db)
	{
		$this->db = $db;
		
		$this->fields=array(
				'type_object'=>array('type'=>'string','length'=>30,'index'=>true)
				,'fk_object'=> array('type'=>'integer','index'=>true)
				,'fk_control'=>array('type'=>'integer', 'index'=>true)
				,'response'=>array('type'=>'string')
		);
		
		$this->init();
		
	}
	
}

class TC_quality extends TObjetStd { //TODO seedObject
	
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
