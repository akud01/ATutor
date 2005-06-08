<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: edit.php 3111 2005-01-18 19:32:00Z joel $

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');

if (!$_SESSION['valid_user']) {

	$info = array('INVALID_USER', $_SESSION['course_id']);
	$msg->printInfos($info);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$_GET['id'] = intval($_GET['id']);

$sql	= 'SELECT member_id, login, website, first_name, last_name FROM '.TABLE_PREFIX.'members WHERE member_id='.$_GET['id'];
$result = mysql_query($sql,$db);
if ($row = mysql_fetch_assoc($result)) {
	
	//get privs
	$sql	= 'SELECT `privileges`, approved FROM '.TABLE_PREFIX.'course_enrollment WHERE member_id='.$_GET['id'];
	$result = mysql_query($sql,$db);
	$row_en = mysql_fetch_assoc($result);

	if ($system_courses[$_SESSION['course_id']]['member_id'] == $_GET['id']) {
		$status = _AT('instructor');
	} else if ( ($row_en['approved'] == 'y') && $row_en['privileges'] ) {
		$status = _AT('enrolled_w_privs');
	} else if ($row_en['approved'] == 'y') {
		$status = _AT('enrolled');
	}

	$privs = array();
	foreach ($_privs as $key => $priv) {		
		if (query_bit($row_en['privileges'], $key)) { 
			$privs[] = $priv['name'];
		}
	}

	$savant->assign('row', $row);
	$savant->assign('status', $status);
	$savant->assign('privs', $privs);
	$savant->display('profile.tmpl.php');
} else {
	$msg->printErrors('NO_SUCH_USER');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>