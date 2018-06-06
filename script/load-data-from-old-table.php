<?php 

	require '../config.php';
	
	$db->query("TRUNCATE TABLE ".MAIN_DB_PREFIX."quality_control");
	
	$db->query("INSERT INTO ".MAIN_DB_PREFIX."quality_control (rowid, date_creation, tms, label, type, question)
	SELECT rowid,date_cre,date_maj,libelle,type,question FROM ".MAIN_DB_PREFIX."asset_control");
	
	
	$db->query("TRUNCATE TABLE  ".MAIN_DB_PREFIX."quality_control_answer");

	$db->query("INSERT INTO  ".MAIN_DB_PREFIX."quality_control_answer (date_creation, tms, type_object, fk_object, fk_control,response)
			SELECT date_cre,date_maj,'of',fk_assetOf, fk_control,response FROM ".MAIN_DB_PREFIX."assetOf_control");
	
	$db->query("TRUNCATE TABLE  ".MAIN_DB_PREFIX."quality_control_multiple");

	$db->query("INSERT INTO  ".MAIN_DB_PREFIX."quality_control_multiple (date_creation, tms, fk_control,value)
		SELECT date_cre,date_maj, fk_control,value FROM ".MAIN_DB_PREFIX."asset_control_multiple");


