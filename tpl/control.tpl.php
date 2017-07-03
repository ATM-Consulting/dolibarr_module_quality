
<form action="[view.url;strconv=no]" method="POST">
	<div class="tabBar">
		<input id="action" type="hidden" value="save" name="action" />
		<input id="id" type="hidden" value="[co.id]" name="id" />
		<table width="100%" class="border">
			<tr><td width="20%">[view.langs.transnoentities(Label)]</td><td>[co.label; strconv=no]</td></tr>
			<tr><td width="10%">Type du contrôle</td><td>[co.type; strconv=no]</td></tr>
			<tr><td width="20%">Question</td><td>[co.question; strconv=no]</td></tr>
		</table>
	</div>
	
	<div class="tabsAction aa" [view.mode;noerr;if [val]=='edit';then 'style="text-align:center"';else '']>
		[onshow;block=begin;when [view.mode]!='edit']
			<a href="?id=[co.id]&action=edit" class="butAction">Modifier</a>
			<a class="butActionDelete" href="control.php?id=[co.id]&action=delete">Supprimer</a>
		[onshow;block=end]
		[onshow;block=begin;when [view.mode]=='edit']
			<input type="submit" value="Enregistrer" name="save" class="button">
			<a style="font-weight:normal;text-decoration:none;" class="button"  href="[view.url]?id=[co.id]&action=view">Annuler</a>
		[onshow;block=end]
	</div>
</form>

[onshow;block=begin;when [view.editValue]==1]
<form action="[view.url;strconv=no]" method="POST">	
	<div class="tabBar" style="margin-top:15px;">
			<input type="hidden" name="action" value="editValueConfirm" />
			<input type="hidden" name="id" value="[co.id]" />
			<input type="hidden" name="id_value" value="[FormVal.id_value;noerr]" />
			
			<table width="100%" class="border">
				<tr><th align="left" colspan="2">[FormVal.id_value;noerr;if [val]==0;then 'Ajouter une valeur';else 'Modifier la valeur']</th></tr>
				<tr><td>Valeur</td><td><input size="45" type="text" name="value" value="[FormVal.value;strconv=no;noerr]" /></td></tr>
			</table>			
	</div>	
	
	<div class="tabsAction" style="text-align:center;">
		<input class="button" type="submit" value="Enregistrer" />
		<a style="font-weight:normal;text-decoration:none" href="?id=[co.id]&action=view" class="button">Annuler</a>
	</div>
</form>
[onshow;block=end]

[onshow;block=begin;when [view.type]=='checkboxmultiple']
	<div class="tabBar" style="margin-top:15px;">
		<table width="100%" class="border">
			<tr height="40px;">
				<td colspan="3">&nbsp;&nbsp;<b>Valeurs disponibles pour ce contrôle</b></td>
			</tr>
			<tr style="background-color:#dedede;">
				<th align="left" width="90%">&nbsp;&nbsp;Valeur</th>
				<th align="center" width"10%">Action</th>
			</tr>
			
			<tr style="background-color:#fff;">
				<td>&nbsp;&nbsp;[TVal.value;strconv=no;block=tr]</td>
				<td align="center">[TVal.action;strconv=no;block=tr]</td>
			</tr>
			
			<tr>
				<td colspan="2" align="center">[TVal;block=tr;nodata]Aucune valeur disponible</td>
			</tr>	
		</table>
	</div>
	
	[onshow;block=begin;when [view.mode]!='editValue']
		<div class="tabsAction">
			<a href="?id=[co.id]&action=editValue" class="butAction">Ajouter une valeur</a>
		</div>
	[onshow;block=end]
[onshow;block=end]


