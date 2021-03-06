<?php

namespace Homestead;

class CachedStudent extends Student {

    public $term;
    public $timestamp; // Unix timestamp of when this record expires

    public function __construct(){
        parent::__construct();

    }

    public function load()
    {
        //TODO
    }

    public function save($term)
    {
        $this->term = $term;
        $this->timestamp = time();

        $db = new \PHPWS_DB('hms_student_cache');
        try {
            $result = $db->saveObject($this);
        }catch(\Exception $e){
            // Silently log any errors
            \PHPWS_Error::logIfError($e);
        }

    }

    public function isCached()
    {
        //TODO
    }

    public static function toCachedStudent(Student $student){
        $varsArray = get_object_vars($student);
        return CachedStudent::plugData($varsArray);

    }

    public static function plugData(Array $data){
        $student = new CachedStudent();

        foreach($data as $key=>$value){
            $student->$key = $value;
        }

        return $student;
    }
}
