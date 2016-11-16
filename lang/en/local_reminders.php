<?php

$string['local_reminders']          = 'Reminders';
$string['pluginname']               = 'Reminders';
$string['pluginadministration']     = 'Reminders';

$string['num']                      = 'Number of Reminders';
$string['confignum']                = 'Reminder fields to display on forms throughout the system.';

$string['remhdr']                   = 'Reminder Messages';
$string['link']                     = '&nbsp;Link to Calendar';
$string['reminder']                 = 'Reminder';
$string['expire_reminder']          = 'Expiry Reminder';
$string['weeks']                    = 'Weeks';
$string['week']                     = 'Week';
$string['days']                     = 'Days';
$string['day']                      = 'Day';
$string['hours']                    = 'Hours';
$string['hour']                     = 'Hour';
$string['mins']                     = 'Minutes';
$string['before']                   = 'Before';
$string['after']                    = 'After';
$string['afteratt']                 = 'After Attending';
$string['afternoshow']              = 'After No-Show';

$string['welcomemessage']           = 'Welcome Message';
$string['bulk_reminders']           = 'Reminder Message';
$string['invitemessage']            = 'Invitation Message';
$string['messagereminderhdr']       = 'Bulk Reminders';
$string['code']                     = 'Message ID';
$string['event']                    = 'Event';

$string['noremindersfound']         = 'There are currently no reminders defined.  Please add a reminder to continue.';
$string['msg_id']                   = 'Message ID';
$string['language']                 = 'Language';
$string['body']                     = 'Body';
$string['edit']                     = 'Edit';
$string['tools']                    = 'Tools';
$string['reminders']                = 'Reminders';
$string['browse']                   = 'Browse Reminders';
$string['preview']                  = 'Reminder Preview';
$string['saveas']                   = 'Add New';
$string['subject']                  = 'Subject';
$string['userfrom']                 = 'From Name';
$string['body']                     = 'Body';
$string['send']                     = 'Send';
$string['test']                     = 'Email Test to Self';
$string['vevent']                   = 'iCalendar attachment';
$string['vevent_description']       = 'Description for iCalendar';
$string['duplicate']                = 'Cannot use the same code as is already used';
$string['fieldhelp']                = 'Available Fields: ';
$string['remindereventdescription'] = 'Event Details:<br />
$a';
if( file_exists( "$CFG->dirroot/local/core/lib.php" ) ){
	$string['fields']               = '[[name]] = Event Name<br/>
									   [[description]] = Event Description<br/>
									   [[event_description]] = Event Description (with heading)<br/>
									   [[date]] = Event Date and Time adjusted for User Timezone<br/>
									   [[timezone]] = User Timezone<br/>
									   [[mins]] = Event Duration (in minutes)<br/>
									   [[duration]] = Event Duration (in hours and minutes)<br/><br/>
									   [[firstname]] = User First Name<br/>
									   [[lastname]]  = User Last Name<br><br/>
									   [[url]] = URL to Course Page<br/>
									   [[urlx]] = URL to Course Page (auto-login)<br/>
									   [[connecturl]] = URL to login for AC meeting or recording (auto-login)<br/>
									   [[ievent]] = URL to Download Outlook reminder<br/>
									   [[cpurl]] = URL address link to Connect Pro meeting login<br/>
									   [[course]] = Full name of course (for course reminders).<br/>
									   [[shortname]] = Short name of course.<br/>
									   [[user#field]] = Field from user.<br/>
									   [[course#field]] = Field from course.<br/>
	                                   [[expiredate]] = The student\'s enrollment expiry date for a specified course. <br/><br/>';
}else{
	$string['fields']                = '[[name]] = Event Name<br/>
										[[description]] = Event Description<br/>
										[[event_description]] = Event Description (with heading)<br/>
										[[date]] = Event Date and Time adjusted for User Timezone<br/>
										[[timezone]] = User Timezone<br/>
										[[mins]] = Event Duration (in minutes)<br/>
										[[duration]] = Event Duration (in hours and minutes)<br/><br/>
										[[firstname]] = User First Name<br/>
										[[lastname]]  = User Last Name<br><br/>
										[[url]] = URL to Moodle Course Page<br/>
										[[ievent]] = URL to Download Outlook reminder<br/>
										[[cpurl]] = URL address link to Connect Pro meeting login<br/>
										[[course]] = Full name of course (for course reminders).<br/>
										[[shortname]] = Short name of course.<br/>
										[[user#field]] = Field from user.<br/>';
}
$string['localreminderscron']       = 'Local Reminders Cron';

?>
