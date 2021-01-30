<?php
date_default_timezone_set("America/New_York");
// grab program and tm list from products table to popluate the drop down box
include('config/db_connect.php');
$sqlProgram = "SELECT DISTINCT ProgramName, TM FROM products WHERE ActiveProgram='Yes' ORDER BY ProgramName";
$resultProgram = $conn->query($sqlProgram);
$conn->close();


if(isset($_POST["insertNewNumber"])){
  if ($_POST["program"] =="") {
    echo"<div class='main'> Please select a TM. </div>";
        } else {
		  $programTmName = $_POST["program"];
				// grab everything before the :
			$programName = strstr($programTmName, ':',true);
				// the 2 is to remove the : and following space
      $TMname = substr(strstr($programTmName, ':'),2);	
						// grab all art number records for the selected program and TM
			include('config/db_connect.php');
			$sqlArtNumbers = "SELECT * FROM artnumbers WHERE ProgramName = '" . $programName . "'";
      $resultArtNumbers = $conn->query($sqlArtNumbers);
      //find highest number
       if ($resultArtNumbers->num_rows > 0) {
        $highestArtNumber = 0;
				while($rowArtNumbers = $resultArtNumbers->fetch_assoc()) {
         	// grab the last 8 digits - highest number
          if($highestArtNumber < substr($rowArtNumbers["ArtNumber"], -8)){
           $highestArtNumber = intval(substr($rowArtNumbers["ArtNumber"], -8));
          }
        }
        }
      //grab prefixes for selected program/tm
      $sqlPrefix = "SELECT DISTINCT ArtNumberPrefix FROM Products WHERE ProgramName = '" . $programName . "' AND TM = '" . $TMname . "'" ;
      $resultPrefix = $conn->query($sqlPrefix);
      // grab prefixes and increment while we're here
      while($rowPrefixes = $resultPrefix->fetch_assoc()){
        $highestArtNumber += 1;
        $newArtNumber = $rowPrefixes["ArtNumberPrefix"] . $highestArtNumber;
        // insert new record for each prefix
        $sql = "INSERT INTO artnumbers (ArtNumber, ProgramName, TM, ArtNumberPrefix, ChargeNumberPrefix, UserName, DateReserved, FigureDescription, SourceArtNumber, WPFileName) VALUES ('" . $newArtNumber . "' ,'" .  $programName . "', '" . $TMname . "', '" . $rowPrefixes["ArtNumberPrefix"] . "' ,'" . substr($highestArtNumber, 0, 3) . "', 'UserName', '" . strval(date("m/d/Y h:i:sa")) . "', '" . $_POST["figDescription"] . "', '" . $_POST["sourceArtNum"] . "', '" . $_POST["wpFileName"] . "')";
          // populate variable with new record and print in variable for use in textarea
        if ($conn->query($sql) === TRUE) {
          $_POST["outputnum"] .= $newArtNumber . ": " . $_POST["figDescription"] . ", " . $_POST["sourceArtNum"] . ", " . $_POST["wpFileName"] . "\n";
        } else {
          echo "<div>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
                // insert new record for each prefix in history table
        $sql2 = "INSERT INTO artnumberhistory (ArtNumber, ActionDate, ActionUserName, PacketNumber, Action) VALUES ('" . $newArtNumber . "', '" . strval(date("m/d/Y h:i:sa")) . "', 'UserName', 'Not Submitted Yet', 'Created')";

        if ($conn->query($sql2) === TRUE) {
          $_POST["outputnum"] .= "History Logged" . "\n";
        } else {
          echo "<div>Error: " . $sql2 . "<br>" . $conn->error . "</div>";
        }

      }
      $conn->close();
    }
  }

?>

<!DOCTYPE html>
<?php include('templates/header.php');?>
<!-- header template includes head tags , style tags & starting body tag -->

<h2 class="main">Get Art Numbers</h2>

<form class="core" action="./getARtNumbers.php" method="POST">
<div class="row">
  <div class="column">
  <label for="programs">Select a TM:</label>
  <select name="program" id="programs">
 <?php 
	if ($resultProgram->num_rows > 0) {
					// output data of each row .. 
					echo "<option value=''>Select One</option>";
					while($rowProgram = $resultProgram->fetch_assoc()) {
						?>
					<option value="<?php echo $rowProgram["ProgramName"] . ": " . $rowProgram["TM"];?>"
						<?php if(!empty($programTmName) && $programTmName == $rowProgram["ProgramName"] . ": " . $rowProgram["TM"]){
            echo "selected='selected'";
            $selectedProgram = $rowProgram["ProgramName"];
            $selectedTM = $rowProgram["TM"];
            }
          ?> >
					<?php echo $rowProgram["ProgramName"] . ": " . $rowProgram["TM"];?>
					</option>
				<?php
				}
	} else {
					echo "0 results";
	}
	?>
  </select>
  <br>
  <label for="wpFileName">WP File Name</label>
  <input type="text" name="wpFileName" id="wpFileName" value="<?php echo isset($_POST['wpFileName']) ? $_POST['wpFileName'] : '' ?>"><br>
  <label for="figDescription">Figure Description</label>
  <input type="text" name="figDescription" id="figDescription" value="<?php echo isset($_POST['figDescription']) ? $_POST['figDescription'] : '' ?>"><br>
  <label for="sourceArtNum">Source Art Number</label>
  <input type="text" name="sourceArtNum" id="sourceArtNum" value="<?php echo isset($_POST['sourceArtNum']) ? $_POST['sourceArtNum'] : '' ?>"><br>

  <input type="submit" name="insertNewNumber" value="Get Number">
  <br>
</div>
<div class="column">
  <label for="outputnum">Your Art Number(s)</label><br>
  <textarea readonly="true" name="outputnum" id="outputnum" cols="60" rows="20">
<?php if(isset($_POST['outputnum'])){echo $_POST['outputnum'];
}
?>
</textarea>
</div>
</div>
</form>
<!-- footer template includes closing body tag -->
<?php include('templates/footer.php'); ?>

</html>