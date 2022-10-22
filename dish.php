<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	#假如$_SESSION['userID']為空值表示沒有登入
	if ($_SESSION['userID'] == null) {
	    echo "<script>alert('請先登入唷！')</script>";
	    echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	} else {
	
	    $userID=$_SESSION['userID'];
	    $query = "SELECT * FROM `user` WHERE `userID`='$userID'";
	    $result = $link->query($query);
	
	    #獲取現在登入者的帳號密碼
	    foreach ($result as $row) {
	        $userID = $row["userid"];
	        $Name = $row["name"];
	    }
	}
	date_default_timezone_set('Asia/Taipei');
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
<style>
	body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
	
	body, html {
		height: 100%;
		line-height: 1.8;
	}
	input[type=text]{margin:10px 0px 10px 0px; width:50%;}
	button{margin:0px 10px 0px 10px;}
	.form1{
		background-color: #FFD79B;
		border-radius:30px;
		width:70%;
		margin:auto;
		padding:30px 0px;
		text-align:center;
	}
	
	.btn{
		border-radius:10px;
		background-color: #FFF;
	}
	
	.btn:hover{
		background-color: #FFB03B;
	}
	td{height:50px;}
</style>
</head>
	
</script>

<body>
<a href="enter_diet_platform.php"><button class="btn1 return">返回</button></a>
    <div class="form1">
		<form method="POST" action="search_dish_modify.php">
			<?PHP
			$dishid = isset($_GET["id"]) ? $_GET["id"] : null; //要改的蔡id
			$_SESSION['dishID']=$dishid;
			//取得菜名
			$query = "SELECT * FROM dish WHERE `ID`=$dishid";
			$result = $link->query($query);
			
			//取得所有需要的資料
			foreach ($result as $row){
				$dish_Name=$row['dishName'];
				$_SESSION['dish_Name']=$dish_Name;
			}
			
			$query = "SELECT count(ID) FROM t_user_histroy_modify WHERE `UID`=$userID";
			$result = $link->query($query);
			$count = $result->fetchColumn();

			//先查看t_user_histroy_modify有沒有資料
			//如果沒有資料
			if($count==0){
				$query = "SELECT * FROM dish WHERE `ID`=$dishid";
				$result = $link->query($query);
	
				//取得所有需要的資料
				foreach ($result as $row){
	
					$count = $link->prepare("SELECT * FROM recipe WHERE `dishID`=$dishid");   
					$count->execute();   
					$count_rows=$count->rowCount(); 
	
					//從菜ID取得菜名稱<使用>
					$query = "SELECT * FROM dish where ID=$dishid";
					$re = $link->query($query);

					foreach ($re as $r){
						$dish_Name=$r['dishName'];
						$_SESSION['dish_Name']=$dish_Name;
					}
					//菜名
					echo '<h5 class="modal-title" id="exampleModalLabel">'.$dish_Name.'</h5>
					<div class="modal-body" style="text-align:center;">
					<table style="margin:auto; width:300px;">
					<tr><td>食材</td><td>份量</td></tr>';
					//取得菜ID使用的食材ID
					$query = "SELECT * FROM recipe where dishID=$dishid";
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
								//食材
								echo '<tr><td style="height:50px; text-align:left;"><input type="checkbox" id="'.$iID_Name.'" name="ingredients[]" style="margin-right:20px" value="' . $iID_Name . '" checked>'.$iID_Name.'</td>'.
								//份量
								'<td style="height:50px;"><input type="number" step="0.1" min="0.1" max="1000.0" value="'.$iID_portion.'" name="new_portion[]">克</td></tr>';									
						}						
					}
				}
			}//if 有資料
			else{
				$a=0;
				$query = "SELECT * FROM t_user_histroy_modify WHERE `UID`=$userID";
				$result = $link->query($query);
				//菜名
				echo '<h5 class="modal-title" id="exampleModalLabel">'.$dish_Name.'</h5>
				<div class="modal-body" style="text-align:center;">
				<table style="margin:auto; width:300px;">
				<tr><td>食材</td><td>份量</td></tr>';

				foreach ($result as $roww){
					$sqldishid=$roww['dishID'];
					$iID=$roww['iID'];
					$portion=$roww['portion'];
					//dishid相符合
					if($sqldishid==$dishid){
						//從食材ID取得食材類別ID和食材名稱<使用>
						$query = "SELECT * FROM ingredients where iID='$iID'";
						$re = $link->query($query);
						foreach ($re as $r){
							$iID_Name=$r['name'];
							//食材
							echo '<tr><td style="height:50px; text-align:left;"><input type="checkbox" id="'.$iID_Name.'" name="ingredients[]" style="margin-right:20px" value="' . $iID_Name . '" checked>'.$iID_Name.'</td>'.
							//份量
							'<td style="height:50px;"><input type="number" step="0.1" min="0.1" max="1000.0" value="'.$portion.'" name="new_portion[]">克</td></tr>';									
						}
						$a=1;				
					}
				}
				if($a==0){
					$query = "SELECT * FROM recipe WHERE `dishID`=$dishid";
					$result = $link->query($query);
					
					//取得所有需要的資料
					foreach ($result as $r){
						$dish_iID=$r['iID'];
						$iID_portion=$r['portion'];
						//從食材ID取得食材類別ID和食材名稱<使用>
						$query = "SELECT * FROM ingredients where iID='$dish_iID'";
						$re = $link->query($query);
						foreach ($re as $r){
							$iID_NID=$r['NID'];
							$iID_Name=$r['name'];
								//食材
								echo '<tr><td style="height:50px; text-align:left;"><input type="checkbox" id="'.$iID_Name.'" name="ingredients[]" style="margin-right:20px" value="' . $iID_Name . '" checked>'.$iID_Name.'</td>'.
								//份量
								'<td style="height:50px;"><input type="number" step="0.1" min="0.1" max="1000.0" value="'.$iID_portion.'" name="new_portion[]">克</td></tr>';									
						}					
					}
				}
				
			}
			?>
			</table>
			<!--<input type="button" value="+" id="add_row" class="btn" style="position:absolute; right:25%;"/><br>-->
			<input type="submit" class="btn" id="newbutton" value="新增" style="margin:10px 10px 10px 10px; width:70%;">
		</form>

		<div id="template" style="display:none;">
			<table>
				<tr class="row_data">
					<td><input list="brow" name="ingredients[]" id="idata"><datalist id="brow">
					<?php
						$query = "SELECT * FROM `ingredients` ";
						$result = $link->query($query);
						
						foreach($result as $row){
							$iID=$row["iID"];
							$Name=$row["name"];
							$Nid=$row["NID"];
							
							echo '<option value="'.$Name.'" id="'.$iID.'">';
							echo $Nid;
						}
					?>
					</datalist></td>
					<td><input type="number" step="0.1" min="0.1" max="1000.0" name="portion[]" id="new_portion" required></td>
				</tr>
			</table>
		</div>
	</div>
</body>
<script> 
	var TheSelectedValue = function() {
		var val = document.getElementById('idata').value;
		var text = $('#brow').find('option[value="' + val + '"]').attr('id');
		document.write(text);
	 }
	$('body').on('click','#add_row',function(){
        $('#template').find('.row_data').clone().appendTo($('#append_position'));
    });
</script> 
</html>