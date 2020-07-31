<?php

class Shipping extends dbobject
{
   public function deliveryLocationList($data)
    {
		$table_name    = "shipping_regions";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0),
            array( 'db' => 'state', 'dt' => 1,'formatter'=>function($d,$row){
                
                return $this->getitemlabel("lga","state_code",$d,"State");
            }),
            array( 'db' => 'lga_list', 'dt' => 2,'formatter'=>function($d,$row){
                $list = explode(',',$d);
                $dd = "";
                foreach($list as $val)
                {
                    $name = $this->db_query("SELECT Lga FROM lga WHERE Lgaid = '$val' LIMIT 1");
                    $dd.= $name[0]['Lga'].",";
                }
                return $dd;
            }),
            array( 'db' => 'delivery_time', 'dt' => 3),
            array( 'db' => 'pickup_station', 'dt' => 4,'formatter'=>function($d,$row){
                
                return $this->getitemlabel("merchant_pickup_stores","id",$d,"title");
            }),
            array( 'db' => 'shipping_rule_type_id', 'dt' => 5,'formatter'=>function($d,$row){
                
                return $this->getitemlabel("shipping_rules","id",$d,"rule_name");
            }),
            array( 'db' => 'id', 'dt' => 6),
            array( 'db' => 'created',  'dt' => 7 )
			);
		$filter = "";
		$filter = ($_SESSION['role_id_sess']=="001")?"":" AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function saveShippingPricing($data)
    {
        $merchant_id     = $_SESSION['merchant_sess_id'];
        $posted_by       = $_SESSION['username_sess'];
        $title           = $data['label'];
        $state           = $data['state'];
        $shipping_rule   = $data['rules'];
        $pickup_station  = $data['pickup_station'];
        $delivery_time   = $data['delivery_time'];
        $flat_rate       = $data['flat_rate'];
        $lga_list        = implode(',',$data['regions']);
        
        $shipping_region_id = date("Ymdhis");
         if($data['is_additional_rule_set'] == 1 && $shipping_rule == 2 )
         {
             $additional_shipping_rule =3;
         }
        elseif($data['is_additional_rule_set'] == 1 && $shipping_rule == 3 )
         {
             $additional_shipping_rule =2;
         }else
        {
            $additional_shipping_rule = NULL;
        }
        $sql = "INSERT INTO shipping_regions (title,id,state,lga_list,delivery_time,pickup_station,merchant_id,shipping_rule_type_id,created,posted_by,additional_shipping_rule) VALUES ('$title','$shipping_region_id','$state','$lga_list','$delivery_time','$pickup_station','$merchant_id','$shipping_rule',NOW(),'$posted_by','$additional_shipping_rule')";
        $result = $this->db_query($sql,FALSE);
        
        if($shipping_rule == 2 || $data['is_additional_rule_set'] == 1)
        {
            
            $weight_settings = $this->combineWeight($data["min_weight"],$data["max_weight"],$data["weight_shipping_rate"]);
            $number = count($weight_settings);
            $loop_count = 0;
            foreach($weight_settings as $row)
            {
                $maxPrice = ($loop_count == ($number -1))?"999999999999999999999999999999":$row['max'];
                $sql = "INSERT INTO shipping_fee_prices (minimum_value,maximum_value,shipping_fee,shipping_region_id,merchant_id,shipping_rule_type_id,created,posted_by) VALUES ('$row[min]','$maxPrice','$row[price]','$shipping_region_id','$merchant_id','2',NOW(),'$posted_by')";
                $result = $this->db_query($sql,FALSE);
            }
        }
        elseif($shipping_rule == 3 || $data['is_additional_rule_set'] == 1)
        {
            $additional_shipping_rule = ($data['is_additional_rule_set'] == 1)?2:NULL;
            $price_settings = $this->combineWeight($data["min_price"],$data["max_price"],$data["price_shipping_rate"]);
            $number = count($price_settings);
            $loop_count = 0;
            foreach($price_settings as $row)
            {
                $maxPrice = ($loop_count == ($number -1))?"999999999999999999999999999999":$row['max'];
                $sql = "INSERT INTO shipping_fee_prices (minimum_value,maximum_value,shipping_fee,shipping_region_id,merchant_id,shipping_rule_type_id,created,posted_by) VALUES ('$row[min]','$maxPrice','$row[price]','$shipping_region_id','$merchant_id','3',NOW(),'$posted_by')";
                $result = $this->db_query($sql,FALSE);
                $loop_count++;
            }
        }
        else
        {
            $shipping_fee = ($shipping_rule == 1)?$flat_rate:"0";
            $sql = "INSERT INTO shipping_fee_prices (minimum_value,maximum_value,shipping_fee,shipping_region_id,merchant_id,shipping_rule_type_id,created,posted_by,additional_shipping_rule) VALUES (NULL,NULL,'$shipping_fee','$shipping_region_id','$merchant_id','$shipping_rule',NOW(),'$posted_by',NULL)";
            $result = $this->db_query($sql,FALSE);
        }
        
        
        return json_encode(array("response_code"=>0,"response_message"=>"Saved Successfully"));
        
    }
    public function combineWeight($min,$max,$price)
    {
        $output = array();
        foreach($min as $key=>$value)
        {
            $output[]   = array("min"=>$min[$key],"max"=>$max[$key],"price"=>$price[$key]);
        }
        return $output;
    }
    
}