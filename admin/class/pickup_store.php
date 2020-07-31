<?php

class PickupStore extends dbobject
{
   public function pickupStoreList($data)
    {
		$table_name    = "merchant_pickup_stores";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0),
            array( 'db' => 'title', 'dt' => 1),
            array( 'db' => 'address', 'dt' => 2),
            array( 'db' => 'state', 'dt' => 3, 'formatter'=>function($d,$row){
                return $this->getitemlabel('lga','state_code',$d,'State');
            }),
            array( 'db' => 'lga', 'dt' => 4, 'formatter'=>function($d,$row){
                return $this->getitemlabel('lga','Lgaid',$d,'Lga');
            }),
            array( 'db' => 'pickup_instructions',  'dt' => 5 ),
            array( 'db' => 'id',  'dt' => 6, 'formatter'=>function($d,$row){
                return "<button class='btn btn-warning'>Edit</button>";
            } ),
            array( 'db' => 'created',  'dt' => 7 )
			);
		$filter = "";
		$filter = ($_SESSION['role_id_sess']=="001")?"":" AND merchant_id='$_SESSION[merchant_sess_id]'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function savePickupLocation($data)
    {
        $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        $data['posted_by'] = $_SESSION['username_sess'];
        $data['created'] = date("Y-m-d h:i:s");
        $count = $this->doInsert('merchant_pickup_stores',$data,array('op','operation','id'));
        return json_encode(array("response_code"=>0,"response_message"=>"OK"));
    }
    
}