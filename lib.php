<?php // $Id: lib.php
/**
 * Library of functions and constants for module local reminders
 *
 * @author  Gary Menezes
 * @version $Id: lib.php
 * @package connect
 **/
if ( file_exists( $CFG->dirroot . '/mod/connectmeeting/connectlib.php' ) ) {
	require_once( $CFG->dirroot . '/mod/connectmeeting/connectlib.php' );
}

define( "REMINDERS_PASSKEY", "thisisthepwd" );  // Issues when used in url translations

function reminders_autologin() {
	global $CFG, $DB, $USER;

	$redirect = optional_param( 'reminder_redirect', '', PARAM_RAW );

	if ( ! empty( $USER->id ) ) {
		if ( $redirect ) {
			redirect( $redirect );
		} else {
			return true;
		}
	}

	$token = optional_param( 'token', '', PARAM_RAW );

	if ( empty( $token ) ) {
		return false;
	}

	$token    = base64_decode( urldecode( $token ) );
	$tokens   = explode( '||', $token );
	$username = $tokens[0];
	$password = function_exists( 'connect_decrypt' ) ? connect_decrypt( urldecode( $tokens[1] ) ) : '';

	if ( $password == 'Null1234' OR $password = 'Null0000' OR $password == REMINDERS_PASSKEY ) {
		$user = $DB->get_record( 'user', array( 'username' => $username ) );
	}

	if ( $user OR $user = authenticate_user_login( $username, $password ) ) {
		$event = \core\event\user_loggedin::create(
			array(
				'userid'   => $user->id,
				'objectid' => $user->id,
				'other'    => array( 'username' => $user->username ),
			)
		);
		$event->trigger();
		$USER = complete_user_login( $user );

		if ( $redirect ) {
			redirect( $redirect );
		}
	}
}

function reminders_update( $eventid, $data ) {
	global $CFG, $USER, $COURSE, $DB;

	$DB->delete_records( 'reminders', array( 'event' => $eventid, 'sent' => 0 ) );
	for ( $i = 1; $i < $CFG->local_reminders + 1; $i ++ ) {
		if ( isset( $data->delta[ $i ] ) AND isset( $data->code[ $i ] ) AND $data->delta[ $i ] AND $data->code[ $i ] != 'none' ) {
			$rem            = new stdClass;
			$rem->event     = $eventid;
			$rem->aftertype = isset( $data->aftertype[ $i ] ) ? $data->aftertype[ $i ] : 0;
			$rem->delta     = $data->delta[ $i ];
			if ( $rem->aftertype ) {
				$rem->delta *= - 1;
			}
			$rem->code         = $data->code[ $i ];
			$rem->sent         = 0;
			$rem->timemodified = time();
			$DB->insert_record( 'reminders', $rem );
		}
	}

	return true;
}

function reminders_get( $eventid, &$mform ) {
	global $CFG, $USER, $COURSE, $DB, $OUTPUT;

	if ( ! $reminders = $DB->get_records( 'reminders', array( 'event' => $eventid, 'sent' => 0 ) ) ) {
		return false;
	}

	if ( isset( $COURSE->id ) AND $COURSE->id ) {
		$crsstr = '&id=' . $COURSE->id;
	} else {
		$crsstr = '';
	}

	$i = 1;
	foreach ( $reminders as $rem ) {
		if ( $rem->aftertype ) {
			$rem->delta *= - 1;
		}
		$mform->setDefault( 'delta[' . $i . ']', $rem->delta );
		$mform->setDefault( 'aftertype[' . $i . ']', $rem->aftertype );
		$mform->setDefault( 'code[' . $i . ']', $rem->code );
		$mform->setDefault( 'link[' . $i . ']', '<a target="#" onClick="window.open(' . "'" . $CFG->wwwroot . '/local/reminders/test.php?code=' . $rem->code . $crsstr . '&event=' . $eventid . "'" . ",'connect','resizable,height=260,width=370'); return false;" . '">' . get_string( 'test', 'local_reminders' ) . '</a>' );
		if ( $i ++ > $CFG->local_reminders ) {
			break;
		}
	}

	return true;
}

function reminders_get_properties( $eventid, &$properties ) {
	global $CFG, $USER, $COURSE, $DB, $OUTPUT;

	if ( ! $reminders = $DB->get_records( 'reminders', array( 'event' => $eventid, 'sent' => 0 ) ) ) {
		return false;
	}

	if ( isset( $COURSE->id ) AND $COURSE->id ) {
		$crsstr = '&id=' . $COURSE->id;
	} else {
		$crsstr = '';
	}

	$i = 1;
	foreach ( $reminders as $rem ) {
		if ( $rem->aftertype ) {
			$rem->delta *= - 1;
		}
		$properties->{'delta[' . $i . ']'}     = $rem->delta;
		$properties->{'aftertype[' . $i . ']'} = $rem->aftertype;
		$properties->{'code[' . $i . ']'}      = $rem->code;
		$properties->{'link[' . $i . ']'}      = '<a target="#" onClick="window.open(' . "'" . $CFG->wwwroot . '/local/reminders/test.php?code=' . $rem->code . $crsstr . '&event=' . $eventid . "'" . ",'connect','resizable,height=260,width=370'); return false;" . '">' . get_string( 'test', 'local_reminders' ) . '</a>';
		if ( $i ++ > $CFG->local_reminders ) {
			break;
		}
	}

	return true;
}

function reminders_form( &$mform, $check = false, $checktodisable = false, $addheader = true, $aftertypetwoonly = false ) {
	global $CFG, $USER, $COURSE, $DB;

	if ( $addheader ) {
		$mform->addElement( 'header', 'remhdr', get_string( 'remhdr', 'local_reminders' ), '', array( "style" => "display:none" ) );
	}
	if ( $check ) {
		$mform->addElement( 'checkbox', 'reminders', '', get_string( 'link', 'local_reminders' ) );
	}

	$lang                             = current_language();
	$deltas                           = array();
	$deltas[ - 60 * 60 * 24 * 7 * 4 ] = '4 ' . get_string( 'weeks', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 7 * 3 ] = '3 ' . get_string( 'weeks', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 7 * 2 ] = '2 ' . get_string( 'weeks', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 7 * 1 ] = '1 ' . get_string( 'week', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 6 * 1 ] = '6 ' . get_string( 'days', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 5 * 1 ] = '5 ' . get_string( 'days', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 4 * 1 ] = '4 ' . get_string( 'days', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 3 * 1 ] = '3 ' . get_string( 'days', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 2 * 1 ] = '2 ' . get_string( 'days', 'local_reminders' );
	$deltas[ - 60 * 60 * 24 * 1 * 1 ] = '1 ' . get_string( 'day', 'local_reminders' );
	$deltas[ - 60 * 60 * 18 * 1 * 1 ] = '18 ' . get_string( 'hours', 'local_reminders' );
	$deltas[ - 60 * 60 * 12 * 1 * 1 ] = '12 ' . get_string( 'hours', 'local_reminders' );
	$deltas[ - 60 * 60 * 06 * 1 * 1 ] = '6 ' . get_string( 'hours', 'local_reminders' );
	$deltas[ - 60 * 60 * 04 * 1 * 1 ] = '4 ' . get_string( 'hours', 'local_reminders' );
	$deltas[ - 60 * 60 * 03 * 1 * 1 ] = '3 ' . get_string( 'hours', 'local_reminders' );
	$deltas[ - 60 * 60 * 02 * 1 * 1 ] = '2 ' . get_string( 'hours', 'local_reminders' );
	$deltas[ - 60 * 90 * 01 * 1 * 1 ] = '90 ' . get_string( 'mins', 'local_reminders' );
	$deltas[ - 60 * 75 * 01 * 1 * 1 ] = '75 ' . get_string( 'mins', 'local_reminders' );
	$deltas[ - 60 * 60 * 01 * 1 * 1 ] = '1 ' . get_string( 'hour', 'local_reminders' );
	$deltas[ - 60 * 45 * 01 * 1 * 1 ] = '45 ' . get_string( 'mins', 'local_reminders' );
	$deltas[ - 60 * 30 * 01 * 1 * 1 ] = '30 ' . get_string( 'mins', 'local_reminders' );
	$deltas[ - 60 * 15 * 01 * 1 * 1 ] = '15 ' . get_string( 'mins', 'local_reminders' );

	$deltas[0] = get_string( 'none' );
	ksort( $deltas );

	$aftertypes    = array();
	$aftertypes[0] = get_string( 'before', 'local_reminders' );
	$aftertypes[1] = get_string( 'after', 'local_reminders' );
	if ( ! $aftertypetwoonly ) {
		$aftertypes[2] = get_string( 'afteratt', 'local_reminders' );
		$aftertypes[3] = get_string( 'afternoshow', 'local_reminders' );
	}

	$codes['none'] = get_string( 'none' );
	if ( $newcodes = $DB->get_records_sql_menu( "SELECT DISTINCT code as id, code FROM {$CFG->prefix}reminder_templates ORDER BY code" ) ) {
		$codes += $newcodes;
	}

	for ( $i = 1; $i < $CFG->local_reminders + 1; $i ++ ) {
		$formgroup   = array();
		$formgroup[] =& $mform->createElement( 'select', "delta[$i]", '', $deltas );
		$mform->setDefault( "delta[$i]", 0 );
		$mform->disabledIf( "delta[$i]", 'reminders' );
		$formgroup[] =& $mform->createElement( 'select', "aftertype[$i]", '', $aftertypes );
		$mform->setDefault( "aftertype[$i]", 0 );
		$mform->disabledIf( "aftertype[$i]", 'reminders' );
		$formgroup[] =& $mform->createElement( 'select', "code[$i]", '', $codes );
		$mform->setDefault( "code[$i]", 'none' );
		$mform->disabledIf( "code[$i]", 'reminders' );
		$formgroup[] =& $mform->createElement( 'static', "link[$i]", '' );
		$mform->setDefault( "link[$i]", '' );
		$mform->disabledIf( "link[$i]", 'reminders' );
		$headerstring = $aftertypetwoonly ? get_string( 'expire_reminder', 'local_reminders' ) : get_string( 'reminder', 'local_reminders' );
		$mform->addElement( 'group', "rem$i", $headerstring, $formgroup, array( ' ' ), false );
	}

	if ( $checktodisable ) {
		$mform->disabledIf( 'reminders', 'start[enabled]', 'notchecked' );
	}

	return true;
}

function reminders_send( $code, $event = null, $user = null, $course = null, $other = null ) {
	global $CFG, $USER, $COURSE, $DB;

	// Deal with an empty user record
	if ( empty( $user ) AND ! empty( $USER ) ) {
		$user = $USER;
	}
	if ( empty( $user ) AND ! empty( $event ) AND $event->userid ) {
		$user = $DB->get_record( 'user', array( 'id' => $event->userid ) );
	}
	if ( empty( $user ) ) {
		return false;
	}
	if ( empty( $user->description ) ) {
		$user->description = '';
	}

	// Get Message we're dealing with
	if ( empty( $message ) AND ! empty( $course->lang ) ) {
		$message = $DB->get_record( 'reminder_templates', array( 'code' => $code, 'lang' => $course->lang ) );
	}
	if ( empty( $message ) AND ! empty( $user->lang ) ) {
		$message = $DB->get_record( 'reminder_templates', array( 'code' => $code, 'lang' => $user->lang ) );
	}
	if ( empty( $message ) AND ! empty( $CFG->lang ) ) {
		$message = $DB->get_record( 'reminder_templates', array( 'code' => $code, 'lang' => $CFG->lang ) );
	}
	if ( empty( $message ) ) {
		$message = $DB->get_record( 'reminder_templates', array( 'code' => $code ) );
	}
	if ( empty( $message ) ) {
		return false;
	}

	// Deal with an empty course record
	if ( empty( $course ) AND ! empty( $COURSE ) ) {
		$course = $COURSE;
	}
	if ( empty( $course ) AND ! empty( $event ) AND $event->courseid ) {
		$course = $DB->get_record( 'course', array( 'id' => $event->courseid ) );
	}
	if ( empty( $course ) ) {
		$course = $DB->get_record( 'course', array( 'id' => 1 ) );
		if ( ! empty( $event ) ) {
			if ( $event->courseid ) {
				$course = $DB->get_record( 'course', array( 'id' => $event->courseid ) );
			}
			$course->fullname  = $event->name;
			$course->summary   = $event->description;
			$course->startdate = $event->timestart;
		}
	}

	// Deal with an empty event record
	if ( empty( $event ) AND ! empty( $course ) AND $DB->count_records( 'event', array( 'courseid' => $course->id ) ) == 1 ) {
		$event = $DB->get_record( 'event', array( 'courseid' => $course->id ) );
	}
	if ( empty( $event ) AND $DB->count_records( 'event', array( 'userid' => $user->id ) ) == 1 ) {
		$event = $DB->get_record( 'event', array( 'userid' => $user->id ) );
	}
	if ( empty( $event ) ) {
		$event               = new stdClass;
		$event->courseid     = empty( $course ) ? 1 : $course->id;
		$event->name         = empty( $course ) ? fullname( $user ) : $course->fullname;
		$event->description  = empty( $course ) ? fullname( $user ) : $course->summary;
		$event->timestart    = empty( $course->startdate ) ? time() : $course->startdate;
		$event->acurl        = '';
		$event->id           = 0;
		$event->timeduration = 0;
	}
	$message->body    = _reminders_msgsub( $message->body, $message, $course, $user, $event, $other );
	$message->subject = _reminders_msgsub( $message->subject, $message, $course, $user, $event, $other );
	$message->vbody   = _reminders_msgsub( $message->vbody, $message, $course, $user, $event, $other );

	$from            = get_admin();
	$from->firstname = empty( $message->userfrom ) ? $CFG->supportname : $message->userfrom;
	$from->lastname  = '';
	$from->email     = $CFG->supportemail;

	$attachfile = null;
	$attach     = null;
	if ( $message->vevent AND isset( $event->id ) AND $event->id ) {
		$attachfile = 'invite' . $event->id . '.ics';
		$attach     = '/ical/' . $attachfile;
		require_once( $CFG->dirroot . '/local/reminders/ieventlib.php' );
		if ( ! write_ievent( $event, $CFG->dataroot . $attach, $user, $message->vbody, $message->subject ) ) {
			error( 'Problem writing attachment' );
			$attachfile = null;
			$attach     = null;
		}
	}

	$messagehtml   = $message->body;
	$message->text = html_to_text( $messagehtml );
	email_to_user( $user, $from, $message->subject, $message->body, $messagehtml, $attach, $attachfile );
	$event = \local_reminders\event\reminder_send::create( array(
		'objectid'      => $event->id,
		'relateduserid' => $user->id,
		'other'         => array( 'description' => 'user:' . $user->username . '-eventid:' . $event->id . '-' . $message->subject )
	) );
	$event->trigger();

	return true;
}

function reminders_get_course_users( $event ) {
	global $CFG, $DB;

	$groupingid = 0;
	if ( $connect = $DB->get_record( 'connectmeeting', array( 'eventid' => $event->id ) ) ) {
		if ( $cm = get_coursemodule_from_instance( 'connectmeeting', $connect->id, $connect->course ) ) {
			if ( $cm->groupmode == 1 AND $cm->groupingid ) {
				$groupingid = $cm->groupingid;
			}
		}
	} elseif ( $instance = $DB->get_record( 'connectmeeting_recurring', array( 'eventid'     => $event->id,
	                                                                           'record_used' => 0
	) )
	) {
		if ( $connect = $DB->get_record( 'connectmeeting', array( 'id' => $instance->connectmeetingid ) ) ) {
			if ( $cm = get_coursemodule_from_instance( 'connectmeeting', $connect->id, $connect->course ) ) {
				if ( $cm->groupmode == 1 AND $instance->groupingid ) {
					$groupingid = $instance->groupingid;
				}
			}
		}
	} elseif ( $DB->get_manager()->table_exists( 'signinreq' ) && $signinreq = $DB->get_record( 'signinreq', array( 'eventid' => $event->id ) ) ) {
		if ( $cm = get_coursemodule_from_instance( 'signinreq', $signinreq->id, $signinreq->course ) ) {
			if ( $cm->groupmode == 1 AND $cm->groupingid ) {
				$groupingid = $cm->groupingid;
			}
		}
	}

	if ( $groupingid ) {
		$users = $DB->get_records_sql( "SELECT u.*
                                        FROM   {$CFG->prefix}user u, 
                                               {$CFG->prefix}groupings_groups g, 
                                               {$CFG->prefix}groups_members m
                                        WHERE  u.id         = m.userid
                                        AND    g.groupid    = m.groupid
                                        AND    u.deleted    = 0
                                        AND    g.groupingid = {$groupingid}" );
	} elseif ( isset( $event->courseid ) AND $event->courseid ) {
		$users = get_enrolled_users( context_course::instance( $event->courseid ) );
	} else {
		return false;
	}

	return $users;
}

/*
*  Internally Called Functions
*
*/

function _reminders_msgsub( $str, $message, $course, $user, $event, $other ) {
	global $CFG, $USER, $COURSE, $PAGE, $DB;

	$passkey = function_exists( 'connect_encrypt' ) ? urlencode( connect_encrypt( REMINDERS_PASSKEY ) ) : '';
	$tok     = '&token=' . urlencode( base64_encode( $user->username . '||' . $passkey ) );
	$t_k     = '?token=' . urlencode( base64_encode( $user->username . '||' . $passkey ) );

	$str = stripslashes( $str );
	$str = str_replace( '[[urlx]]', $CFG->wwwroot . '/course/view.php?id=' . $course->id . $tok, $str );
	$str = str_replace( '[[url]]', $CFG->wwwroot . '/course/view.php?id=' . $course->id, $str );
	$str = str_replace( '[[expire]]', $CFG->wwwroot . '/mod/certificate/reset.php?id=' . $course->id . $tok, $str );
	$str = str_replace( '[[autologin]]', $tok, $str );
	$str = str_replace( '[[ref]]', $course->fullname, $str );
	$str = str_replace( '[[course]]', $course->fullname, $str );
	$str = str_replace( '[[fullname]]', $course->fullname, $str );
	$str = str_replace( '[[shortname]]', $course->shortname, $str );
	$str = str_replace( '[[summary]]', $course->summary, $str );
	$str = str_replace( '[[first]]', $user->firstname, $str );
	$str = str_replace( '[[firstname]]', $user->firstname, $str );
	$str = str_replace( '[[last]]', $user->lastname, $str );
	$str = str_replace( '[[lastname]]', $user->lastname, $str );
	$str = str_replace( '[[userdesc]]', $user->description, $str );
	$str = str_replace( '[[username]]', $user->username, $str );
//     $str = str_replace( '[[password]]',          connect_decrypt($user->ackey), $str );
	$str = str_replace( '[[timezone]]', preg_replace( "/[(][^)]*[)][ \t]*/", "", ( $user->timezone == 99 ) ? $CFG->timezone : $user->timezone ), $str );
	$str = str_replace( '[[date]]', userdate( $event->timestart, '', ( $user->timezone == 99 ) ? $CFG->timezone : $user->timezone ), $str );
	$str = str_replace( '[[name]]', $event->name, $str );
	$str = str_replace( '[[mins]]', gmdate( "H", $event->timeduration ) * 60 + gmdate( "i", $event->timeduration ) . ' ' . get_string( 'mins', 'local_reminders' ), $str );
	$str = str_replace( '[[duration]]', gmdate( "H:i", $event->timeduration ), $str );
	$str = str_replace( '[[event_description]]', get_string( 'remindereventdescription', 'local_reminders', $event->description ) . "<br />\n", $str );
	$str = str_replace( '[[description]]', empty( $event->description ) ? $course->summary : "<br />\n" . $event->description . "<br />\n", $str );
	$str = str_replace( '[[ievent]]', ( $event->id == 0 ) ? '' : $CFG->wwwroot . '/local/reminders/ievent.php/?evt=' . $event->id . '&rem=' . $message->id . $tok, $str );
	$str = str_replace( '[[cpurl]]', empty( $event->acurl ) ? '' : $CFG->wwwroot . '/mod/connectmeeting/launch.php?acurl=' . $event->acurl . '&guest=1', $str );
	$str = str_replace( '[[acurl]]', empty( $event->acurl ) ? '' : $CFG->wwwroot . '/mod/connectmeeting/launch.php?acurl=' . $event->acurl . '&guest=1', $str );
	$str = str_replace( '[[meeting]]', empty( $event->acurl ) ? '' : $CFG->wwwroot . '/mod/connectmeeting/launch.php?acurl=' . $event->acurl . '&course=' . $course->id . $tok, $str );
	$str = str_replace( '[[connecturl]]', empty( $event->acurl ) ? '' : $CFG->wwwroot . '/mod/' . $event->modulename . '/launch.php?acurl=' . $event->acurl . ( $event->modulename == 'rtrecording' ? '&rtrecording_id=' . $event->instance : '' ) . '&course=' . $course->id . $tok, $str );
	$str = str_replace( '[[nurlx]]', $CFG->wwwroot . '/course/view.php?id=' . $course->id . $tok, $str );
	$str = str_replace( '[[nurl]]', $CFG->wwwroot . '/course/view.php?id=' . $course->id, $str );
	$str = str_replace( '[[nmeeting]]', empty( $event->acurl ) ? '' : $CFG->wwwroot . '/mod/connectmeeting/launch.php?acurl=' . $event->acurl . $tok, $str );
	$str = str_replace( '[[nievent]]', ( $event->id == 0 ) ? '' : $CFG->wwwroot . '/local/reminders/ievent.php/?evt=' . $event->id . '&rem=' . $message->id . $tok, $str );
    $str = str_replace( '[[acmeetingname]]', empty( $event->acurl ) ? '' : $event->acurl, $str);

	// Only applicable if called from Invitations
	if ( isset( $user->refername ) ) {
		$str = str_replace( '[[refername]]', $user->refername, $str );
		$str = str_replace( '[[invitelink]]', $user->invitelink, $str );
		$str = str_replace( '[[invitenote]]', $user->invitenote, $str );
	}

	// Only applicable if called from RoleAlerts
	if ( isset( $other->roleid ) ) {
		$str = str_replace( '[[role]]', $other->role, $str );
		$str = str_replace( '[[roleverify]]', 'local/roleverify/index.php?role=' . $other->roleid, $str );
	}

	if ( get_config( 'local_coursefields', 'version' ) ) {
		global $COURSE;
		require_once( $CFG->dirroot . '/local/coursefields/lib.php' );
		$COURSE = clone( $course );
		coursefields_set_course( $COURSE );

		$search = '/\[\[course#([^\]]+)\]\]/is';
		$str    = preg_replace_callback( $search, 'local_course_callback', $str );
	}

	//RT-1511#tag for enrollment expiry
	if ( strstr( $str, '[[expiredate]]' ) && ! empty( $course ) && ! empty( $user ) ) {
		$query = "SELECT ue.timestart, ue.timeend FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid = e.id WHERE ue.userid = ? AND e.courseid = ?";
		$enrol = $DB->get_record_sql( $query, array( $user->id, $course->id ) );
		if ( empty( $enrol ) || empty( $enrol->timeend ) ) {
			$str = str_replace( '[[expiredate]]', '-', $str );
		} else {
			$date = userdate( $enrol->timeend, '%B %d, %Y' );
			$str  = str_replace( '[[expiredate]]', $date, $str );
		}
	}

	$user->profile = array();
	global $CURUSER;
	if ( ! isguestuser( $user ) ) {
		require_once( $CFG->dirroot . '/user/profile/lib.php' );
		profile_load_custom_fields( $user );
		$CURUSER = clone( $user );

		$search = '/\[\[user#([^\]]+)\]\]/is';
		$str    = preg_replace_callback( $search, 'local_user_callback', $str );
	}

	if ( $other ) {
		global $OTHER;
		$OTHER = clone( $other );

		$search = '/\[\[other#([^\]]+)\]\]/is';
		$str    = preg_replace_callback( $search, 'local_other_callback', $str );
	}

	$str = str_replace( '/' . $CFG->wwwroot, '', $str );//base url was being doubled in some cases, so removing doubles

	return $str;
}

function local_course_callback( $link ) {
	global $CFG, $COURSE;

	if ( empty( $link[1] ) ) {
		return;
	}
	$field = strip_tags( strtolower( $link[1] ) );
	if ( ! isset( $COURSE->$field ) ) {
		return;
	}

	return $COURSE->$field;
}

function local_user_callback( $link ) {
	global $CFG, $CURUSER;

	if ( empty( $link[1] ) ) {
		return;
	}
	$field = strip_tags( strtolower( $link[1] ) );
	if ( isset( $CURUSER->$field ) ) {
		return $CURUSER->$field;
	}
	if ( isset( $CURUSER->profile->$field ) ) {
		return $CURUSER->profile->$field;
	}
	if ( isset( $CURUSER->profile[ $field ] ) ) {
		return $CURUSER->profile[ $field ];
	}

	return;
}

function local_other_callback( $link ) {
	global $CFG, $OTHER;

	if ( empty( $link[1] ) ) {
		return;
	}
	$field = strtolower( $link[1] );
	if ( isset( $OTHER->$field ) ) {
		return $OTHER->$field;
	}

	return;
}

?>
