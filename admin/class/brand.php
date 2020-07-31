<?php
include_once("response.php");
class Brand extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
   
    public function saveBrand($data)
    {
        $data['created']     = date("Y-m-d h:i:s");
        $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        $data['posted_user'] = $_SESSION['username_sess'];
        if($data['operation'] == "new")
        {
            $count   = $this->doInsert("brand",$data,array('operation','op','id'));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'brand Created Successfully'));
            }
            else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'brand Creation Failed'));
            }
        }
        else
        {
            $count   = $this->doUpdate("brand",$data,array('operation','op','id'),array('id'=>$data['id']));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'brand Updated Successfully'));
            }
            else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'No update made'));
            }
        }
    }
 
    
    
    
    public function brandList($data)
    {
        $table_name    = "brand";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'merchant_id',  'dt' => 2 ),
			
			array( 'db' => 'id',  'dt' => 3,'formatter' => function( $d,$row ) {
                
						return '<a class="badge badge-warning" onclick="getModal(\'setup/brand_setup.php?op=edit&brand_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Brand</a>';
					} ),
			array( 'db' => 'created', 'dt' => 4, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
		$filter = " AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function getBrand($data)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql    = "SELECT * FROM brand WHERE merchant_id = '$merchant_id'";
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