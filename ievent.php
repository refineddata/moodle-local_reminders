<?php // $Id: ievent.php,v 1.5.2.10 2009/01/20 06:21:03 moodler Exp $

global $CFG, $USER, $DB;

require_once('../../config.php');
require_once($CFG->dirroot.'/local/reminders/ieventlib.php');
require_once($CFG->dirroot.'/local/reminders/lib.php');

$eventid = required_param( 'evt',   PARAM_INT );
$remid   = required_param( 'rem',   PARAM_INT );

if ( ! $event = $DB->get_record( 'event', array( 'id'=>$eventid ) ) 
  OR ! $event->courseid 
  OR ! $course = $DB->get_record( 'course', array( 'id'=>$event->courseid ) )
  OR ! $rem = $DB->get_record( 'reminder_templates', array( 'id'=>$remid ) ) ) {
    print_error( "Invalid parameters passed." );
}
require_login( $course );
$user = $USER;

$subj = _reminders_msgsub( $rem->subject, $rem, $course, $user, $event, null );
$body = _reminders_msgsub( $rem->vbody,   $rem, $course, $user, $event, null );
$unix = array( "\r\n", "\n\r", "\n", "\r" );
$desc = str_replace( $unix, '<br/>', $desc ); 

if ( ! isset( $subj ) 
  OR ! isset( $body )
  OR ! $str = write_ievent( $event, null, $user, $body, $subj ) 
  OR empty( $str ) ) {
    print_error( 'Invalid Vevent Description.' );
}

//IE compatibility HACK!
if(ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

$filename = 'icalexport.ics';

header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
header('Expires: '. gmdate('D, d M Y H:i:s', 0) .'GMT');
header('Pragma: no-cache');
header('Accept-Ranges: none'); // Comment out if PDFs do not work...
header('Content-disposition: attachment; filename='.$filename);
header('Content-length: '.strlen($str));
header('Content-type: text/calendar');

echo $str;

?>
