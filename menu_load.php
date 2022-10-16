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
<link href="image/logo.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="css/mine.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

</head>
<body>

	<table style="color:#FFF; font-size:20px; width:300px;">
        <tr><td>食材</td><td>克數</td></tr>
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
	</table>

</body>
</html>