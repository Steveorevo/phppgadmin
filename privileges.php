<?php

	/**
	 * Manage views in a database
	 *
	 * $Id: privileges.php,v 1.2 2003/01/26 00:00:27 slubek Exp $
	 */

	// Include application functions
	include_once('conf/config.inc.php');
	
	$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : '';
	if (!isset($msg)) $msg = '';
	$PHP_SELF = $_SERVER['PHP_SELF'];
	
	/** 
	 * Function to save after editing a view
	 */
	function doSaveEdit() {
		global $localData;
		
		$status = $localData->setView($_POST['view'], $_POST['formDefinition']);
		if ($status == 0)
			doProperties('View updated.');
		else
			doEdit('View update failed.');
	}
	
	/**
	 * Function to allow editing of a view
	 */
	function doEdit($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition;
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Views: ", htmlspecialchars($_REQUEST['view']), ": Edit</h2>\n";
		$misc->printMsg($msg);
		
		$viewdata = &$localData->getView($_REQUEST['view']);
		
		if ($viewdata->recordCount() > 0) {
			echo "<form action=\"$PHP_SELF\" method=post>\n";
			echo "<table width=100%>\n";
			echo "<tr><th class=data>{$strName}</th></tr>\n";
			echo "<tr><td class=data1>", htmlspecialchars($viewdata->f[$data->vwFields['vwname']]), "</td></tr>\n";
			echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
			echo "<tr><td class=data1><textarea style=\"width:100%;\" rows=20 cols=50 name=formDefinition wrap=virtual>", 
				htmlspecialchars($viewdata->f[$data->vwFields['vwdef']]), "</textarea></td></tr>\n";
			echo "</table>\n";
			echo "<input type=hidden name=action value=save_edit>\n";
			echo "<input type=hidden name=view value=\"", htmlspecialchars($_REQUEST['view']), "\">\n";
			echo "<input type=hidden name=database value=\"", htmlspecialchars($_REQUEST['database']), "\">\n";
			echo "<input type=submit value=Save> <input type=reset>\n";
			echo "</form>\n";
		}
		else echo "<p>No data.</p>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?database=", urlencode($_REQUEST['database']), "\">Show All Views</a> |\n";
		echo "<a class=navlink href=\"$PHP_SELF?action=properties&database=", urlencode($_REQUEST['database']), "&view=", 
			urlencode($_REQUEST['view']), "\">Properties</a></p>\n";
	}
	
	/**
	 * Show read only properties for a view
	 */
	function doProperties($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition;
	
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Views: ", htmlspecialchars($_REQUEST['view']), ": Properties</h2>\n";
		$misc->printMsg($msg);
		
		$viewdata = &$localData->getView($_REQUEST['view']);
		
		if ($viewdata->recordCount() > 0) {
			echo "<table width=100%>\n";
			echo "<tr><th class=data>{$strName}</th></tr>\n";
			echo "<tr><td class=data1>", htmlspecialchars($viewdata->f[$data->vwFields['vwname']]), "</td></tr>\n";
			echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
			echo "<tr><td class=data1>", nl2br(htmlspecialchars($viewdata->f[$data->vwFields['vwdef']])), "</td></tr>\n";
			echo "</table>\n";
		}
		else echo "<p>No data.</p>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?database=", urlencode($_REQUEST['database']), "\">Show All Views</a> |\n";
		echo "<a class=navlink href=\"$PHP_SELF?action=edit&database=", urlencode($_REQUEST['database']), "&view=", 
			urlencode($_REQUEST['view']), "\">Edit</a></p>\n";
	}
	
	/**
	 * Show confirmation of drop and perform actual drop
	 */
	function doDrop($confirm) {
		global $localData, $database;
		global $PHP_SELF;

		if ($confirm) { 
			echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Views: ", htmlspecialchars($_REQUEST['view']), ": Drop</h2>\n";
			
			echo "<p>Are you sure you want to drop the view \"", htmlspecialchars($_REQUEST['view']), "\"?</p>\n";
			
			echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
			echo "<input type=hidden name=action value=drop>\n";
			echo "<input type=hidden name=view value=\"", htmlspecialchars($_REQUEST['view']), "\">\n";
			echo "<input type=hidden name=database value=\"", htmlspecialchars($_REQUEST['database']), "\">\n";
			echo "<input type=submit name=choice value=\"Yes\"> <input type=submit name=choice value=\"No\">\n";
			echo "</form>\n";
		}
		else {
			$status = $localData->dropView($_POST['view']);
			if ($status == 0)
				doDefault('View dropped.');
			else
				doDefault('View drop failed.');
		}
		
	}
	
	/**
	 * Displays a screen where they can enter a new view
	 */
	function doCreate($msg = '') {
		global $data, $localData, $misc;
		global $PHP_SELF, $strName, $strDefinition;
		
		if (!isset($_POST['formView'])) $_POST['formView'] = '';
		if (!isset($_POST['formDefinition'])) $_POST['formDefinition'] = '';
		
		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": Views: Create View</h2>\n";
		$misc->printMsg($msg);
		
		echo "<form action=\"$PHP_SELF\" method=post>\n";
		echo "<table width=100%>\n";
		echo "<tr><th class=data>{$strName}</th></tr>\n";
		echo "<tr><td class=data1><input name=formView size={$data->_maxNameLen} maxlength={$data->_maxNameLen} value=\"", 
			htmlspecialchars($_POST['formView']), "\"></td></tr>\n";
		echo "<tr><th class=data>{$strDefinition}</th></tr>\n";
		echo "<tr><td class=data1><textarea style=\"width:100%;\" rows=20 cols=50 name=formDefinition wrap=virtual>", 
			htmlspecialchars($_POST['formDefinition']), "</textarea></td></tr>\n";
		echo "</table>\n";
		echo "<input type=hidden name=action value=save_create>\n";
		echo "<input type=hidden name=database value=\"", htmlspecialchars($_REQUEST['database']), "\">\n";
		echo "<input type=submit value=Save> <input type=reset>\n";
		echo "</form>\n";
		
		echo "<p><a class=navlink href=\"$PHP_SELF?database=", urlencode($_REQUEST['database']), "\">Show All Views</a></p>\n";
	}
	
	/**
	 * Actually creates the new view in the database
	 */
	function doSaveCreate() {
		global $localData, $strViewNeedsName, $strViewNeedsDef;
		
		// Check that they've given a name and a definition
		if ($_POST['formView'] == '') doCreate($strViewNeedsName);
		elseif ($_POST['formDefinition'] == '') doCreate($strViewNeedsDef);
		else {		 
			$status = $localData->createView($_POST['formView'], $_POST['formDefinition']);
			if ($status == 0)
				doDefault('View created.');
			else
				doCreate('View creation failed.');
		}
/*
	$i = 0;
	while ($p = $arrPrivileges[$i]) {
		$cb_priv[$p] = '<input type="checkbox" name="privileges[]" value="'. "$p\"> ". ucfirst($p) ."</input>";
		$i++;
	}
	$Expected = $strYes;
	$Action = "grant";
	$strToFrom = "to";

	$privileges = get_privilege($table);
	switch ($action) {
		case "revoke":
			$Expected =  $strNo;
			$Action = "revoke";
			$strToFrom = "from";
		case "grant":
			$name = rawurldecode($user);

			$i = 0;
			while ($p = $arrPrivileges[$i]) {
				if ($privileges[$name][$p] == $Expected) {
					unset($cb_priv[$p]); }
				$i++;
			}
			$user = "$cfgQuotes$name$cfgQuotes";
			$user = eregi_replace("${cfgQuotes}group ", "GROUP $cfgQuotes", $user);
			$user = eregi_replace("${cfgQuotes}public$cfgQuotes", "PUBLIC", $user);
			$input_user = '<input type="hidden" name="user" value="'. rawurlencode($user) .'">';
			break;
		case "grantuser":
			$qrUsers = "SELECT 'public'::text AS thename UNION SELECT '$cfgQuotes' || usename || '$cfgQuotes' AS thename FROM pg_user WHERE usename NOT IN ('root', '$cfgSuperUser'";
			@reset($privileges);
			while (list($key) = @each ($privileges))
				if (!ereg("group ", $key))
					$qrUsers .= ", '$key'";
			$qrUsers .= ") ORDER BY thename";
		case "grantgroup":
			if (!isset($qrUsers)) {
				$qrUsers = "SELECT 'group $cfgQuotes' || groname || '$cfgQuotes' AS thename FROM pg_group";
				@reset($privileges);
				while (list($key) = @each($privileges)) 
					if (ereg("^group (.+)$", $key, $regs))
						$tmp .=", '".$regs[1]."'";

				if (isset($tmp)) {
					$tmp[0] = '(';
					$qrUsers .= " WHERE groname NOT IN $tmp)";
				}
				$qrUsers .= " ORDER BY thename";
			}
			if (!$res = @pg_exec($link, $qrUsers)) {
				pg_die(pg_errormessage($link), $qrUsers, __FILE__, __LINE__);
			} else {
				$name = '<select name="user">';
			        $num_rows = pg_numrows($res);
				for ($i = 0; $i < $num_rows; $i++) {
			                $row = pg_fetch_array($res, $i);
					$name .= '<option value="'.rawurlencode($row['thename']) . '">'. $row['thename'] ."</option>";
				}
				$name .= "</select>\n";
			}
		}
	unset($action);
*/


	}	

	/**
	* Show the grant menu on the screen
	*/

	function doModify($action) {
		global $data, $localData, $misc, $database;
		global $PHP_SELF, $strPrivileges, $strGrant, $strRevoke, $strCancel; 
		global $strUser,$strGroup,$strSelect,$strInsert,$strUpdate,$strDelete,$strRule;
		global $strReferences,$strTrigger,$strAction,$strYes,$strNo;

		$object = $_REQUEST['object'];
		// $server = $_REQUEST['server'];
		$server = 'deprecated';
		$user = $_REQUEST['user'];
		$db = $_REQUEST['database'];

		$arrPrivileges = array('select',	'insert', 	'update', 	'delete', 	'rule',	'references', 	'trigger');
		$arrAcl        = array('r',      	'a',      	'w',      	'd',		'R',	'x',			't');		

		$i = 0;
		while ($p = $arrPrivileges[$i]) {
			$cb_priv[$p] = '<input type="checkbox" name="privileges[]" value="'. "$p\"> ". ucfirst($p) ."</input>";
			$i++;
		}

		// $privileges = get_privilege($table);
		$privileges = &$localData->getPrivileges($object);
	
		$GrantRevoke = $strGrant;
		$ToFrom = 'to';
		$Expected = $strYes;

		switch ($action) {
			case "revoke":
				$GrantRevoke = $strRevoke;
				$ToFrom = 'from';
				$Expected =  $strNo;
			case "grant":

				$name = rawurldecode($user);

				$i = 0;
				while ($p = $arrPrivileges[$i]) {
					echo $privileges[$name][$p];
					if ($privileges[$name][$p] == $Expected) {
						unset($cb_priv[$p]); }
					$i++;
				}
				$user = "\"$name\"";
				$user = eregi_replace("group", "GROUP", $user);
				$user = eregi_replace("public", "PUBLIC", $user);
				$input_user = '<input type="hidden" name="user" value="'. rawurlencode($user) .'">';
				break;
			case "grantuser":
				$qrUsers = "SELECT 'public'::text AS thename UNION SELECT '$cfgQuotes' || usename || '$cfgQuotes' AS thename FROM pg_user WHERE usename NOT IN ('root', '$cfgSuperUser'";
				@reset($privileges);
				while (list($key) = @each ($privileges))
					if (!ereg("group ", $key))
						$qrUsers .= ", '$key'";
				$qrUsers .= ") ORDER BY thename";
			case "grantgroup":
				if (!isset($qrUsers)) {
					$qrUsers = "SELECT 'group $cfgQuotes' || groname || '$cfgQuotes' AS thename FROM pg_group";
					@reset($privileges);
					while (list($key) = @each($privileges)) 
						if (ereg("^group (.+)$", $key, $regs))
							$tmp .=", '".$regs[1]."'";
					if (isset($tmp)) {
						$tmp[0] = '(';
						$qrUsers .= " WHERE groname NOT IN $tmp)";
					}
					$qrUsers .= " ORDER BY thename";
				}
				if (!$res = @pg_exec($link, $qrUsers)) {
					pg_die(pg_errormessage($link), $qrUsers, __FILE__, __LINE__);
				} else {
					$name = '<select name="user">';
				        $num_rows = pg_numrows($res);
					for ($i = 0; $i < $num_rows; $i++) {
				                $row = pg_fetch_array($res, $i);
						$name .= '<option value="'.rawurlencode($row['thename']) . '">'. $row['thename'] ."</option>";
					}
					$name .= "</select>\n";
				}
			}
		unset($action);

		echo "<h2>", htmlspecialchars($db), ": $strPrivileges : $object : $GrantRevoke</h2>\n";

		echo strtoupper($GrantRevoke);
	
		echo '<form method="post" action="$PHP_SELF">';
		
		$i = 0;
		while ($p = $arrPrivileges[$i]) {
			if (isset($cb_priv[$p])) { 
				echo $cb_priv[$p], "<br>";
			}
		$i++;
		}
 
		echo "ON $object ". strtoupper($ToFrom) ." $name";

		echo '<input type="hidden" name="server" value="'. rawurlencode($server) ."\">\n";
		echo '<input type="hidden" name="object" value="'. $object ."\">\n";
		echo '<input type="hidden" name="db" value="'. $db ."\">\n";
		echo $input_user;
		echo '<p>';
		echo '<input type="submit" name="todo" value="'. strtoupper($GrantRevoke) ."\">\n";
		echo '<input type="button" value="'. $strCancel .'" onClick="history.back()">';
		echo '</form>';



/*
		$privs = &$localData->getPrivileges($object);

		if ($privs->recordCount() == 1) {

			$i = 0;
			while ($p = $privs[$i]) {
			$cb_priv[$p] = '<input type="checkbox" name="privileges[]" value="'. "$p\"> ". ucfirst($p) ."</input>";
				$i++;
			}
			$Expected = $strYes;
			$strToFrom = "to";


			$name = rawurldecode($_REQUEST['user']);

			$i = 0;
			while ($p = $privs[$i]) {
				if ($privs[$name][$p] == $Expected) {
					unset($cb_priv[$p]); }
				$i++;
			}
			$user = "$name";
			$user = eregi_replace(" ", "GROUP", $user);
			$user = eregi_replace(" ", "PUBLIC", $user);
			$input_user = '<input type="hidden" name="user" value="'. rawurlencode($user) .'">';
		}	

		echo '<form method="post" action="$PHP_SELF">';
		
		$i = 0;
		while ($p = $arrPrivileges[$i]) {
			if (isset($cb_priv[$p])) { 
				echo $cb_priv[$p], "<br>";
			}
		$i++;
		}

*/
	}


	/**
	 * Show default list of views in the database
	 */
	function doDefault($msg = '') {
		global $data, $localData, $misc, $database;
		global $PHP_SELF, $strPrivileges, $strGrant, $strRevoke; 
		global $strUser,$strGroup,$strSelect,$strInsert,$strUpdate,$strDelete,$strRule;
		global $strReferences,$strTrigger,$strAction,$strYes,$strNo;
	

		// the intention here is to look for an 'object' in the request array, and if found
		// use it, if not found, use table. unfortunatly this throws an error (though it works)
	
		$object = $_REQUEST['object'] ? $_REQUEST['object'] : $_REQUEST['table'];

		echo "<h2>", htmlspecialchars($_REQUEST['database']), ": $strPrivileges : $object</h2>\n";
		$misc->printMsg($msg);

		$privileges = &$localData->getPrivileges($object);

		// We must return only one row from the above query

		echo "<table>\n";

		$i = 1;
		for ($y=0;$y<count($privileges);$y++)
		{
			$thisuser = explode('@!@',$privileges[$y]);
			$id = (($i % 2) == 0 ? '1' : '2');
			if ($y==0) {
				$otf = '<th class=data>';
				$ctf = '</th>';
			} else {
				$otf = "<td class=data{$id}>";
				$ctf = '</td>';
			}

			echo "<tr>";

			for ($x=0;$x<count($thisuser);$x++)
			{
				echo "$otf";
				switch ($thisuser[$x]) {
					case 'Yes':	echo $strYes;
							break;
					case 'No':	echo $strNo;
							break;
					default:	echo $thisuser[$x];
				}
				echo "$ctf";
			}	

			// $endcap = "<td><a href=#>$strGrant</a></td><td><a href=#>$strRevoke</a>";
			$endcap = $otf . "<a href=\"$PHP_SELF?database=". urlencode($_REQUEST['database']) ."&object=". urlencode($object) ."&action=grant&user=". urlencode($thisuser[0]) ."\">$strGrant</a>" . $ctf . $otf ."<a href=\"$PHP_SELF?database=". urlencode($_REQUEST['database'])  ."&object=". urlencode($object)  ."&action=revoke&user=". urlencode($thisuser[0])  ."\">$strRevoke</a>" . $ctf;

			if ($y==0) {
				$endcap = "<th colspan=2 class=data>$strAction</td>";
			}

			echo $endcap;
			echo "</tr>\n";
			$i++;
		}

		echo '</table>';


		//} else {
		//	echo "Could Not Retrieve ACL for Object $object";
		//}

	}

	$misc->printHeader($strPrivileges);

	switch ($action) {
		case 'save_create':
			doSaveCreate();
			break;
		case 'create':
			doCreate();
			break;
		case 'drop':
			if ($_POST['choice'] == 'Yes') doDrop(false);
			else doDefault();
			break;
		case 'confirm_drop':
			doDrop(true);
			break;			
		case 'save_edit':
			doSaveEdit();
			break;
		case 'edit':
			doEdit();
			break;
		case 'properties':
			doProperties();
			break;
		case 'grant':
			doModify('grant');
			break;
		case 'revoke':
			doModify('revoke');
			break;
		default:
			doDefault();
			break;
	}	

	$misc->printFooter();
	
?>