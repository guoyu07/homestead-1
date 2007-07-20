<?php

  /**
   * @author theFergus <kevin at tux dot appstate dot edu>
   */

function hms_update(&$content, $currentVersion)
{
    switch ($currentVersion) {
        case version_compare($currentVersion, '0.1.2', '<'):
            $db = & new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_2.sql');
            if (PEAR::isError($result)) {
                return $result;
            }
            
            $files = array();
            $files[] = 'templates/student/rlc_signup_form_page2.tmp';
            
            PHPWS_Boost::updateFiles($files, 'hms');
            
            $content[] = _('+ RLC application form template');
            $content[] = _('+ RLC application table');
        
        case version_compare($currentVersion, '0.1.3', '<'):
            $files = array();
            $files[] = 'templates/student/rlc_signup_confirmation.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = _('+ Complete system for RLC applications');
        
        case version_compare($currentVersion, '0.1.4', '<'):
            $db = & new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_4.sql');
            if (PEAR::isError($result)) {
                return $result;
            }

            $files = array();
            $files[] = 'templates/admin/display_final_rlc_assignments.tpl';
            $files[] = 'templates/admin/display_rlc_student_detail_form_questions.tpl';
            $files[] = 'templates/admin/make_new_rlc_assignments.tpl';
            $files[] = 'templates/admin/make_new_rlc_assignments_summary.tpl';
            $files[] = 'templates/admin/display_rlc_student_detail.tpl';
            $files[] = 'templates/admin/deadlines.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = _('+ RLC administration templates');
            $content[] = _('+ Deadline for Questionnaire replaced by deadlines for Profile and Application');
            $content[] = _('+ Deadline added for editing applications');
            $content[] = _('+ Deadline added for submitting RLC applications'); 

        case version_compare($currentVersion, '0.1.5', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_5.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files      = array();
            $files[]    = 'templates/admin/deadlines.tpl';
            $files[]    = 'templates/admin/statistics.tpl';
            $files[]    = 'templates/student/application_search.tpl';
            $files[]    = 'templates/student/application_search_pager.tpl';
            $files[]    = 'templates/student/application_search_results.tpl';
            $files[]    = 'templates/student/contract.tpl';
            $files[]    = 'templates/student/student_application.tpl';
            $files[]    = 'templates/student/student_application_combined.tpl';
            $files[]    = 'templates/student/student_application_redo.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = _('+ Fixed RLC deadline bug in deadlines.tpl');
            $content[] = _('+ Added Number of People Assigned');
            $content[] = _('+ Added Number of Applications Received');
            $content[] = _('+ Added Number of Learning Community Applications Received');
            $content[] = _('+ Refactored questionnaire references to application');
            $content[] = _('+ Added the contract verbage for when a student first logs in');
            $content[] = _('+ Completed Housing applications now go straight into the RLC application if the student said they were interested');
            $content[] = _('+ Added link to allow students to go to the RLC application on first login as soon as they complete an application');
            $content[] = _('+ Added link to the pdf of the contract for students that want to print it out');

        case version_compare($currentVersion, '0.1.6', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_6.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
            
            $files      = array();
            $files[]    = 'templates/admin/maintenance.tpl';
            $files[]    = 'templates/misc/login.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = _('+ Modifying permissions for RLC admins to approve members and assign to rooms');
            $content[] = _('+ Added verbage for students to see before they login');

        case version_compare($currentVersion, '0.1.7', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_7.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files   = array();
            $files[] = 'templates/admin/make_new_rlc_assignments_summary.tpl';
            $files[] = 'templates/admin/rlc_assignments_page.tpl';
            $files[] = 'templates/admin/add_floor.tpl';
            $files[] = 'templates/admin/display_room_data.tpl';
            $files[] = 'templates/admin/display_hall_data.tpl';
            $files[] = 'templates/admin/get_hall_floor_room.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Removed capacity_per_room';
            $content[] = '+ Added bedrooms_per_room';
            $content[] = '+ Added beds_per_bedroom';
            $content[] = '+ Added list of existing halls when adding new halls';
            $content[] = '+ Room assignments working - assignments now by bed instead of room';

        case version_compare($currentVersion, '0.1.8', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_8.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files = array();
            $files[] = 'templates/admin/display_learning_community_data.tpl';
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/admin/display_hall_data.tpl';
            $files[] = 'templates/admin/add_floor.tpl';
            $files[] = 'templates/admin/display_floor_data.tpl';
            $files[] = 'templates/student/student_application.tpl';
            $files[] = 'templates/admin/select_room_for_delete.tpl';
            $files[] = 'templates/admin/display_room_data.tpl';
            $files[] = 'templates/admin/verify_delete_room.tpl';
            $files[] = 'templates/admin/select_floor_for_delete_room.tpl';
            $files[] = 'templates/misc/side_thingie.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Added abbreviation and capacity changes to Add RLC template. They now properly save and delete.';
            $content[] = '+ Deleting a building now deletes the bedrooms and beds in that building.';
            $content[] = '+ Hid Edit Building temporarily. Bedroom/bed maintenance needs to be finished first.';
            $content[] = '+ Editing a floor works again. Can not delete/add rooms from floor maintenance, must go through room menu.';
            $content[] = '+ Removed gender option from student_application.tpl';

        case version_compare($currentVersion, '0.1.9', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_9.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files = array();
            $files[] = 'templates/admin/maintenance.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Sync\'d with the current live release.';
       
        case version_compare($currentVersion, '0.1.10', '<'):
            $files = array();
            $files[] = 'templates/admin/assign_floor.tpl';
            $files[] = 'templates/admin/bed_and_id.tpl';
            $files[] = 'templates/admin/get_hall_floor.tpl';
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/admin/select_floor_for_edit.tpl';
            $files[] = 'templates/admin/select_residence_hall.tpl';
            $files[] = 'templates/admin/select_room_for_edit.tpl';
            $fiels[] = 'templates/student/student_application.tpl';
            
            PHPWS_Boost::updateFiles($files, 'hms');
            
            $content[] = '+ Changed templates regarding editing/deleting rooms and floors to be more user friendly';
            $content[] = '+ Changed to version 0.1.10 to get all dev sites and production site in sync';
            $content[] = '+ Changed HMS_Room so beds are deleted manually instead of through a db object';
            $content[] = '+ Added mechanism to handle mass assignment of an entire floor';
            $content[] = '+ Added student\'s name and gender to student application template';
            $content[] = '+ All locations where usernames are saved have been extended to size 32';
            $content[] = '+ All RLC question response lengths have been extended to 2048 characters';
            $content[] = '+ WSDL modified to reflect change in Web Services server location';

        case version_compare($currentVersion, '0.1.11', '<'):
            $content[] = '+ Fixed minor glitch where assignment by room range was pulling rooms incorrectly (did not take floor number into account)';

        case version_compare($currentVersion, '0.1.12', '<'):
            $files = array();
            $files[] = 'templates/student/contract.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Contract text now shows in a scrollable iframe';
            $content[] = '+ PDF of the contract now opens in a new tab/window';
            $content[] = '+ Link to Acrobat download, opens in new tab/window';
            $content[] = '+ Added link to a FAQ page. We need to make sure there *is* a FAQ page.';

        case version_compare($currentVersion, '0.1.13', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_13.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
            
            $files = array();
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/admin/bed_and_id.tpl';
            $files[] = 'templates/misc/side_thingie.tpl';
            $files[] = 'templates/student/profile_form.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');
            
            $content[] = '+ Jeremy\'s updates to the side bar and various debugging options';
            $content[] = '+ Alphabetization of hall drop-downs';
            $content[] = '+ Assign by floor should always show ascending room numbers';
            $content[] = '+ Fixed bug in assign by floor that kept *all* assignments from going through';
            $content[] = '+ At building creation, all deleteds should be set to 0 instead of NULL';
            $content[] = '+ Added mechanism to allow viewing of all available and assigned rooms/beds in a hall';
            $content[] = '+ Various bug and syntax fixes by Jeremy';
            $content[] = '+ Added meal plan option when assigning by an entire floor';
            $content[] = '+ Adjusted color of "optionally skipped" items in side thingie';
            $content[] = '+ Added a template for the profile form';

        case version_compare($currentVersion, '0.1.14', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_14.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
            
            $files = array();
            $files[] = 'templates/student/profile_form.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Updated profile_form template';
        
        case version_compare($currentVersion, '0.1.15', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_15.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
            
            $files = array();
            $files[] = 'templates/student/student_success_failure_message.tpl';
            $files[] = 'templates/admin/deadlines.tpl';
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/admin/display_final_rlc_assignments.tpl';
            $files[] = 'templates/admin/rlc_assignments_pager.tpl';
            $files[] = 'templates/admin/make_new_rlc_assignments_summary.tpl';
            $files[] = 'templates/student/rlc_application.tpl';
            $files[] = 'templates/student/profile_form.tpl';
            $files[] = 'templates/student/verify_single_student.tpl';
            $files[] = 'templates/admin/get_single_username.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Added success/failure message template';
            $content[] = '+ Added unique constraint to user_id column in student profiles.';
            $content[] = '+ Added new deadlines (for profiles) to the deadlines page.';
            $content[] = '+ Allowed access to RLC assignments on the maintenance page.';
            $content[] = '+ Finalized Final RLC Assignments page.';
            $content[] = '+ Fixed formatting in the RLC Applicatition assignments pager and the corresponding summary.';
            $content[] = '+ Added student viewing of their RLC applications.';
        
        case version_compare($currentVersion, '0.1.16', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_16.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files = array();
            $files[] = 'templates/student/profile_form.tpl';
            
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Added "writing" column to student_profiles table';
            $content[] = '+ Adjusted student profile template for reuse in viewing profiles';
            $content[] = '+ jtickle\'s additions for ordering RLC applications';

        case version_compare($currentVersion, '0.1.17', '<'):
            $content[] = '+ Added profile editing!';

        case version_compare($currentVersion, '0.1.18', '<'):
            $files = array();
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/student/profile_form.tpl';
            $files[] = 'templates/profile_search.tpl';
            $files[] = 'templates/profile_search_pager.tpl';
            $files[] = 'templates/profile_search_results.tpl';

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Yay for searching by student';
        

            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Added profile searching!';
            $content[] = '+ Added code to prevent duplicate RLC Applications';
            $content[] = '+ Improved "Side Thingie" to show roomate status/deadlines';
        
        case version_compare($currentVersion, '0.1.19', '<'):
            $files = array();
            $files[] = 'templates/admin/rlc_assignments_pager.tpl';
            $files[] = 'templates/admin/make_new_rlc_assignments.tpl';
            $files[] = 'templates/student/show_student_info.tpl';
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/admin/rlc_roster.tpl';
            $files[] = 'templates/admin/rlc_roster_table.tpl';
            $files[] = 'templates/admin/search_by_rlc.tpl';
            $files[] = 'templates/admin/full_name_gender_email.tpl';
            
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Minor improvement to RLC Assignments pager';
            $content[] = '+ Yay for searching by student actually working';
            $content[] = '+ Yay for searching by RLC =)';

        case version_compare($currentVersion, '0.1.21', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_21.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
           
            $files = array();
            $files[] = 'templates/student/rlc_application.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '* Modified some text when viewing a RLC app for less clutter';
            $content[] = 'Roommate preference (or assigned roommate) listed in the RLC application export';
            $content[] = '+ Added support for an aggregate value in the application';
            
            $content[] = 'Calculating Aggregates...';
            
            $db = &new PHPWS_DB('hms_application');
            $db->addColumn('id');
            $db->addColumn('term_classification');
            $db->addColumn('student_status');
            $db->addColumn('preferred_bedtime');
            $db->addColumn('room_condition');
            $result = $db->select();
            if(PEAR::isError($result)) {
                return $result;
            }

            /*
             * The following is weird, and I just wanted to take a few minutes
             * to explain exactly what the hell is going on here.  Any students
             * in the database at this point have filled out an application but
             * do not have the aggregate number that is used to autoassign
             * roommates.  What follows generates the appropriate aggregate
             * number for each student.  The aggregate number is a bitmask that
             * will end up looking like this:
             *
             * Bits Meaning
             * 43   term_classification
             * 2    student_status
             * 1    preferred_bedtime
             * 0    room_condition
             *
             * Unfortunately, this code is duplicated in HMS_Application.
             * Fortunately, this code should only be needed once.
             */
            $i = 0;
            foreach($result as $row) {
                $aggregate = 0;
                $aggregate |= ($row['term_classification'] - 1) << 3;
                $aggregate |= ($row['student_status']      - 1) << 2;
                $aggregate |= ($row['preferred_bedtime']   - 1) << 1;
                $aggregate |= ($row['room_condition']      - 1);

                $db->reset();
                $db->addWhere('id', $row['id']);
                $db->addValue('aggregate', $aggregate);
                $result = $db->update();
                if(PEAR::isError($result)) {
                    return $result;
                }
                $i++;
            }
            
            $content[] = "+ Calculated $i aggregates.";

        case version_compare($currentVersion, '0.1.22', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_22.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
           
            $files = array();
            $files[] = 'templates/admin/display_room_data.tpl';
            $files[] = 'templates/student/profile_search_pager.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = "* Fixed bug in pagination of student profile results";
            $content[] = '** Bug still exists where search values need to be set in $_SESSION';
            $content[] = "+ Added 'displayed room number' to room editing";
            $content[] = "+ Added 'displayed room number' to the assign by floor/mass assignment page";
            $content[] = "+ Fixed bug in assigned RLC members page where address/telephone number were displaying incorrectly or not at all";

        case version_compare($currentVersion, '0.1.23', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_23.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
      
            $content[] = '+ Increased length of the asu_username field for RLC assignments';
            $content[] = '+ Added stateful pagination when assigning people to RLCs';
            $content[] = '+ Corrected count when viewing the Learning Community Assignments';

        case version_compare($currentVersion, '0.1.24', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_24.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files = array();
            $files[] = 'templates/student/show_student_info.tpl';
            $files[] = 'templates/admin/add_floor.tpl';
            $files[] = 'templates/admin/display_floor_data.tpl';
            $files[] = 'templates/admin/display_room_data.tpl';
            $files[] = 'templates/admin/display_hall_data.tpl';
            $files[] = 'templates/admin/add_room.tpl';
            $files[] = 'templates/admin/maintenance.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Added a _deleted_ flag to hms_assignment';
            $content[] = '+ Added a timestamp field to hms_assignment';
            $content[] = '+ Added a move_student method to move a single student between rooms';
            $content[] = '+ Student Housing Application now a link when displaying other student information (results from a search)';
            $content[] = '+ Added a flag to hms_room for private rooms';
            $content[] = '+ Added a flag to hms_room for ra rooms';
            $content[] = '+ Added a flag to hms_room for freshman reserved rooms';
            $content[] = '+ Student\'s first, middle and last names now show beside the username at the building overview page of assigned rooms/students';
            $content[] = '+ Added method to add a room to a floor';
            $content[] = '+ Added pricing tier to the room. Always.';
            $content[] = '+ Added roommate status to the student search results.';

        case version_compare($currentVersion, '0.1.25', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_25.sql');
            if(PEAR::isError($result)) {
                return $result;
            }

            $files = array();
            $files[] = 'templates/admin/full_name_gender_email.tpl';
            $files[] = 'templates/admin/rlc_roster_table.tpl';
            $files[] = 'templates/admin/maintenance.tpl';
            $files[] = 'templates/admin/verify_break_roommates.tpl';
            $files[] = 'templates/admin/confirm_remove_from_rlc.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Fixed numerous bugs that arrived with the _deleted_ flag';
            $content[] = '+ Added the ability to review RLC Applications after assignment';
            $content[] = '+ Fixed error reporting for assigning students to nonexistent rooms';
            $content[] = '+ Re-instated the ability to create and break roommates';
            $content[] = '+ Auto Assignment';

        case version_compare($currentVersion, '0.1.26', '<'):
            $db = &new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0_1_26.sql');
            if(PEAR::isError($result)) {
                return $result;
            }
            
            $content[] = '+ Letters are ready!';

        case version_compare($currentVersion, '0.1.27', '<'):
            $files = array();
            $files[] = 'templates/student/show_student_info.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = '+ Added ability to change meal plans to student search screen';

    }

    return TRUE;
}

?>
