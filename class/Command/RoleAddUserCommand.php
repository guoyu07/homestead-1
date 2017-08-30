<?php

namespace Homestead\Command;

use \Homestead\HMS_Role;
use \Homestead\Exception\PermissionException;

class RoleAddUserCommand extends Command {

    public function getRequestVars(){
        return array();
    }

    public function execute(CommandContext $context){
        if(!\Current_User::allow('hms', 'edit_role_members')){
            throw new PermissionException('You do not have permission to edit role members.');
        }

        $username  = $context->get('username');
        $role_id   = $context->get('role');
        $classname = $context->get('class');
        $instance  = $context->get('instance');
        if(is_null($username) || is_null($role_id)){
            echo json_encode(false);
            exit;
        }

        $role = new HMS_Role();
        $role->id = $role_id;
        if($role->load()){

            try{
                $role->addUser($username, $classname, $instance);
                echo json_encode('true');
                exit;
            }catch(\Exception $e){
                echo json_encode($e->getMessage());
                exit;
            }
        }
    }
}
