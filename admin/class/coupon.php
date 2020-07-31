<?php
include_once("response.php");
class Coupon extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
   
    public function saveCoupon($data)
    {
        $data['created']     = date("Y-m-d h:i:s");
        $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        $data['customer_link'] = ($data['set_customer_link'] == "*")?"*":$data['customer_link'];
        if($data['operation'] == "new")
        {
            $count   = $this->doInsert("coupon",$data,array('operation','op','set_customer_link'));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'coupon Created Successfully'));
            }
            else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'coupon Creation Failed'));
            }
        }
        else
        {
            $count   = $this->doUpdate("coupon",$data,array('operation','op','id','set_customer_link'),array('id'=>$data['id']));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'coupon Updated Successfully'));
            }
            else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'No update made'));
            }
        }
    }
    public function generateCouponID($data)
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $res = "";
        for ($i = 0; $i < 10; $i++) {
            $res .= $chars[mt_rand(0, strlen($chars)-1)];
        }
        return $res;
    }
    
    
    
    public function couponList($data)
    {
        $table_name    = "coupon";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'id', 'dt' => 1 ),
			array( 'db' => 'name',  'dt' => 2 ),
			array( 'db' => 'value',  'dt' => 3,'formatter'=>function($d,$row){
                return "&#x20A6 ".$d;
            } ),
			array( 'db' => 'expire_date',  'dt' => 4 ),
			array( 'db' => 'merchant_id',  'dt' => 5 ),
			array( 'db' => 'customer_link',  'dt' => 6, 'formatter' => function( $d,$row ) {
                return ($d == "*")?"<i class='fa fa-users'></i> All Customers":$d;
            } ),
			array( 'db' => 'used_status',  'dt' => 7,'formatter' => function( $d,$row ) {
                return ($d == "1")?"Used":"Not Used";
            } ),
			array( 'db' => 'is_active',  'dt' => 8,'formatter' => function( $d,$row ) {
                return ($d == "1")?"Active":"Inactive";
            } ),
			array( 'db' => 'used_date',  'dt' => 9 ),
			array( 'db' => 'used_by',  'dt' => 10 ),
			array( 'db' => 'id',  'dt' => 11,'formatter' => function( $d,$row ) {
                
						return '<a class="badge badge-warning" onclick="getModal(\'setup/coupon_setup.php?op=edit&coupon_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Coupon</a>';
					} ),
			array( 'db' => 'created', 'dt' => 12, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
		$filter = ($_SESSION['role_id_sess']=="001")?"":" AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function getCategory($data)
    {
        $merchant_id = $data['merchant_id'];
        $sql    = "SELECT * FROM product_categories";
        $result = $this->db_query($sql);
        $options = array();
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $options[] = array('id'=>$row['id'],'name'=>$row['name'],'merchant_id'=>$row['merchant_id']);
            }
            return json_encode(array('responseCode'=>0,'data'=>$options));
        }
        else
        {
            return json_encode(array('responseCode'=>77,'data'=>''));
        }
        
    }
  
}