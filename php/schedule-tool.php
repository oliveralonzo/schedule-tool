<?php
/**
* Class that simulates a course, containing all the information necessary
*
* @author Josh Nichols & Oliver Alonzo
* @version 1.0
*/
class Course {
    private $crn;
    private $course;
    private $instructor;
    private $title;
    private $times;
    private $credits;
    //private $category;

    /**
    * Constructor method for class object.
    *   Initializes fields and calls helper processTimes method
    */
    function __construct($crn, $course, $instructor, $title, $times, $credits) {
        $this->crn = $crn;
        $this->course = $course;
        $this->instructor = $instructor;
        $this->title = $title;
        $this->times = [];
        $this->processTimes($times);
        $this->credits= $credits;
        //$this->category = $category;
    }

    /**
    * Helper method to process the times in case a class has more than one time
    *   (e.g. business classes with test times scheduled in)
    * @param String $times the times of the class
    */
    private function processTimes($times) {
        if (!empty($times)) {
          $timesArray = explode(" && ", $times);
          foreach ($timesArray as $times) {
              $this->addTimes($times);
          }
        }
    }

    /**
    * Accessor method for times
    */
    public function getTimes() {
      return $this->times;
    }

    /**
    * Accessor method for times as a String
    */
    public function getTimesString() {
        $times = "";
        $last_days = end(array_keys($this->getTimes()));
        foreach ($this->times as $days=>$hours) {
          foreach($hours as $i=>$hour) {
            $times .= $days . " " . $hour;
            if (strcmp($last_days, $days) != 0 or $i != count($hours)-1) {
              $times .= " && ";
            }
          }
        }
        return $times;
    }

    /**
    * Accessor method for crn
    */
    public function getCrn() {
        return $this->crn ;
    }

    /**
    * Accessor method for course code
    */
    public function getCourse() {
        return $this->course ;
    }

    /**
    * Accessor method for course title
    */
    public function getTitle() {
        return $this->title;
    }

    /**
    * Accessor method for instructor's full name
    */
    public function getInstructor() {
        return $this->instructor ;
    }

    /**
    * Accessor method for course's credits
    */
    public function getCredits() {
        return $this->credits ;
    }

    /**
    * Method that adds a new meeting ime to a course
    */
    public function addTimes($times) {
        $days_hours = explode(" ", trim($times));
        if (!isset($this->times[$days_hours[0]])) {
          $this->times[$days_hours[0]] = [];
        }
        array_push($this->times[$days_hours[0]], $days_hours[1]);
    }

    /**
    * Accessor method for categories
    *   categories should be added in the future (e.g. Critical Issues)
    */
    // public function getCategory() {
    //     return $this->category ;
    // }

    /**
    * To string method for course
    * @return String a string representation of the course
    */
    public function __toString() {
        if (empty($this->crn)) {
          return "";
        } else {
          return implode(', ', array($this->crn, $this->course, $this->instructor, $this->title, $this->getTimesString(), $this->credits));
        }
    }

    /**
    * Function that checks if another course would have conflict with the
    * current instance.
    * @param Course $other the other course to checks
    * @return bool true or false depending on whether there is a conflict
    */
    public function checkConflicts($other) {
        foreach ($this->getTimes() as $this_days=>$this_hours) {
          foreach ($this_hours as $this_current_hours) {
            foreach ($other->getTimes() as $other_days=>$other_hours) {
              foreach ($other_hours as $other_current_hours) {
                if ($this->checkDayConflict(trim($this_days), trim($other_days)) == true and $this->checkTimeConflict(trim($this_current_hours), trim($other_current_hours)) == true) {
                    return true;
                }
              }
            }
          }
        }
        return false;
    }

    /**
    * Helper method to check if two courses meet on the same day
    * @param String $this_days the days for the current instance of Course
    * @param String $other_days the days for the other instance of Course
    * @return bool true or false depending on whether there is a conflict
    */
    public function checkDayConflict($this_days, $other_days) {
        if (strcmp($this_days, "ONLINE") == 0 or strcmp($other_days, "ONLINE") == 0) {
            return false;
        } else {
            foreach (str_split($this_days) as $day) {
                if (strpos($other_days, $day) !== false) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
    * Helper method to check if two courses meet during the same hours
    * @param String $this_hours the hours for the current instance of Course
    * @param String $other_hours the hours for the other instance of Course
    * @return bool true or false depending on whether there is a conflict
    */
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

    /**
    * Defines what makes two Courses equal (i.e. conflicts, crn or title)
    */
    public function equals($other) {
        //include category here?
        return $this->checkConflicts($other) or strcmp($this->getCrn(), $other->getCrn()) == 0 or strcmp($this->getTitle(), $other->getTitle()) == 0;
    }
}

/**
* Class that simulates a schedule, containing all the information necessary
*
* @author Josh Nichols & Oliver Alonzo
* @version 1.0
*/
class Schedule {
	private $courses;
	private $maxCredits;
	private $credits;

  /**
  * Constructor method for schedule
  * @param int $maxCredits the maximum amount of credits for the scheduled
  * @param String $blocks the times when the schedule shouldn't have classes
  */
	function __construct($maxCredits, $blocks) {
		$this->courses = [];
		$this->maxCredits = $maxCredits;
		$this->credits = 0;
    $this->addBlocks($blocks);
	}

  /**
  * Helper method that converts blocks into a course so no classes are added
  *   at those times
  * @param String $blocks the times when the schedule shouldn't have classes
  */
  private function addBlocks($blocks) {
    if (count($blocks)>0) {
        $dummy = new Course("", "", "", "", "", "0");
        foreach ($blocks as $block) {
          $dummy->addTimes($block);
        }
        $this->addCourse($dummy);
    }
  }

  /**
  * Accessor method four courses in schedule
  */
	public function getCourses() {
		return $this->courses;
	}

  /**
  * Method that checks if the schedule is at max capacity
  * @return bool true or false depending on whether schedule is at max capacity
  */
	public function full() {
        // echo "current credits". $this->credits . ", max credits:" . $this->maxCredits."<br>";
		return $this->credits === $this->maxCredits;
	}

  /**
  * Method that adds a course to the schedule, if possible
  * @return bool true or false depending of whether the course was added
  */
	public function addCourse($course) {
    if ($this->getCurrentCredits() + $course->getCredits() > $this->maxCredits) {
      //echo "hey";
			return "exceeds";
		} else if ($this->courseIsContained($course) !== false) {
      //echo "contained" . $course . "<br>";
      return false;
    } else {
      //echo "added ".$course. "<br>";
			$this->courses[] = $course;
			$this->credits += $course->getCredits();
			return true;
		}
	}

  /**
  * Method that removes a course to the schedule, if it is contained
  * @return bool true or false depending of whether the course was removed
  */
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

  /**
  * Method that checks if course is course is contained in schedule
  * @param $course the course to checks
  * @return String key for array if the course is contained, false if not
  */
	private function courseIsContained($course) {
		foreach ($this->courses as $key => $current) {
			if ($current->equals($course)) {
				return $key;
			}
		}
		return false;
	}

  /**
  * Accessor method for how many credits are currently in schedule
  */
	public function getCurrentCredits() {
		return $this->credits;
	}

  /**
  * Accessor method for schedule's maximum credits allowed
  */
	public function getMaxCredits() {
		return $this->maxCredits;
	}

  /**
  * Method that creates a 'hash' for a schedule, using all its courses' crns
  * @return String the hash for the schedule
  */
  public function hash() {
    $hash = "";
    foreach($this->courses as $course) {
      $hash .= $course->getCrn();
    }
    return $hash;
  }

  /**
  * To string method for schedule
  * @return String a string representation of the schedule
  */
	public function __toString() {
		return implode("\n", $this->courses);
    //Add if you want to print credit hours
    //."\ncredit hours:".$this->credits."\n";
	}

  /**
  * Defines what makes two Schedules equal (i.e. their string representations)
  */
	public function equals($other) {
		return strcmp($this->__toString(), $other->__toString()) == 0;
	}
}

/**
* Class that contains an array of Schedule objects
* @author Josh Nichols & Oliver Alonzo
* @version 1.0
*/
class Schedules {
	private $schedules;
	private $coursesByTitle;
	private $courseTitles;
	private $amount;
	private $maxCredits;

  /**
  * Constructor method that takes an array of courses mapped by title and
  *   calls helper method to populate schedules
  * @param array $coursesByTitle an array of courses, indexed by titled
  * @param int $maxCredits the maximum credits the schedules should have
  * @param String $blocks the times the schedules shouldn't have classes
  */
	function __construct($coursesByTitle, $maxCredits, $blocks) {
		$this->amount = 0;
		$this->coursesByTitle = $coursesByTitle;
		$this->courseTitles = array_keys($coursesByTitle);
		$this->maxCredits = $maxCredits;
		$this->schedules = [];
		$this->generateSchedules($blocks);
	}

  /**
  * Method that initializes helper method to generate schedules
  * @param String $blocks the times the schedules shouldn't have classes
  */
	private function generateSchedules($blocks) {
		$numTitles = count($this->courseTitles);
		$this->generateSchedulesHelper(0, new Schedule($this->maxCredits, $blocks), $numTitles, 0);
	}

  /**
  * Helper method that generates possible combinations of courses, currently
  *   capping at 15. In the future, should take a capping NumberFormatter
  * @param int $currentIndex the index for the course title to look for
  * @param Schedule $schedule the current working schedule
  * @param int $numTitles how many titles there are in $coursesByTitle
  * @param int $amount how many schedules there currently are
  */
	private function generateSchedulesHelper($currentIndex, $schedule, $numTitles, $amount) {
		if ($currentIndex <= $numTitles) {
			for ($i = $currentIndex; $i<$numTitles; $i++) {
        if (!$schedule->full()) {
  				$currentTitle = $this->courseTitles[$i];
  				foreach ($this->coursesByTitle[$currentTitle] as $course) {
  					$added = $schedule->addCourse($course);
            if ($added === true) {
    					if ($schedule->full()) {
                $hash = $schedule->hash()."<br>";
                if (!isset($this->schedules[$hash])) {
    						  $this->schedules[$hash] = clone $schedule;
                }
    						$schedule->removeCourse($course);
    					}
    					$this->generateSchedulesHelper($currentIndex+1, clone $schedule, $numTitles, $amount);
    					$schedule->removeCourse($course);
            } else if ($added === "exceeds") {
              break;
            }
            // 15 IS SET AS A CONSTANT, MAKE IT A VARIABLE
            if (count($this->schedules) == 15) {
              return;
            }
  				}
          $newAmount = count($this->schedules);
          if (count($this->schedules) == 0 and $i == $numTitles-1) {
            return;
          }
        }
			}
		}
	}

  /**
  * Accessor method for array of Schedule objects
  */
	public function getSchedules() {
		return $this->schedules;
	}

  /**
  * To string method for Schedules
  * @return String a string representation of the schedules
  */
	public function __toString() {
		return implode("\n\n", array_values($this->getSchedules()));
	}
}

?>
