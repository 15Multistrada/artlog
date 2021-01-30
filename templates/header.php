 <head>
	  <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./css/style.css">
 
<title>XMCO Tools</title>
   </head>
   <body>

	<!-- Top navbar -->
<div class="navbar">
        <a href="./index.php">XMCO Tools</a>
        <?php if(isset($_POST["program"])){
          echo "<a>" . $_POST["program"] . "</a>";
        } else {
          echo "<a>Please select a program.</a>";
        }	?>
</div>

<!-- Side navigation -->
<div class="sidenav">
  <a href="./getartnumbers.php">Get Art Numbers</a>
  <a href="./submitArt.php">Submit Art</a>
  <a href="./artapprovals.php">Approve Art</a>
  <a href="./artHistory.php">History</a>
  <a href="#">Writer Status</a>
</div> 
