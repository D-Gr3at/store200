<?php
include_once("response.php");
class Product extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
    public function saveProduct($data2)
    {
        if($data2['operation'] == "edit")
        {
            return $this->editProduct($data2);
        }
        else
        {
            
            $errors       = 0;
            $file_data    = $data2['_files'];
            $data         = $data2['product'];
            $prd_option   = json_encode($data['product_option']);
            $posted_user  = $_SESSION['username_sess'];
            $merchant_id  = $_SESSION['merchant_sess_id'];
            $product_id   = date("Ymdhis");
            $long_url     = $this->generateProductUrl($product_id);
            
            $sql          = "INSERT INTO products (id,name,description,category_id,created,track_inventory,stock_status,has_variant,manage_variant_inventory,price,sku_id,ribbon,visibility,merchant_id,inventory,variant_json,brand_id,discount_price,discounted_percentage,is_discounted,weight,purchase_price,product_long_url) VALUES('$product_id','$data[name]','$data[description]','$data[category]',NOW(),'$data[is_track_inventory]','$data[stock_status]','$data[has_variant]','$data[is_manage_variant]','$data[price]','$data[sku]','$data[ribbon]','$data[is_visibility]','$merchant_id','$data[inventory]','$prd_option','$data[brand]','$data[discount_price]','$data[discounted_percentage]','$data[is_discounted]','$data[pd_weight]','$data[purchase_price]','$long_url')";
            file_put_contents("lll2.txt",json_encode($sql));
            $count      = $this->db_query($sql,false);
            if(count($count) > 0)
            {
                if(isset($data2['additional_data']))
                {
                    foreach($data2['additional_data'] as $row)
                    {
                        $sql = "INSERT INTO product_additional_information (merchant_id,product_id,info_title,description,created,posted_by) VALUES('$merchant_id','$product_id','$row[title]','$row[description]',NOW(),'$posted_user')";
                        $this->db_query($sql,false);
                    }
                }
                if(count($data2['product']['manage_variant']) > 0)
                {
                    foreach($data2['product']['manage_variant'] as $row)
                    {
                        $sql = "INSERT INTO product_variant (product_id,name,price,weight,weight_measurement,visibility,track_inventory,stock_status,merchant_id,charge,sku_id,created,posted_user,inventory) VALUES('$product_id','$row[variant]','$row[price]','weight','weight_measurement','$row[visible]','track_inventory','$row[status]','$merchant_id','$row[charge]','sku_id',NOW(),'$posted_user','$row[inventory]')";
                        $this->db_query($sql,false);
                    }
                }
                $path = './uploads/'.$merchant_id.'/products/';
                $ff = $this->saveMerchantImage($file_data,$merchant_id,$path,$product_id);
                $ff = json_decode($ff,true);
                if($ff['response_code'] == "0")
                {
                    $full_path = $ff['data'];
                    $sql       = "UPDATE products SET image = '$full_path' WHERE id = '$product_id' LIMIT 1";
                    $count = $this->db_query($sql,false);
                    return ($count == 1)?json_encode(array('response_code'=>'0','response_mesage'=>'Successful','data'=>array('product_id'=>$product_id))):json_encode(array('response_code'=>'471','response_mesage'=>'Failed to update product image'));
                }
                else
                {
                    json_encode(array('response_code'=>'71','response_mesage'=>'Failed to upload product image'));
                }
            }
            else
            {
                json_encode(array('response_code'=>'78','response_mesage'=>'Failed to save product.'));
            }
        }
    }
    public function editProduct($data2)
    {
        $data         = $data2['product'];
        $prd_option   = json_encode($data['product_option']);
        $posted_user  = $_SESSION['username_sess'];
        $merchant_id  = $_SESSION['merchant_sess_id'];
        
        
        $sql          = "UPDATE products SET name='$data[name]',description = '$data[description]',category_id='$data[category]',created = NOW(),track_inventory='$data[is_track_inventory]',stock_status='$data[stock_status]',has_variant='$data[has_variant]',manage_variant_inventory='$data[is_manage_variant]',price='$data[price]',sku_id='$data[sku]',ribbon='$data[ribbon]',visibility='$data[is_visibility]',inventory='$data[inventory]',variant_json='$prd_option',brand_id='$data[brand]',discount_price='$data[discount_price]',discounted_percentage='$data[discounted_percentage]',is_discounted='$data[is_discounted]',weight='$data[pd_weight]',purchase_price='$data[purchase_price]' WHERE id = '$data[product_id]' AND merchant_id = '$merchant_id' LIMIT 1";
        $count        = $this->db_query($sql,false);
        
        if(isset($data2['additional_data']))
        {
            foreach($data2['additional_data'] as $row)
            {
                $sql = "UPDATE product_additional_information SET info_title ='$row[title]',description='$row[description]',created=NOW(),posted_by='$posted_user' WHERE product_id = '$data[product_id]' AND merchant_id = '$merchant_id' LIMIT 1";
                $this->db_query($sql,false);
            }
        }
        if(count($data2['product']['manage_variant']) > 0)
        {
            file_put_contents("rrr21.txt",json_encode($data2['product']['manage_variant']));
            foreach($data2['product']['manage_variant'] as $row)
            {
                $sql = "UPDATE product_variant SET name='$row[variant]', price='$row[price]', weight_measurement ='', visibility='$row[visible]', track_inventory='$row[status]', stock_status='', charge='$row[charge]', sku_id='', created=NOW(), posted_user='$posted_user', inventory='$row[inventory]' WHERE id = '$row[id]' LIMIT 1 ";
                $this->db_query($sql,false);
                file_put_contents("rrr2.txt",json_encode($sql));
            }
        }
        return json_encode(array('response_code'=>'0','response_mesage'=>'Successful'));
    }
    public function generateProductUrl($id)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $merchant_name = $this->getitemlabel("merchant_reg","merchant_id",$merchant_id,"merchant_name");
        return $long        = "https://www.vuvaa_shop.com/shop/".$merchant_name."/product/".$id;
//        $sql         = "UPDATE products SET product product_long_url = '$long' WHERE id = '$id' AND merchant_id = '$merchant_id' LIMIT 1";
//        $count = $this->db_query($sql);
        
    }
    public function featuredList($data)
    {
        $table_name    = "products";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'image', 'dt' => 1,'formatter'=>function($d,$row){
                return "<img width='50px' height='50px' class='img-thumbnail' src='".$d."' />";
            } ),
			array( 'db' => 'name',  'dt' => 2 ),
			array( 'db' => 'merchant_id',  'dt' => 3 ,'formatter' => function( $d,$row ) {
                
                return $this->getitemlabel("merchant_reg","merchant_id",$d,"merchant_name");
            } ),
			
			array( 'db' => 'id',  'dt' => 4,'formatter' => function( $d,$row ) {
                
						return '<a class="badge badge-warning" onclick="removeFeature(\''.$d.'\')"  href="javascript:void(0)" >Remove Feature</a>';
					} ),
			array( 'db' => 'created', 'dt' => 5, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
		$filter = " AND merchant_id='$_SESSION[merchant_sess_id]' AND is_featured_product = '1'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function unsetFeatureProduct($data)
    {
        $product_id = $data['product_id'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql = "UPDATE products SET is_featured_product = '0' WHERE id = '$product_id' AND merchant_id = '$merchant_id' LIMIT 1";
            $count2       = $this->db_query($sql,false);
            if($count2 == 1)
            {
                return json_encode(array('response_code'=>'0','response_message'=>'Product has been removed from features'));
            }else
            {
                return json_encode(array('response_code'=>'71','response_message'=>'Unable to remove product from feature. Try again'));
            }
    }
    public function setFeatureProduct($data)
    {
        $product_id = $data['product_id'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql         = "SELECT no_of_featured_product_allowed FROM merchant_reg WHERE merchant_id = '$merchant_id' LIMIT 1";
        $result      = $this->db_query($sql);
        $allowed_feature = $result[0]['no_of_featured_product_allowed'];
        $sql         = "SELECT id FROM products WHERE merchant_id = '$merchant_id' AND is_featured_product = '1' ";
        $count       = $this->db_query($sql,false);
        if($count < $allowed_feature)
        {
            $sql = "UPDATE products SET is_featured_product = '1' WHERE id = '$product_id' AND merchant_id = '$merchant_id' LIMIT 1";
            $count2       = $this->db_query($sql,false);
            if($count2 == 1)
            {
                return json_encode(array('response_code'=>'0','response_mesage'=>'Product is now set as a feature!'));
            }else
            {
                return json_encode(array('response_code'=>'71','response_mesage'=>'Unable to set product as a feature. Try again'));
            }
        }else
        {
            return json_encode(array('response_code'=>'77','response_mesage'=>'You have reached the limit of features allowed for your profile'));
        }
    }
    public function addFeatureImage($data)
    {
        $file_data   = $data['_files'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        $product_id  = $data['product_id'];
        $path        = './uploads/'.$merchant_id.'/products/';
//        foreach($data['_files'] as $file_data)
        $image_id    = rand(1111,999999999).date('his');
//        file_put_contents("product_keeper.txt",json_encode($file_data),FILE_APPEND);
        $ff = $this->saveMerchantImage($file_data,$merchant_id,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            $sql       = "INSERT INTO product_images (product_id,created,location,merchant_id) VALUES('$product_id',NOW(),'$full_path','$merchant_id')";
            $count     = $this->db_query($sql,false);
            return json_encode(array('response_code'=>'0','response_mesage'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_mesage'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function updateProductImage($data)
    {
        
        file_put_contents("image_keeper.txt",json_encode($data),FILE_APPEND);
         $upload_type     = $data['u_type'];
        $image_location   = $data['image_location']; // used to unset the image from the product folder
        $file_data        = $data['_files'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        $path        = './uploads/'.$merchant_id.'/products/';
//        foreach($data['_files'] as $file_data)
        $image_id    = rand(1111,999999999).date('his');
        
        $ff = $this->saveMerchantImage($file_data,$merchant_id,$path,$image_id);
        $ff = json_decode($ff,true);
        if($ff['response_code'] == "0")
        {
            $full_path = $ff['data'];
            if($upload_type == "primary")
            {
                $product_id  = $data['product_id']; // used to udate the products table and used as the image id when it's a primary product update
                $sql       = "UPDATE products SET image = '$full_path' WHERE id = '$product_id' LIMIT 1";
            }else
            {
                $saved_image_id   = $data['image_id']; // used to update the product_images table when it's a feature image update
                $sql       = "UPDATE product_images SET location = '$full_path' WHERE id = '$saved_image_id' LIMIT 1";
            }
            
            $count     = $this->db_query($sql,false);
            unlink($image_location);
            return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
        }else
        {
            return json_encode(array('response_code'=>'458','response_message'=>'Unable to upload '.$file_data['upfile']['name']));
        }
    }
    public function removeImage($data)
    {
        $saved_image_id   = $data['image_id'];
        $image_location   = $data['image_location'];
        $merchant_id      = $_SESSION['merchant_sess_id'];
        
        $sql              = "DELETE FROM product_images WHERE id = '$saved_image_id' AND merchant_id = '$merchant_id' LIMIT 1";
        $count            = $this->db_query($sql,false);
        unlink($image_location);
        return json_encode(array('response_code'=>'0','response_message'=>'Successful'));
    }
    public function saveMerchantImage($data,$user_id,$path,$image_id="")
    {
        $_FILES = $data;
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Invalid parameter.'));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return json_encode(array('response_code'=>'74','response_mesage'=>'No file sent.'));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
            default:
                return json_encode(array('response_code'=>'74','response_mesage'=>'Unknown errors.'));
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.'));
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
    //    $finfo = new finfo(FILEINFO_MIME_TYPE);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            finfo_file($finfo,$_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            ),
            true
        )) {
            return json_encode(array('response_code'=>'74','response_mesage'=>'Invalid file format.'));
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
       $email = ($image_id == "")?date('mdhis'):$image_id;
        
        if (!move_uploaded_file(
            $_FILES['upfile']['tmp_name'],
            sprintf($path.'%s.%s',
                $email,
                $ext
            )
        )) {
            return json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.'));
        }
        $full_path = $path.$email.'.'.$ext;
        return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
    }
    
    public function deleteCategory($data)
    {
        $menu_id = $data['menu_id'];
        $sql     = "DELETE FROM menu WHERE menu_id = '$menu_id'";
        $this->db_query($sql,false);
        $sql     = "DELETE FROM menu_group WHERE menu_id = '$menu_id'";
        $this->db_query($sql,false);
        return $this->response->publishResponse("0","Deleted successfully","");
    }
    
    public function productList($data)
    {
        $table_name    = "products";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'image', 'dt' => 1,'formatter'=>function($d,$row){
                $p_id = $row['id'];
                return '<div align="center"><img width="50px" height="50px" class="img-thumbnail" src="'.$d.'" /><a class="badge badge-warning" style="display:block; margin-top:5px"  onclick="getModal(\'setup/product_image.php?op=edit&product_id='.$p_id.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary" >Edit Image</a></div>';
            } ),
			array( 'db' => 'name',  'dt' => 2, 'formatter'=>function($d,$row){
                $p_id = $row['id'];
                return '<b style="font-size:18px">'.$d.'</b><a class="badge badge-primary" onclick="getpage(\'setup/product_setup.php?op=edit&product_id='.$p_id.'\',\'page\')" style="display:block; margin-top:5px"  href="javascript:void(0)" >Edit Product</a>';
            } ),
			array( 'db' => 'inventory',  'dt' => 3,'formatter'=>function($d,$row){
                $track_inventory = $row['track_inventory'];
                if($track_inventory == 0)
                {
                    return $row['stock_status'];
                }else
                {
                    return $d;
                }
            } ),
			array( 'db' => 'category_id',  'dt' => 4,'formatter'=>function($d,$row){ 
                return $this->getitemlabel("product_categories","id",$d,"name");
            }),
			array( 'db' => 'price',  'dt' => 5,'formatter'=>function($d,$row){ 
                return ($d != $row['discount_price'])?"&#x20A6 ".number_format($row['discount_price'],2)."<div class='df'>".$d."</div>":"&#x20A6 ".number_format($d,2);
            }),
			array( 'db' => 'product_long_url',  'dt' => 6,'formatter'=>function($d,$row){ 
                return "<a target='_blank' href='".$d."'>Visit product page</a>";
            } ),
			
//            array( 'db' => 'description',  'dt' => 6 ),
			array( 'db' => 'discount_price',  'dt' => 7,'formatter' => function( $d,$row ) {
                        $d = $row['id'];
//						return '<a class="badge badge-warning" onclick="getpage(\'setup/product_setup.php?op=edit&product_id='.$d.'\',\'page\')"  href="javascript:void(0)" >Edit Product</a>';
					} ),
            array( 'db' => 'stock_status',  'dt' => 8,'formatter' => function( $d,$row ) {
                        $d = $row['id'];
                        if($row['visibility'] == "1")
                        {
                            return '<a class="badge badge-primary" onclick="hideProduct(\''.$d.'\',0)"   href="javascript:void(0)">Hide Product</a>';
                        }else
                        {
                             return '<a class="badge badge-primary" onclick="hideProduct(\''.$d.'\',1)"   href="javascript:void(0)">Show Product</a>';
                        }
						
					} ),
            array( 'db' => 'is_featured_product',  'dt' => 9,'formatter' => function( $d,$row ) {
                        $product = $row['id'];
						// if($d == 1)
                        // {
                        //     return '<a class="badge badge-primary" onclick="unsetFeature(\''.$product.'\')"   href="javascript:void(0)">Unset from features</a>';
                        // }else
                        // {
                        //      return '<a class="badge badge-success" onclick="setFeature(\''.$product.'\')"   href="javascript:void(0)">Set to features</a>';
                        // }
					} ),
			
            array( 'db' => 'created', 'dt' => 10, 'formatter' => function( $d,$row ) {
						return $d;
					}
				),
            array( 'db' => 'visibility', 'dt' => 11, 'formatter' => function( $d,$row ) {
						return "";
					}
				)
			);
		$filter = "";
		$filter = ($_SESSION['role_id_sess']=="001")?"":" AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function setVisibility($data)
    {
        $change_to = $data['change_to'];
        $product_id = $data['product_id'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql = "UPDATE products SET visibility = '$change_to' WHERE id = '$product_id' AND merchant_id = '$merchant_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count == 1)
        {
            return json_encode(array("responseCode"=>0,"responseMessage"=>"Products Visibility updated!"));
        }else
        {
            return json_encode(array("responseCode"=>45,"responseMessage"=>"Products Visibility could not be updated"));
        }
    }
     public function getProduct($data)
     {
         $enteredText = $data['entered_text'];
         $merchant_id = $_SESSION['merchant_sess_id'];//$data['merchantID'];
         $sql = "SELECT
                     products.sku_id,
                     products.price, 
                     products.image,
                     products.ribbon,
                     products.has_variant,
                     products.stock_status,
                    products.id as product_id,
                    products.name as product_name,
                    products.description as product_desc,
                    product_categories.id as cat_id,
                    product_categories.name as cat_name
                     FROM products
                     INNER JOIN product_categories 
                    ON products.category_id = product_categories.id 
                    WHERE 
                    products.merchant_id = '$merchant_id' AND products.visibility = '1' AND stock_status = 'In Stock' AND product_categories.merchant_id IN ('$merchant_id','*') AND products.name LIKE '%$enteredText%'";
         $result = $this->db_query($sql);
         $product = array();
         if(count($result) > 0)
         {
             foreach($result as $row)
             {
                 $variant    = array();
//                 $sql2       = "SELECT * FROM product_variant WHERE product_id = '$row[product_id]' AND stock_status = 'In Stock' AND visibility = '1' AND merchant_id = '$merchant_id'";
//                 $result2    = $this->db_query($sql2);
//                  if(count($result2) > 0)
//                  {
//                      foreach($result2 as $row2)
//                     {
//                         $variant[] = array("id"=>$row2['id'],"name"=>$row2['name'],"price"=>$row2['price']);
//                     }
//                  }
                 
                 $product[]  = array(
                                "id"=>$row['product_id'], 
                                "name"=>$row['product_name'],  
                                "description"=>$row['product_desc'],
                                "categoryID"=>$row['cat_id'],
                                "categoryName"=>$row['cat_name'],
                                "stockStatus"=>$row['stock_status'],
                                "primaryImage"=>$row['image'],
                                "sku"=>$row['sku_id'],
                                "hasVariant"=>($row['has_variant'] == "1")?true:false,
                                "variant"=>$variant,
                                "price"=>(float)$row['price'],
                                "ribbon"=>$row['ribbon']
                                );
             }
             
             header('Content-Type: application/json');
             return json_encode(array("responseCode"=>0,"responseMessage"=>"Products Found","responseBody"=>array("items"=>$product)));
         }else
         {
             header('Content-Type: application/json; charset=UTF-8');
             return json_encode(array('responseCode'=>774,'responseMessage'=>'No Products Found','responseBody'=>''));
         }
     }
    public function getSearchFilter($data)
     {
//        var_dump($data);
         $merchant_id = $_SESSION['merchant_sess_id'];//$data['merchantID'];
        $search_item = $data['item'];
         $sql = "SELECT * FROM products WHERE name LIKE '$search_item%' AND merchant_id = '$merchant_id' AND visibility = '1' AND is_featured_product = '0'";
         $result = $this->db_query($sql);
         $product = array();
         if(count($result) > 0)
         {
             foreach($result as $row)
             {
                 $product[]  = array(
                                "status"=>0,
                                "id"=>$row['id'], 
                                "name"=>$row['name'], 
                                "price"=>(float)$row['discount_price'],
                                "primaryImage"=>$row['image']
                                );
             }
             
             header('Content-Type: application/json');
             return json_encode(array("responseCode"=>0,"responseMessage"=>"Products Found","responseBody"=>array("items"=>$product)));
         }else
         {
              $product[] = array("status"=>55);
             header('Content-Type: application/json; charset=UTF-8');
             return json_encode(array('responseCode'=>774,'responseMessage'=>'No Products Found','responseBody'=>array("items"=>$product)));
         }
     }
    public function updateProductVisibility($data)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $product_id = $data['product_id'];
        $state = $data['state'];
        $sql = "UPDATE products SET visibility = '$state' WHERE product_id = '$product_id' AND merchant_id = '$merchant_id' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count == 1)
        {
            return json_encode(array("responseCode"=>0,"responseMessage"=>"Updated!"));
        }else
        {
            return json_encode(array("responseCode"=>14,"responseMessage"=>"Could not update product visibility"));
        }
    }
    public function getProductDetails($data)
     {
         $merchant_id = $_SESSION['merchant_sess_id'];//$data['merchantID'];
         $product_id = $data['product_id'];//$data['merchantID'];
          $sql = "SELECT
                     products.sku_id,
                     products.variant_json,
                     products.price, 
                     products.discount_price, 
                     products.category_id, 
                     products.inventory, 
                     products.brand_id, 
                     products.discounted_percentage, 
                     products.purchase_price, 
                     products.weight, 
                     products.manage_variant_inventory, 
                     products.visibility, 
                     products.track_inventory, 
                     products.image,
                     products.ribbon,
                     products.has_variant,
                     products.stock_status,
                    products.id as product_id,
                    products.name as product_name,
                    products.description as product_desc
                    FROM products
                    WHERE 
                    products.merchant_id = '$merchant_id' AND products.id = '$product_id' LIMIT 1 ";
         $result = $this->db_query($sql);
//        var_dump($result);
         $product = array();
         if(count($result) > 0)
         {
             foreach($result as $row)
             {
                 $variant    = array();
                 $sql2       = "SELECT * FROM product_variant WHERE product_id = '$row[product_id]' AND merchant_id = '$merchant_id'";
                 $result2    = $this->db_query($sql2);
                  if(count($result2) > 0)
                  {
                      foreach($result2 as $row2)
                     {
                         $variant[] = array("id"=>$row2['id'],"name"=>$row2['name'],"price"=>$row2['price'],"weight"=>$row2['weight'],"visibility"=>$row2['visibility'],"stockStatus"=>$row2['stock_status'],"charge"=>$row2['charge'],"inventory"=>$row2['inventory']);
                     }
                  }
                 
                 $additional_sql = "SELECT info_title,description FROM product_additional_information  WHERE product_id = '$product_id' AND merchant_id = '$merchant_id'";
                $additional_result = $this->db_query($additional_sql);
                $product_additional_info = null;
                if(count($additional_result) > 0)
                {
                    $product_additional_info = array();
                    foreach($additional_result as $row22)
                    {
                        $product_additional_info[] = array("title"=>$row22['info_title'],"description"=>$row22['description']);
                    }
                }
                 $sql_4 = "SELECT * FROM product_images WHERE product_id = '$product_id' AND merchant_id = '$merchant_id'";
                 $result_44 = $this->db_query($sql_4);
//                 var_dump($result_44);
                 $productImages = array();
                 if(count($result_44) > 0)
                 {
                     foreach($result_44 as $rows)
                     {
                         $productImages[] = $rows['location'];
                     }
                 }
                 $product  = array(
                                "id"=>$row['product_id'], 
                                "name"=>$row['product_name'],  
                                "description"=>$row['product_desc'],
                                "categoryID"=>$row['category_id'],
                                "brandID"=>$row['brand_id'],
                                "trackInventory"=>$row['track_inventory'],
                                "visibility"=>$row['visibility'],
                                "discountedPercentage"=>$row['discounted_percentage'],
                                "discountPrice"=>$row['discount_price'],
                                "manageVariantInventory"=>$row['manage_variant_inventory'],
                                "purchasePrice"=>$row['purchase_price'],
                                "weight"=>$row['weight'],
                                "stockStatus"=>$row['stock_status'],
                                "primaryImage"=>$row['image'],
                                "sku"=>$row['sku_id'],
                                "inventory"=>$row['inventory'],
                                "hasVariant"=>$row['has_variant'],
                                "variantJson"=>json_decode($row['variant_json'],true),
                                "variant"=>$variant,
                                "price"=>(float)$row['price'],
                                "ribbon"=>$row['ribbon'],
                                "additional_info"=>$product_additional_info,
                                "otherImages"=>$productImages
                                );
             }
             
//             header('Content-Type: application/json');
             return json_encode(array("responseCode"=>0,"responseMessage"=>"Products Found","responseBody"=>$product));
         }else
         {
//             header('Content-Type: application/json; charset=UTF-8');
             return json_encode(array('responseCode'=>774,'responseMessage'=>'No Products Found','responseBody'=>''));
         }
         
     }
    
  
}