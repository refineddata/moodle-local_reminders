<?php
namespace local_reminders\task;

class local_reminders_cron extends \core\task\scheduled_task {      
    public function get_name() {
        // Shown in admin screens
        return get_string('localreminderscron', 'local_reminders');
    }
                                                                     
    public function execute() { 
        global $CFG, $DB;
        
        mtrace('++ Reminders Cron Task: start');
        
        require_once( $CFG->dirroot.'/local/reminders/lib.php' );
        
        $now = time();
        if ( $events = $DB->get_records_sql( "SELECT e.*, r.id as rid, r.code, r.aftertype FROM {$CFG->prefix}event e, {$CFG->prefix}reminders r WHERE e.id = r.event AND r.sent = 0 AND ( e.timestart + r.delta ) < $now" ) ) {
        	foreach( $events as $event ) {
                    //echo "------- Reminders -----------" . $event->modulename;
        		//Ensure Grading is Already Done in Case Aftertypes Are Set
        		if ( $event->aftertype > 1 ) {
        			if ( $connect = $DB->get_record( 'connectmeeting', array( 'eventid'=>$event->id ) ) ) {
        				if ( $connect->detailgrading AND !$connect->complete ) {
        					require_once( $CFG->dirroot . '/mod/connectmeeting/lib.php' );
        					connectmeeting_grade_meeting( 0, '', $connect );
        				}
        			}
        		}
        
        		if ( $event->courseid ) $course = $DB->get_record( 'course', array( 'id'=>$event->courseid ) );
        		else $course = null;
        
        		$users = array();
                        if ($event->eventtype == 'profilefield'){
                            $users = $DB->get_records_sql( "SELECT u.* FROM {user} u JOIN {user_enrolments} ue WHERE u.id = ue.userid AND ue.enrolid = ?", array($event->instance) );                            
                        } else {
                            if ( $event->groupid )                $users = $DB->get_records_sql( "SELECT u.* FROM {$CFG->prefix}user u, {$CFG->prefix}group_members g WHERE u.id = g.userid AND g.groupid = {$event->groupid}" );
                            elseif ( $course )                    $users = reminders_get_course_users( $event );
                            elseif ( $event->userid )             $users = $DB->get_records( 'user', array( 'id'=>$event->userid ) );
                        }
        
        		foreach( $users as $user ) {
                                //echo "------- Reminders -----------" . $user->firstname;
        			/*if ( $event->aftertype > 1 ) {
        			 if ( $grade = $DB->get_field_sql( "SELECT e.grade FROM {$CFG->prefix}connect_entries e, {$CFG->prefix}connect c WHERE e.connectid = c.id AND c.eventid={$event->id} AND e.userid={$user->id}" ) ) {
        			if ( ( $grade == 100 AND $event->aftertype == '3' ) OR ( $grade < 100 AND $event->aftertype == '2' ) ) continue;
        			} else continue;
        			}*/
        			$query = "SELECT * FROM {connectmeeting_entries} e, {connectmeeting} c WHERE e.connectmeetingid = c.id AND c.eventid={$event->id} AND e.userid={$user->id}";
        			if (($event->aftertype == 2) && (!$entries = $DB->get_records_sql( $query ))) {
        				continue;
        			}
        
        			if (($event->aftertype == 3) && ($entries = $DB->get_records_sql( $query ))) {
        				continue;
        			}
        			reminders_send( $event->code, $event, $user, $course );
        		}
        		if ( $reminder = $DB->get_record( 'reminders', array( 'id'=>$event->rid ) ) ) {
        			$reminder->sent = time();
        			$DB->update_record( 'reminders', $reminder );
        		}
        	}
        }
        mtrace('++ Reminders Cron Task: finish');
    }                                                                                                                               
} 