<?php

class Course {
    private $crn;
    private $course;
    private $instructor;
    private $title;
    private $days;
    private $time;
    private $credits;
    private $category;

    function __construct($crn, $course, $instructor, $title, $days, $time, $credits, $category) {
        $this->crn = $crn;
        $this->course = $course;
        $this->instructor = $instructor;
        $this->title = $title;
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
        return trim($this->time);
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

    public function getCategory() {
        return $this->category ;
    }

    public function __toString() {
        return implode(', ', array($this->crn, $this->course, $this->instructor, $this->title, $this->days, $this->time, $this->course, $this->credits, $this->category));
    }

    public function checkConflicts($other) {
        return $this->checkDayConflict($other) == true and $this->checkTimeConflict($other) == true;
    }

    public function checkDayConflict($other) {
        if (strcmp($this->getDays(), "ONLINE") == 0 or strcmp($other->getDays(), "ONLINE") == 0) {
            return false;
        } else {
            foreach (str_split($this->getDays()) as $day) {
                if (strpos($other->getDays(), $day) != false) {
                    return true;
                }
            }
            return false;
        }
    }

    private function checkTimeConflict($other) {
        $time = explode("-", $this->getTime());
        $otherTime = explode("-", $other->getTime());

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
		return $this->credits == $this->maxCredits;
	}

	public function addCourse($course) {
		if ($this->courseIsContained($course) or $this->getCurrentCredits() + $course->getCredits() > $this->maxCredits) {
			return false;
		} else {
			$this->courses[] = $course;
			$this->credits += $course->getCredits();
			return true;
		}
	}

	public function removeCourse($course) {
		if(($key = array_search($course, $this->courses)) != false) {
    		unset($this->courses[$key]);
			$this->credits -= $course->getCredits();
			return true;
		}
		return false;
	}

	private function courseIsContained($course) {
		foreach ($this->courses as $current) {
			if ($current->equals($course)) {
				return true;
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
		return implode("\n", $this->courses);
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
		$this->amount = $amount;
		$this->coursesByTitle = $coursesByTitle;
		$this->courseTitles = array_keys($coursesByTitle);
		$this->maxCredits = $maxCredits;
		$this->schedules = [];
		$this->generateSchedules();
	}

	private function generateSchedules() {
		$numTitles = count($this->courseTitles);
		for ($i = 0; $i<$numTitles; $i++) {
			//echo "restart: " . $i ."<br><br><br><br>";
			$this->generateSchedulesHelper($i, new Schedule($this->maxCredits));
		}
	}

	private function generateSchedulesHelper($currentIndex, $schedule) {
		//echo "count: ". count($schedule) . "<br>";
		$numTitles = count($this->courseTitles);
		if ($currentIndex <= $numTitles) {
			for ($i = $currentIndex; $i<$numTitles; $i++) {
				$currentTitle = $this->courseTitles[$i];
				foreach($this->coursesByTitle[$currentTitle] as $course) {
					$schedule->addCourse($course);
					//echo nl2br($schedule). "<br><br>";
					if ($schedule->full()) {
						//echo "yes" ."<br><br><br><br>";
						array_push($this->schedules, clone $schedule);
						$schedule->removeCourse($course);
					}
					$this->generateSchedulesHelper(++$currentIndex, $schedule);
					$schedule->removeCourse($course);
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