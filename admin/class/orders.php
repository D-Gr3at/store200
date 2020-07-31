<?php

class Orders extends dbobject
{
    public function orderList($data)
    {
        $table_name    = "orderdetails";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0),
            array( 'db' => 'orderid', 'dt' => 1),
            array( 'db' => 'product_id', 'dt' => 2, 'formatter'=>function($d,$row){
                $product_name = $this->getitemlabel('products', 'id', $d, 'name');
                // var_dump($product_name);
                return $product_name;
            }),
            array( 'db' => 'original_price', 'dt' => 3),
            array( 'db' => 'quantity', 'dt' => 4),
            array( 'db' => 'total', 'dt' => 5),
            array( 'db' => 'order_status', 'dt' => 6,'formatter'=>function($d,$row){
                
                if($d == 0)
                {
                    return "PENDING";
                }elseif($d == 1)
                {
                    return "COMPLETED";
                }
                elseif($d == 2)
                {
                    return "PROCESSING";
                }
                elseif($d == 3)
                {
                    return "REJECTED";
                }else
                {
                    return "";
                }
            }),
            array( 'db' => 'shipping_status', 'dt' => 7,'formatter'=>function($d,$row){
                
                if($d == 0)
                {
                    return "NOT SHIPPED";
                }
                elseif($d == 1)
                {
                    return "SHIPPED";
                }
                elseif($d == 2)
                {
                    return "IN TRANSIT";
                }
                elseif($d == 3)
                {
                    return "IN WAREHOUSE";
                }elseif($d == 4)
                {
                    return "DELIVERED";
                }
            }),
            array( 'db' => 'customerid', 'dt' => 8),
            array( 'db' => 'id', 'dt' => 9,'formatter'=>function($d,$row){
                $order_action_display = ($row['order_status'] == 0)?"block":"none";
                $shipping_action_display = ($row['order_status'] == 1)?"block":"none";
                return '<div class="dropdown" style="display:'.$order_action_display.'">
                                <a href="#" class="action_btn" data-toggle="dropdown" data-display="static" aria-expanded="false">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical align-middle "><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                  Order Action 
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" onclick="declineOrder(\''.$d.'\', \''.$row["customerid"].'\', \''.$row["product_id"].'\', \''.$row["orderid"].'\')" href="#">Decline</a>
                                    <a class="dropdown-item" onclick="confirmOrder(\''.$d.'\', \''.$row["customerid"].'\', \''.$row["product_id"].'\', \''.$row["orderid"].'\')" href="#">Confirm</a>
                                    </div>
                                </a>   
                        </div>   <div class="dropdown" style="display:'.$shipping_action_display.'"><a href="#" class="action_btn" data-toggle="dropdown" data-display="static" aria-expanded="false">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical align-middle"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                                  Shipping Action
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" onclick="shipOrder(\''.$d.'\', \''.$row["customerid"].'\', \''.$row["product_id"].'\', \''.$row["orderid"].'\')" href="#">Shipped</a>
                                    <a class="dropdown-item" onclick="orderInTransit(\''.$d.'\', \''.$row["customerid"].'\', \''.$row["product_id"].'\', \''.$row["orderid"].'\')" href="#">In Transit</a>
                                    <a class="dropdown-item" onclick="orderInWarehouse(\''.$d.'\', \''.$row["customerid"].'\', \''.$row["product_id"].'\', \''.$row["orderid"].'\')" href="#">In Warehouse</a>
                                    <a class="dropdown-item" onclick="orderDelivered(\''.$d.'\', \''.$row["customerid"].'\', \''.$row["product_id"].'\', \''.$row["orderid"].'\')" href="#">Delivered</a>
                                    </div>
                                </a>   </div> ';
            }),
            array( 'db' => 'createdon',  'dt' => 10 )
			);
		$filter = "";
		$filter = ($_SESSION['role_id_sess']=="001")?"":" AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function sendOtp($data)
    {
        var_dump($data);
    }
    public function declineOrder($data)
    {
        // var_dump($data);
        $sql = "UPDATE orderdetails SET order_status = '2', order_rejected_date = NOW() WHERE customerid = '".$data["customer_id"]."' AND id = '".$data["id"]."' AND product_id = '".$data["product_id"]."' AND orderid = '".$data["order_id"]."'";
        // echo $sql;
        $result = $this->db_query($sql,false);
        if($result >0)
        {
            return json_encode(array("responseCode"=>0,"responseBody"=>"Done"));
        }else
        {
            return json_encode(array("responseCode"=>62,"responseBody"=>"Could not update order"));
        }
    }
    public function confirmOrder($data)
    {
        // var_dump($data);
        $sql = "UPDATE orderdetails SET order_status = '1',  order_confirmation_date = NOW() WHERE customerid = '".$data["customer_id"]."' AND id = '".$data["id"]."' AND product_id = '".$data["product_id"]."' AND orderid = '".$data["order_id"]."'";
        // echo $sql;
        $result = $this->db_query($sql,false);
        if($result >0)
        {
            return json_encode(array("responseCode"=>0,"responseBody"=>"Done"));
        }else
        {
            return json_encode(array("responseCode"=>62,"responseBody"=>"Could not update order"));
        }
    }
    public function shipOrder($data)
    {
        // var_dump($data);
        $sql = "UPDATE orderdetails SET shipping_status = '1',  order_shipped_date = NOW() WHERE customerid = '".$data["customer_id"]."' AND id = '".$data["id"]."' AND product_id = '".$data["product_id"]."' AND orderid = '".$data["order_id"]."'";
        // echo $sql;
        $result = $this->db_query($sql,false);
        if($result >0)
        {
            return json_encode(array("responseCode"=>0,"responseBody"=>"Done"));
        }else
        {
            return json_encode(array("responseCode"=>62,"responseBody"=>"Could not update order"));
        }
    }
    public function orderInTransit($data)
    {
        // var_dump($data);
        $sql = "UPDATE orderdetails SET shipping_status = '2',  order_transit_date = NOW() WHERE customerid = '".$data["customer_id"]."' AND id = '".$data["id"]."' AND product_id = '".$data["product_id"]."' AND orderid = '".$data["order_id"]."'";
        // echo $sql;
        $result = $this->db_query($sql,false);
        if($result >0)
        {
            return json_encode(array("responseCode"=>0,"responseBody"=>"Done"));
        }else
        {
            return json_encode(array("responseCode"=>62,"responseBody"=>"Could not update order"));
        }
    }
    
    public function orderInWarehouse($data)
    {
        // var_dump($data);
        $sql = "UPDATE orderdetails SET shipping_status = '3',  order_warehouse_processing_date = NOW() WHERE customerid = '".$data["customer_id"]."' AND id = '".$data["id"]."' AND product_id = '".$data["product_id"]."' AND orderid = '".$data["order_id"]."'";
        // echo $sql;
        $result = $this->db_query($sql,false);
        if($result >0)
        {
            return json_encode(array("responseCode"=>0,"responseBody"=>"Done"));
        }else
        {
            return json_encode(array("responseCode"=>62,"responseBody"=>"Could not update order"));
        }
    }
    public function orderDelivered($data)
    {
        // var_dump($data);
        $sql = "UPDATE orderdetails SET shipping_status = '4',  order_delivered_date = NOW() WHERE customerid = '".$data["customer_id"]."' AND id = '".$data["id"]."' AND product_id = '".$data["product_id"]."' AND orderid = '".$data["order_id"]."'";
        // echo $sql;
        $result = $this->db_query($sql,false);
        if($result >0)
        {
            return json_encode(array("responseCode"=>0,"responseBody"=>"Done"));
        }else
        {
            return json_encode(array("responseCode"=>62,"responseBody"=>"Could not update order"));
        }
    }
    public function generateOrderID($customerid)
    {
        $chars = "0123456789";
        $res = "";
        for ($i = 0; $i < 10; $i++) 
        {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        $sql = "SELECT orderid FROM orderdetails WHERE customerid = '$customerid' AND orderid = '$res' LIMIT 1";
        $count = $this->db_query($sql,false);
        if($count > 0)
        {
            $this->generateOrderID($customerid);
        }else
        {
            return $res;
        }
    }
    public function verifyOrderToken($data)
    {
        
    }
    public function fulfillOrder($data)
    {
        
    }
    public function updateOrder()
    {
        
    }
    public function updateShipping()
    {
        
    }
}