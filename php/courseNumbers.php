<?php

/**
 * Connects and pulls all course numbers, titles and credits from database for
 * a given subject code. Creates and displays a dropdown containing the info.
 *
 * @author Josh Nichols & Oliver Alonzo
 * @version 1.0
 */

function pullInfo() {
  $servername = "RaDLabpeoplecounter.creighton.edu";
  $username = "jen94317";
  $password = "SVOOyr80h7m3sSsT";
  $mainDatabase= "for_capstone";
  // Create connection
  $conn = new mysqli($servername, $username, $password, $mainDatabase);

  // Check connection
  if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
      echo "Connected fail";
  }
  $sql="SHOW DATABASES";

  $subjectCode = $_POST["subject_code"];

  $query = "SELECT DISTINCT course_number, course_title, credit_hrs FROM vw_class_info WHERE subject_code = '".$subjectCode."'";

  if (!($result=mysqli_query($conn,$query))) {
    printf("Error: %s\n", mysqli_error($conn));
  }

  $courseNumbersArray = array();
  // builds array of pulled data seperated with &&
  while($row = $result->fetch_assoc()) {
    $courseNumbersArray[] = str_replace('"',"",$row['course_number']) . " && " . $row['course_title']. " && " . $row['credit_hrs'];
  }

  $conn->close();

  asort($courseNumbersArray);
  foreach ($courseNumbersArray as $i => $num) {
    // seperates data by &&
    $values = explode(" && ", $num);
    // Displayes a dropdown for the user with metadata for future functions
    echo '<option title="'.$values[1].'" course="'.$subjectCode.$values[0].'" credits="'.$values[2].'">'.$values[0].'</option>';
  }

}

pullinfo();

?>
