<?php

namespace Homestead;

class ShowViewByRlc extends View{

    private $rlc;

    public function __construct(HMS_Learning_Community $rlc)
    {
        $this->rlc = $rlc;
    }

    public function show()
    {
        \Layout::addPageTitle("View By RLC");

        $tpl = array();
        $tpl['RLC_ID'] = $this->rlc->getId();

        $rlc = RlcFactory::getRlcById($this->rlc->getId());
        $tpl['RLC_TITLE'] = $rlc->getName();
        $term = Term::getSelectedTerm();
        $tpl['TERM'] = Term::toString($term);

        $tpl['vendor_bundle'] = AssetResolver::resolveJsPath('assets.json', 'vendor');
        $tpl['entry_bundle'] = AssetResolver::resolveJsPath('assets.json', 'rlcMembersList');

        return \PHPWS_Template::process($tpl, 'hms', 'admin/rlcMembersList.tpl');
    }
}
