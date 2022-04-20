<?

	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';
	require __DIR__.'/db_config.php';

	$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);
    
   
	try
	{  
        
        //create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname); 

        
        /*
	     * Get product data to post shopify product 
	     *
	     */ 
	       
        $sql = "SELECT houseofbaboon_table.description, houseofbaboon_table.Product_type,product_description.longDescriptionNL,product_description.longDescriptionEN, houseofbaboon_table.initial_status,houseofbaboon_table.final_status,houseofbaboon_table.itemCode,houseofbaboon_table.StockIndicator, houseofbaboon_table.Product_Images, product_pricing.price,product_pricing.retailPrice,houseofbaboon_table.eanCode FROM houseofbaboon_table
		JOIN product_description ON houseofbaboon_table.itemCode = product_description.itemCode
		JOIN product_pricing ON product_description.itemCode = product_pricing.itemCode";
        $result  =  $conn->query($sql);
        
       // $row = mysqli_fetch_array($result);
       
        
        while($row = mysqli_fetch_array($result)) {
        
        $checkStockIndicator  =  $row["StockIndicator"];   
        
        if(isset($checkStockIndicator)) {
        
        if($checkStockIndicator>=0) {
        
        $StockIndicator  =      $checkStockIndicator;
        $description     =      $row["description"];
        $Product_type    =      $row["Product_type"];
        
        $longDescriptionNL  =  $row["longDescriptionNL"];
        $longDescriptionEN  =  $row["longDescriptionEN"];
        
        $initial_status  =  $row["initial_status"];
        $final_status  =  $row["final_status"];
         
        $itemCode  =  $row["itemCode"];
       
        
       
        $product_price  =  $row["price"];
        
        $retailPrice  =  $row["retailPrice"];
        
        $eanCode  =  $row["eanCode"];
        
        
        $product_images  = explode(",",$row["Product_Images"]);
    

        if($initial_status==0) {
        
        /*
         *  update status of products
         *
        */
        
            
        
        # Making an API request can throw an exception
		$product = $shopify('POST /admin/products.json', array(), array
		(   
			'product' => array
			(
                "title" => $description,
                "body_html" => $longDescriptionNL,
                //"vendor" => "Burton",
                "product_type" => $Product_type,
                "fulfillment_service"=>"manual",
                "inventory_management"=>"shopify",
				
                'images' => array(
                        array( 
                            'src' =>'https://www.richmondinteriors.nl/wp-content/uploads/wpallimport/files/'.$product_images[0]
                    )
                ),
              //  "tags"=>["Barnes \u0026 Noble","Big Air","John's Fav"],
                        
				// "variants" => array
				// (
				// 	array
				// 	(
				// 		"option1" => "First",
				// 		"price" => "10.00",
				// 		"sku" => 123,
				// 	),
				// 	array (
				// 		"option1" => "Second",
				// 		"price" => "20.00",
				// 		"sku" => "123"
				// 	)
				// )
			)
		  ));
		  
	        $get_productID  = $product['id'];
	        
	        if(isset($product_images)) {
	     
	        for($j=1;$j<count($product_images);$j++) {
	                    
	               //     print_r('https://www.richmondinteriors.nl/wp-content/uploads/wpallimport/files/'.$product_images[$j]);
	               // $conver_img = base64_encode('https://www.richmondinteriors.nl/wp-content/uploads/wpallimport/files/'.$product_images[$j]);
	               // print_r($conver_img);
	                
                 # Making an API request can throw an exception
                $product_images_curl = $shopify('POST /admin/api/2022-04/products/'.$get_productID.'/images.json', array(), array
                ( 
                        	'image' => array
			                (
			                    'src'=>'https://www.richmondinteriors.nl/wp-content/uploads/wpallimport/files/'.$product_images[$j],
			                
	                        )
                
                ));
	            
	            
	        }
	        
        }
	        
	        
		  /*
		   * available inventory quanity
		   * 
		  */
		   
         # Making an API request can throw an exception
                
            $count_variant = count($product['variants']);
            
            
          
           
            for($i=0;$i<$count_variant;$i++) {
                        
                    
                   	$post_variant = $shopify('PUT /admin/api/2022-04/variants/'.$product['variants'][$i]['id'].'.json', array(), array
    	                (  
    	                        
	                      'variant' => array
		                    (
		                        "id"=>  $product['variants'][$i]['id'],
		                        "fulfillment_service" =>"manual",
		                        "inventory_management"=>"shopify",
		                        "price"=>$retailPrice,
		                        "sku" => $itemCode,
		                        "barcode"  =>$eanCode,
		                        
                        )
    	            
    	            ));
    	            
                    /*
                     * available quanity api
                     *
                    */
                    $inventory_item_id   =   $product['variants'][$i]['inventory_item_id'];
                    
                    $quanity_available = $shopify('POST /admin/api/2022-04/inventory_levels/set.json', array(), array
        	        (
        	        
            	        "location_id"=> 68475289853,
                        "inventory_item_id" =>$product['variants'][$i]['inventory_item_id'],
                        "available"=>$StockIndicator,
        	    ));
        	    
        	    
        	        /*
                     *  update cost per items
                     *
                    */
                    
                    $Update_costPerItem = $shopify('PUT /admin/api/2022-04/inventory_items/'.$product['variants'][$i]['inventory_item_id'].'.json', array(), array
        	        (
        	        
            	        'inventory_item' => array
		                    (
		                        "id"=>  $product['variants'][$i]['inventory_item_id'],
		                        "cost"=>$product_price+50,
		                        "tracked"  =>true,
		                        
                        )
        	        ));
            }
        
		  /*
		   *  End curl of inventory quanity 
		   *
		  */
		  
		  /*
		   * update status on database after post 
		   *
		  */
		   
		   if($StockIndicator==0) {
		       
		        /*
		         *  show product in Draft
		         *
		        */
		        
		
		        # Making an API request can throw an exception
        		$product = $shopify('PUT /admin/api/2022-04/products/'.$get_productID.'.json', array(), array
        		(   
        			'product' => array
        			(
        			    "status" => "draft",     
        			    
        			 )
        			 
        	    ));
		        
		        
		        /*
		         *
		         *
		        */
		        $update_query = "UPDATE houseofbaboon_table SET initial_status=1,final_status=0,inventory_item_id='$inventory_item_id',product_id='$get_productID' WHERE itemCode='$itemCode'";
		        mysqli_query($conn, $update_query);    
		       
		   } else {
		    
		    $update_query = "UPDATE houseofbaboon_table SET initial_status=1,final_status=1,inventory_item_id='$inventory_item_id' WHERE itemCode='$itemCode'";
		        mysqli_query($conn, $update_query);
		    
		       
		   }
		   
		  
		  
		
        } else {
            
           
           if($final_status == 0) {
               
               if($StockIndicator==1) {
				   
					
			$sql = "SELECT inventory_item_id,StockIndicator, product_id  FROM houseofbaboon_table where itemCode='$itemCode'";
            $result = mysqli_query($conn, $sql);
            
             if ($result->num_rows > 0) {
               
                    while($row = $result->fetch_assoc()) {
                         
                          $update_inventory_item_id     = $row['inventory_item_id'];
                          $update_StockIndicator= $row['StockIndicator'];
                          $update_product_id= $row['product_id'];
                         
            /*
             * available quanity api
             *
            */
                
                    
            $quanity_available = $shopify('POST /admin/api/2022-04/inventory_levels/set.json', array(), array
            (
            
                "location_id"=> 68475289853,
                "inventory_item_id" =>$update_inventory_item_id,
                "available"=>$update_StockIndicator,
            ));
            
			
            
            /*
             * update active status of product api
             *
            */
                
                $updateproduct = $shopify('PUT /admin/api/2022-04/products/'.$update_product_id.'.json', array(), array
                (   
                    'product' => array
                    (
                        "status" => "active",     
                    
                    )
                 
                ));
            
            /*
             * end update active status
             *
            */
            
            
                $update_query = "UPDATE houseofbaboon_table SET final_status=1 WHERE itemCode='$itemCode'";
    	        mysqli_query($conn, $update_query); 
    	        
    	        
                }
            } 

        	 /*
        	  * End available quanity
        	  * 
        	 */
			 
				
			} else {
				
				
				 continue;
			    }
		   
            } else {
                
            continue; 
                
        }
       }
      }/*  check stock indication */ else { 
             
             continue;
             
         } 
        } //Isset stock indication
      }
	}
	catch (shopify\ApiException $e)
	{
		# HTTP status code was >= 400 or response contained the key 'errors'
		echo $e;
		print_R($e->getRequest());
		print_R($e->getResponse());
	}
	catch (shopify\CurlException $e)
	{
		# cURL error
		echo $e;
		print_R($e->getRequest());
		print_R($e->getResponse());
	}

?>