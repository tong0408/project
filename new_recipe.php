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
<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<style>
	input[type=text]{margin:10px 0px 10px 0px; width:30%;}
	input[type=number]{margin:10px 0px 10px 0px; width:80%;}
	select{margin:10px 0px 10px 0px; width:80%; height:35px;}	
	td{height:50px; width:150px;}
</style>
</head>
<body>
<?php include("header.php"); ?>
<a href="enter_diet_platform.php"><button class="btn1 return">返回</button></a>
    <div class="form1">
		<form method="POST" action="enter_recipe.php">				
				菜名：<input type="text" name="new_dish" maxlength="10" required>
				<table style="margin:auto;" id="append_position">
				<tr><td>類別</td><td>食材</td><td>份量(克)</td></tr>
				<tr class="row_data">
				<td><select name="new_category[]" required>
					<option value="全榖雜糧類">全榖雜糧類</option>
					<option value="蔬菜類">蔬菜類</option>
					<option value="豆魚蛋肉類">豆魚蛋肉類</option>
					<option value="乳品類">乳品類</option>
					<option value="水果類">水果類</option>
					<option value="油脂與堅果種子類">油脂與堅果種子類</option>
					<option value="調味料">調味料</option>
				</select></td>
				<td><input list="brow" name="new_ingredients[]" ><datalist id="brow">
					<?php
						$query = "SELECT * FROM `ingredients` ";
						$result = $link->query($query);
						
						foreach($result as $row){
							$iID=$row["iID"];
							$Name=$row["name"];
							
							echo '<option value="'.$Name.'">';
						}
					?>
					</datalist></td>
				<td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]" required></td></tr>
				</table>
				<input type="button" value="+" id="add_row" class="btn" style="position:absolute; right:25%;"/><br>
				<input type="submit" class="btn" value="新增" >
		</form>	
		<div id="template" style="display:none;">
			<table>
				<tr class="row_data">
					<td><select name="new_category[]">
					<option value="全榖雜糧類">全榖雜糧類</option>
					<option value="蔬菜類">蔬菜類</option>
					<option value="豆魚蛋肉類">豆魚蛋肉類</option>
					<option value="乳品類">乳品類</option>
					<option value="水果類">水果類</option>
					<option value="油脂與堅果種子類">油脂與堅果種子類</option>
					<option value="調味料">調味料</option>
					</select></td>
					<td><input list="brow" name="new_ingredients[]" ><datalist id="brow">
					<?php
						$query = "SELECT * FROM `ingredients` ";
						$result = $link->query($query);
						
						foreach($result as $row){
							$iID=$row["iID"];
							$Name=$row["name"];
							
							echo '<option value="'.$Name.'">';
						}
					?>
					</datalist></td>
					<td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>
				</tr>
			</table>
		</div>
	</div>	
  
</body>
<script>  
$('body').on('click','#add_row',function(){
        $('#template').find('.row_data').clone().appendTo($('#append_position'));
    });   
</script> 
</html>
