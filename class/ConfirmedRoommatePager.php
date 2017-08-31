<?php

namespace Homestead;

class ConfirmedRoommatePager extends View {

    public function __construct(){
    }

    public function show()
    {
        $pager = new \DBPager('hms_roommate', 'HMS_Roommate');

        $pager->db->addWhere('confirmed', 1);
        $pager->db->addWhere('term', Term::getSelectedTerm());

        $pager->setModule('hms');
        $pager->setTemplate('admin/roommate_pager.tpl');
        $pager->addRowTags('get_roommate_pager_tags');
        $pager->setEmptyMessage('No roommate groups found.');

        # Setup searching on the requestor and requestee columns
        $pager->setSearch('requestor', 'requestee');

        return $pager->get();
    }
}
