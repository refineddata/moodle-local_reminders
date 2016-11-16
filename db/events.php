<?php
/**
 */

$observers = array(
    array(
        'eventname'   => '\core\event\role_assigned',
        'includefile' => '/local/reminders/eventlib.php',
        'callback' => 'reminders_role_assigned_event',
        'internal'        => true
    )
);

?>