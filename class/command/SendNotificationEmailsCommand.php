<?
/**
 * SendNotificationEmailsCommand
 *
 *  Sends the hall notification emails.
 *
 * @author Daniel West <lw77517 at appstate dot edu>
 * @package mod
 * @subpackage hms
 */
//PHPWS_Core::initModClass('hms', 'SendNotificationEmailsView.php');

class SendNotificationEmailsCommand extends Command {

    public function getRequestVars(){
        $vars = array('action'  => 'SendNotificationEmails');

        foreach(array('anonymous', 'subject', 'body', 'hall') as $key){
            if( !is_null($this->context) && !is_null($this->context->get($key)) ){
                $vars[$key] = $this->context->get($key);
            }
        }

        return $vars;
    }

    public function execute(CommandContext $context)
    {
        if(!Current_User::allow('hms', 'message_hall')){
            PHPWS_Core::initModClass('hms', 'exception/PermissionException.php');
            throw new PermissionException('You do not have permission to send messages.');
        }

        if(is_null($context->get('hall'))){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'You must select a hall to continue!');
            $cmd = CommandFactory::getCommand('ShowHallNotificationSelect');
            $cmd->redirect();
        }

        $subject   = $context->get('subject');
        $body      = $context->get('body');
        $anonymous = (!is_null($context->get('anonymous')) && $context->get('anonymous')) ? true : false;
        $from      = ($anonymous && Current_User::allow('hms', 'anonymous_notification')) ? PHPWS_Settings::get('anonymous_from_address') : Current_User::getEmail();
        $halls     = $context->get('hall');

        if(empty($subject)){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'You must fill in the subject line of the email.');
            $cmd = CommandFactory::getCommand('ShowHallNotificationEdit');
            $cmd->loadContext($context);
            $cmd->redirect();
        } else if(empty($body)){
            NQ::simple('hms', HMS_NOTIFICATION_ERROR, 'You must fill in the message to be sent.');
            $cmd = CommandFactory::getCommand('ShowHallNotificationEdit');
            $cmd->loadContext($context);
            $cmd->redirect();
        }

        //Consider using a batch process instead of doing this this inline
        PHPWS_Core::initModClass('hms', 'HMS_Residence_Hall.php');
        PHPWS_Core::initModClass('hms', 'HMS_Email.php');
        PHPWS_Core::initModClass('hms', 'HMS_Activity_Log.php');

        // Log that this is happening
        if($anonymous){
            HMS_Activity_Log::log_activity(Current_User::getUsername(), ACTIVITY_ANON_NOTIFICATION_SENT, Current_User::getUsername());
        }else{
            HMS_Activity_Log::log_activity(Current_User::getUsername(), ACTIVITY_NOTIFICATION_SENT, Current_User::getUsername());
        }

        if(is_array($halls)){
            foreach($halls as $hall_id){
                $hall = new HMS_Residence_Hall($hall_id);
                $floors = $hall->get_floors();
                foreach($floors as $floor){
                    $rooms = $floor->get_rooms();
                    foreach($rooms as $room){
                        $students = $room->get_assignees();
                        foreach($students as $student){
                            HMS_Email::send_email($student->asu_username . '@appstate.edu', $from, $subject, $body);
                        }
                    }
                }
                if($anonymous){
                    HMS_Activity_Log::log_activity(Current_User::getUsername(), ACTIVITY_HALL_NOTIFIED_ANONYMOUSLY, Current_User::getUsername(), $hall->hall_name);
                } else {
                    HMS_Activity_Log::log_activity(Current_User::getUsername(), ACTIVITY_HALL_NOTIFIED, Current_User::getUsername(), $hall->hall_name);
                }
            }
        } else {
            $hall = new HMS_Residence_Hall($halls);
            $floors = $hall->get_floors();
            foreach($floors as $floor){
                $rooms = $floor->get_rooms();
                foreach($rooms as $room){
                    $students = $room->get_assignees();
                    foreach($students as $student){
                        HMS_Email::send_email($student->asu_username . '@appstate.edu', $from, $subject, $body);
                    }
                }
            }
            if($anonymous){
                HMS_Activity_Log::log_activity(Current_User::getUsername(), ACTIVITY_HALL_NOTIFIED_ANONYMOUSLY, Current_User::getUsername(), $hall->hall_name);
            } else {
                HMS_Activity_Log::log_activity(Current_User::getUsername(), ACTIVITY_HALL_NOTIFIED, Current_User::getUsername(), $hall->hall_name);
            }
        }

        NQ::simple('hms', HMS_NOTIFICATION_SUCCESS, 'Emails sent successfully!');
        $cmd = CommandFactory::getCommand('ShowAdminMaintenanceMenu');
        $cmd->redirect();
    }
}
?>