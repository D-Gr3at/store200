<?php
include_once("response.php");
class Advert extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
   
    public function saveAdvert($data)
    {
        $data['created'] = date("Y-m-d h:i:s");
        if($data['operation'] == "new")
        {
            
            $count   = $this->doInsert("advert",$data,array('operation','op','id'));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Category Created Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'Category Creation Failed'));
            }
        }else
        {
            $count   = $this->doUpdate("advert",$data,array('operation','op','id'),array('id'=>$data['id']));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Category Updated Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'No update made'));
            }
        }
    }
    
   
    
    public function advertList($data)
    {
        $table_name    = "advert";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'label',  'dt' => 1 ),
			array( 'db' => 'banner_path',  'dt' => 2 ),
			array( 'db' => 'expire_date',  'dt' => 3,'formatter' => function( $d,$row ) {
                
						return $d;
					} ),
            array( 'db' => 'link_range',  'dt' => 4 ),
            array( 'db' => 'target_id',  'dt' => 5,'formatter' => function( $d,$row ) {
                        if($row['link_range'] == "product")
                        {
                            return $this->getitemlabel("products","id",$d,"name");
                        }elseif($row['link_range'] == "brand")
                        {
                            return $this->getitemlabel("brand","id",$d,"name");
                        }elseif($row['link_range'] == "category")
                        {
                            return $this->getitemlabel("product_categories","id",$d,"name");
                        }else
                        {
                            return "All";
                        }
						
					} ),
            array( 'db' => 'status',  'dt' => 6,'formatter' => function( $d,$row ) {
                
						return ($d == "1")?"Active":"Inactive";
					} ),
            array( 'db' => 'id',  'dt' => 7 ),
			array( 'db' => 'created', 'dt' => 8, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
        $filter = " AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    
  
}