<?php

class Split extends dbobject
{
    public function splitList($data)
    {
		$table_name    = "splitting";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'min_amt', 'dt' => 1 ),
			array( 'db' => 'max_amt',  'dt' => 2 ),
			array( 'db' => 'church_type',   'dt' => 3, 'formatter' => function( $d,$row ) {
                    return $this->getitemlabel("church_type","id",$d,"name");
            } ),
            array( 'db' => 'percentage',  'dt' => 4,'formatter' => function( $d,$row ) {
						return $d."%";
					} ),
            array( 'db' => 'posted_user',  'dt' => 5 ),
            array( 'db' => 'code',     'dt' => 6, 'formatter' => function( $d,$row ) {
						return "<span onclick=\"deleteSplit('$d','$row[min_amt]','$row[max_amt]')\"  class=\"badge badge-danger\" style='cursor:pointer'><i class='fa fa-trash'></i> Delete</span>";
					}
				),
            array( 'db' => 'created',  'dt' => 7 )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function hqSplitList($data)
    {
		$table_name    = "splitting_state_hq";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'state_id', 'dt' => 1,'formatter'=>function($d,$row){
                return $this->getitemlabel('lga','stateid',$d,'State');
            } ),
			array( 'db' => 'account_number', 'dt' => 2 ),
			array( 'db' => 'bank_code', 'dt' => 3,'formatter'=>function($d,$row){
                return $this->getitemlabel('banks','bank_code',$d,'bank_name');
            }  ),
			array( 'db' => 'state_hq_share',  'dt' => 4 ),
			array( 'db' => 'hq_share',   'dt' => 5 ),
            array( 'db' => 'hq_share',   'dt' => 6,'formatter'=>function($d,$row){
                return $this->getitemlabel('church_table','church_type',"1",'account_no');
            } ),
            array( 'db' => 'id',   'dt' => 7,'formatter'=>function($d,$row){
                return "<button class=\"btn btn-primary\"  onclick=\"getModal('setup/splitting_hq_setup.php?op=edit&code=".$d."','modal_div')\"  href=\"javascript:void(0)\" data-toggle=\"modal\" data-target=\"#defaultModalPrimary\">Edit</button>";
            } ),
            array( 'db' => 'posted_by',  'dt' => 8),
            array( 'db' => 'created',  'dt' => 9 )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function deleteSplit($data)
    {
        $code = $data['split_code'];
        $sql = "DELETE FROM splitting WHERE code = '$code'";
        $this->db_query($sql);
        return json_encode(array("response_code"=>0,"response_message"=>"Deleted")); 
    }
    public function saveSplitStateHQ($data)
    {
        $data['posted_by'] = $_SESSION['username_sess'];
        $data['created']   = date("Y-m-d h:i:s");
        $total      = 0;
        $total      = $data['hq_share'] + $data['state_hq_share'];
        $validation = $this->validate($data,
                    array(
                        'hq_share'=>'required|int',
                        'state_hq_share'=>'required|int',
                        'state_id'=>'required',
                        'bank_code'=>'required',
                        'account_number'=>'required',
                        'account_name'=>'required'
                    ),
                    array('hq_share'=>'Head Quarters','state_hq_share'=>'State Head Quarters','state_id'=>'State','bank_code'=>'Bank Name','account_name'=>'Account Name','account_number'=>'Account Number')
                   );
        if(!$validation['error'])
        {
            if($total != 0)
            {
                if($total == 100)
                {
                    if($data['operation'] == 'new')
                    {
                        $count = $this->doInsert('splitting_state_hq',$data,array('op','operation','id','code'));
                        if($count == 1)
                        {
                            return json_encode(array("response_code"=>0,"response_message"=>"Record saved!"));
                        }else
                        {
                            return json_encode(array("response_code"=>698,"response_message"=>"Failed to save record. This state has already a split setup"));
                        }
                    }
                    if($data['operation'] == 'edit')
                    {
                        $count = $this->doUpdate('splitting_state_hq',$data,array('op','operation','id','code'),array('id'=>$data['id']));
                        if($count == 1)
                        {
                            return json_encode(array("response_code"=>0,"response_message"=>"Record saved!"));
                        }else
                        {
                            return json_encode(array("response_code"=>698,"response_message"=>"Failed to save record. This state has already a split setup"));
                        }
                    }
                    
                }else
                {
                    return json_encode(array("response_code"=>413,"response_message"=>"Total sum has to be 100"));
                }
            }else
            {
                return json_encode(array("response_code"=>453,"response_message"=>"You can't have a zero total"));
            }
        }
        else
        {
            return json_encode(array("response_code"=>853,"response_message"=>$validation['messages'][0]));
        }
        
        
    }
    public function saveSplit($data)
    {
        $total = 0;
        foreach($data['church_type'] as $amt)
        {
            
            if($amt < 0)
            {
                return json_encode(array("response_code"=>744,"response_message"=>"You can't have a negative number")); 
            }
            $total = $total + $amt;
            
        }
        if($total != 0)
        {
            if($total == 100)
            {
                $min        = trim($data['min_amt']);
                $max        = trim($data['max_amt']);
                if(!isset($data['infinite']))
                {
                  $validation = $this->minMaxValidation($min,$max);  
                }
                if(in_array(0,$data['church_type']))
                {
                   return json_encode(array("response_code"=>68,"response_message"=>"One or more value(s) in the split formular contains a zero. Navigate to the 'church type' menu to disable it from the split"));   
                }
                
                $max = (isset($data['infinite']))?'99000000000000':$max;
                if($validation['response_code'] == 0)
                {
                    if($data['operation'] == 'new')
                    {
                        $split_code = $this->generateSplitCode();
                        foreach($data['church_type'] as $key=>$value)
                        {
                            $sql = "INSERT INTO splitting (code,min_amt,max_amt,church_type,percentage,created,posted_user) VALUES('$split_code','$min','$max','$key','$value',NOW(),'$_SESSION[username_sess]')";
                            $this->db_query($sql);
                        }
                        return json_encode(array("response_code"=>0,"response_message"=>"Split saved successfully"));
                    }else
                    {
                        foreach($data['church_type'] as $key=>$value)
                        {
                             $sql = "UPDATE splitting SET min_amt = '$min', max_amt = '$max', percentage='$value',created = NOW(), posted_user='$_SESSION[username_sess]' WHERE church_type = '$key' AND code = '$data[code]' ";
                            $this->db_query($sql);
                        }
                        return json_encode(array("response_code"=>0,"response_message"=>"Split updated successfully"));
                    }
                }
                else
                {
                    return json_encode($validation);
                }
            }else
            {
                return json_encode(array("response_code"=>714,"response_message"=>"Total percentage must be 100"));
            }
        }else
        {
            return json_encode(array("response_code"=>714,"response_message"=>"Total percentage cannot be zero"));
        }
    }
    public function minMaxValidation($min,$max)
    {
        if($min == "" || $max == "")
        {
            return array('response_code'=>74,'response_message'=>'Minimu / maximum amount cannot be empty');
        }
        if($min > $max)
        {
            return array('response_code'=>74,'response_message'=>'Minimum amount cannot be greater than maximum amount');
        }
        if($min == $max)
        {
            return array('response_code'=>74,'response_message'=>'You entered the same amount for both minimum and maximum amount');
        }
        $sql    = "SELECT min_amt,max_amt FROM splitting";
        $result = $this->db_query($sql);
        $count = count($result);
        if($count > 0)
        {
            foreach($result as $row)
            {
                
                    if(($min <= $row['max_amt']) && ($min >= $row['min_amt']))
                    {
                        return array('response_code'=>74,'response_message'=>number_format($min).' falls between an existing split range of '.number_format($row['min_amt']).' and '.number_format($row['max_amt']));
                    }
            }
        }
        return array('response_code'=>0,'response_message'=>'OK');
    }
    private function generateSplitCode()
    {
        return $this->paddZeros($this->getnextid("splitting"),4);
    }
   
}