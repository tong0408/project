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
					
	//header("Location: enter_diet_platform.php");
?>