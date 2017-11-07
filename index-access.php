<?php

function connectToDatabase() {
$servername = "RaDLabpeoplecounter.creighton.edu";
$username = "jen94317";
$password = "SVOOyr80h7m3sSsT";
$mainDatabase= "attendance";
// Create connection
$conn = new mysqli($servername, $username, $password, $mainDatabase);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    echo "Connected fail";
}
$sql="SHOW DATABASES";


// Notice the subtraction from $current_id
$query = "SELECT * FROM vw_class_info WHERE class_id = 2325";
// Would output:
// SELECT `title` FROM `table` WHERE `id` = '5'

if (!($result=mysqli_query($conn,$query))) {
  printf("Error: %s\n", mysqli_error($conn));
}

while ($row = $result->fetch_row()) {
        //printf ("%s (%s)\n", $row[5], $row[6]);
        $classRows = "$row[3], $row[4], $row[17] $row[19], $row[6], $row[7]$row[8]$row[9]$row[10]$row[11]$row[12]$row[13], $row[14]-$row[15], 3, Req";
        printf ("%s\n", $classRows);
    }
//$row = $result->fetch_assoc();

$previous_title = $row['title'];

/* close connection */
$conn->close();

}
function retrieveData(){

}



connectToDatabase();
retrieveData();
 ?>
