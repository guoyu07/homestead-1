<?php

PHPWS_Core::initModClass('hms', 'ApplicationFeature.php');

class RlcSelfSelectionRegistration extends ApplicationFeatureRegistration {
    
	public function __construct()
    {
    	$this->name = 'RlcSelfSelection';
        $this->description = 'RLC Self-selection';
        $this->startDateRequired = true;
        $this->endDateRequired = true;
        $this->priority = 4;
    }
    
    public function showForStudent(Student $student, $term)
    {
    	if($student->getApplicationTerm() <= Term::getCurrentTerm()){
    		return true;
    	} else {
    		return false;
    	}
    }
}

class RlcSelfSelection extends ApplicationFeature {
	
    public function getMenuBlockView(Student $student)
    {
        PHPWS_Core::initModClass('hms', 'HMS_RLC_Assignment.php');
    	PHPWS_Core::initModClass('hms', 'HMS_Assignment.php');
        PHPWS_Core::initModClass('hms', 'RlcSelfSelectionMenuBlockView.php');
        
        $rlcAssignment = HMS_RLC_Assignment::getAssignmentByUsername($student->getUsername(), $this->getTerm());
        
        $roomAssignment = HMS_Assignment::getAssignmentByBannerId($student->getBannerId(), $this->getTerm());
        
        return new RlcSelfSelectionMenuBlockView($this->term, $this->getStartDate(), $this->getEndDate(), $rlcAssignment, $roomAssignment);
    }
}