<?php
global $CFG, $USER, $DB;
require_once("../../config.php");
require_once("lib.php");

$code = optional_param('code', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$eid = optional_param('event', 0, PARAM_INT);

require_login();
$PAGE->set_url('/local/reminders/test.php');
$PAGE->set_pagelayout('admin');
$context = context_course::instance(SITEID);
$PAGE->set_context($context);
$PAGE->set_title('Reminders');

$event = null;
$course = null;
if (empty($code) OR (empty($id) AND empty($eid))) echo 'Reminder test called incorrectly.';
if (isset($id) AND $id) $course = $DB->get_record('course', array('id' => $id));
if (isset($eid) AND $eid) $event = $DB->get_record('event', array('id' => $eid));
if ($event AND !$course) $course = $DB->get_record('course', array('id' => $event->courseid));

if (reminders_send($code, $event, $USER, $course)) echo '<center>Reminder test sent to your email.</center>';
else echo '<center>Reminder test failed.</center>';
die;