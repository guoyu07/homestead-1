<?php

namespace Homestead\Report\ApplicationsWithIncompleteContracts;

use \Homestead\ReportHtmlView;
use \Homestead\Term;

class ApplicationsWithIncompleteContractsHtmlView extends ReportHtmlView
{

    protected function render()
    {
        parent::render();

        $this->tpl['TERM'] = Term::toString($this->report->getTerm());

        $this->tpl['rows'] = $this->report->getData();

        return \PHPWS_Template::process($this->tpl, 'hms', 'admin/reports/ApplicationsWithIncompleteContracts.tpl');
    }

}
