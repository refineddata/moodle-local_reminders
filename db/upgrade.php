<?php

function xmldb_local_reminders_upgrade( $oldversion ) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();

    $result = true;

    //===== 1.9.0 upgrade line ======//
    if ($oldversion < 2010080300) {
    }

    if ($oldversion < 2012030701) {
        $table = new xmldb_table('course');
        $field = new xmldb_field('welcomemessage', XMLDB_TYPE_CHAR, '100', null, NULL, false, '', 'timemodified');
        if ( !$dbman->field_exists( $table, $field ) ) $dbman->add_field( $table, $field );
        
        upgrade_plugin_savepoint(true, 2012030701, 'local', 'reminders');
    }

    if ($oldversion < 2013070201) {

        // Define field aftertype to be added to reminders
        $table = new xmldb_table('reminders');
        $field = new xmldb_field('aftertype', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'sent');

        // Conditionally launch add field aftertype
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // reminders savepoint reached
        upgrade_plugin_savepoint(true, 2013070201, 'local', 'reminders');
    }

     if ($oldversion < 2015062901) {

        // Define field aftertype to be added to reminders
        $table = new xmldb_table('reminder_templates');
        $field = new xmldb_field('vbody', XMLDB_TYPE_TEXT, '2000', null, null, null, null);

        // Conditionally launch add field aftertype
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // reminders savepoint reached
        upgrade_plugin_savepoint(true, 2015062901, 'local', 'reminders');
    }
   

    return $result;
}


