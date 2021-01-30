<?php
include('config/db_connect.php');
$sqlProgram = "SELECT DISTINCT ProgramName, TM FROM products WHERE ActiveProgram='Yes' ORDER BY ProgramName";
$resultProgram = $conn->query($sqlProgram);
$conn->close();

if(isset($_POST["program"])){
	$programandTmName = $_POST["program"];
	$programName = substr($programandTmName,0,stripos($programandTmName,":"));
	$TmName = substr(strstr($programandTmName,":"),2);

	include('config/db_connect.php');
	$sqlartNumbers = "SELECT * FROM artnumbers WHERE ProgramName='" . $programName . "' AND TM='" . $TmName . "'";
	$resultArtNumbers = $conn->query($sqlartNumbers);
	$conn->close();
}
?>


<!DOCTYPE html>


<?php include('templates/header.php');?>
<!-- header template includes head tags , style tags & starting body tag -->
<h1 class="main">Home</h1>
<form class="main" action="./index.php" method="POST">
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

  <!-- <input type="submit" value="submit">-->
</form>




<?php if(isset($_POST["program"])){
	
?>
<div class="table-responsive-sm">
		<table class="table table-sm table-hover table-bordered">
		<caption><?php echo $programandTmName;?></caption>
		<tr>
		<t<th>ArtNumber</th>
				<th>Art Number</th>
				<th>Program Name</th>
				<th>TM</th>
				<th>User Name</th>
				<th>Date Reserved</th>
				<th>Date Submitted</th>
				<th>Submitted By</th>
				<th>Packet Number</th>
				<th>Packet UID</th>
				<th>Figure Description</th>
				<th>Source ArtNumber</th>
				<th>WP File Name</th>
				<th>Tiff Created Date</th>
				<th>Approved By</th>
				<th>Approval Date</th>
				<th>New Or Updated</th>
				<th>Date To Art Dept</th>
				<th>Assigned To Illustrator</th>
				<th>Illustrator Name</th>
				<th>CompleteDate</th>
				<th>To Writer Prov Date</th>
				<th>Tiffd Sheet T Prod Date</th>
				<th>Binder Number</th>
				<th>Illus Comment</th>
		</tr>
<?php		if ($resultArtNumbers->num_rows > 0) {
				// output data of each row
				while($rowArtNumbers = $resultArtNumbers->fetch_assoc()) {
				echo "<tr><td>" . $rowArtNumbers["ArtNumber"] . "</td>"; 
				echo "<td>" . $rowArtNumbers["ProgramName"] . "</td>";
				echo "<td>" . $rowArtNumbers["TM"] . "</td>";
				echo "<td>" . $rowArtNumbers["UserName"] . "</td>";
				echo "<td>" . $rowArtNumbers["DateReserved"] . "</td>";
				echo "<td>" . $rowArtNumbers["DateSubmitted"] . "</td>";
				echo "<td>" . $rowArtNumbers["SubmittedBy"] . "</td>";
				echo "<td>" . $rowArtNumbers["PacketNumber"] . "</td>";
				echo "<td>" . $rowArtNumbers["PacketUID"] . "</td>";
				echo "<td>" . $rowArtNumbers["FigureDescription"] . "</td>";
				echo "<td>" . $rowArtNumbers["SourceArtNumber"] . "</td>";
				echo "<td>" . $rowArtNumbers["WPFileName"] . "</td>";
				echo "<td>" . $rowArtNumbers["TiffCreatedDate"] . "</td>";
				echo "<td>" . $rowArtNumbers["ApprovedBy"] . "</td>";
				echo "<td>" . $rowArtNumbers["ApprovalDate"] . "</td>";
				echo "<td>" . $rowArtNumbers["NewOrUpdated"] . "</td>";
				echo "<td>" . $rowArtNumbers["DateToArtDept"] . "</td>";
				echo "<td>" . $rowArtNumbers["AssignedToIllustrator"] . "</td>";
				echo "<td>" . $rowArtNumbers["IllustratorName"] . "</td>";
				echo "<td>" . $rowArtNumbers["CompleteDate"] . "</td>";
				echo "<td>" . $rowArtNumbers["ToWriterProvDate"] . "</td>";
				echo "<td>" . $rowArtNumbers["TiffdSheetToProdDate"] . "</td>";
				echo "<td>" . $rowArtNumbers["BinderNumber"] . "</td>";
				echo "<td>" . $rowArtNumbers["IllusComment"] . "</td></tr>";
				}
				} 
?>
		</table></div>
<?php		} ?>

<!-- footer template includes closing body tag -->
<?php include('templates/footer.php'); ?>


</html>
