<?php

class Application_UI{

    /**
     * Shows the Terms & Agreement page.
     *
     * @param terms_and_agreemtn_only bool If true, only the terms will be shown, with no way to agree/disagree. If false, 'agree' and 'disagree' buttons will be shown.
     * @param action String The action which will be submitted in the agree/disagree form.
     */
    public function showTermsAndAgreement($terms_and_agreement_only = FALSE, $action = NULL, $term = NULL)
    {
        PHPWS_Core::initModClass('hms', 'HMS_Side_Thingie.php');
        $side_thingie = new HMS_Side_Thingie(HMS_SIDE_STUDENT_AGREE);

        if($terms_and_agreement_only){
            $side_thingie->show(TRUE);
        }else{
            $side_thingie->show(FALSE);
        }

        $form = new PHPWS_Form;
        $form->addHidden('module', 'hms');
        $form->addHidden('type', 'student');
        $form->addSubmit('begin', _('I Agree'));
        $form->addSubmit('quit', _('I Disagree'));
        $form->addSubmit('agreed_to_terms', 1);

        $form->addHidden('type', 'student');
        $form->addHidden('op', $action);
        $form->addHidden('term', $term);

        $tpl = $form->getTemplate();

        $tpl['CONTRACT'] = str_replace("\n", "<br />", file_get_contents('mod/hms/inc/contract.txt'));

        return PHPWS_Template::process($tpl, 'hms', 'student/applications/contract.tpl');
    }

    public function show_housing_application($error_msg = NULL)
    {
        PHPWS_Core::initModClass('hms', 'HMS_Application_Features.php');
        PHPWS_Core::initModClass('hms', 'HousingApplication.php');
        PHPWS_Core::initModClass('hms', 'FallApplication.php');
        PHPWS_Core::initModClass('hms', 'HMS_Term.php');
        PHPWS_Core::initModClass('hms', 'HMS_SOAP.php');
        PHPWS_Core::initModClass('hms', 'HMS_Util.php');

        # Try to load the user's application, in case it already exists
        $app_result = HousingApplication::checkForApplication($_SESSION['asu_username'], $_SESSION['application_term']);
        $application = new FallApplication($app_result['id']);
        
        PHPWS_Core::initCoreClass('Form.php');
        $form = &new PHPWS_Form();

        $tpl = array();

        /*************
         * Term Info *
         *************/
        $terms        = HMS_Term::get_valid_application_terms($_SESSION['application_term']);
        $term_values  = array();
        $term_labels  = array();
        $term_matches = array();
        foreach($terms as $term){
            $label = HMS_Term::term_to_text($term['term']);
            $term_values[]  = $term['term'];
            $term_labels[]  = $label['term'] . ' ' . $label['year'];
            if((int)$term['required'] == 1){
                $term_matches[] = $term['term'];
            }
        }
        $i = 0;
        foreach($term_values as $term){
            $form->addCheck('terms_'.$i, $term);
            $i++;
        }

        $i = 0;
        foreach($term_labels as $term){
            $form->setLabel('terms_'.$i, $term);
            $i++;
        }
        
        /* If this term cannot be deselected then disable it in the form */
        foreach($term_values as $key => $value){
            foreach($term_matches as $term){
                if($value == $term){
                    $form->setMatch('terms_'.$key, $term);
                    $form->setDisabled('terms_'.$key);
                    //let the next page know that the user is "requesting" this term
                    $form->addHidden('required_terms_'.$key, $term);
                }
            }
        }

        $tpl['TERM_MSG'] = "Please check to make sure that your application terms are correct.  Do not fill out an application until your entry term (the first term on the list) is correct.";

        /****************
         * Display Info *
         ****************/
        $tpl['STUDENT_NAME']    = HMS_SOAP::get_full_name($_SESSION['asu_username']);
        $tpl['GENDER']          = (HMS_SOAP::get_gender($_SESSION['asu_username'],TRUE) == FEMALE) ? FEMALE_DESC : MALE_DESC;
        $tpl['ENTRY_TERM']      = HMS_Term::term_to_text($_SESSION['application_term'], TRUE);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = HMS_Util::formatClass(HMS_SOAP::get_student_class($_SESSION['asu_username'], $_SESSION['application_term']));
        $tpl['STUDENT_STATUS_LBL'] = HMS_Util::formatType(HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']));
        $form->addText('area_code');
        $form->setSize('area_code', 3);
        $form->setMaxSize('area_code', 3);
        $form->addText('exchange');
        $form->setSize('exchange', 3);
        $form->setMaxSize('exchange', 3);
        $form->addText('number');
        $form->setSize('number', 4);
        $form->setMaxSize('number', 4);
        $form->addCheck('do_not_call', 1);
        
        /***************
         * Meal option *
         ***************/
        # Don't show *low* meal option to freshmen
        if(HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']) != 'F'){
            $form->addDropBox('meal_option', array(BANNER_MEAL_LOW=>_('Low'),
                                                   BANNER_MEAL_STD=>_('Standard'),
                                                   BANNER_MEAL_HIGH=>_('High'),
                                                   BANNER_MEAL_SUPER=>_('Super')));
        }else{
            $form->addDropBox('meal_option', array(BANNER_MEAL_STD=>_('Standard'),
                                                   BANNER_MEAL_HIGH=>_('High'),
                                                   BANNER_MEAL_SUPER=>_('Super')));
        }
        
        if(isset($_REQUEST['meal_option'])){
            $form->setMatch('meal_option',$_REQUEST['meal_option']);
        }elseif(isset($application->meal_option)){
            $form->setMatch('meal_option',$application->meal_option);
        }else{
            $form->setMatch('meal_option', BANNER_MEAL_STD);
        }
        
        /*************
         * Lifestyle *
         *************/
        # TODO: get rid of the magic numbers!!!
        $form->addDropBox('lifestyle_option', array('1'=>_('Single Gender Building'),
                                                    '2'=>_('Co-Ed Building')));
        if(isset($_REQUEST['lifestyle_option'])){
            $form->setMatch('lifestyle_option',$_REQUEST['lifestyle_option']);
        }else if(isset($application->lifestyle_option)){
            $form->setMatch('lifestyle_option',$application->lifestyle_option);
        }else{
            $form->setMatch('lifestyle_option', '1');
        }

        /************
         * Bed time *
         ************/
        # TODO: magic numbers
        $form->addDropBox('preferred_bedtime', array('1'=>_('Early'),
                                                     '2'=>_('Late')));
        if(isset($_REQUEST['preferred_bedtime'])){
            $form->setMatch('preferred_bedtime',$_REQUEST['preferred_bedtime']);
        }else if(isset($application->preferred_bedtime)){
            $form->setMatch('preferred_bedtime',$application->preferred_bedtime);
        }else{
            $form->setMatch('preferred_bedtime', '1');
        }

        /******************
         * Room condition *
         ******************/
        #TODO: magic numbers
        $form->addDropBox('room_condition', array('1'=>_('Neat'),
                                                  '2'=>_('Cluttered')));
        if(isset($_REQUEST['room_condition'])){
            $form->setMatch('room_condition',$_REQUEST['room_condition']);
        }else if(isset($application->room_condition)){
            $form->setMatch('room_condition',$application->room_condition);
        }else{
            $form->setMatch('room_condition', '1');
        }

        /*****************
         * Special needs *
         *****************/
        $tpl['SPECIAL_NEEDS_TEXT'] = ''; // setting this template variable to anything causes the special needs text to be displayed
        $form->addCheck('special_need', array('special_need'));
        $form->setLabel('special_need', array('Yes, I require special needs housing.'));

        if(isset($_REQUEST['special_need'])){
            $form->setMatch('special_need', $_REQUEST['special_need']);
        }else if($application->physical_disability == 1 || 
                 $application->psych_disability == 1 ||
                 $application->medical_need == 1||
                 $application->gender_need == 1){
            $form->setMatch('special_need', 'special_need');
        }

        /*******
         * RLC *
         *******/
        if(HMS_Application_Features::is_feature_enabled($_SESSION['application_term'], APPLICATION_RLC_APP)
           && HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']) == 'F'
           /* && HMS_Entry_Term::get_entry_semester($_SESSION['asu_username']) == TERM_FALL */
           && HMS_RLC_Application::check_for_application($_SESSION['asu_username'], $_SESSION['application_term']) == FALSE)
        {
            $form->addRadio('rlc_interest', array(0, 1));
            $form->setLabel('rlc_interest', array(_("No"), _("Yes")));
            if(isset($_REQUEST['rlc_interest'])){
                $form->setMatch('rlc_interest',$_REQUEST['rlc_interest']);
            }else{
                $form->setMatch('rlc_interest', '0');
            }
        }else{
            $form->addHidden('rlc_interest', 0);
        }

        if(isset($_REQUEST['special_needs'])){
            $form->addHidden('special_needs', $_REQUEST['special_needs']);
        }

        $form->addSubmit('submit', _('Continue'));
        $form->addHidden('module', 'hms');
        $form->addHidden('type', 'student');
        $form->addHidden('op', 'submit_application');

        
        if(isset($error_msg)){
            $tpl['ERROR_MSG'] = $error_msg;
        }

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        return PHPWS_Template::process($tpl,'hms','student/student_application.tpl');
    }

    public function submit_application()
    {
        # Check to see if the student has special needs
        if(isset($_REQUEST['special_need'])){
            return Application_UI::show_special_needs();
        }else{
            return Application_UI::show_application_review();
        }
    }

    public function show_special_needs()
    {
        PHPWS_Core::initCoreClass('Form.php');
        $form = &new PHPWS_Form();

        $form->addCheck('special_needs', array('physical_disability','psych_disability','medical_need','gender_need'));
        $form->setLabel('special_needs', array('Physical disability', 'Psychological disability', 'Medical need', 'Transgender housing'));

        if(isset($_REQUEST['special_needs'])){
            $form->setMatch('special_needs', $_REQUEST['special_needs']);
        }

        # Carry over all the fields submitted on the first page of the application
        $form->addHidden('agreed_to_terms',     $_REQUEST['agreed_to_terms']);
        $form->addHidden('meal_option',         $_REQUEST['meal_option']);
        $form->addHidden('lifestyle_option',    $_REQUEST['lifestyle_option']);
        $form->addHidden('preferred_bedtime',   $_REQUEST['preferred_bedtime']);
        $form->addHidden('room_condition',      $_REQUEST['room_condition']);
        $form->addHidden('rlc_interest',        $_REQUEST['rlc_interest']);
        $form->addHidden('special_need',        $_REQUEST['special_need']); // pass it on, just in case the user needs to redo their application
        $form->addHidden('area_code',           $_REQUEST['area_code']);
        $form->addHidden('exchange',            $_REQUEST['exchange']);
        $form->addHidden('number',              $_REQUEST['number']);
        
        for($i = 0; $i < 4; $i++){
            if(isset($_REQUEST['terms_'.$i]) || isset($_REQUEST['required_terms_'.$i])){
                if(isset($_REQUEST['required_terms_'.$i])){
                    $form->addHidden('terms_'.$i, $_REQUEST['required_terms_'.$i]);
                } else {
                    $form->addHidden('terms_'.$i, $_REQUEST['terms_'.$i]);
                }
            }
        }

        $form->addHidden('module', 'hms');
        $form->addHidden('type','student');
        $form->addHidden('op','submit_application_special_needs');
        
        $form->addSubmit('submit', 'Continue');

        return PHPWS_Template::process($form->getTemplate(), 'hms', 'student/special_needs.tpl');
    }

    public function show_application_review()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Util.php');

        # Perform sanity checks on submitted values
        if(!is_numeric($_REQUEST['meal_option']) 
           || !is_numeric($_REQUEST['lifestyle_option']) 
           || !is_numeric($_REQUEST['preferred_bedtime']) 
           || !is_numeric($_REQUEST['room_condition']) 
           || !is_numeric($_REQUEST['rlc_interest']))
        {
            return Application_UI::show_housing_application('Invalid values submitted. Please try again');
        }

        $tpl = array();
        $tpl['REVIEW_MSG']      = ''; // set this to show the review message

        $tpl['STUDENT_NAME']    = HMS_SOAP::get_full_name($_SESSION['asu_username']);
        $tpl['GENDER']          = (HMS_SOAP::get_gender($_SESSION['asu_username'],TRUE) == FEMALE) ? FEMALE_DESC : MALE_DESC;
        $tpl['ENTRY_TERM']      = HMS_Term::term_to_text($_SESSION['application_term'], TRUE);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = HMS_Util::formatClass(HMS_SOAP::get_student_class($_SESSION['asu_username'], $_SESSION['application_term']));
        $tpl['STUDENT_STATUS_LBL']          = HMS_Util::formatType(HMS_SOAP::get_student_type($_SESSION['asu_username'], $_SESSION['application_term']));

        $tpl['MEAL_OPTION']         = HMS_Util::formatMealOption($_REQUEST['meal_option']);
        $tpl['LIFESTYLE_OPTION']    = $_REQUEST['lifestyle_option'] == 1?'Single gender':'Co-ed';
        $tpl['PREFERRED_BEDTIME']   = $_REQUEST['preferred_bedtime'] == 1?'Early':'Late';
        $tpl['ROOM_CONDITION']      = $_REQUEST['room_condition'] == 1?'Clean':'Dirty';

        if( !empty($_REQUEST['area_code']) && !empty($_REQUEST['exchange'])
            && !empty($_REQUEST['number']) )
        {
            $tpl['AREA_CODE']   = $_REQUEST['area_code'];
            $tpl['EXCHANGE']    = $_REQUEST['exchange'];
            $tpl['NUMBER']      = $_REQUEST['number'];
        } elseif(!isset($_REQUEST['do_not_call'])){
            return Application_UI::show_housing_application('Please provide a cell-phone number or click the checkbox stating that you do not wish to share your number with us.');
        }
        
        //Term information
        $values = array();
        if(isset($_REQUEST['terms'])){
            foreach($_REQUEST['terms'] as $key => $value){
                $values[] = $value;
            }
        }
        
        for($i = 0; $i < 4; $i++){
            if(isset($_REQUEST['terms_'.$i]) || isset($_REQUEST['required_terms_'.$i])){
                $values[] = isset($_REQUEST['required_terms_'.$i]) ? $_REQUEST['required_terms_'.$i] : $_REQUEST['terms_'.$i];
            }
        }
        
        if(sizeof($values) > 0){
            sort($values);
            $i = 0;
            foreach($values as $term){
                $term = substr(''.$term, 4, 2);
                if($term == TERM_SPRING){
                    $tpl['TERMS_'.$i.'_LABEL'] = 'Spring';
                    $tpl['TERMS_'.$i]          = 'Selected';
                }
                if($term == TERM_SUMMER1){
                    $tpl['TERMS_'.$i.'_LABEL'] = 'Summer Session 1';
                    $tpl['TERMS_'.$i]          = 'Selected';
                }
                if($term == TERM_SUMMER2){
                    $tpl['TERMS_'.$i.'_LABEL'] = 'Summer Session 2';
                    $tpl['TERMS_'.$i]          = 'Selected';
                }
                if($term == TERM_FALL){
                    $tpl['TERMS_'.$i.'_LABEL'] = 'Fall';
                    $tpl['TERMS_'.$i]          = 'Selected';
                }
                $i++;
            }
        }

        //Special Needs
        $special_needs = "";
        if(isset($_REQUEST['special_needs']['physical_disability'])){
            $special_needs = 'Physical disability<br />';
        }
        if(isset($_REQUEST['special_needs']['psych_disability'])){
            $special_needs .= 'Psychological disability<br />';
        }
        if(isset($_REQUEST['special_needs']['medical_need'])){
            $special_needs .= 'Medical need<br />';
        }
        if(isset($_REQUEST['special_needs']['gender_need'])){
            $special_needs .= 'Gender need<br />';
        }

        if($special_needs == ''){
            $special_needs = 'None';
        }
        $tpl['SPECIAL_NEEDS_RESULT'] = $special_needs;

        if(HMS_Entry_Term::get_entry_semester($_SESSION['asu_username']) == FALL){
            $tpl['RLC_INTEREST_1'] = $_REQUEST['rlc_interest'] == 0?'No':'Yes';
        }

        $form = &new PHPWS_Form('hidden_form');
        
        # Carry over all the fields submitted on the first page of the application
        $form->addHidden('agreed_to_terms',     $_REQUEST['agreed_to_terms']);
        $form->addHidden('meal_option',         $_REQUEST['meal_option']);
        $form->addHidden('lifestyle_option',    $_REQUEST['lifestyle_option']);
        $form->addHidden('preferred_bedtime',   $_REQUEST['preferred_bedtime']);
        $form->addHidden('room_condition',      $_REQUEST['room_condition']);
        $form->addHidden('rlc_interest',        $_REQUEST['rlc_interest']);
        if(isset($_REQUEST['special_need']))
            $form->addHidden('special_need',        $_REQUEST['special_need']); // pass it on, just in case the user needs to redo their application
        if(isset($_REQUEST['special_need']))
            $form->addHidden('special_needs',       $_REQUEST['special_needs']);
        $form->addHidden('terms',               $values);

        if( isset($_REQUEST['area_code']) && isset($_REQUEST['exchange'])
            && isset($_REQUEST['number']) )
        {
            $form->addHidden('area_code',   $_REQUEST['area_code']);
            $form->addHidden('exchange',    $_REQUEST['exchange']);
            $form->addHidden('number',      $_REQUEST['number']);
        }

        $form->addHidden('module',  'hms');
        $form->addHidden('type',    'student');
        $form->addHidden('op',      'submit_application_review');

        $form->addButton('redo_button',         'Modify application');
        $form->setExtra('redo_button',          'onClick="document.getElementById(\'hidden_form\').op.value=\'redo_application\';document.getElementById(\'hidden_form\').submit()"');
        $form->addSubmit('submit_application',  'Verify information & continue');

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();
        
        return PHPWS_Template::process($tpl, 'hms', 'student/student_application.tpl');
    }

    public function submit_application_review()
    {
        PHPWS_Core::initModClass('hms', 'HousingApplication.php');
        PHPWS_Core::initModClass('hms', 'SpringApplication.php');
        PHPWS_Core::initModClass('hms', 'SummerApplication.php');
        PHPWS_Core::initModClass('hms', 'FallApplication.php');
        PHPWS_Core::initModClass('hms', 'HMS_SOAP.php');
        PHPWS_Core::initModClass('hms', 'HMS_Term.php');
        
        //determine which terms requested by the user are valid and make an application
        $valid_terms = array();
        foreach($_REQUEST['terms'] as $term){
            if(HMS_Term::is_valid_term($term)){
                $valid_terms[] = $term;
            }
        }

        foreach($valid_terms as $key => $term){
            $sem = HMS_Term::get_term_sem($term);

            # Check for an existing application and delete it
            $app_result = HousingApplication::checkForApplication($_SESSION['asu_username'], $term);
            if($app_result !== FALSE){
                switch($sem){
                    case TERM_SPRING:
                        $application = new SpringApplication($app_result['id']);
                        break;
                    case TERM_SUMMER1:
                    case TERM_SUMMER2:
                        $application = new SummerApplication($app_result['id']);
                        break;
                    case TERM_FALL:
                        $application = new FallApplication($app_result['id']);
                }

                $application->delete();
            }

            $banner_id = HMS_SOAP::get_banner_id($_SESSION['asu_username']);

            // Hard code a sumemr meal option for all summer applications.
            // Application for other terms use whatever the student selected
            if($sem == TERM_SUMMER1 || $sem == TERM_SUMMER2){
                $meal_plan = BANNER_MEAL_5WEEK;
            }else{
                $meal_plan = $_REQUEST['meal_option'];
            }


            # Create a new application from the request data and save it
            if($sem == TERM_SUMMER1 || $sem == TERM_SUMMER2){
                $application = new SummerApplication(0, $term, $banner_id, $_SESSION['asu_username'],
                                                HMS_SOAP::get_gender($_SESSION['asu_username'], TRUE),
                                                HMS_SOAP::get_student_type($_SESSION['asu_username'],$term),
                                                HMS_SOAP::get_application_term($_SESSION['asu_username']),
                                                $_REQUEST['area_code'] . $_REQUEST['exchange'] . $_REQUEST['number'],
                                                $meal_plan,
                                                isset($_REQUEST['special_needs']['physical_disability']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['psych_disability']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['gender_need']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['medical_need']) ? 1 : 0,
                                                ROOM_TYPE_DOUBLE
                                                );

            }else if ($sem == TERM_SPRING){
                $application = new SpringApplication(0, $term, $banner_id, $_SESSION['asu_username'],
                                                HMS_SOAP::get_gender($_SESSION['asu_username'], TRUE),
                                                HMS_SOAP::get_student_type($_SESSION['asu_username'], $term),
                                                HMS_SOAP::get_application_term($_SESSION['asu_username']),
                                                $_REQUEST['area_code'] . $_REQUEST['exchange'] . $_REQUEST['number'],
                                                $meal_plan,
                                                isset($_REQUEST['special_needs']['physical_disability']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['psych_disability']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['gender_need']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['medical_need']) ? 1 : 0,
                                                $_REQUEST['lifestyle_option'],
                                                $_REQUEST['preferred_bedtime'],
                                                $_REQUEST['room_condition']);
            }else if ($sem == TERM_FALL){
                $application = new FallApplication(0, $term, $banner_id, $_SESSION['asu_username'],
                                                HMS_SOAP::get_gender($_SESSION['asu_username'], TRUE),
                                                HMS_SOAP::get_student_type($_SESSION['asu_username'], $term),
                                                HMS_SOAP::get_application_term($_SESSION['asu_username']),
                                                $_REQUEST['area_code'] . $_REQUEST['exchange'] . $_REQUEST['number'],
                                                $meal_plan,
                                                isset($_REQUEST['special_needs']['physical_disability']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['psych_disability']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['gender_need']) ? 1 : 0,
                                                isset($_REQUEST['special_needs']['medical_need']) ? 1 : 0,
                                                $_REQUEST['lifestyle_option'],
                                                $_REQUEST['preferred_bedtime'],
                                                $_REQUEST['room_condition'],
                                                $_REQUEST['rlc_interest']);
            }else{
                // Error because of invalid semester

            }

            

            $result = $application->save();

            $tpl = array();
            
            if($result == TRUE){
                # Log the fact that the application was submitted
                PHPWS_Core::initModClass('hms', 'HMS_Activity_Log.php');
                HMS_Activity_Log::log_activity($_SESSION['asu_username'], ACTIVITY_SUBMITTED_APPLICATION, $_SESSION['asu_username']);
                
                # report the application to banner;
                $application->reportToBanner();

                # Send the email confirmation
                PHPWS_Core::initModClass('hms', 'HMS_Email.php');
                HMS_Email::send_hms_application_confirmation($_SESSION['asu_username'], null);

            }else{
                # Show an error
                $tpl['TITLE'] = 'Error';
                $tpl['MESSAGE'] = 'There was an error saving your application. Please contact housing.';
                return PHPWS_Template::process($tpl,'hms', 'student/student_success_failure_message.tpl');
            }
        }

        if($_REQUEST['rlc_interest'] == 1){
            # Show the RLC application
            PHPWS_Core::initModClass('hms', 'HMS_RLC_Application.php');
            return HMS_RLC_Application::show_rlc_application_form();
        }else{
            # Show the regular menu
            $tpl['VIEW_APPLICATION']    = PHPWS_Text::secureLink(_('View My Application'), 'hms', array('type'=>'student', 'op'=>'view_application'));
            $tpl['MAIN_MENU_LINK']      = PHPWS_Text::secureLink(_('Back to Main Menu'), 'hms', array('type'=>'student','op'=>'main'));
            $tpl['LOGOUT_LINK']         = PHPWS_Text::moduleLink(_('Logout'), 'users', array('action'=>'user', 'command'=>'logout'));
            
            PHPWS_Core::initModClass('hms','HMS_Entry_Term.php');
            if(HMS_Entry_Term::get_entry_semester($_SESSION['asu_username']) == TERM_FALL){
                $tpl['RLC_LINK'] = PHPWS_Text::secureLink(_('Unique Housing Options Application'), 'hms', array('type'=>'student', 'op'=>'show_rlc_application_form'));
            }
            
            return PHPWS_Template::process($tpl, 'hms', 'student/student_application_thankyou.tpl');
        }

    }

    public function view_housing_application($username,$term)
    {
        PHPWS_Core::initModClass('hms', 'HousingApplication.php');
        PHPWS_Core::initModClass('hms', 'FallApplication.php');
        PHPWS_Core::initModClass('hms', 'SpringApplication.php');
        PHPWS_Core::initModClass('hms', 'SummerApplication.php');

        $possible_terms = HMS_Term::get_valid_application_terms($term);

        if($possible_terms !== false){
            $term_list = array();
            foreach($possible_terms as $possible_term){
                $app_result = HousingApplication::checkForApplication($username, $possible_term['term']);
                $application = new FallApplication($app_result['id']);
                if($application->id > 0){
                    $term_list[] = $possible_term;
                }
            }
        }
        
        $app_result = HousingApplication::checkForApplication($username, $term);

        $sem = HMS_Term::get_term_sem($term);
        switch($sem){
            case TERM_SPRING:
                $application = new SpringApplication($app_result['id']);
                break;
            case TERM_SUMMER1:
            case TERM_SUMMER2:
                $application = new SummerApplication($app_result['id']);
                break;
            case TERM_FALL:
                $application = new FallApplication($app_result['id']);
        }

        if($application->id == 0){
            return "No application found for the specified user and term.";
        }
        
        $tpl = array();
        //If the application has been submitted plug in the date it was created
        if( isset($application->created_on) )
            $tpl['RECEIVED_DATE']   = "Received on: " . date('d-F-Y h:i:s a', $application->created_on);

        //Plug the terms the user has applied for into the tags
        $i = 0;
        foreach($term_list as $t){
            $tpl['TERMS_'.$i] = HMS_Term::term_to_text($term_list[$i]['term']);
            $i++;
        }

        $tpl['STUDENT_NAME']                = HMS_SOAP::get_full_name($username);
        $tpl['GENDER']                      = (HMS_SOAP::get_gender($username,TRUE) == FEMALE) ? FEMALE_DESC : MALE_DESC;
        $tpl['ENTRY_TERM']                  = HMS_Term::term_to_text($term, TRUE);
        $tpl['CLASSIFICATION_FOR_TERM_LBL'] = HMS_Util::formatClass(HMS_SOAP::get_student_class($username, $term));
        $tpl['STUDENT_STATUS_LBL']          = HMS_Util::formatType(HMS_SOAP::get_student_type($username, $term));

        $tpl['MEAL_OPTION']         = HMS_Util::formatMealOption($application->meal_plan);
        $tpl['LIFESTYLE_OPTION']    = $application->lifestyle_option == 1?'Single gender':'Co-ed';
        $tpl['PREFERRED_BEDTIME']   = $application->preferred_bedtime == 1?'Early':'Late';
        $tpl['ROOM_CONDITION']      = $application->room_condition == 1?'Clean':'Dirty';

        if(strlen($application->cell_phone) == 10){
            $tpl['CELLPHONE']   .= '('.substr($application->cell_phone, 0, 3).')';
            $tpl['CELLPHONE']   .= '-'.substr($application->cell_phone, 3, 3);
            $tpl['CELLPHONE']   .= '-'.substr($application->cell_phone, 6, 4);
        }
        
        $special_needs = "";
        if($application->physical_disability == 1){
            $special_needs = 'Physical disability<br />';
        }
        if($application->psych_disability){
            $special_needs .= 'Psychological disability<br />';
        }
        if($application->medical_need){
            $special_needs .= 'Medical need<br />';
        }
        if($application->gender_need){
            $special_needs .= 'Gender need<br />';
        }

        if($special_needs == ''){
            $special_needs = 'None';
        }
        $tpl['SPECIAL_NEEDS_RESULT'] = $special_needs;

        if($sem == TERM_FALL){
            $tpl['RLC_INTEREST_1'] = $application->rlc_interest == 0?'No':'Yes';
        }

        if(Current_User::getUsername() == "hms_student"){
            $tpl['MENU_LINK'] = PHPWS_Text::secureLink('Back to main menu', 'hms', array('type'=>'student', 'op'=>'show_main_menu'));
        }

        return PHPWS_Template::process($tpl, 'hms', 'student/student_application.tpl');
    }
    
    /**
      * Shows the feature enabling/disabling interface.
      *
      * @param int $term The term to display
      *
      * @return string $template Processed template ready for display
      */
    public function show_feature_interface($success = NULL, $error = NULL){
        PHPWS_Core::initModClass('hms', 'HMS_Term.php');

        $features = array(APPLICATION_RLC_APP          => 'RLC Applications',
                          APPLICATION_ROOMMATE_PROFILE => 'Roommate Profile Searching',
                          APPLICATION_SELECT_ROOMMATE  => 'Selecting Roommates');

        if(isset($_REQUEST['submit_form'])){
            PHPWS_Core::initModClass('hms', 'HMS_Application_Features.php');
            HMS_Application_Features::save($_REQUEST);
        }

        PHPWS_Core::initModClass('hms', 'HMS_Term.php');
        //$term = (isset($_REQUEST['term']) ? $_REQUEST['term'] : HMS_Term::get_current_term());
        $term = HMS_Term::get_selected_term();

        $db = &new PHPWS_DB('hms_application_features');
        $db->addWhere('term', $term);
        $result = $db->select();

        if(PHPWS_Error::logIfError($result)){
            return false;
        }

        $matches = array();
        foreach($result as $match){
            if((int)$match['enabled'] == 1)
                $matches[] = $match['feature'];
        }
        sort($matches);

        $form = &new PHPWS_Form('features');
/*
        $form->addSelect('term',    HMS_Term::get_available_terms_list());
        $form->setMatch('term',     $term);
        $form->setExtra('term',     'onchange=refresh_page(form)');
*/
        $form->addCheck('feature',  array_keys($features));
        $form->setLabel('feature',  $features);
        $form->setMatch('feature',  $matches);
        $form->useRowRepeat();

        $form->addHidden('type',            'application_features');
        $form->addHidden('op',              'edit_features');

        // Keep track of the term being viewed for the purpose of saving it's 
        // result separately from the term being selected in the dropbox, 
        // this prevents page refreshes from overwriting data as you browse 
        // term feature settings.
        $form->addHidden('selected_term',   $term); 

        $tpl['ERROR']       = $error;
        $tpl['SUCCESSS']    = $success;
        $form->mergeTemplate($tpl);

        $form->addSubmit('submit_button',   'Submit');

        javascript('/modules/hms/page_refresh/');

        return PHPWS_Template::process($form->getTemplate(), 'hms', 'admin/features.tpl');
    }
}
?>
