<?php

class Airline extends dbobject
{
    public function airlineList($data)
    {
        $table_name    = "airline";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			
			array( 'db' => 'airline_code', 'dt' => 1 ),
            array( 'db' => 'airline_name', 'dt' => 2  ),
			array( 'db' => 'provider_type',  'dt' => 3 ),
			array( 'db' => 'active',   'dt' => 4, 'formatter'=>function($d,$row){
                return  ($d == "1")?"YES":"NO";
            }  ),
			array( 'db' => 'id',   'dt' => 5, 'formatter'=>function($d,$row){
                 return  "<button class=\"btn btn-primary\"  onclick=\"getModal('setup/airline_setup.php?op=edit&airline_id=".$d."','modal_div')\"  href=\"javascript:void(0)\" data-toggle=\"modal\" data-target=\"#defaultModalPrimary\">Edit</button>";
            } ),
			array( 'db' => 'created',   'dt' => 6 )
			);
//		$filter = ($_SESSION['role_id_sess'] != 001)?" AND church_id = '$_SESSION[church_id_sess]'":"";
        $filter = "";
        $datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
   
    public function saveAirline($data)
    {
        $data['created'] = date("Y-m-d h:i:s");
        if($data['operation'] == 'new')
        {
            $count = $this->doInsert('airline',$data,array('operation','op','id'));
        }else
        {
            $count = $this->doUpdate('airline',$data,array('operation','op'),array('id'=>$data['id']));
        }
        if($count > 0){
            return json_encode(array("response_code"=>0,"response_message"=>"Saved successfully"));
        }else{
            return json_encode(array("response_code"=>56,"response_message"=>"No changes made"));
        }
    }
    public function saveChurchType($data)
    {
        $data['created'] = date("Y-m-d");
            if($data['operation'] == 'new')
            {
                $count = $this->doInsert('church_type',$data,array('operation','op','id'));
            }else
            {
                $count = $this->doUpdate('church_type',$data,array('operation','op'),array('id'=>$data['id']));
            }
        return $count;
    }
}