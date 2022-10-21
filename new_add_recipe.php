<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$new_dish = isset($_POST["new_dish"]) ? $_POST["new_dish"] : null; //新增菜名
    $new_ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : null; //新增食材名稱
    $new_portion = isset($_POST["portion"]) ? $_POST["portion"] : null; //新增份量
    $userid= $_SESSION['userID'];

    for($i=0;$i<count($new_ingredients);$i++){
		if($new_ingredients[$i]!=null){
			$query = "INSERT INTO `t_newrecipe`(`UID`, `dishName`, `ingredients`, `portion`) 
			VALUES('$userid','$new_dish','$new_ingredients[$i]','$new_portion[$i]')";
			$count = $link->exec($query);
		}
    }
    
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
<a href="new_recipe.php"><button class="btn1 return">返回</button></a>
    <div class="form1">
		<form method="POST" action="new_add_recipephp.php">
				<table style="margin:auto;" id="append_position">
				<tr><td>分類</td><td>食材</td><td>份量(克)</td><td>熱量(克)</td><td>蛋白質(克)</td><td>脂肪(克)</td><td>碳水化合物/醣類(克)</td><td>糖類(克)</td><td>鈉(克)</td></tr>
				<tr class="row_data">
                <td><input list="brow" name="new_nutrient[]" id="ndata"><datalist id="brow">
					<?php
						$query = "SELECT * FROM `nutrient` ";
						$result = $link->query($query);
						
						foreach($result as $row){
							$NID=$row["NID"];
							$category=$row["category"];
							
							echo '<option value="'.$category.'" id="'.$NID.'">';
						}
					?>
					</datalist></td>
				<td><input type="text" name="new_ingredients[]" maxlength="10" required></td>
				<td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="cal[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="protein[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="fat[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="carbohydrate[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="totalsugar[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="sodium[]" maxlength="10" required></td></tr>
				</table>
				<input type="button" value="+" id="add_row" class="btn" style="position:absolute; right:25%;"/><br>
				<input type="submit" class="btn" value="新增" >
		</form>	
		<div id="template" style="display:none;">
			<table>
				<tr class="row_data">
                <td><input list="brow" name="new_nutrient[]" id="ndata"><datalist id="brow">
					<?php
						$query = "SELECT * FROM `nutrient` ";
						$result = $link->query($query);
						
						foreach($result as $row){
							$NID=$row["NID"];
							$category=$row["category"];
							
							echo '<option value="'.$category.'" id="'.$NID.'">';
						}
					?>
					</datalist></td>
					<td><input type="text" name="new_ingredients[]" maxlength="10" required></td>
					<td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]" required></td>
                    <td><input type="number" step="0.1" min="0.1" max="1000.0" name="cal[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="protein[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="fat[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="carbohydrate[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="totalsugar[]" maxlength="10" required></td>
                <td><input type="number" step="0.1" min="0.1" max="1000.0" name="sodium[]" maxlength="10" required></td></tr>
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
