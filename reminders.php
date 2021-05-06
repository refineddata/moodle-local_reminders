<?php
    global $CFG, $OUTPUT, $PAGE, $DB;
    require_once("../../config.php");

    $task  = optional_param( 'task',   '',     PARAM_ALPHA );
    $id    = optional_param( 'id',     0,      PARAM_INT );
    $ack   = optional_param( 'ack',    '',     PARAM_RAW );
    $sort  = optional_param( 'sort',   'code', PARAM_RAW );
    $reminder = null;
    
    require_login();
    $PAGE->set_url('/local/reminders/reminders.php');
    $PAGE->set_pagelayout('admin');
    $context = context_course::instance(SITEID);
    $PAGE->set_context($context);
    $PAGE->set_title('Reminders');
    $PAGE->set_heading('Reminders');
   
    if ( isset( $id ) and $id ) $reminder = $DB->get_record( 'reminder_templates', array('id' => $id ) );
    
    if ( $task == 'conf' AND $reminder AND $ack == md5( $id.'CHECKINGISCOOL' ) ) {
        $DB->delete_records( 'reminder_templates', array( 'id' => $id ) );
    } elseif ( $task == 'del' AND !empty( $reminder ) ) {
        echo $OUTPUT->confirm('Are you sure you want to delete reminder '.$reminder->code.' ('.$reminder->lang.')', $PAGE->url.'?id='.$id.'&task=conf&ack='.md5($id.'CHECKINGISCOOL'), $PAGE->url );
        die;
    } elseif ( $task == 'add' OR $task == 'edit' OR $task == 'display') {
        edit( $task, $reminder );
    } elseif ( $task == 'save' ) {
        ssave();
    } elseif ( $task == 'test' ) {
        test( $reminder );
    }

    $toolsstr  = get_string( 'tools', 'local_reminders' );
    $remstr    = get_string( 'reminders', 'local_reminders' );
    $browsestr = get_string( 'browse', 'local_reminders' ); 
//     echo $OUTPUT->header($toolsstr, $remstr, build_navigation(array(array('name'=>$browsestr,'link'=>'','type'=>'misc'))));
    
    $PAGE->navbar->add( $browsestr );
    echo $OUTPUT->header($toolsstr, $remstr);
    
    echo $OUTPUT->heading('<center><h1>'.$browsestr.'</h1></center>');
    // Set up configuration variables
    $editicon = " <img src='" . $OUTPUT->image_url('/t/edit') . "' class='iconsmall' alt='Edit' />";
    $deleteicon = " <img src='" . $OUTPUT->image_url('/t/delete') . "' class='iconsmall' alt='Delete' />";

    // Get all reminders
    $reminders    = get_reminders( $sort );
    
    echo $OUTPUT->heading('<a href="'.$PAGE->url.'?task=add">Add reminder</a>');

    // Create a table to display the reminders with all the formated information
    $table = new html_table();
    if ( !$reminders ) echo $OUTPUT->heading(get_string('noremindersfound', 'local_reminders'));
    else {
        $code     = '<a href="'.$PAGE->url.'?sort=code">'.get_string('msg_id','local_reminders').'</a>';
        $lang     = '<a href="'.$PAGE->url.'?sort=lang">'.get_string('language','local_reminders').'</a>';
        $table->head  = array ( $code, $lang, get_string('body','local_reminders'), get_string( 'edit', 'local_reminders' ), get_string( 'send', 'local_reminders' ) );
        $table->align = array ( "left", "left", "left", "center", "center" );
        $table->width = "90%";

        foreach ($reminders as $rem) {
            $editbutton      = "<a href=\"".$PAGE->url."?id=$rem->id&task=edit\">$editicon</a>";
            $deletebutton    = "<a href=\"".$PAGE->url."?id=$rem->id&task=del\">$deleteicon</a>";
            $table->data[]   = array ( "<a href=\"".$PAGE->url."?id=$rem->id&task=display\">$rem->code</a>",
                                       $rem->lang,
                                       $rem->subject,
                                       "{$editbutton} {$deletebutton}",
                                       '<a href="'.$PAGE->url."?id=$rem->id&task=test".'">' . get_string( 'test', 'local_reminders' ) . '</a>' );
        }
    }
    // Display the information & table
    if (!empty($table)) echo html_writer::table($table);
    echo $OUTPUT->footer();
    
function edit( $task, $reminder ) {
    global $OUTPUT, $PAGE, $reminder;
    classdef($reminder);
	
    $toolsstr = get_string( 'tools', 'local_reminders' );
    $remstr   = get_string( 'reminders', 'local_reminders' );
//     echo $OUTPUT->header($toolsstr, $remstr, build_navigation(array(array('name'=>ucwords($task),'link'=>'','type'=>'misc'))));
    
    $PAGE->navbar->add( ucwords($task) );
    echo $OUTPUT->header($toolsstr, $remstr);

    $sform = new local_reminder_form();
	
    $reminder->lasttask = $task;
    $reminder->task     = 'save';
    //Translate \" to "
    $sform->set_data( $reminder );
    $sform->display();

    echo $OUTPUT->footer();
    die;
}

function ssave() {
    global $DB, $OUTPUT, $PAGE;
    
    classdef();
    $rform = new local_reminder_form();

    if ( $fdata = $rform->get_data() AND !isset( $fdata->cancel ) ) {
        if ( isset( $fdata->lasttask ) AND $fdata->lasttask != 'display' ) {
            $fdata->timemodified = time();
            
			// Set data input for editors
			$fdata->body = $fdata->body['text'];
			$fdata->vbody = $fdata->vevent_description['text'];
			$fdata->vevent_description = $fdata->vevent_description['text'];

			if ($fdata->body === null) $fdata->body = "";
			if ($fdata->vbody === null) $fdata->vbody = "";

            if ( !isset($fdata->id) or !$fdata->id or ( isset( $fdata->saveas ) AND $fdata->saveas ) ) {
                unset($fdata->id);
				
                if ( !$DB->insert_record('reminder_templates', $fdata )) {
                    error('Error creating reminder record');
                }
            }  elseif ( !$DB->update_record('reminder_templates', $fdata )) {
                error('Error updating reminder record');
            }
        } else {
            $task = optional_param( 'lasttask', 'edit', PARAM_ALPHANUM );
            $toolsstr = get_string( 'tools', 'local_reminders' );
            $remstr   = get_string( 'reminders', 'local_reminders' );
            echo $OUTPUT->header($toolsstr, $remstr, build_navigation(array(array('name'=>ucwords($task),'link'=>'','type'=>'misc'))));
            $rform->set_data( $_POST );
            $rform->display();
            echo $OUTPUT->footer();
            die;
        }
    }
    return;
}

function classdef() {
    global $CFG;
    require_once($CFG->dirroot.'/lib/formslib.php');
    require_once($CFG->dirroot.'/local/reminders/lib.php');

    class local_reminder_form extends moodleform {

        // Define the form
        function definition() {
            global $CFG, $reminder;
            $mform =& $this->_form;
	
            // Need to set editor data by using ->setValue();            
            if ( $reminder === null ) {
                $reminder = new stdClass;
                $reminder->body = "";
                $reminder->vbody = "";
            }
    
            $mform->addElement('hidden', 'id', null);
            $mform->setType('id', PARAM_INT);
            $mform->addElement('hidden', 'task', 'save');
            $mform->setType('task', PARAM_TEXT);
            $mform->addElement('hidden', 'lasttask', 'display');
            $mform->setType('lasttask', PARAM_TEXT);

            if ( ! $id = $mform->getElementValue('id') ) $id = 0;

            // **** General
            $mform->addElement( 'header', 'generalhdr', get_string('general', 'form') );
			$editorOptions = array('maxbytes'=>0,'maxfiles'=> EDITOR_UNLIMITED_FILES);

            // Code
            $mform->addElement('text', 'code', get_string('msg_id', 'local_reminders'), 'size="30"');
            $mform->addRule('code', null, 'required', null, 'client');
            $mform->setType('code', PARAM_RAW);
        
            // Saveas
            $mform->addElement('checkbox', 'saveas', get_string('saveas', 'local_reminders') );

            // -- Language
            $mform->addElement('select', 'lang', get_string('language', 'local_reminders'), get_string_manager()->get_list_of_translations());
            $mform->setDefault('lang', $CFG->lang);

            // Subject
            $mform->addElement('text', 'subject', get_string('subject', 'local_reminders'), 'size="50", maxsize="255"');
            $mform->addRule('subject', null, 'required', null, 'client');
            $mform->setType('subject', PARAM_RAW);

            // From
            $mform->addElement('text', 'userfrom', get_string('userfrom', 'local_reminders'), 'size="50"');
            $mform->setType('userfrom', PARAM_RAW);
        
			// Preview (task = _display)
			$task  = optional_param( 'task',   '',     PARAM_ALPHA );
			if ( $task == "display" ) $mform->addElement('html', "<br /><h2 style='text-align:center'>".get_string('preview', 'local_reminders')."</h2><br /><br /><div id='reminderPreview'>$reminder->body</div>");
		
            // Body
            $mform->addElement('editor', 'body', get_string('body', 'local_reminders'),array('rows'=>'25','cols'=>'45'), $editorOptions)->setValue(array('text'=>$reminder->body));
            $mform->addRule('body', null, 'required', null, 'client');
            $mform->setType('body', PARAM_RAW);

            // VEvent
            $mform->addElement('selectyesno', 'vevent', get_string('vevent', 'local_reminders'));
            $mform->setAdvanced('vevent', 'vevent');

            // VEvent Description
            $mform->addElement('editor', 'vevent_description', get_string('vevent_description', 'local_reminders'),array('rows'=>'10','cols'=>'45'), $editorOptions)->setValue(array('text'=>$reminder->vbody));
            $mform->setType('vevent_description', PARAM_RAW);
            $mform->setAdvanced('vevent_description', 'vevent');
			
            $this->add_action_buttons();

            $mform->addElement('static', 'fieldhelp', get_string('fieldhelp', 'local_reminders'), get_string('fields', 'local_reminders'), true);
        }

        function definition_after_data() {
            $mform =& $this->_form;
            if ( $mform->getElementValue('lasttask') == 'display' ) $mform->hardFreeze();
        }

        function validation($data, $files) {
            global $DB;

            $errors = parent::validation($data, $files);
            $data   = (object)$data;
            $id     = isset( $data->id ) ? $data->id : 0;
            if ( $DB->record_exists_select( 'reminder_templates', "code = '{$data->code}' AND lang = '{$data->lang}' AND id != {$id}" ) ) {
                $errors['code'] = get_string( 'duplicate', 'local_reminders' );
            }
				return $errors;
        }
    }
} 

function get_reminders( $sort='code' ) {
    global $CFG, $USER, $DB;

    $all = $DB->get_records( 'reminder_templates', array(), $sort );

    $allowed = array();
    foreach( $all as $rem ) {
        $rem->code     = stripslashes( $rem->code );
        $rem->subject  = substr( stripslashes( $rem->subject ), 0, 50 );
        $rem->access   = false;
        $allowed[] = $rem;
    }
    return $allowed;
}

function test( $reminder ) {
    global $CFG, $USER, $DB;
    
    $event = $DB->get_record_sql( "SELECT e.* FROM {$CFG->prefix}event e, {$CFG->prefix}reminders r WHERE e.id = r.event AND r.code = '{$reminder->code}' LIMIT 1" );

    require_once( $CFG->dirroot.'/local/reminders/lib.php' );    
    reminders_send( $reminder->code, $event, $USER );
}
?>
