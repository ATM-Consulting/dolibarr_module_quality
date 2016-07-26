<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_quality.class.php
 * \ingroup quality
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsQuality
 */
class ActionsQuality
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function lineObjectOptions($parameters, &$object, &$action, $hookmanager)
	{
		

		if (in_array('ofcard', explode(':', $parameters['context'])))
		{
		  	if($action == 'view') {
		  		
				global $langs;
				
				$langs->load('quality@quality');
				
		  		$line = &$parameters['line'];	
				$of = &$parameters['of'];
				
				if($of->status == 'CLOSE') {
					
					if($line['qty_used'] != $line['qty']) {
						$line['qty_used'].=' <a title="'.$langs->trans('DefineQualityUsage').'" href="javascript:qualityDefineMotif('.$object->rowid.',\'TAssetOFLine\','.(float)$object->qty.')" class="classfortooltip quality_define_link">Q</a>';
						
						$this->results = $line;
						
						return 1;	
					}
				}
		  	}
		  	
		  
		  
		}

	}
}