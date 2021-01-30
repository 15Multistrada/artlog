<?php
session_start();

$packetuid = $_SESSION["packetUID"];
include('config/db_connect.php');
$sql = "SELECT * FROM artnumbers WHERE PacketUID = '" . $packetuid . "'";
$resultArtfromuid = $conn->query($sql);
$conn->close();


include('config/db_connect.php');
$sql = "SELECT * FROM artnumbers WHERE PacketUID = '" . $packetuid . "'";
$resultsForTbs = $conn->query($sql);
$conn->close();

while ($row = $resultsForTbs ->fetch_assoc()) {
    $programName = $row["ProgramName"];
    $tmName = $row["TM"];
    $userName = $row["UserName"];
    $dateSubmitted = substr($row["DateSubmitted"],0,strpos($row["DateSubmitted"]," "));
    $submttedBy = $row["SubmittedBy"];
    $packetNumber = $row["PacketNumber"];
    $chargeNumPrefix = $row["ChargeNumberPrefix"];
    $IllustratorName = $row["IllustratorName"];
    $BinderNumber = $row["BinderNumber"];
    $ToWriterProvDate = substr($row["ToWriterProvDate"],0,strpos($row["ToWriterProvDate"]," "));
    $TiffdSheetToProdDate = substr($row["TiffdSheetToProdDate"],0,strpos($row["TiffdSheetToProdDate"]," "));
    $CompleteDate = substr($row["CompleteDate"],0,strpos($row["CompleteDate"]," "));
    $ApprovalDate = substr($row["ApprovalDate"],0,strpos($row["ApprovalDate"]," "));
}

?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="./css/plain.css">
    <title>XMCO Tools</title>
 </head>
</body>
<h3 class="main">Art Submittal Form (<?php echo $programName . ": ". $tmName;?>)</h3>

 <form class="core-boxed">
    <div class="row">
     <div class="column">
        <label for="submttedBy"><b>User Name: </b><?php echo $submttedBy; ?></label>
    </div>
    <div class="column">
        <label for="chargeNumPrefix"><b>Charge Number: </b><?php echo $chargeNumPrefix; ?></label>
    </div>
    <div class="column">
        <label for="ToWriterProvDate"><b>Writer/Prov Review: </b><?php echo $ToWriterProvDate; ?></label>  
    </div>
</div>
<div class="row">
     <div class="column">
     <label for="dateSubmitted"><b>Date Submitted: </b><?php echo $dateSubmitted; ?></label>
     </div>
     <div class="column">
        <label for="tmName"><b>TM Name: </b><?php echo $tmName; ?></label>
     </div>
     <div class="column">
     <label for="TiffdSheetToProdDate"><b>Art TIFFd: </b><?php echo $TiffdSheetToProdDate; ?></label>
     </div>
</div>
<div class="row">
    <div class="column">
        <label for="packetuid"><b>Packet UID: </b><?php echo $packetuid; ?></label>
    </div>
     <div class="column">
        <label for="packetNumber"><b>Packet Number: </b><?php echo $packetNumber; ?></label>
    </div>
    <div class="column">
        <label for="CompleteDate"><b>Delivered: </b><?php echo $CompleteDate; ?></label>  
    </div>
</div>
<div class="row">
    <div class="column">
        <label for="illustratorName"><b>Illustrator: </b><?php echo $IllustratorName; ?></label>
    </div>
    <div class="column">
        <label for="BinderNumber"><b>Binder Number: </b><?php echo $BinderNumber; ?></label>
    </div>
    <div class="column">
    <label for="ApprovalDate"><b>Writer/Prov OK to TIFF: </b><?php echo $ApprovalDate; ?></b></label>
    </div>
</div>

</form>
            <table id="Table1">
            <thead>
            <tr>
                    <th>No.</th>
                    <th>Art Number</th>
                    <th>Source Art Number</th>
                    <th>New Or Updated</th>
                    <th>WP File Name</th>
                    <th>Status</th>
            </tr>
            </thead>
            <tbody>
    <?php if ($resultArtfromuid->num_rows > 0) {
            $x=0;
                    while($row2 = $resultArtfromuid->fetch_assoc()) {
                        $x +=1;
                        echo "<tr><td>" . $x . "</td>";
                        echo "<td>" . $row2["ArtNumber"] . "</td>"; 
                        echo "<td>" . $row2["SourceArtNumber"] . "</td>";
                        echo "<td>" . $row2["NewOrUpdated"] . "</td>";
                        echo "<td>" . $row2["WPFileName"] . "</td>";
                        echo "<td>OK / Correction <td></tr>";
                    }
                }      
    ?>
            </tbody>
            </table>

<br>
<div class="core">
<a href="./getArtNumbers.php">Get Art Numbers</a>
<a href="./submitArt.php">Submittals</a>
<a href="./artHistory.php">History</a>
</div>
<!-- footer template includes closing body tag -->
<?php include('templates/footer.php'); ?>
</html>