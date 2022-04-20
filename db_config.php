 <?php
$servername = "localhost";
$username = "houseapp_user";
$password = "YK;R31WbXGj(";
$dbname = "houseapp_houseofbaboonapp_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

mysqli_close($conn);
?> 