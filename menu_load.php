<!DOCTYPE html>
<?php
session_start();
include("configure.php");
$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
?>
<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="image/icon.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="css/mine.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

</head>
<style>
    .ta1 {        
		width: 350px;
		text-align: center;
		color:#FFF;
		font-size:20px;
    }   
    
	.ta1 tbody {
		display: block;
		height: 450px;
		overflow-y: scroll;
    }
	 
	.ta1 tr {
		width: 100%;
		display: table;
    }
	
	thead{
		border:1px solid #FFF;
		background: #FFF;
		color:#FFB03B;
	}
	
	th{
		text-align: center;   
	}
	
	tbody {        
		scrollbar-width: 0.5em; 
    }
     
    tbody::-webkit-scrollbar {
		width: 0.5em;
    }	
	
	tbody::-webkit-scrollbar-track {
		box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	}
		
	tbody::-webkit-scrollbar-thumb {
		background-color: #FFD79B;
	}
</style>
<body>

	<table class="ta1">
        <thead><th>食材</th><th>克數</th></thead>
			<tbody>
			<?PHP
				//判斷使用者是否有輸入
				if (isset($_GET['s'])) {
					$dishid = $_GET['s'];
					//搜尋dishid的食材ID
					$query = "SELECT * FROM recipe where `dishID`='$dishid'";
					$result = $link->query($query);
					
					// 搜尋有資料時顯示搜尋結果
					foreach($result as $row){
						$iID=$row["iID"];
						$portion=$row["portion"];
						//搜尋dishid的食材
						$query = "SELECT * FROM ingredients where `iID`='$iID'";
						$result = $link->query($query);
	
						foreach($result as $r){
							$name=$r["name"];
							echo "<tr>";
							echo '<td>'.$name.'</td><td>'.$portion.'</td>';									
							echo "</tr>";
						}
					}
				}
			?>
			</tbody>
	</table>

</body>
</html>