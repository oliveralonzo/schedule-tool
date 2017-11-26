<?php

class Course {
    private $crn;
    private $course;
    private $instructor;
    private $title;
    private $times;
    private $credits;
    //private $category;

    function __construct($crn, $course, $instructor, $title, $times, $credits) {
        $this->crn = $crn;
        $this->course = $course;
        $this->instructor = $instructor;
        $this->title = $title;
        $this->days = strtoupper($days);
        $this->times = [];
        $this->processTimes($times);
        $this->credits= $credits;
        //$this->category = $category;
    }

    private function processTimes($times) {
        $timesArray = explode(" && ", $times);
        foreach ($timesArray as $times) {
            $days_hours = explode(" ", trim($times));
            $this->times[$days_hours[0]] = $days_hours[1];
        }
    }

    public function getTimes() {
      return $this->times;
    }

    public function getTimesString() {
        $times = "";
        $last_days = end(array_keys($this->getTimes()));
        foreach ($this->getTimes() as $days=>$hours) {
            $times .= $days . " " . $hours;
            if (strcmp($last_days, $days) != 0) {
              $times .= " && ";
            }
        }
        return $times;
    }

    public function getCrn() {
        return $this->crn ;
    }

    public function getCourse() {
        return $this->course ;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getInstructor() {
        return $this->instructor ;
    }

    public function getCredits() {
        return $this->credits ;
    }

    public function addTimes($times) {
        $days_hours = explode(" ", trim($times));
        $this->times[$days_hours[0]] = $days_hours[1];
    }

    // public function getCategory() {
    //     return $this->category ;
    // }

    public function __toString() {
        return implode(', ', array($this->crn, $this->course, $this->instructor, $this->title, $this->getTimesString(), $this->credits));
    }

    public function checkConflicts($other) {
        foreach ($this->getTimes() as $this_days=>$this_hours) {
            foreach ($other->getTimes() as $other_days=>$other_hours) {
                if ($this->checkDayConflict(trim($this_days), trim($other_days)) == true and $this->checkTimeConflict(trim($this_hours), trim($other_hours)) == true) {
                    return true;
                }
            }
        }
        return false;
    }

    public function checkDayConflict($this_days, $other_days) {
        if (strcmp($this_days, "ONLINE") == 0 or strcmp($other_days, "ONLINE") == 0) {
            return false;
        } else {
            foreach (str_split($this_days) as $day) {
                if (strpos($other_days, $day) != false) {
                    return true;
                }
            }
            return false;
        }
    }

    private function checkTimeConflict($this_hours, $other_hours) {
        $time = explode("-", $this_hours);
        $otherTime = explode("-", $other_hours);
        if (strcmp($time[0],$otherTime[0]) >= 0 and strcmp($time[0],$otherTime[1]) <= 0){
            return true;
        } else if (strcmp($otherTime[0],$time[0]) >= 0 and strcmp($otherTime[0],$time[1]) <= 0) {
            return true;
        }
        return false;
    }

    public function equals($other) {
        //include category here?
        return $this->checkConflicts($other) or strcmp($this->getCrn(), $other->getCrn()) == 0 or strcmp($this->getTitle(), $other->getTitle()) == 0;
    }
}

class Schedule {
	private $courses;
	private $maxCredits;
	private $credits;

	function __construct($maxCredits) {
		$this->courses = [];
		$this->maxCredits = $maxCredits;
		$this->credits = 0;
	}

	public function getCourses() {
		return $this->courses;
	}

	public function full() {
        // echo "current credits". $this->credits . ", max credits:" . $this->maxCredits."<br>";
		return $this->credits === $this->maxCredits;
	}

	public function addCourse($course) {
		if ($this->courseIsContained($course) !== false or $this->getCurrentCredits() + $course->getCredits() > $this->maxCredits) {
			return false;
		} else {
			$this->courses[] = $course;
			$this->credits += $course->getCredits();
			return true;
		}
	}

	public function removeCourse($course) {
        $key = $this->courseIsContained($course);
		if($key === false) {
    		return false;
		} else {
            unset($this->courses[$key]);
            $this->credits -= $course->getCredits();
			return true;
        }
	}

	private function courseIsContained($course) {
		foreach ($this->courses as $key => $current) {
			if ($current->equals($course)) {
				return $key;
			}
		}
		return false;
	}

	public function getCurrentCredits() {
		return $this->credits;
	}

	public function getMaxCredits() {
		return $this->maxCredits;
	}

	public function __toString() {
		return implode("\n", $this->courses)."\ncredit hours:".$this->credits."\n";
	}

	public function equals($other) {
		return strcmp($this->__toString(), $other->__toString()) == 0;
	}
}

class Schedules {
	private $schedules;
	private $coursesByTitle;
	private $courseTitles;
	private $amount;
	private $maxCredits;

	function __construct($coursesByTitle, $maxCredits) {
		$this->amount = 0;
		$this->coursesByTitle = $coursesByTitle;
		$this->courseTitles = array_keys($coursesByTitle);
		$this->maxCredits = $maxCredits;
		$this->schedules = [];
		$this->generateSchedules();
	}

	private function generateSchedules() {
		$numTitles = count($this->courseTitles);
		for ($i = 0; $i<$numTitles; $i++) {
			//echo "larger restart: " . $i ."<br><br><br><br>";
			$this->generateSchedulesHelper($i, new Schedule($this->maxCredits));
		}
	}

	private function generateSchedulesHelper($currentIndex, $schedule) {
		$numTitles = count($this->courseTitles);
		if ($currentIndex <= $numTitles) {
			for ($i = $currentIndex; $i<$numTitles; $i++) {
				$currentTitle = $this->courseTitles[$i];
				foreach ($this->coursesByTitle[$currentTitle] as $course) {
          //echo $course.", credits in schedule: ".$schedule->getCurrentCredits()."<br>";
					$added = $schedule->addCourse($course);
					//echo nl2br($schedule). "<br><br>";
					if ($schedule->full()) {
                        // echo "yes<br>";
						array_push($this->schedules, clone $schedule);
						$schedule->removeCourse($course);
					}
					$this->generateSchedulesHelper($currentIndex+1, clone $schedule);
          if ($added) {
					  $schedule->removeCourse($course);
          }
				}
			}
		}
	}

	public function getSchedules() {
		return array_unique($this->schedules);
	}

	public function __toString() {
		return implode("\n\n", array_values($this->getSchedules()));
	}
}

?>
