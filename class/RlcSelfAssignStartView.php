<?php

class RlcSelfAssignStartView extends Homestead\View {

    private $student;
    private $term;
    private $rlcAssignment;
    private $housingApp;
	
    public function __construct(Student $student, $term, HMS_RLC_Assignment $rlcAssignment, HousingApplication $housingApp = null)
    {
    	$this->student = $student;
        $this->term = $term;
        $this->rlcAssignment = $rlcAssignment;
        $this->housingApp = $housingApp;
    }
    
    public function show()
    {
    	$tpl = array();
        
        $tpl['COMMUNITY_NAME'] = $this->rlcAssignment->getRlcName();
        $tpl['TERM'] = Term::toString($this->term);
        
        $form = new PHPWS_Form();
        
        $submitCmd = CommandFactory::getCommand('RlcSelfSelectInviteSave');
        $submitCmd->setTerm($this->term);
        $submitCmd->initForm($form);
        
        $form->addCheck('terms_cond', array('true'));
        $form->setLabel('terms_cond', array('I agree to the terms and conditions for this learning community. I agree to the terms of the Residence Hall License Contract. I understand & acknowledge that if I cancel my License Contract my student account will be charged <strong>$250</strong>.'));
        
        $form->addRadioAssoc('acceptance',array('accept'=>'Accept this Invitation', 'decline'=>'Decline this invitiation'));
        
        
        /**************
         * Cell Phone *
         */
        $form->addText('cellphone');
        $form->setMaxSize('cellphone', 10);
        
        if(!is_null($this->housingApp)){
        	$form->setValue('cellphone', $this->housingApp->getCellPhone());
        }
        
        $form->addCheck('do_not_call', 1);
        if(!is_null($this->housingApp) && is_null($this->housingApp->getCellPhone())){
            $form->setMatch('do_not_call', 1);
        }
        
        /*********************
         * Emergency Contact *
        *********************/
        $form->addText('emergency_contact_name');
        $form->addText('emergency_contact_relationship');
        $form->addText('emergency_contact_phone');
        $form->addText('emergency_contact_email');
        $form->addTextArea('emergency_medical_condition');

        if(!is_null($this->housingApp)){
            $form->setValue('emergency_contact_name', $this->housingApp->getEmergencyContactName());
            $form->setValue('emergency_contact_relationship', $this->housingApp->getEmergencyContactRelationship());
            $form->setValue('emergency_contact_phone', $this->housingApp->getEmergencyContactPhone());
            $form->setValue('emergency_contact_email', $this->housingApp->getEmergencyContactEmail());
            $form->setValue('emergency_medical_condition', $this->housingApp->getEmergencyMedicalCondition());
        }

        /******************
         * Missing Person *
        ******************/

        $form->addText('missing_person_name');
        $form->addText('missing_person_relationship');
        $form->addText('missing_person_phone');
        $form->addText('missing_person_email');

        if(!is_null($this->housingApp)){
            $form->setValue('missing_person_name', $this->housingApp->getMissingPersonName());
            $form->setValue('missing_person_relationship', $this->housingApp->getMissingPersonRelationship());
            $form->setValue('missing_person_phone', $this->housingApp->getMissingPersonPhone());
            $form->setValue('missing_person_email', $this->housingApp->getMissingPersonEmail());
        }
        
        $form->addSubmit('submit', 'Submit');
        
        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();
        
        return PHPWS_Template::process($tpl, 'hms', 'student/rlcSelfAssignStart.tpl');
    }
    
}