<?php
session_start();
date_default_timezone_set("America/New_York");
include('config/db_connect.php');
$sql = "SELECT DISTINCT ProgramName, TM FROM products WHERE ActiveProgram='Yes' ORDER BY ProgramName";
$resultProgram = $conn->query($sql);

$conn->close();

if(isset($_POST["program"])){
    $programandTmName = $_POST["program"];
    $programName = substr($programandTmName,0,stripos($programandTmName,":"));
    $TmName = substr(strstr($programandTmName,":"),2);

    include('config/db_connect.php');
    if (isset($_POST['showallcb'])){
         $sqlartNumbers = "SELECT * FROM artnumbers WHERE ProgramName = '" . $programName . "' AND TM='" . $TmName . "'AND PacketUID IS NOT NULL and ApprovalDate IS NOT NULL";
    }else{
       $sqlartNumbers = "SELECT * FROM artnumbers WHERE ProgramName = '" . $programName . "' AND TM='" . $TmName . "' AND PacketUID IS NULL OR ProgramName = '" . $programName . "' AND TM='" . $TmName . "' AND PacketUID = ''";  
    }
   
    $resultArtNumbers = $conn->query($sqlartNumbers);
    //grab the row count
    $rowcount = mysqli_num_rows($resultArtNumbers);
    $conn->close();
}

// omg finally - write submtted art stuff to DB
if(isset($_POST["confirmed"])){
    // check to make sure art numbers are in textarea.
    if (strlen(trim($_POST['artConfirmed']))){
        if($_POST["devType"] =="") {
             echo"<div class='main'> Please select a Phase. </div>";
        } else {
        echo"<div class='main'> confirmed " . $_POST["artConfirmed"] . "</div>";
        
        $artNumbersToSubmit = explode(',', $_POST["artConfirmed"]);
        $artNumberCount = count($artNumbersToSubmit);
        $programName = $_POST["programName"];
        $TmName = $_POST["TmName"];
    
        //calculate the art packet UID
        include('config/db_connect.php');
        $sql = "SELECT PacketUID FROM artnumbers";
        $resultPacketUID = $conn->query($sql);
        $highestUID = 0;
        while ($row = $resultPacketUID ->fetch_assoc()) {
            if($highestUID < $row["PacketUID"]){
                $highestUID = $row["PacketUID"];
            }
        }
        $newPacketUID = $highestUID + 1;
        echo"<div class='main'> confirmed " . $newPacketUID . "</div>";
        $conn->close();
    
        //assemble the packet number
        include('config/db_connect.php');
        $sql = "SELECT * FROM artnumbers WHERE ProgramName='" . $programName . "' AND TM='" . $TmName . "' AND PacketNumber IS NOT NULL AND PacketNumber <> 'InitialNumber1'";
        $resultPacketNum = $conn->query($sql);
        $highestPacketNum = 0;
        while ($row = $resultPacketNum ->fetch_assoc()) {
            if($highestPacketNum < substr($row["PacketNumber"], strripos($row["PacketNumber"],"-")+1)) {
                $highestPacketNum = substr($row["PacketNumber"], strripos($row["PacketNumber"],"-")+1);
            }
        }
        $conn->close();
        $highestPacketNum += 1;
    
        //pull art prefix from one of the art numbers
        $artNumPrefix = substr($artNumbersToSubmit[0],0,-8);

        $newPacketNumber = $_POST["devType"] . "-" . $artNumPrefix . "-" . $highestPacketNum;
        echo"<div class='main'> confirmed " . $newPacketNumber . "</div>";
        
  

	    // -- Get New/Update info and DO The WRITTING HERE***
	    for($x = 0; $x < $artNumberCount; $x++){
            // Figure out if this is a new submission or updated submission
            include('config/db_connect.php');
            $sql = "SELECT * FROM artnumbers WHERE ArtNumber = '". $artNumbersToSubmit[$x] . "'";
            $resultNewUpdate = $conn->query($sql);
            while ($row = $resultNewUpdate ->fetch_assoc()) {

                if($row["NewOrUpdated"]=== "New") {
                    $newUpdate = "Update";
                }else{
                    $newUpdate = "New";
                }
            }
            $conn->close();


		    include('config/db_connect.php');
		    $sql = "UPDATE artnumbers SET DateSubmitted = '" . date('m/d/Y h:i:sa') . "', SubmittedBy = 'Michael Althoff', PacketNumber = '". $newPacketNumber . "', PacketUID = '" . $newPacketUID . "', Approved = '0', ApprovedBy = NULL, ApprovalDate = NULL, NewOrUpdated = '" . $newUpdate . "' WHERE ArtNumber = '". $artNumbersToSubmit[$x] . "'";
		
		    // execute query
		    // Still need to get username 
		    if ($conn->query($sql) === TRUE) {
			    echo "<div class='main'>Record updated successfully</div>";
		    } else {
			    echo "Error updating record: " . $conn->error;
		    }

		            // insert new record for each artnumber in history table
            $sql2 = "INSERT INTO artnumberhistory (ArtNumber, ActionDate, ActionUserName, PacketNumber, PacketUID, Action) VALUES ('" . $artNumbersToSubmit[$x] . "', '" . strval(date("m/d/Y h:i:sa")) . "', 'Michael Althoff', '" . $newPacketNumber . "', '" . $newPacketUID . "', 'Submitted')";

            if ($conn->query($sql2) === TRUE) {
                echo "<div class='main'>History Logged</div>";
            } else {
            echo "<div class='main'>Error: " . $sql2 . "<br>" . $conn->error . "</div>";
            }
		  
	    $conn->close();
        }
            // Add info to history table
            // Send new packet UID to artsubmittalpage

        $_SESSION["packetUID"] = $newPacketUID;

        header('location: ./artSubmittalForm.php');
        }

    }
}
?>

<!DOCTYPE html>
<?php include('templates/header.php');?>
<!-- header template includes head tags , style tags & starting body tag -->
<h2 class="main">Art Submittal</h2>
<form class="core" action="./submitArt.php" method="POST">

 <div class="row">
     <div class="column">
     <input type="checkbox" name="showallcb" <?php if(isset($_POST['showallcb'])) echo "checked='checked'"; ?> >
     <label for="showallcb"> Include previously submitted art </label><br>
  <label for="programs">Choose a Program:</label><br>
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
<form class="core" action="./submitArt.php" method="POST">
<div class="column">
    <label for="devType">Choose a Development Phase</label>
    <select name="devType" id="devType">    
        <option value="">Select One</option>
        <option value="Dev">Dev</option>
        <option value="Val">Val</option>
        <option value="LD">LD</option>
        <option value="NOGO">NOGO</option>
        <option value="Ver">Ver</option>
        <option value="FDEP">FDEP</option>
        <option value="FRC">FRC</option>
    </select><br><br>
    <label for="artconfirmed">Selected Art Numbers</label>
    <textarea name="artConfirmed" id="artConfirmed" rows="5" cols="50" readonly="true"></textarea>
    <textarea class="hideDontTakeUpSpace"name="programName" id="programName" rows="1" cols="50"></textarea>
    <textarea class="hideDontTakeUpSpace"name="TmName" id="TmName" rows="1" cols="50"></textarea>
	<input type="submit" name="confirmed" value="Confirm Selection">
</div>
</div>
</form>

<!-- <input class="core" type="button" value="Get Selected" onclick="GetSelected()" /> -->

<?php if(isset($_POST["program"])){
?>
   <table class="core" id="Table1">
   <caption><?php echo $programandTmName . ": (". $rowcount . " Art Numbers)";?></caption>
   <thead>
   <tr>
   <th>Select</th>
   <th>Art Number</th>
   <th>Program Name</th>
   <th>TM</th>
   <th>User Name</th>
   <th>Date Reserved</th>
   <th>Figure Description</th>
   <th>WP File Name</th>
   </tr>
   </thead>
   <tbody>
<?php if ($resultArtNumbers->num_rows > 0) {
// output data of each row
while($rowArtNumbers = $resultArtNumbers->fetch_assoc()) {

   echo "<tr><td><input type='checkbox' value='on' onclick='GetSelected()'>";
   echo "<td>" . $rowArtNumbers["ArtNumber"] . "</td>";
   echo "<td>" . $rowArtNumbers["ProgramName"] . "</td>";
   echo "<td>" . $rowArtNumbers["TM"] . "</td>";
   echo "<td>" . $rowArtNumbers["UserName"] . "</td>";
   echo "<td>" . $rowArtNumbers["DateReserved"] . "</td>";
   echo "<td>" . $rowArtNumbers["FigureDescription"] . "</td>";
   echo "<td>" . $rowArtNumbers["WPFileName"] . "</td></tr>";
}
}
?>
</tbody>
</table>

<script type="text/javascript">
    function GetSelected() {
        //Reference the Table.
        var grid = document.getElementById("Table1");
 
        //Reference the CheckBoxes in Table.
        var checkBoxes = grid.getElementsByTagName("INPUT");
        var message = [];
        var $programName , $TmName;
 
        //Loop through the CheckBoxes.
        for (var i = 0; i < checkBoxes.length; i++) {
            if (checkBoxes[i].checked) {
                var row = checkBoxes[i].parentNode.parentNode;
                message.push(row.cells[1].innerHTML);
                $programName = (row.cells[2].innerHTML);
                $TmName = (row.cells[3].innerHTML);
            }
        }
 
        //Display selected Row data in textareas above.
		document.getElementById("artConfirmed").value = message;
		document.getElementById("programName").value = $programName;
		document.getElementById("TmName").value = $TmName;
    }
</script>
<?php } ?>

<!-- footer template includes closing body tag -->
<?php include('templates/footer.php'); ?>

</html>
