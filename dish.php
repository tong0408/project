<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//這個要等整合才會有效
	$userid= $_SESSION['userID'];
	date_default_timezone_set('Asia/Taipei');
?>
<html>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="image/logo.png" rel="icon" type="image/x-icon" />

 

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/mine.css">
<style>
	.btn{
		border-radius:10px;
		background-color: #FFB03B;
	}
	
	.btn:hover{
		background-color: #FFB03B;
	}
</style>
</head>
 
<body>
 <!-- Button trigger modal -->
<span  class="btn" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</span>
 
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        
        <?PHP

				
				$query = "SELECT * FROM dish WHERE `ID`=1";
				$result = $link->query($query);
				
				//取得所有需要的資料
				foreach ($result as $row){
					
					
					$count = $link->prepare("SELECT * FROM recipe WHERE `dishID`=1");   
					$count->execute();   
					$count_rows=$count->rowCount(); 
					
					
					
					//從菜ID取得菜名稱<使用>
					$query = "SELECT * FROM dish where ID=1";
					$re = $link->query($query);
					foreach ($re as $r){
						$dish_Name=$r['dishName'];
					}
					//菜名
					echo '<h5 class="modal-title" id="exampleModalLabel">'.$dish_Name.'</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							</div>
							<div class="modal-body" style="text-align:center;">
					<table style="margin:auto; width:300px;">';
					//取得菜ID使用的食材ID
					$query = "SELECT * FROM recipe where dishID=1";
					$re = $link->query($query);
					foreach ($re as $r){
						$dish_iID=$r['iID'];
						$iID_portion=$r['portion'];
						//從食材ID取得食材類別ID和食材名稱<使用>
						$query = "SELECT * FROM ingredients where iID='$dish_iID'";
						$re = $link->query($query);
						foreach ($re as $r){
							$iID_NID=$r['NID'];
							$iID_Name=$r['name'];
							//從食材類別ID取得食材類別名稱<使用>
							$query = "SELECT * FROM nutrient where NID='$iID_NID'";
							$re = $link->query($query);
							foreach ($re as $r){
									//食材
									echo '<tr><td style="height:50px; text-align:left;"><input type="checkbox" id="'.$iID_Name.'" name="dish[]" style="margin-right:20px" value="' . $iID_Name . '" checked>'.$iID_Name.'</td>'.
									//份量
									'<td style="height:50px;"><input type="number" step="0.1" min="0.1" max="1000.0" value="'.$iID_portion.'" name="new_portion[]">克</td></tr>';									
								}							
							}						
						}
				}
					
			
			?>
	  </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</body>

</html>