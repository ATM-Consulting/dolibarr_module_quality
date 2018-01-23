<div>
	<form action="[view.url]" method="POST">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="fk_of" value="[assetOf.id]" />
		<table width="100%" class="border workstation">
			<tr height="40px;">
				<td colspan="3">&nbsp;&nbsp;<b>Contrôle à ajouter</b></td>
			</tr>
			<tr style="background-color:#dedede;">
				<th align="left" width="50%">&nbsp;&nbsp;Libellé du contrôle</th>
				<th align="center" width="20%">Type</th>
				<th width="5%" class="draftedit">Ajouter</th>
			</tr>
			<tr id="WS[workstation.id]" style="background-color:#fff;">
				<td align="left">&nbsp;&nbsp;[TControl.libelle;strconv=no;block=tr]</td>
				<td align="center">[TControl.type;strconv=no;block=tr]</td>
				<td align='center' class="draftedit">[TControl.action;strconv=no;block=tr]</td>
			</tr>
			<tr>
				<td colspan="4" align="center">[TControl;block=tr;nodata]Aucun contrôle disponible</td>
			</tr>
		</table>
		
		<div class="tabsAction">
			<div class="inline-block divButAction">
				<input [view.nbTControl;noerr;if [val]==0;then 'disabled="disabled"';else ''] class="butAction" type="submit" value="Ajouter les contrôles" />
			</div>
		</div>
	</form>	
	
	<form action="[view.url]" method="POST">
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="fk_of" value="[assetOf.id]" />
		<table width="100%" class="border">
			<tr height="40px;">
				<td colspan="4">&nbsp;&nbsp;<b>Contrôles associés</b></td>
			</tr>
			<tr style="background-color:#dedede;">
				<th align="left" width="10%">&nbsp;&nbsp;Libellé du contrôle</th>
				<th align="left" width="30%">&nbsp;&nbsp;Question</th>
				<th align="left" width="30%">&nbsp;&nbsp;Réponse</th>
				<th width="5%">Supprimer</th>
			</tr>
			<tr style="background-color:#fff;">
				<td>&nbsp;&nbsp;[TQualityControlAnswer.libelle;strconv=no;block=tr]</td>
				<td>&nbsp;&nbsp;[TQualityControlAnswer.question;strconv=no;block=tr]</td>
				<td>[TQualityControlAnswer.response;strconv=no;block=tr]</td>
				<td align="center">[TQualityControlAnswer.delete;strconv=no;block=tr]</td>
			</tr>
			<tr>
				<td colspan="4" align="center">[TQualityControlAnswer;block=tr;nodata]Aucun contrôle associé</td>
			</tr>
		</table>
		
		<div class="tabsAction">
			<div class="inline-block divButAction">
				<input [view.nbTQualityControlAnswer;noerr;if [val]==0;then 'disabled="disabled"';else ''] class="butAction" type="submit" value="Modifier les contrôles" />
			</div>
		</div>
	</form>
</div>