<?php
	require '../config.php';
	
	$langs->load('quality@quality');
	
?>
function qualityDefineMotif(fk_object,type_object,qty) {
	
	$div = $('<div />');
	
	$div.load("<?php echo dol_buildpath('/quality/script/interface.php',1) ?>?get=quality_definition&qty="+qty+"&fk_object="+fk_object+"&type_object="+type_object,function() {
		$div.dialog({
			title:"<?php echo $langs->transnoentities('DefineQualityUsage'); ?>"
			,modal:true
			,width:'50%'
		});
		
		$div.find('form').submit(function() {
			
			$.post($(this).attr('action'), $(this).serialize(), function() {
				
			});
		
			$div.dialog('close');			
			
			return false;
	
			
		});
		
	});
	
	
	
	
	
}
