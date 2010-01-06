<?php

PHPWS_Core::initModClass('hms', 'HMS_Util.php');

class FreshmenApplicationReview extends View {
	
	private $student;
	private $term;
	private $app;
	
	public function __construct(Student $student, $term, HousingApplication $app)
	{
		$this->student	= $student;
		$this->term		= $term;
		$this->app		= $app;
	}
	
	public function show()
	{	
        $tpl = array();
        $tpl['REVIEW_MSG']      = ''; // set this to show the review message

        $tpl['STUDENT_NAME']    = $this->student->getFullName();
        $tpl['GENDER']          = $this->student->getPrintableGender();
        $tpl['ENTRY_TERM']      = Term::toString($this->term);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = $this->student->getPrintableClass();
        $tpl['STUDENT_STATUS_LBL']          = $this->student->getPrintableType();

        $tpl['MEAL_OPTION']         = HMS_Util::formatMealOption($this->app->getMealPlan());
        $tpl['LIFESTYLE_OPTION']    = $this->app->getLifestyleOption()	== 1?'Single gender':'Co-ed';
        $tpl['PREFERRED_BEDTIME']   = $this->app->getPreferredBedtime()	== 1?'Early':'Late';
        $tpl['ROOM_CONDITION']      = $this->app->getRoomCondition()	== 1?'Clean':'Dirty';

        $tpl['CELLPHONE']   = is_null($this->app->getCellPhone())?"(not provided)":$this->app->getCellPhone();
        
        //Special Needs
        $special_needs = "";
        if(isset($this->app->physical_disability)){
            $special_needs = 'Physical disability<br />';
        }
        if(isset($this->app->psych_disability)){
            $special_needs .= 'Psychological disability<br />';
        }
        if(isset($this->app->medical_need)){
            $special_needs .= 'Medical need<br />';
        }
        if(isset($this->app->gender_need)){
            $special_needs .= 'Gender need<br />';
        }

        if($special_needs == ''){
            $special_needs = 'None';
        }
        $tpl['SPECIAL_NEEDS_RESULT'] = $special_needs;

        if(Term::getTermSem($this->term) == FALL){
            $tpl['RLC_INTEREST_1'] = $this->app->rlcInterest == 0?'No':'Yes';
        }

        $form = new PHPWS_Form('hidden_form');
        $submitCmd = CommandFactory::getCommand('HousingApplicationConfirm');
        $submitCmd->setVars($_REQUEST);
		
        $submitCmd->initForm($form);
        
        $form->addSubmit('submit', 'Confirm & Continue');
        $form->setExtra('submit', 'class="hms-application-submit-button"');
        
        
        $redoCmd = CommandFactory::getCommand('ShowHousingApplicationForm');
        $redoCmd->setTerm($this->term);
        $redoCmd->setAgreedToTerms(1);
        $redoCmd->setVars($_REQUEST);
        
		$tpl['REDO_BUTTON'] = $redoCmd->getLink('modify your application');
        
        $form->mergeTemplate($tpl);
        
        $tpl = $form->getTemplate();
        
        return PHPWS_Template::process($tpl, 'hms', 'student/student_application.tpl');
	}
}

?>