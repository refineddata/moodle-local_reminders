<?php
defined('MOODLE_INTERNAL') || die();

$tasks = array(                                                                                                                     
    array(                                                                                                                          
        'classname' => 'local_reminders\task\local_reminders_cron',                                                                            
        'blocking' => 0,                                                                                                            
        'minute' => '*/15',                                                                                                            
        'hour' => '*',                                                                                                              
        'day' => '*',                                                                                                               
        'dayofweek' => '*',                                                                                                         
        'month' => '*'                                                                                                              
    )
);