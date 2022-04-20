<?php 
    
    /*
     *  display error set in php
     *
    */
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    
     $servername = "localhost";
    $username = "houseapp_user";
    $password = "YK;R31WbXGj(";
    $dbname = "houseapp_houseofbaboonapp_db";
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    
    /*
     * Get Product pricing using curl  
     *
    */
    
          $curl = curl_init();
    
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://www.richmondinteriors.nl/restapi/RISServices/public/products/stocklevels/121957/486141f82e2f290f3f8637c0628f93c3',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
    
        $response = curl_exec($curl);
        $obj = json_decode($response);
        
     
     
        for($i=0;$i<count($obj);$i++) {
    
             $itemCode  =  $obj[$i]->itemCode;
                 $unit  =  $obj[$i]->unit;
                   $StockIndicator  =  $obj[$i]->StockIndicator;
                     $Pakketdienst  =  $obj[$i]->Pakketdienst;
                        $IsStockItem  =  $obj[$i]->IsStockItem;
                            $current_date = date("Y-m-d");
                    
                     
        
            $get_checked_itemcode_productStockDetails= "SELECT itemCode FROM product_stock_details where itemCode='$itemCode'";
            $check_itemCodePoductStockDetails =  mysqli_query($conn, $get_checked_itemcode_productStockDetails);
            
            if(mysqli_num_rows($check_itemCodePoductStockDetails) > 0) {
        
              continue;
     
          } else {
            
            $sql = "INSERT INTO product_stock_details (itemCode, unit, StockIndicator, Pakketdienst,IsStockItem,created_at)
            VALUES ('$itemCode', '$unit' , '$StockIndicator','$Pakketdienst','$IsStockItem','$current_date')";
            
            mysqli_query($conn, $sql);
         } 
      
        
    }




?>