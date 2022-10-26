<?php

    #抓取資料庫目前吃的六大類數值
    
    #預設起始值為吃0份
    $now_category=array(0,0,0,0,0,0);
    $now_cal=0;
    $now_glyco=0;
	$now_fat=0;
	$now_protein=0;
    $now_sodium=0;

    #抓取資料庫紀錄
    #history -> dishID portion
    #recipe -> history.dishID可找到食材iID及一份的portion (*history.portion)
    #ingredients -> 食材iID找到他的營養素(內都是100g為主)
    ###
    #全榖雜糧，每份醣類15g
	#蛋豆魚肉，每份蛋白質7g
	#乳品類，每份蛋白質8g
	#蔬菜類，每份25大卡
	#水果類，每份60大卡
	#油脂與堅果種子類，每份脂肪5g
    ###
    #recipe.portion(*history.portion)*(食材主營養素/100) / 食材主營養素每份數要求 -> 獲得六大類份數
    #ingredients.cal / 100 * recipe.portion(*history.portion)-> 紀錄卡路里
    #ingredients.protien /100 * recipe.portion(*history.portion)-> 記錄蛋白質
    #ingredients.fat /100 * recipe.portion(*history.portion)-> 記錄脂肪
    #ingredients.carbohydrate /100 * recipe.portion(*history.portion)-> 記錄醣類 glyco
    #ingredients.sodium /100 * recipe.portion(*history.portion) ->紀錄鈉含量

    $today = date("Y-m-d");
    echo $today;
    $query = "SELECT * FROM `history` WHERE `UID`='$userID' and `date`='$today'";
    $result = $link->query($query);

    #history
    foreach ($result as $row){
        $history_dishID=$row["dishID"];
        $history_portion=$row["portion"];

        #recipe
        $query = "SELECT * FROM `recipe` WHERE `dishID`='$history_dishID'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recipe_iID=$row["iID"];
            $recipe_portion=$row["portion"];

            #ingredients
            $query = "SELECT * FROM `ingredients` WHERE `iID`='$recipe_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $ingredients_NID=$row["NID"]; #食材的六大類歸屬
                $ingredients_cal=$row["cal"]; #食材的卡路里
                $ingredients_protein=$row["protein"]; #食材的蛋白質
                $ingredients_fat=$row["fat"]; #食材的脂質
                $ingredients_saturatedfat=$row["saturatedfat"]; #食材的飽和脂肪
                $ingredients_glyco=$row["carbohydrate"]; #食材的醣類
                $ingredients_totalsugar=$row["totalsugar"]; #食材的總糖
                $ingredients_sodium=$row["sodium"]; #食材的鈉含量

                #獲取六大類份數
                if($ingredients_NID==1){ #全榖雜糧 醣類/15
                    $now_category[0]=(($recipe_portion*$history_portion)*($ingredients_glyco/100))/15;
                }
                else if($ingredients_NID==2){ #蛋豆魚肉 蛋白質/7
                    $now_category[1]=(($recipe_portion*$history_portion)*($ingredients_protein/100))/7;
                }
                else if($ingredients_NID==3){ #乳品類 蛋白質/8
                    $now_category[2]=(($recipe_portion*$history_portion)*($ingredients_protein/100))/8;
                }
                else if($ingredients_NID==4){ #蔬菜類 熱量/25
                    $now_category[3]=(($recipe_portion*$history_portion)*($ingredients_cal/100))/25;
                }
                else if($ingredients_NID==5){ #水果類 熱量/60
                    $now_category[4]=(($recipe_portion*$history_portion)*($ingredients_cal/100))/60;
                }
                else if($ingredients_NID==6){ #油脂與堅果種子 脂肪/5
                    $now_category[5]=(($recipe_portion*$history_portion)*($ingredients_fat/100))/5;
                }

                #計算三大營養素+熱量
                $now_cal=($ingredients_cal / 100) * ($recipe_portion*$history_portion);
                $now_glyco=($ingredients_glyco / 100) * ($recipe_portion*$history_portion);
                $now_fat=($ingredients_fat / 100) * ($recipe_portion*$history_portion);
                $now_protein=($ingredients_protein / 100) * ($recipe_portion*$history_portion);
                
            }
        }
    }


?>
