<?php 
    
    /*
     *  display error set in php
     *
    */
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    
    /*
     * Database connection established
     *
    */
    
    $servername = "localhost";
    $username = "houseapp_user";
    $password = "YK;R31WbXGj(";
    $dbname = "houseapp_houseofbaboonapp_db";
    
    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);


    /*
     * Get Richmond Interiors Sales Services  products 
     *
    */
  
    
    $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://www.richmondinteriors.nl/restapi/RISServices/public/products/details/121957/486141f82e2f290f3f8637c0628f93c3',
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
        
        
        /*
         * Starting loop of curl response
         *
        */

        for($i=0;$i<=5;$i++) {
                    
                     
                $description  =  mysqli_real_escape_string($conn,$obj[$i]->description);
                 $description2  =  mysqli_real_escape_string($conn,$obj[$i]->description2);
                   $description3  =  mysqli_real_escape_string($conn,$obj[$i]->description3);
                     $description4  =  mysqli_real_escape_string($conn, $obj[$i]->description4);
                       $description5  =  mysqli_real_escape_string($conn, $obj[$i]->description5);
                          $eanCode  =  mysqli_real_escape_string($conn, $obj[$i]->eanCode);
                             $itemCode  =  mysqli_real_escape_string($conn, $obj[$i]->itemCode);
                               $purchasePackageSize  =  mysqli_real_escape_string($conn, $obj[$i]->purchasePackageSize);
                                
                $unit  =  mysqli_real_escape_string($conn,$obj[$i]->unit);
                    $vatIncluded  =  mysqli_real_escape_string($conn, $obj[$i]->vatIncluded);
                        $vatPercentage  =  mysqli_real_escape_string($conn, $obj[$i]->vatPercentage);
                            $StockIndicator  = mysqli_real_escape_string($conn, $obj[$i]->StockIndicator);
                           
                                $Collection  =  mysqli_real_escape_string($conn, $obj[$i]->Collection);
                                    //$Product type  =  $obj[$i]->Product type;
                    
                    
                    
                $Exterior      =  mysqli_real_escape_string($conn, $obj[$i]->Exterior);
                  $Interior  =  mysqli_real_escape_string($conn, $obj[$i]->Interior);
                    $Finishing  =  mysqli_real_escape_string($conn, $obj[$i]->Finishing);
                      $Hardware  =  mysqli_real_escape_string($conn, $obj[$i]->Hardware);
                       //Product Status New  =  $obj[$i]->Product Status New;
                         //Product Status Fire Retardant  =  $obj[$i]->Product Status Fire Retardant;
                         
                         
                $Pakketdienst      =  mysqli_real_escape_string($conn, $obj[$i]->Pakketdienst);
                    //$Product Status Stock  =  $obj[$i]->Product Status Stock;
                       //$Product Status Customized  =  $obj[$i]->Product Status Customized;
                        //$Sales Unit  =  $obj[$i]->Sales Unit;
                        //$Product Status =  $obj[$i]->Product Status
                        //$Height  =  $obj[$i]->Height;
                         
                         
                // $Width      =  $obj[$i]->Width;
                  // $Depth =  $obj[$i]->Depth;
                    $Volume =  mysqli_real_escape_string($conn, $obj[$i]->Volume);
                      //$Bruto Weight =  $obj[$i]->Bruto Weight
                        $Materiaal=  mysqli_real_escape_string($conn, $obj[$i]->Materiaal);
                          // $Extra Informatie  =  $obj[$i]->Extra Informatie
                 
                 
                $Colli  =  mysqli_real_escape_string($conn, $obj[$i]->Colli);
                    // $Product Images Location  =  $obj[$i]->Product Images Location
                    // $Product Images  =  $obj[$i]->Product Images
            
                $initial_status = 0;
                $final_status = 0;
                
        
        /*
        * Insert  the data in database for confirmation.
        *
        */
        
        $get_checked_itemcode = "SELECT itemCode,StockIndicator FROM houseofbaboon_table where itemCode='$itemCode'";
        $check_itemCode =  mysqli_query($conn, $get_checked_itemcode);
        
      
           
        
        if(mysqli_num_rows($check_itemCode) > 0) {
        
            $get_itemCode  =  mysqli_fetch_array($check_itemCode);
        
            
            if($StockIndicator == $get_itemCode['StockIndicator']) {
            
                 continue;
            
            } else {
            
            // update stock indicator
                    $update_stock = "UPDATE houseofbaboon_table SET  StockIndicator='$StockIndicator'  WHERE itemCode = '$itemCode'";
                    mysqli_query($conn, $update_stock);
            
            // end
        }
        
     
        } else {
            
            $sql = "INSERT INTO houseofbaboon_table (description, description2, description3, description4, description5,eanCode,itemCode,purchasePackageSize,unit,vatIncluded,vatPercentage,StockIndicator,Collection,Exterior,Interior,Finishing,Hardware,Pakketdienst,Volume,Materiaal,Colli,initial_status,final_status)
            VALUES ('$description', '$description2' , '$description3','$description4', '$description5', '$eanCode', '$itemCode', '$purchasePackageSize' , '$unit' , '$vatIncluded', '$vatPercentage', '$StockIndicator', '$Collection' , '$Exterior', '$Interior', '$Finishing', '$Hardware', '$Pakketdienst', '$Volume', '$Materiaal', '$Colli','$initial_status','$final_status')";
            
            mysqli_query($conn, $sql);
        }
    
            
    }
    /********************  product price saved data in database  ***************************/   
    /*
     * Get Product pricing using curl  
     *
    */
    
    $curl_productPrice = curl_init();

        curl_setopt_array($curl_productPrice, array(
          CURLOPT_URL => 'https://www.richmondinteriors.nl/restapi/RISServices/public/products/pricing/121957/486141f82e2f290f3f8637c0628f93c3',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response_productPrice = curl_exec($curl_productPrice);
        $objProductPrice = json_decode($response_productPrice);
        
          
         for($i=0;$i<count($objProductPrice);$i++) {
    
             $itemCodeProductPrice  =  mysqli_real_escape_string($conn, $objProductPrice[$i]->itemCode);
                 $price  =  mysqli_real_escape_string($conn, $objProductPrice[$i]->price);
                   $priceListId  =  mysqli_real_escape_string($conn, $objProductPrice[$i]->priceListId);
                     $retailPrice  =  mysqli_real_escape_string($conn, $objProductPrice[$i]->retailPrice);
                     
                     $current_date = date("Y-m-d");
                    
                     
        
            $get_checked_itemcode_productPrice = "SELECT itemCode FROM product_pricing where itemCode='$itemCodeProductPrice'";
            $check_itemCodePoductPrice =  mysqli_query($conn, $get_checked_itemcode_productPrice);
            
            if(mysqli_num_rows($check_itemCodePoductPrice) > 0) {
        
              continue;
     
          } else {
            
            $sql = "INSERT INTO product_pricing (itemCode, price, priceListId, retailPrice,created_at)
            VALUES ('$itemCodeProductPrice', '$price' , '$priceListId','$retailPrice','$current_date')";
            
            mysqli_query($conn, $sql);
         }
            
    }
    /********************   product price saved data in database  *************************/ 
    
    
    /********************** Product description ******************************************/
        
       $curlProductDescription = curl_init();
    
            curl_setopt_array($curlProductDescription, array(
              CURLOPT_URL => 'https://www.richmondinteriors.nl/restapi/RISServices/public/products/descriptions/121957/486141f82e2f290f3f8637c0628f93c3',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
    
        $responseProductDescription = curl_exec($curlProductDescription);
        $objProdoctDescription = json_decode($responseProductDescription);
        
        
        for($i=0;$i<count($objProdoctDescription);$i++) {
    
             $itemCodeProductDescription  =  mysqli_real_escape_string($conn, $objProdoctDescription[$i]->itemCode);
                 $longDescriptionNL  =  mysqli_real_escape_string($conn, $objProdoctDescription[$i]->longDescriptionNL);
                   $longDescriptionEN  =  mysqli_real_escape_string($conn, $objProdoctDescription[$i]->longDescriptionEN);
                     $longDescriptionDE  =  mysqli_real_escape_string($conn, $objProdoctDescription[$i]->longDescriptionDE);
                        $longDescriptionFR  =  mysqli_real_escape_string($conn, $objProdoctDescription[$i]->longDescriptionFR);
                            $current_date = date("Y-m-d");
                    
                     
        
            $get_checked_itemcode_productDescription = "SELECT itemCode FROM product_description where itemCode='$itemCodeProductDescription'";
            $check_itemCodePoducttDescription =  mysqli_query($conn, $get_checked_itemcode_productDescription);
            
            if(mysqli_num_rows($check_itemCodePoducttDescription) > 0) {
        
              continue;
     
          } else {
            
            $sql = "INSERT INTO product_description (itemCode, longDescriptionNL, longDescriptionEN, longDescriptionDE,longDescriptionFR,created_at)
            VALUES ('$itemCodeProductDescription', '$longDescriptionNL' , '$longDescriptionEN','$longDescriptionDE','$longDescriptionFR','$current_date')";
            
            mysqli_query($conn, $sql);
         } 
      
        
    }
    
    /**************************** End description ******************************************/
    
    /************************ Product stock level  ******************************/
    
     $curlProductStock = curl_init();
    
            curl_setopt_array($curlProductStock, array(
              CURLOPT_URL => 'https://www.richmondinteriors.nl/restapi/RISServices/public/products/stocklevels/121957/486141f82e2f290f3f8637c0628f93c3',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
    
        $responseProductStock = curl_exec($curlProductStock);
        $objProductStock = json_decode($responseProductStock);
        
        
          for($i=0;$i<count($objProductStock);$i++) {
    
             $itemCodeProductStaock  =  mysqli_real_escape_string($conn, $objProductStock[$i]->itemCode);
                 $unit  =  mysqli_real_escape_string($conn, $objProductStock[$i]->unit);
                   $StockIndicator  =  mysqli_real_escape_string($conn, $objProductStock[$i]->StockIndicator);
                     $Pakketdienst  =  mysqli_real_escape_string($conn, $objProductStock[$i]->Pakketdienst);
                        $IsStockItem  =  mysqli_real_escape_string($conn, $objProductStock[$i]->IsStockItem);
                            $current_date = date("Y-m-d");
                    
                     
        
            $get_checked_itemcode_productStockDetails= "SELECT itemCode,StockIndicator FROM product_stock_details where itemCode='$itemCode'";
            $check_itemCodePoductStockDetails =  mysqli_query($conn, $get_checked_itemcode_productStockDetails);
            
            if(mysqli_num_rows($check_itemCodePoductStockDetails) > 0) {
        
                 $get_itemCode  =  mysqli_fetch_array($check_itemCodePoductStockDetails);
        
            
            if($StockIndicator == $get_itemCode['StockIndicator']) {
            
                 continue;
            
            } else {
            
            // update stock indicator
                    $update_stock = "UPDATE product_stock_details SET  StockIndicator='$StockIndicator'  WHERE itemCode = '$itemCodeProductStaock'";
                    mysqli_query($conn, $update_stock);
            
            // end
        }
     
          } else {
            
            $sql = "INSERT INTO product_stock_details (itemCode, unit, StockIndicator, Pakketdienst,IsStockItem,created_at)
            VALUES ('$itemCodeProductStaock', '$unit' , '$StockIndicator','$Pakketdienst','$IsStockItem','$current_date')";
            
            mysqli_query($conn, $sql);
         } 
    }
    
    
    /************************ End Product stock level  ***********************/
  
    curl_close($curl);

?>