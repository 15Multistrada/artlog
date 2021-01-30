<?php
date_default_timezone_set("America/New_York");
//generate list of programs and TMs for select list
include('config/db_connect.php');
$sql = "SELECT DISTINCT ProgramName, TM FROM products WHERE ActiveProgram='Yes' ORDER BY ProgramName";
$resultProgram = $conn->query($sql);
$conn->close();
//generate list of UIDs for select list
include('config/db_connect.php');
$sql = "SELECT DISTINCT PacketUID FROM artnumbers WHERE ApprovalDate IS NULL ORDER BY PacketUID DESC";
$resultUID = $conn->query($sql);
$conn->close();

//generate list of art numbers from selected program & TM
if(isset($_POST["program"])){
	$programandTmName = $_POST["program"];
	$programName = substr($programandTmName,0,stripos($programandTmName,":"));
	$TmName = substr(strstr($programandTmName,":"),2);

	include('config/db_connect.php');
	$sqlartNumbers = "SELECT * FROM artnumbers WHERE ProgramName='" . $programName . "' AND TM='" . $TmName . "' AND PacketUID IS NOT NULL AND ApprovalDate IS NULL";
	$resultArtNumbers = $conn->query($sqlartNumbers);
	$conn->close();
}
//generate list of art numbers from selected UID
if(isset($_POST["UID"])){
	$packetuid = $_POST["UID"];

	include('config/db_connect.php');
	$sql = "SELECT * FROM artnumbers WHERE PacketUID='" . $packetuid . "'";
	$resultArtfromuid = $conn->query($sql);
	$conn->close();

}
// omg finally write submtted art stuff to DB: (todos: calc UID, art packet # (with dev phase) and get user name.)
if(isset($_POST["confirmed"])){
	// echo"<div class='main'> Art number approved:" . $_POST["approvalConfirmed"] . "</div>";
	$artNumbersToApprove = explode(',', $_POST["approvalConfirmed"]);
	$artNumberCount = count($artNumbersToApprove);

// grab the UID and Packet Number for these art numbers 
// just use the first art numbers info
		include('config/db_connect.php');
		$sql = "SELECT * FROM artnumbers WHERE ArtNumber='". $artNumbersToApprove[0] . "'";
    $resultArtNumbers = $conn->query($sql);

    while ($row = $resultArtNumbers ->fetch_assoc()) {
        $packetUid = $row["PacketUID"];
        $packetNumber = $row["PacketNumber"];
        }

	// -- DO The WRITTING HERE***
	for($x = 0; $x < $artNumberCount; $x++){
		include('config/db_connect.php');
		$sql = "UPDATE artnumbers SET Approved='1', ApprovedBy='Michael Althoff', ApprovalDate='" . date('m/d/Y h:i:sa') . "' WHERE ArtNumber='". $artNumbersToApprove[$x] . "'";
		
		// execute query
		if ($conn->query($sql) === TRUE) {
			echo "<div class='main'> Art number approved:" . $artNumbersToApprove[$x] . "</div>";
		} else {
			echo "Error updating record: " . $conn->error;
		}
  
	    // insert new record for each artnumber in history table
     $sql2 = "INSERT INTO artnumberhistory (ArtNumber, ActionDate, ActionUserName, PacketNumber, PacketUID, Action) VALUES ('" . $artNumbersToApprove[$x] . "', '" . strval(date("m/d/Y h:i:sa")) . "', 'Michael Althoff', '" . $packetNumber . "', '" . $packetUid . "', 'Approved')";

    if ($conn->query($sql2) === TRUE) {
      // echo "<div class='main'>History Logged</div>";
    } else {
      echo "<div class='main'>Error: " . $sql2 . "<br>" . $conn->error . "</div>";
    }

	$conn->close();
	

	}
}
?>


<!DOCTYPE html>

<?php include('templates/header.php');?>
<!-- header template includes head tags , style tags & starting body tag -->
<h2 class="main">Art Approvals</h2>

<div class="row">
<form class="core" action="./artapprovals.php" method="POST">

<div class="column">
  <label for="UID">Choose a UID:</label>
  <select name="UID" id="UID" onchange="this.form.submit()">
	<?php 
	if ($resultUID->num_rows > 0) {
		// output data of each row .. 
		echo "<option>Select One</option>";
		while($row = $resultUID->fetch_assoc()) {
	  ?>
		<option value="<?php echo $row["PacketUID"];?>"
			<?php if(!empty($packetuid) && $packetuid == $row["PacketUID"]){
				echo "selected='selected'"; 
			}?> >
		<?php echo $row["PacketUID"];?>
		</option>
	<?php
	}
	} else {
		echo "0 results";
	}
	?>
  </select>
</div>
</form>

<form class="core" action="./artapprovals.php" method="POST">
<div class="column">
  <label for="programs">Choose a Program:</label>
  <select name="program" id="programs" onchange="this.form.submit()">
	<?php 
	if ($resultProgram->num_rows > 0) {
		// output data of each row .. 
		echo "<option>Select One</option>";
		while($rowProgram = $resultProgram->fetch_assoc()) {
	  ?>
		<option value="<?php echo $rowProgram["ProgramName"] . ": " . $rowProgram["TM"];?>"
			<?php if(!empty($programandTmName) && $programandTmName == $rowProgram["ProgramName"] . ": " . $rowProgram["TM"]){
				echo "selected='selected'"; 
			}?> >
		<?php echo $rowProgram["ProgramName"] . ": " . $rowProgram["TM"];?>
		</option>
	<?php
	}
	} else {
		echo "0 results";
	}
	?>
  </select>
</div>
</form>




<!-- confirm approval form -->
<form class="core" action="./artapprovals.php" method="POST">
<div class="column">
  <!-- <p class="main" id="demo2"></p> -->
  <textarea name="approvalConfirmed" id="artConfirmed" rows="5" cols="50" readonly="true"></textarea>
	<!-- <input class="core" type="text" name="approvalConfirmed" id="demo3"> -->
	<input type="submit" name="confirmed" value="Confirm Selection">
</div>
</form>

</div>
<!-- confirm button -->
<!-- <input class="main" type="button" value="Get Selected" onclick="GetSelected()" /> -->

<!-- table from program select list -->
<?php if(isset($_POST["program"])){
	
    ?>
    <div>
            <table id="Table1">
            <caption><?php echo $programandTmName;?></caption>
            <tr>
            <th>Select</th>
                    <th>Art Number</th>
                    <th>User Name</th>
                    <th>Date Reserved</th>
                    <th>Date Submitted</th>
                    <th>Submitted By</th>
                    <th>Packet Number</th>
                    <th>Packet UID</th>
                    <th>Figure Description</th>
                    <th>Approved By</th>
                    <th>Approval Date</th>
                    <th>New Or Updated</th>
            </tr>
    <?php	if ($resultArtNumbers->num_rows > 0) {
                    // output data of each row
                    while($rowArtNumbers = $resultArtNumbers->fetch_assoc()) {
                    echo "<tr><td><input type='checkbox' name='checksubmit' value='on' onclick='GetSelected()'>";
                    echo "<td>" . $rowArtNumbers["ArtNumber"] . "</td>"; 
                    echo "<td>" . $rowArtNumbers["UserName"] . "</td>";
                    echo "<td>" . $rowArtNumbers["DateReserved"] . "</td>";
                    echo "<td>" . $rowArtNumbers["DateSubmitted"] . "</td>";
                    echo "<td>" . $rowArtNumbers["SubmittedBy"] . "</td>";
                    echo "<td>" . $rowArtNumbers["PacketNumber"] . "</td>";
                    echo "<td>" . $rowArtNumbers["PacketUID"] . "</td>";
                    echo "<td>" . $rowArtNumbers["FigureDescription"] . "</td>";
                    echo "<td>" . $rowArtNumbers["ApprovedBy"] . "</td>";
                    echo "<td>" . $rowArtNumbers["ApprovalDate"] . "</td>";
                    echo "<td>" . $rowArtNumbers["NewOrUpdated"] . "</td>";
                    }
                    } 
    ?>
            </table></div>
    <?php		} ?>

<!-- table from UID select list -->
    <?php if(isset($_POST["UID"])){
	
    ?>
    <div>
            <table id="Table2">
            <caption><?php echo $programandTmName;?></caption>
            <tr>
            <th>Select</th>
                    <th>Art Number</th>
                    <th>User Name</th>
                    <th>Date Reserved</th>
                    <th>Date Submitted</th>
                    <th>Submitted By</th>
                    <th>Packet Number</th>
                    <th>Packet UID</th>
                    <th>Figure Description</th>
                    <th>Approved By</th>
                    <th>Approval Date</th>
                    <th>New Or Updated</th>
            </tr>
    <?php		if ($resultArtfromuid->num_rows > 0) {
                    while($row = $resultArtfromuid->fetch_assoc()) {
                    echo "<tr><td><input type='checkbox' name='checksubmit' value='on' onclick='GetSelected()'>";
                    echo "<td>" . $row["ArtNumber"] . "</td>"; 
                    echo "<td>" . $row["UserName"] . "</td>";
                    echo "<td>" . $row["DateReserved"] . "</td>";
                    echo "<td>" . $row["DateSubmitted"] . "</td>";
                    echo "<td>" . $row["SubmittedBy"] . "</td>";
                    echo "<td>" . $row["PacketNumber"] . "</td>";
                    echo "<td>" . $row["PacketUID"] . "</td>";
                    echo "<td>" . $row["FigureDescription"] . "</td>";
                    echo "<td>" . $row["ApprovedBy"] . "</td>";
                    echo "<td>" . $row["ApprovalDate"] . "</td>";
                    echo "<td>" . $row["NewOrUpdated"] . "</td>";
                    }
                    } 
    ?>
            </table></div>
    <?php		} ?>

    <script type="text/javascript">
    function GetSelected() {
        //Reference the Table.
        var grid1 = document.getElementById("Table1");
        var grid2 = document.getElementById("Table2");
        if(grid1){
          //Reference the CheckBoxes in Table.
            var checkBoxes = grid1.getElementsByTagName("INPUT");
            var message = [];
        }
        if(grid2){
          //Reference the CheckBoxes in Table.
            var checkBoxes = grid2.getElementsByTagName("INPUT");
            var message = [];
        }
 
        //Loop through the CheckBoxes.
        for (var i = 0; i < checkBoxes.length; i++) {
            if (checkBoxes[i].checked) {
                var row = checkBoxes[i].parentNode.parentNode;
                message.push(row.cells[1].innerHTML);
            }
        }
 
        //Display selected Row data in <p> above.
        // document.getElementById("demo2").innerHTML = message;
		document.getElementById("artConfirmed").value = message;
    }
</script>
<!-- footer template includes closing body tag -->
<?php include('templates/footer.php'); ?>
</html>

