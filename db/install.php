<?php //$Id: upgrade.php

// This file keeps track of upgrades to 
// the connect module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

function xmldb_local_reminders_install( $oldversion = 0 ) {
	global $CFG, $DB;

	$dbman = $DB->get_manager();

	// Add Login & Password to User table
	$table = new xmldb_table( 'event' );
	$field = new xmldb_field( 'acurl', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, ' ', 'sequence' );
	if ( ! $dbman->field_exists( $table, $field ) ) {
		$dbman->add_field( $table, $field );
	}

	set_config( 'local_reminders', 3 );

	$reminder_template           = new \stdClass();
	$reminder_template->code     = 'Refined_meet';
	$reminder_template->lang     = 'en_us';
	$reminder_template->userfrom = 'Refined Training';
	$reminder_template->subject  = '[[name]]';
        include($CFG->dirroot . '/local/reminders/templates/email_refined_meet.php');
	$reminder_template->body     = $template;
	$reminder_template->vevent   = 0;
	$reminder_template->vbody    = '';

	$DB->insert_record( 'reminder_templates', $reminder_template );

	$reminder_template           = new \stdClass();
	$reminder_template->code     = 'Refined_general';
	$reminder_template->lang     = 'en_us';
	$reminder_template->userfrom = 'Refined Training';
	$reminder_template->subject  = '[[name]]';
        include($CFG->dirroot . '/local/reminders/templates/email_refined_general.php');
	$reminder_template->body     = $template;
	$reminder_template->vevent   = 0;
	$reminder_template->vbody    = '';

	$DB->insert_record( 'reminder_templates', $reminder_template );
}
