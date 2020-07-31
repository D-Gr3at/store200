<?php

class Transaction extends dbobject
{
   public function transactionList($data)
    {
		$table_name    = "transaction_table";
		$primary_key   = "transaction_id";
		$columner = array(
			array( 'db' => 'transaction_id', 'dt' => 0 ),
			array( 'db' => 'transaction_id', 'dt' => 1),
            array( 'db' => 'transaction_amount', 'dt' => 2),
            array( 'db' => 'source_acct', 'dt' => 3),
            array( 'db' => 'merchant_id', 'dt' => 4,'formatter'=>function($d,$row)
              {
                  return $this->getitemlabel('merchant_reg','merchant_id',$d,'merchant_name');
              }),
            array( 'db' => 'payment_mode', 'dt' => 5),
            array( 'db' => 'response_code', 'dt' => 6),
            array( 'db' => 'response_message', 'dt' => 7),
            array( 'db' => 'customer_id',     'dt' => 8),
            array( 'db' => 'order_id', 'dt' => 9),
            array( 'db' => 'order_id', 'dt' => 10,'formatter'=>function($d,$row)
              {
                  $split_dist = "<button class='btn btn-secondary' onclick=\"getModal('transaction_details.php?payment_id=$d&viewer=general&church_id=$row[customer_id]','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Order Details</button>";
                  return $split_dist;
              }),
            array( 'db' => 'created',  'dt' => 11 )
			);
       $filter = "";
		$filter = ($_SESSION['role_id_sess']=="001")?"":" AND destination_acct='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function checkCart($data)
    {
        $data = array("total"=>"", 
                      "item"=>array( 
                                  array("quantity"=>5, 
                                        "product"=>array(
                                            "id"=>"", 
                                            "price"=>""
                                        ), 
                                        "variant"=>array(
                                            "id"=>"", 
                                            "price"=>"" 
                                        )
                                       )
                                )
                       );
    }
    public function logOrderTransaction($data)
    {
        $customerid = $data['customer_id'];
        $trans_id   = date("Ymdhis");
        $order_id   = $this->generateOrderID($customerid);
        $cart_item  = $data['payload'];
        var_dump($cart_item);
        $amount     = $this->getCartTotalAmount($cart_item) + $this->getShippingFee($cart_item);
        $sql = "INSERT INTO transaction_table (transaction_id,order_id,transaction_amount,total_shipping_price,source_acct,destination_acct,trans_type,transaction_desc,response_code,response_message,payment_mode,posted_ip,merchant_id,customer_id,customer_state,customer_lga,created,posted_user) VALUES('transaction_id','order_id','transaction_amount','total_shipping_price','source_acct','destination_acct','trans_type','transaction_desc','response_code','response_message','payment_mode','posted_ip','merchant_id','customer_id','customer_state','customer_lga','created','posted_user')";
        $sql2 = "INSERT INTO orderdetails () VALUES()";
        
    }
    public function getCartTotalAmount($cart_item)
    {
        $amount = 0;
        foreach($cart_item as $row)
        {
            $id_check = $this->checkProductID($row['id']);
            if($id_check == 1)
            {
                $price     = $this->getitemlabel("products","id",$row['id'],"discount_price");
                if($price != $row['price'])
                {
                    return json_encode(array("responseCode"=>62,"responseMessage"=>"Invalid amount (".$row['price'].") was specified for ".$row['label']));
                }
            }else
            {
                return json_encode(array("responseCode"=>64,"responseMessage"=>$row['label']." does not exist for this merchant. "));
            }
        }
    }
    public function checkProductID($id)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql         = "SELECT id FROM products WHERE id = '$id' AND merchant_id = '$merchant_id' LIMIT 1";
        $count       = $this->db_query($sql,false);
        return $count;
    }
    public function checkProductPrice($id)
    {
        $sql = "SELECT has_variant FROM products WHERE id = '$id'";
        $sql = "SELECT product_id,id,name,price FROM product_variant WHERE product_id = '$id'";
    }
    public function checkProductStock($id)
    {
        
    }
    public function shouldIncludeVariant($id)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql         = "SELECT product_id,id,name,price FROM product_variant WHERE product_id = '$id' AND merchant_id = '$merchant_id'";
        $result      = $this->db_query($sql);
        if(count($result) > 0)
        {
            $variant = array();
            foreach($result as $row)
            {
                 $variant[] = array("productID"=>$row['product_id'],"variantID"=>$row['id'],"name"=>$row['name'],"price"=>$row['price']);
            }
           
            return json_encode(array("responseCode"=>0,"responseMessage"=>"This product should have variant included","responseBody"=>$variant));
        }
        else
        {
            return json_encode(array("responseCode"=>66,"responseMessage"=>"This product should not have variant included","responseBody"=>null));
        }
    }
    public function getOrderSummary()
    {
        
    }
    public function getShippingFee($cart_item)
    {
        
    }
    public function generateOrderID($customerid)
    {
        $orderObj = new Orders();
        return $orderObj->generateOrderID($customerid);
    }
    public function setCustomerOrder($data)
    {
        $sql = "INSERT INTO orderdetails () VALUES()";
    }
    
}