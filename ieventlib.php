<?php // $Id: ieventlib.php,v 1.5.2.10 2009/01/20 06:21:03 moodler Exp $

require_once(dirname(__FILE__) . '/../../config.php');

function write_ievent($event, $file, $user, $text = null, $subj = null) {
    global $CFG;

    if (empty($text)) $text = '<h2><b>' . $event->name . '</b></h2><br/>' . DATE(DATE_COOKIE, $event->timestart) . '<br/><br/>' . $CFG->wwwroot . '/filter/connect/launch.php?acurl=' . $event->acurl . '?token=' . str_replace("=", "%61", base64_encode($user->username . '||' . $user->ackey));
    if (empty($subj)) $subj = $event->name;

    $hostaddress = str_replace('http://', '', $CFG->wwwroot);
    $hostaddress = str_replace('https://', '', $hostaddress);

    $text = $text;
    $dtend = gmstrftime('%Y%m%dT%H%M%SZ', ($event->timestart + $event->timeduration));
    $dtstamp = gmstrftime('%Y%m%dT%H%M%SZ', time());
    $dtstart = gmstrftime('%Y%m%dT%H%M%SZ', $event->timestart);
    $dtmodified = gmstrftime('%Y%m%dT%H%M%SZ', $event->timemodified);
    $lang = current_language();
    $summary = $event->name;
    $uid = $event->id . '@' . $hostaddress;
    $html = str_replace("\\n", "<br/>\\n", $text);

    $str = 'BEGIN:VCALENDAR' . "\r\n";
    $str .= 'PRODID:-//Microsoft Corporation//Outlook 12.0 MIMEDIR//EN' . "\r\n";
    $str .= 'VERSION:2.0' . "\r\n";
    $str .= 'METHOD:PUBLISH' . "\r\n";
    $str .= 'X-MS-OLK-FORCEINSPECTOROPEN:TRUE' . "\r\n";
    $str .= 'BEGIN:VEVENT' . "\r\n";
    $str .= 'CLASS:PUBLIC' . "\r\n";
    $str .= 'CREATED:' . $dtstamp . "\r\n";
    $str .= 'DESCRIPTION: ' . $text . "\r\n";
    if ($event->timeduration > 0) $str .= 'DTEND:' . $dtend . "\r\n";
    $str .= 'DTSTAMP:' . $dtstamp . "\r\n";
    $str .= 'DTSTART:' . $dtstart . "\r\n";
    $str .= 'LAST-MODIFIED:' . $dtmodified . "\r\n";
    $str .= 'LOCATION:My Computer' . "\r\n";
    $str .= 'PRIORITY:5' . "\r\n";
    $str .= 'SEQUENCE:0' . "\r\n";
    $str .= 'SUMMARY;LANGUAGE=' . $lang . ':' . $summary . "\r\n";
    $str .= 'TRANSP:OPAQUE' . "\r\n";
    $str .= 'UID:' . $uid . "\r\n";
    $str .= 'X-ALT-DESC;FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN"><HTML><BODY>' . $html . '</BODY></HTML>' . "\r\n";
    $str .= 'X-MICROSOFT-CDO-BUSYSTATUS:BUSY' . "\r\n";
    $str .= 'X-MICROSOFT-CDO-IMPORTANCE:1' . "\r\n";
    $str .= 'X-MICROSOFT-DISALLOW-COUNTER:FALSE' . "\r\n";
    $str .= 'X-MS-OLK-ALLOWEXTERNCHECK:TRUE' . "\r\n";
    $str .= 'X-MS-OLK-AUTOFILLLOCATION:FALSE' . "\r\n";
    $str .= 'X-MS-OLK-CONFTYPE:0' . "\r\n";
    $str .= 'BEGIN:VALARM' . "\r\n";
    $str .= 'TRIGGER:-PT15M' . "\r\n";
    $str .= 'ACTION:DISPLAY' . "\r\n";
    $str .= 'DESCRIPTION:Reminder' . "\r\n";
    $str .= 'END:VALARM' . "\r\n";
    $str .= 'END:VEVENT' . "\r\n";
    $str .= 'END:VCALENDAR' . "\r\n";

    if (empty($file)) return $str;

    $dir = dirname($file);
    
    if (!file_exists($dir)) {
    	mkdir($dir, 0755, true);
    }
    
    $fp = fopen($file, 'wb');
    fwrite($fp, $str);
    fclose($fp);

    return true;
}