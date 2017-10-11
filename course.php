<?php

class Course {
    private $crn;
    private $course;
    private $instructor;
    private $days;
    private $time;
    private $credits;
    private $category;

    function __construct($crn, $course, $instructor, $days, $time, $credits, $category) {
        $this->crn = $crn;
        $this->course = $course;
        $this->instructor = $instructor;
        $this->days = strtoupper($days);
        $this->time = $time;
        $this->credits= $credits;
        $this->category = $category;
    }

   public function getCrn() {
        return $this->crn ;
    }

    public function getCourse() {
        return $this->course ;
    }

    public function getDays() {
        return $this->days ;
    }

    public function getTime() {
        return $this->time;
    }

    public function getInstructor() {
        return $this->instructor ;
    }

    public function getCredits() {
        return $this->credits ;
    }

    public function getCategory() {
        return $this->category ;
    }

    public function __toString() {
        return implode(', ', array($this->crn, $this->course, $this->instructor, $this->days, $this->time, $this->course, $this->credits, $this->category));
    }

    public function checkConflicts($other) {
        return $this->checkDayConflict($other) == true and $this->checkTimeConflict($other);
    }

    public function checkDayConflict($other) {
        if (strcmp($this->getDays(), "ONLINE") == 0 or strcmp($other->getDays(), "ONLINE") == 0) {
            return false;
        } else {
            foreach (str_split($this->days) as $day) {
                if (strpos($other->getDays(), $day) !== false) {
                    return true;
                }
            }
            return false;
        }
    }

    private function checkTimeConflict($other) {
        $time = explode("-", $this->getTime());
        $otherTime = explode("-", $other->getTime());

        if (strcmp($time[0], $otherTime[0]) == 0) {
            return true;
        } else if (strcmp($time[0],$otherTime[0]) >= 0 and strcmp($time[0],$otherTime[1]) <= 0){
            return true;
        } else if (strcmp($otherTime[0],$time[0]) >= 0 and strcmp($otherTime[0],$time[1]) <= 0) {
            return true;
        }

        return strcmp(time[0], otherTime[0]) == 0;
    }

    public function equals($other) {
        //include category here?
        return $this->checkConflicts($other) or strcmp($this->getCrn(), $other->getCrn()) == 0 or strcmp($this->getCourse(), $other->getCourse()) == 0;
    }
}

?>
