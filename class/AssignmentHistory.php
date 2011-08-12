<?php

PHPWS_Core::initModClass('hms', 'HMS_Item.php');
PHPWS_Core::initModClass('hms', 'UserStatus.php');
PHPWS_Core::initModClass('hms', 'Term.php');

class AssignmentHistory extends HMS_Item {
	
	private final $db_table = 'hms_assignment_history';
	
	public $id = null;
	public $banner_id;
	public $room;
	public $assigned_on;
	public $assigned_by;
	public $assigned_reason;
	public $removed_on;
	public $removed_by;
	public $removed_reason;
	public $term;
	
	/**
	 * returns the database initialized correctly
	 * 
	 * @param none
	 * @return Database object
	 */
	public getDb() {
		return new PHPWS_DB($db_table);
	}
	
	/**
	 * returns the id of this object
	 * 
	 * @param none
	 * @return int id of this object
	 */
	public getID() {
		return $this->$id;
	}
	
	/**
	 * sets the banner id member inside this object
	 * 
	 * @param int $bannerID the banner ID of student
	 * @return boolean flag to signal failure/success
	 */
	public setBanner($bannerID=null) {
		if ( is_null($bannerID) )
			return false;
		
		$this->banner_id = $bannerID;
		return true;
	}
	
	/**
	 * sets the room member inside this object
	 * 
	 * @param String $room the room in which this history relates
	 * @return boolean flag to signal failure/success
	 */
	public setRoom($room=null) {
		if ( is_null($room) )
			return false;
		
		$this->room = $room;
	}
	
	public setTerm($term=null) {
		if ( !is_null($term) )
			$this->term = $term;
		else
			$this->term = Term::getCurrentTerm();
	}
	
	/**
	 * sets the assignment members inside this object
	 * 
	 * @param String $assign_reason A defined reason for assignment (see definitions)
	 * @param String $assigned_by the user who assigned this history (defaults to current user)
	 * @param int $assigned_on the timestamp (defaults to current time)
	 * @return none
	 */
	public setAssign($assign_reason=ASSIGN_NOREASON, $assigned_by=null, $assigned_on=null) {
		if ( is_null($assigned_on) ) // use current time
			$this->assigned_on = DateTime::getTimestamp();
		else
			$this->assigned_on = $assigned_on;
			
		if ( is_null($assigned_by) ) // use current user
			$this->assigned_by = UserStatus::getUsername();
		else
			$this->assigned_by = $assigned_by;	
			
		$this->assign_reason = $assign_reason;
	}
	
	/**
	 * sets the removal members inside this object
	 * 
	 * @param String $removed_reason A defined reason for removal (see definitions)
	 * @param String $removed_by the user who assigned this history (defaults to current user)
	 * @param int $removed_on the timestamp (defaults to current time)
	 * @return none
	 */
	public setRemove($removed_reason=UNASSIGN_NOREASON, $removed_by=null, $removed_on=null) {
		if ( is_null($removed_on) ) // use current time
			$this->removed_on = DateTime::getTimestamp();
		else
			$this->removed_on = $removed_on;
			
		if ( is_null($removed_by) ) // use current user
			$this->removed_by = UserStatus::getUsername();
		else
			$this->removed_by = $removed_by;	
		
		$this->removed_reason = $removed_reason;
	}
	
	/**
	 * initialize the data for this object by means of passed AssignmentHistory id
	 * 
	 * @param int $id AssignmentHistory id to pull from database
	 * @return boolean flag to signal failure/success
	 */
	public init($id=null) {
		if ( is_null($id) ) 
			return false;
		
		// do a database call
		$db = $this->getDb();
    	$db->addWhere('id', $id);
        $result = $db->load($this);
		
		if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('hms', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        
        return true;
	}
	
	/**
	 * static method to enable an AssignmentHistory to be pulled from the database
	 * without instantiation of a class in advance
	 * 
	 * @param int $id AssignmentHistory id to pull from database
	 * @return AssignmentHistory an AssignmentHistory object with data pertaining to passed id
	 */
	public static getHistory($id) {
		if (is_null($id))
			return false;
		
		// do a database call
		$db = new PHPWS_DB('hms_assignment_history');
    	$db->addWhere('id', $id);
        
    	// create an AssignmentHistory object with results
	   	$rObject = new AssignmentHistory;
        $result = $db->load($rObject);
		
        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('hms', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        
		// return object
		return $rObject;
	}
	
	/**
	 * static method to enable an AssignmentHistory to be create by means of passing an
	 * assignment and reason only
	 * 
	 * @param HMS_Assignment $assignment HMS_Assignment object from which to pull data
	 * @param String $reason A defined reason for assignment if not wishing to use one in assignment (see definitions)
	 * @return int|boolean the id of inserted AssignmentHistory, false if failure
	 */
	public static makeAssignmentHistory($assignment=null, $reason=null) {
		if ( is_null($assignment) ) 
			return false;
		
		if ( is_null($reason) )
			$reason = $assignment->reason;
			
		$ah = new AssignmentHistory();
		$ah->setBanner($assignment->banner_id);
		$ah->setRoom($assignment->where_am_i());
		$ah->setAssign($reason); // set all the assignment data
		$ah->save();
	}
	
	public static makeUnassignmentHistory($assignment=null, $reason=UNASSIGN_NOREASON) {
		if ( is_null($assignment) ) 
			return false;

		$db = new PHPWS_DB('hms_assignment_history');
    	$db->addWhere('banner_id', 	$assignment->banner_id);
    	$db->addWhere('room', 		$assignment->where_am_i());
    	$db->addWhere('removed_on', 'NULL', 'IS');
    	
    	$tHistory = new AssignmentHistory();
    	$result = $db->load($tHistory); // to discover ID
    	
		if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('hms', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
			
		$tHistory->setRemove($reason);
		$result = $tHistory->save();
	
		if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('hms', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }
        
        return true;
	}
	
	// Locate correct ID
	public static getHistoryID($assignment) {
		if ( is_null($assignment) )
			return false;
			
		
    	
		
	}
	
}
?>