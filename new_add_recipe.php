<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$new_dish = isset($_POST["new_dish"]) ? $_POST["new_dish"] : null; //新增菜名
    $new_ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : null; //新增食材名稱
    $new_portion = isset($_POST["portion"]) ? $_POST["portion"] : null; //新增份量
    $userid= $_SESSION['userID'];
	$q=0;
	if($new_dish==null){
		$query = "SELECT count(ID) FROM `t_newrecipe` WHERE `UID`='$userid'";
		$result = $link->query($query);
		$count = $result->fetchColumn();
        
		if($count==0){
			echo "<script>alert('請輸入菜名！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='new_recipe.php'>";
		}else{
			$query = "SELECT * FROM `t_newrecipe` WHERE `UID`='$userid'";
			$result = $link->query($query);

			foreach($result as $row){
				if($row['dishName']==null){
					echo "<script>alert('請輸入菜名！')</script>";
					echo "<meta http-equiv=REFRESH CONTENT=0;url='new_recipe.php'>";
				}
			}
		}
        
		
	}else{
		//dish如果已經有了 擋回去
		$query = "SELECT count(ID) FROM `dish` WHERE `dishName`='$new_dish'";
		$res = $link->query($query);
		$c = $res->fetchColumn();
		
		if($c!=0){
			echo "<script>alert('此道菜已經存在囉')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='enter_diet_platform.php'>";
		}else{
			$query = "SELECT count(ID) FROM `t_newrecipe` WHERE `UID`='$userid'";
			$result = $link->query($query);
			$count = $result->fetchColumn();
			
			if($count==0){
				if(count($new_ingredients)==1){
					
					$query = "INSERT INTO `t_newrecipe`(`UID`, `dishName`, `ingredients`, `portion`) 
					VALUES('$userid','$new_dish','',0)";
					$cou = $link->exec($query);
				}else{
					for($i=0;$i<count($new_ingredients);$i++){
						
						$query = "INSERT INTO `t_newrecipe`(`UID`, `dishName`, `ingredients`, `portion`) 
						VALUES('$userid','$new_dish','$new_ingredients[$i]','$new_portion[$i]')";
						$cou = $link->exec($query);
					}
				}
					
			}else{
				$query = "SELECT * FROM `t_newrecipe` WHERE `UID`='$userid'";
				$result = $link->query($query);

				foreach($result as $row){
					$dN=$row['dishName'];
					if($dN!=$new_dish){
						$query = "UPDATE `t_newrecipe` SET `dishName`='$new_dish' WHERE `UID`='$userid'";
						$cou = $link->exec($query);
					}
				}
				
				if(count($new_ingredients)!=0){
					
					for($i=0;$i<count($new_ingredients);$i++){
						$query = "SELECT * FROM `t_newrecipe` WHERE `UID`='$userid' and `dishName`='$new_dish'";
						$result = $link->query($query);

						foreach($result as $row){
							$t_ingredients=$row["ingredients"];

							//echo count($new_ingredients);
							if($new_ingredients[$i]!=null){
									
								if($row["portion"]==0){
									
									$query = "UPDATE `t_newrecipe` SET `ingredients`='$new_ingredients[$i]',`portion`='$new_portion[$i]' WHERE `dishName`='$new_dish' and `UID`='$userid'";
									$count = $link->exec($query);
									echo $new_ingredients[$i];
									$q=1;

								}else{
									if($t_ingredients==$new_ingredients[$i]){
										$q=1;
										break;
									}else{
										$q=2;
									}
								}
							}

							
						}
						if($q==2){
							echo $i;
							$query = "INSERT INTO `t_newrecipe`(`UID`, `dishName`, `ingredients`, `portion`) 
							VALUES('$userid','$new_dish','$new_ingredients[$i]','$new_portion[$i]')";
							$cou = $link->exec($query);
						}
					}
				}
			}
		}
	}
    
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
<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<style>
	input[type=text]{margin:10px 0px 10px 0px; width:100%;}
	input[type=number]{margin:10px 0px 10px 0px; width:50%;}
	select{margin:10px 0px 10px 0px; width:80%; height:35px;}	
	td{height:50px; width:150px;}
</style>
</head>
<body>
<?php include("header.php"); ?>
<a href="new_recipe.php"><button class="btn1 return">返回</button></a><br>
    <div class="form1" style="width:85%;">
		<form method="POST" action="new_add_recipephp.php">
				<table style="margin:auto;" id="append_position">
				<tr><td>分類</td><td>食材</td><td>份量(克)</td><td>熱量(克)</td><td>蛋白質(克)</td><td>脂肪(克)</td><td>碳水化合物/<br>醣類(克)</td><td>糖類(克)</td><td>鈉(毫克)</td></tr>
				<tr class="row_data">
                <td><input list="brow" name="new_nutrient[]" id="ndata" style="width:80%;" required><datalist id="brow">
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
				</table><br>
				<input type="button" value="+" id="add_row" class="btn" style="position:absolute; right:10%;"/><br>
				<input type="submit" class="btn" value="新增" >
		</form>
		<div id="template" style="display:none;">
			<table>
				<tr class="row_data">
                <td><input list="brow" name="new_nutrient[]" id="ndata" style="width:80%;"><datalist id="brow">
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
					<td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]" ></td>
                    <td><input type="number" step="0.1" min="0.1" max="1000.0" name="cal[]" maxlength="10" ></td>
                	<td><input type="number" step="0.1" min="0.1" max="1000.0" name="protein[]" maxlength="10" ></td>
                	<td><input type="number" step="0.1" min="0.1" max="1000.0" name="fat[]" maxlength="10" required></td>
                	<td><input type="number" step="0.1" min="0.1" max="1000.0" name="carbohydrate[]" maxlength="10" ></td>
                	<td><input type="number" step="0.1" min="0.1" max="1000.0" name="totalsugar[]" maxlength="10" ></td>
                	<td><input type="number" step="0.1" min="0.1" max="1000.0" name="sodium[]" maxlength="10" ></td>
					<td style="width:20px;"><input type="button" value="-" id="remove_row" class="btn"/></td>
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
	$('body').on('click','#remove_row',function(){
        $(this).parent().parent().remove();
    });
</script> 
</html>
