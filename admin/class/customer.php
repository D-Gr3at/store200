<?php
include_once("response.php");
class Customer extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
   
    public function saveCustomer($data)
    {
        $data['created'] = date("Y-m-d h:i:s");
        if($data['operation'] == "new")
        {
            
            $count   = $this->doInsert("customer_registration",$data,array('operation','op','id'));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Customer Created Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'Customer Creation Failed'));
            }
        }else
        {
            $count   = $this->doUpdate("customer_registration",$data,array('operation','op','id'),array('email'=>$data['email']));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Customer Updated Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'No update made'));
            }
        }
    }
    
    
    
    public function customerList($data)
    {
        $table_name    = "userdata_customer";
		$primary_key   = "username";
		$columner = array(
			array( 'db' => 'username', 'dt' => 0 ),
			array( 'db' => 'first_name',  'dt' => 1 ),
			array( 'db' => 'last_name',  'dt' => 2 ),
			array( 'db' => 'email',  'dt' => 3 ),
			array( 'db' => 'phone',  'dt' => 4 ),
			array( 'db' => 'sex',  'dt' => 5 ),
			array( 'db' => 'address',  'dt' => 6 ),
			array( 'db' => 'state_lga',  'dt' => 7 ),
			array( 'db' => 'state_lga',  'dt' => 8 ),
			
			array( 'db' => 'created', 'dt' => 9, 'formatter' => function( $d,$row ) {
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