<?php

function connectAndPullTitles() {
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


// Notice the subtraction from $current_id
$query = "SELECT DISTINCT course_title FROM vw_class_info";
// Would output:
// SELECT `title` FROM `table` WHERE `id` = '5'

if (!($result=mysqli_query($conn,$query))) {
  printf("Error: %s\n", mysqli_error($conn));
}

$courseTitleArray = array();
while($row = $result->fetch_assoc()) {
    $courseTitleArray[] = str_replace('"',"",$row['course_title']);
}
 //print_r(array_values($courseTitleArray));
/* close connection */
$conn->close();

//asort($courseTitleArray); 
$resultDrop = "";

//echo '<form id="classSelector" action="index.html">';

echo '<select id="titles" name="credits" onchange="">';
foreach ($courseTitleArray as $i => $title) {
  if ($i == 0){
    continue;
  }
  echo '<option value="'.$title.'">'.$title.'</option>';
}
echo '</select>';
echo '<input id="submitB" type="button" name="add" value="Add">';
//echo "</form>";

} //close connectAndPullTitles

connectAndPullTitles();

?>
