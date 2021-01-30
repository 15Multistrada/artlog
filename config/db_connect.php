 <?php
$servername = "localhost";
$username = "mike";
$password = "test123";
$db_name = "xmco_tools";

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);



// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error) . "<br>";
}

// echo "Connected successfully. <br>";


?>