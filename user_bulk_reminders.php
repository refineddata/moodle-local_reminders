<?php
require_once( '../../config.php' );
require_once( $CFG->libdir . '/adminlib.php' );
require_once( $CFG->dirroot . '/local/reminders/lib.php' );

$code     = optional_param('code',  '', PARAM_CLEAN);
$eventid  = optional_param('event',  0, PARAM_INT);
$crsid    = optional_param('course', 0, PARAM_INT);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
if ( empty( $eventid ) )  $eventid = 0;
if ( empty( $crsid ) )    $crsid   = 0;

require_login();
admin_externalpage_setup('userbulk');
require_capability('moodle/site:readallmessages', context_system::instance());

$return = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

if (empty($SESSION->bulk_users)) {
    redirect($return);
}

//TODO: add support for large number of users

if ( $confirm and !empty( $code ) and confirm_sesskey() ) {
    $eventrec  = $DB->get_record( 'event',  array( 'id'=>$eventid  ) );
    $courserec = $DB->get_record( 'course', array( 'id'=>$crsid ) );
    list($in, $params) = $DB->get_in_or_equal($SESSION->bulk_users);
    $rs = $DB->get_recordset_select('user', "id $in", $params);
    foreach ($rs as $user) {
        reminders_send( $code, $eventrec, $user, $courserec );
    }
    $rs->close();
    redirect($return);
}

classdef();
$msgform = new user_reminder_form( 'user_bulk_reminders.php' );

if ($msgform->is_cancelled()) {
    redirect($return);

} else if ($formdata = $msgform->get_data()) {
    $options = new stdClass();
    $options->para     = false;
    $options->newlines = true;
    $options->smiley   = false;

    //$msg = $DB->get_field( 'reminder_templates', 'body', array( 'code'=>$formdata->code ) );
    $msgs = $DB->get_records( 'reminder_templates', array( 'code'=>$formdata->code ) );

    list($in, $params) = $DB->get_in_or_equal($SESSION->bulk_users);
    $userlist     = $DB->get_records_select_menu('user', "id $in", $params, 'fullname', 'id,'.$DB->sql_fullname().' AS fullname');
    $usernames    = implode(', ', $userlist);
    $usernames    = 'Course: ' . $crsid . ': ' . $usernames;
    $formcontinue = new single_button(new moodle_url('user_bulk_reminders.php', array('confirm'=>1, 'code'=>$code, 'course'=>$crsid, 'event'=>$eventid ) ), get_string('yes'));
    $formcancel   = new single_button(new moodle_url('/admin/user/user_bulk.php'), get_string('no'), 'get');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('confirmation', 'admin'));
    foreach ($msgs as $msg){
        echo $OUTPUT->box("<br /><h3>** ".$msg->lang . ":</h3><br /><br /> " , 'boxalignleft generalbox', 'preview');
        echo $OUTPUT->box($msg->body, 'boxalignleft generalbox', 'preview'); //TODO: clean once we start using proper text formats here
        echo $OUTPUT->box("<br /><br />");
    }
    echo $OUTPUT->confirm(get_string('confirmmessage', 'bulkusers', $usernames), $formcontinue, $formcancel);
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();
$msgform->display();
echo $OUTPUT->footer();

function classdef() {
    global $CFG;
    require_once($CFG->libdir.'/formslib.php');

    class user_reminder_form extends moodleform {

        function definition() {
            global $CFG, $DB;
            
            $mform =& $this->_form;
            $mform->addElement('header', 'general', get_string('messagereminderhdr', 'local_reminders'));
     
            $now     = time();
            $codes   = $DB->get_records_sql_menu( "SELECT DISTINCT code as id, code FROM {$CFG->prefix}reminder_templates ORDER BY code" );
            $sql = "SELECT e.id, CONCAT(CONCAT(e.name, ' - '),LEFT(FROM_UNIXTIME(e.timestart),10)) as code 
            	FROM {event} e, {course_modules} cm, {modules} m 
				WHERE e.timestart > ( $now - 86400 ) 
				AND e.instance = cm.instance
				AND cm.module = m.id
				AND m.name = e.modulename
				ORDER BY e.name, e.timestart";
            $events  = $DB->get_records_sql_menu( $sql );
            $courses = array(0=>get_string('none')) + $DB->get_records_sql_menu( "SELECT id, fullname as code FROM {$CFG->prefix}course ORDER BY code" );
            $events  = array(0=>get_string('none')) + ( empty( $events ) ? array() : $events );
        
            $mform->addElement('select', 'code', get_string('code', 'local_reminders'), $codes );
            $mform->addElement('select', 'event', get_string('event', 'local_reminders'), $events );
            $mform->addElement('select', 'course', get_string('course'), $courses );

            $this->add_action_buttons();
        }
    }
}
