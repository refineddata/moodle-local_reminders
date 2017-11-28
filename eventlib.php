<?php
/**
 */

function reminders_role_assigned_event( $ra ) {
    global $DB, $CFG;
    require_once( $CFG->dirroot . '/local/reminders/lib.php' );
    
    if ( !isset( $CFG->studentrole ) ) set_config( 'studentrole', $DB->get_field( 'role', 'id', array( 'archetype'=>'student' ) ) );
    if ( $CFG->studentrole == $ra->objectid ) {
        $user    = $DB->get_record( 'user', array( 'id'=>$ra->relateduserid ) );
        $context = $DB->get_record( 'context', array( 'id'=>$ra->contextid ) );
        if ( ! $course  = $DB->get_record( 'course', array( 'id'=>$context->instanceid ) ) ) return true;
        if ( empty( $course->welcomemessage ) ) return true;
        reminders_send( $course->welcomemessage, null, $user, $course );
    }
    return true;
}
?>