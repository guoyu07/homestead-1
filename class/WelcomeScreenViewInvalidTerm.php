<?php

PHPWS_Core::initModClass('hms', 'View.php');

class WelcomeScreenViewInvalidTerm extends View {
    private $term;
    private $cmd;

    public function __construct($term, $cmd)
    {
        $this->term = $term;
        $this->cmd  = $cmd;
    }
	
	public function show()
    {
        $tpl = array('ENTRY_TERM'   => $this->term,
                     'CONTACT_LINK' => $this->cmd->getLink('click here'));
		return PHPWS_Template::process($tpl, 'hms', 'student/welcome_screen_no_entry_term.tpl');
	}
}

?>
