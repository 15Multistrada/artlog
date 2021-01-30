<?php
session_start();

if(isset($_POST["artNumSearch"])){
  $artNumber = $_POST["userArtNum"];
  include('config/db_connect.php');
  $sql = "SELECT * FROM artnumberhistory WHERE ArtNumber = '" . $artNumber . "' ORDER BY ID ASC";
  $resultArtNumbers = $conn->query($sql);
  $conn->close();

}
if(isset($_POST["uidSearch"])){
  $UIDNumber = $_POST["userUID"];
//   include('config/db_connect.php');
//   $sql = "SELECT * FROM artnumbers WHERE PacketUID = '" . $UIDNumber . "' ORDER BY ID ASC";
//   $resultUID = $conn->query($sql);
//   $conn->close();

  $_SESSION["packetUID"] = $UIDNumber;
  header('location: ./artSubmittalForm.php');

}
if(isset($_POST["uidSearchTable"])){
   $UIDNumber = $_POST["userUID"];
   include('config/db_connect.php');
   $sql = "SELECT * FROM artnumbers WHERE PacketUID = '" . $UIDNumber . "' ORDER BY ID ASC";
   $resultUID = $conn->query($sql);
   $conn->close();
 
 }
?>

<!DOCTYPE html>
<?php include('templates/header.php');?>
<!-- header template includes head tags , style tags & starting body tag -->
<h2 class="main">Art History</h2>

<form class="core" action="./artHistory.php" method="POST">
 <div class="row">
   <div class="column">
<label for='userArtNum'>Enter Art Number</label><br>
<input type="text" name='userArtNum'>
<input type='submit' name='artNumSearch' value='Art Number History'>
   </div>
   </form>
   <form class="core" action="./artHistory.php" method="POST">
   <div class="column">
    <label for='userUID'>Enter UID Number</label><br>
    <input type='text' name='userUID'>
    <input type='submit' name='uidSearch' value='Open ASF'>
    <input type='submit' name='uidSearchTable' value='UID History'>
   </div>
 </div>
   </form>

   <?php if(isset($_POST["artNumSearch"])){
?>
   <table class="core">
   <caption>Search Results</caption>
   <thead>
   <tr>
   <th>Art Number</th>
   <th>Action Date</th>
   <th>User Name</th>
   <th>Packet Number</th>
   <th>Packet UID</th>
   <th>Action</th>
   </tr>
   </thead>
   <tbody>
<?php if ($resultArtNumbers->num_rows > 0) {
   while($row = $resultArtNumbers->fetch_assoc()) {
      echo "<td style='text-align:center'>" . $row["ArtNumber"] . "</td>";
      echo "<td>" . $row["ActionDate"] . "</td>";
      echo "<td style='text-align:center'>" . $row["ActionUserName"] . "</td>";
      echo "<td style='text-align:center'>" . $row["PacketNumber"] . "</td>";
      echo "<td style='text-align:center'>" . $row["PacketUID"] . "</td>";
      echo "<td>" . $row["Action"] . "</td></tr>";
   }
  }
 }

?>
</tbody>
</table>

   <?php if(isset($_POST["uidSearchTable"])){
?>
   <table class="core">
   <caption>Search Results</caption>
   <thead>
   <tr>
   <th>Art Number</th>
   <th>User Name</th>
   <th>Date Submitted</th>
   <th>Packet Number</th>
   <th>Packet UID</th>
   <th>Figure Description</th>
   <th>WP File Name</th>
   </tr>
   </thead>
   <tbody>
<?php if ($resultUID->num_rows > 0) {
   while($row = $resultUID->fetch_assoc()) {
      echo "<td style='text-align:center'>" . $row["ArtNumber"] . "</td>";
      echo "<td>" . $row["UserName"] . "</td>";
      echo "<td style='text-align:center'>" . $row["DateSubmitted"] . "</td>";
      echo "<td style='text-align:center'>" . $row["PacketNumber"] . "</td>";
      echo "<td style='text-align:center'>" . $row["PacketUID"] . "</td>";
      echo "<td>" . $row["FigureDescription"] . "</td>";
      echo "<td>" . $row["WPFileName"] . "</td></tr>";
   }
  }
 }

?>
</tbody>
</table>

<!-- footer template includes closing body tag -->
<?php include('templates/footer.php'); ?>

</html>