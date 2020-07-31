<?php

class Collection extends dbobject
{
    public function collectionTypeList($data)
    {
        $table_name    = "collection_type";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			
			array( 'db' => 'name', 'dt' => 1 ),
			array( 'db' => 'id',   'dt' => 2 , 'formatter'=>function($d,$row){
                return  "<button class=\"btn btn-primary\"  onclick=\"getModal('setup/collection_type_setup.php?op=edit&id=".$d."','modal_div')\"  href=\"javascript:void(0)\" data-toggle=\"modal\" data-target=\"#defaultModalPrimary\">Edit</button>";
            } ),
			array( 'db' => 'category_id',   'dt' => 3, 'formatter'=>function($d,$row)
                  {
                      return $this->getitemlabel("collection_category","id",$d,"name");
                  }),
			array( 'db' => 'created',   'dt' => 4 )
			);
		$filter = "";
        $datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function saveCollectionType($data)
    {
        $validation = $this->validate($data,
                    array(
                        'name'=>'required',
                        'category_id'=>'required'
                    ),
                    array('name'=>'Collection Name','category_id'=>'Category ID')
                   );
        if(!$validation['error'])
        {
            $data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == 'new')
            {
                $count = $this->doInsert('collection_type',$data,array('operation','op','id'));

            }else
            {
                $count = $this->doUpdate('collection_type',$data,array('operation','op'),array('id'=>$data['id']));
            }
            if($count == 1)
            {

                return json_encode(array("response_code"=>0,"response_message"=>"collection type saved successfully"));
            }
            else
            {
                return json_encode(array("response_code"=>74,"response_message"=>"Did not save nor update any record"));
            }
        }else
        {
            return json_encode(array("response_code"=>374,"response_message"=>$validation['messages'][0]));
        }
        
    }
    
    
    public function postCollectionList($data)
    {
        $table_name    = "collections_basket";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'church_id', 'dt' => 1, 'formatter'=>function($d,$row){
                return $this->getitemlabel('church_table','church_id',$d,'church_name');
            } ),
			array( 'db' => 'payment_id', 'dt' => 2, 'formatter'=>function($d,$row){
                if($row['accountant_approval'] == 1 && $row['monitoring_unit_approval'] == 1 && $row['head_usher_approval'] == 1)
                {
                    return $d;
                }else
                {
                    return "xxx-xxx";
                }
            } ),
            array( 'db' => 'payment_id', 'dt' => 3, 'formatter'=>function($d,$row){
                return "&#8358; ".number_format($this->getitemlabel("remittance","payment_id",$d,"amount_collected"),2);
            } ),
            array( 'db' => 'amount', 'dt' => 4, 'formatter'=>function($d,$row){
                return "<b style='font-size:16px'>&#8358; ".number_format($d,2)."</b>";
            } ),
            array( 'db' => 'payment_status', 'dt' => 5, 'formatter'=>function($d,$row){
//                return $d;
                
                    if($d == '303')
                    {
                        return "Payment schedule not generated yet";
                    }else if($d == '00')
                    {
                        return "<span class='badge badge-success'><i class='fa fa-check'></i> Payment has been made</span>";
                    }else if($d == '99')
                    {
                        return "Awaiting payment";
                    }
                
            } ),
            array( 'db' => 'date_of_collection', 'dt' => 6 ),
            array( 'db' => 'accountant_approval', 'dt' => 7, 'formatter'=>function($d,$row){
                return ($d==1)?"<span class='badge badge-success'><i class='fa fa-check'></i> Accepted</span>":"<span class='badge badge-info'><i class='fa fa-exclamation-circle'></i> Pending</span>";
            }  ),
            array( 'db' => 'monitoring_unit_approval', 'dt' => 8, 'formatter'=>function($d,$row){
                return ($d==1)?"<span class='badge badge-success'><i class='fa fa-check'></i> Accepted</span>":"<span class='badge badge-info'><i class='fa fa-exclamation-circle'></i> Pending</span>";
            }  ),
            array( 'db' => 'head_usher_approval', 'dt' => 9, 'formatter'=>function($d,$row){
                return ($d==1)?"<span class='badge badge-success'><i class='fa fa-check'></i> Accepted</span>":"<span class='badge badge-info'><i class='fa fa-exclamation-circle'></i> Pending</span>";
            }  ),
			array( 'db' => 'is_suspended',   'dt' => 10,'formatter'=>function($d,$row){
                $is_suspended = $d;
                $d = $row['id'];
                
                $view_details = "<button class='btn btn-warning btn-sm' onclick=\"getModal('view_collection_breakdown.php?id=$d','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Breakdown</button>";
                $action       = "";
                $suspension = "";
                $split_dist = "<button class='btn btn-secondary' onclick=\"getModal('view_split_distribution.php?payment_id=$row[payment_id]','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Remittance</button>";
                file_put_contents("jjd.txt",json_encode($row));
                
                if($row['accountant_approval'] == 1 && $row['monitoring_unit_approval'] == 1 && $row['head_usher_approval'] == 1)
                {
                    if($_SESSION['role_id_sess'] == "002")
                    {
                        $church_name = $this->getitemlabel('church_table','church_id',$row[church_id],'church_name');
                        $invoice = "<button onclick='printDiv(\"$row[payment_id]\",\"$row[amount]\",\"$church_name\")' class='btn btn-info'>Print Collection Invoice</button>";
                    }
                    
                }
                else
                {
                    if($_SESSION['role_id_sess'] == "002" && $row['is_suspended'] == "0" )
                    {
                        $suspension = "<button class='btn btn-danger' onclick='suspend_collection(\"$d\",\"$is_suspended\")'>Suspend</button>";
                    }elseif($_SESSION['role_id_sess'] == "002" && $row['is_suspended'] == "1" )
                    {
                        $suspension = "<button class='btn btn-primary' onclick='suspend_collection(\"$d\",\"$is_suspended\")'>Unsuspend</button>";
                    }
                }
                if($_SESSION['role_id_sess'] == "008" )
                {
                    if($row['monitoring_unit_approval'] != 1 && $row['is_suspended'] == "0")
                    {
                        $action = "<button style='margin-top:5px' class='btn btn-primary' onclick=\"approve_collection('$d')\">APPROVE COLLECTION</button>";
                    }elseif($row['monitoring_unit_approval'] != 1)
                    {
                        $action = "<small style='color:red'>This collection has been suspended by the Accountant</span><span  onclick=\"getModal('suspension_reason.php?id=".$row[id]."','modal_div')\"  data-toggle='modal' data-target='#defaultModalPrimary' class='badge badge-info' style='cursor:pointer'><i class='fa fa-question-circle'></i> See why</span>";  
                    }
                }
                if($_SESSION['role_id_sess'] == "004" )
                {
                    if($row['head_usher_approval'] != 1 && $row['is_suspended'] == "0")
                    {
                        $action = "<button style='margin-top:5px' class='btn btn-primary btn-sm' onclick=\"approve_collection('$d')\">APPROVE COLLECTION</button>";
                    }elseif($row['head_usher_approval'] != 1)
                    {
                        $action = "<small style='color:red'>This collection has been suspended by the Accountant</span><span  onclick=\"getModal('suspension_reason.php?id=".$row[id]."','modal_div')\"  data-toggle='modal' data-target='#defaultModalPrimary' class='badge badge-info' style='cursor:pointer'><i class='fa fa-question-circle'></i> See why</span>";
                    }
                }
                return $view_details.$action.$split_dist.$suspension.$invoice;
            } ),
			array( 'db' => 'created',   'dt' => 11)
			);
        $filter = "";
        if($_SESSION['role_id_sess'] != 001)
        {
            $filter = " AND church_id = '$_SESSION[church_id_sess]'";
        }
		
        $datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function postCollectionListAdmin($data)
    {
        $table_name    = "collections_basket";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'church_id', 'dt' => 1, 'formatter'=>function($d,$row){
                return $this->getitemlabel('church_table','church_id',$d,'church_name');
            } ),
			array( 'db' => 'payment_id', 'dt' => 2, 'formatter'=>function($d,$row){
                if($row['accountant_approval'] == 1 && $row['monitoring_unit_approval'] == 1 && $row['head_usher_approval'] == 1)
                {
                    return $d;
                }else
                {
                    return "xxx-xxx";
                }
            } ),
            array( 'db' => 'payment_id', 'dt' => 3, 'formatter'=>function($d,$row){
                return "&#8358; ".number_format($this->getitemlabel("remittance","payment_id",$d,"amount_collected"),2);
            } ),
            array( 'db' => 'amount', 'dt' => 4, 'formatter'=>function($d,$row){
                return "<b style='font-size:16px'>&#8358; ".number_format($d,2)."</b>";
            } ),
            array( 'db' => 'payment_status', 'dt' => 5, 'formatter'=>function($d,$row){
//                return $d;
                
                    if($d == '303')
                    {
                        return "Payment schedule not generated yet";
                    }else if($d == '00')
                    {
                        return "<span class='badge badge-success'><i class='fa fa-check'></i> Payment has been made</span>";
                    }else if($d == '99')
                    {
                        return "Awaiting payment";
                    }
                
            } ),
            array( 'db' => 'payment_id', 'dt' => 6,'formatter'=>function($d,$row){
                $ds = $row['id'];
                
                $view_details = "<button class='btn btn-warning btn-sm' onclick=\"getModal('view_collection_breakdown.php?id=$ds','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Breakdown</button>";
                $action       = "";
                $suspension = "";
                $split_dist = "<button class='btn btn-secondary' onclick=\"getModal('view_split_distribution.php?payment_id=$d','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Remittance</button>";
                return $view_details.$split_dist;
            } ),
            array( 'db' => 'date_of_collection', 'dt' => 7 ),
			
			array( 'db' => 'created',   'dt' => 8)
			);
        $filter = "";
        if($_SESSION['role_id_sess'] == "005")
        {
            $region_filter = ($data['church_region'] == "")?"":" AND church_id = '$data[church_region]'";
        }
        if($_SESSION['role_id_sess'] == "006")
        {
            $state_filter  = " AND church_id IN (SELECT church_id FROM church_table WHERE state = '$data[church_state]') ";
            $region_filter = ($data[church_region] == "")?"":" AND church_id IN (SELECT church_id FROM church_table WHERE church_type = '2')";
            $branch_filter = ($data[branch_ch] == "")?"":" AND church_id = '$data[branch_ch]'";
        }
		$filter = $filter.$state_filter.$region_filter.$branch_filter;
        $datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    
    public function hqPostCollectionList($data)
    {
        $table_name    = "collections_basket";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'church_id', 'dt' => 1, 'formatter'=>function($d,$row){
                return $this->getitemlabel('church_table','church_id',$d,'church_name');
            } ),
			array( 'db' => 'payment_id', 'dt' => 2, 'formatter'=>function($d,$row){
                if($row['accountant_approval'] == 1 && $row['monitoring_unit_approval'] == 1 && $row['head_usher_approval'] == 1)
                {
                    return $d;
                }else
                {
                    return "xxx-xxx";
                }
            } ),
            array( 'db' => 'amount', 'dt' => 3, 'formatter'=>function($d,$row){
                return "<b style='font-size:16px'>&#8358; ".number_format($d,2)."</b>";
            } ),
            array( 'db' => 'payment_status', 'dt' => 4, 'formatter'=>function($d,$row){
//                return $d;
                
                    if($d == '303')
                    {
                        return "Payment schedule not generated yet";
                    }else if($d == '00')
                    {
                        return "<span class='badge badge-success'><i class='fa fa-check'></i> Payment has been made</span>";
                    }else if($d == '99')
                    {
                        return "Awaiting payment";
                    }
                
            } ),
            array( 'db' => 'date_of_collection', 'dt' => 5 ),
            array( 'db' => 'accountant_approval', 'dt' => 6, 'formatter'=>function($d,$row){
                return ($d==1)?"<span class='badge badge-success'><i class='fa fa-check'></i> Accepted</span>":"<span class='badge badge-info'><i class='fa fa-exclamation-circle'></i> Pending</span>";
            }  ),
            array( 'db' => 'monitoring_unit_approval', 'dt' => 7, 'formatter'=>function($d,$row){
                return ($d==1)?"<span class='badge badge-success'><i class='fa fa-check'></i> Accepted</span>":"<span class='badge badge-info'><i class='fa fa-exclamation-circle'></i> Pending</span>";
            }  ),
            array( 'db' => 'head_usher_approval', 'dt' => 8, 'formatter'=>function($d,$row){
                return ($d==1)?"<span class='badge badge-success'><i class='fa fa-check'></i> Accepted</span>":"<span class='badge badge-info'><i class='fa fa-exclamation-circle'></i> Pending</span>";
            }  ),
			array( 'db' => 'is_suspended',   'dt' => 9,'formatter'=>function($d,$row){
                $is_suspended = $d;
                $d = $row['id'];
                
                $view_details = "<button class='btn btn-warning btn-sm' onclick=\"getModal('view_collection_breakdown.php?id=$d','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Breakdown</button>";
               
                $split_dist = "<button class='btn btn-secondary' onclick=\"getModal('view_split_distribution.php?payment_id=$row[payment_id]','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>View Remittance</button>";
                
                return $view_details.$split_dist;
            } ),
			array( 'db' => 'created',   'dt' => 10)
			);
        $filter = "";
        
        $datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    } 
    public function collectionPosting($data)
    {
        $total = 0;
        foreach($data['collection'] as $amt)
        {
            if($amt < 0)
            {
                return json_encode(array("response_code"=>744,"response_message"=>"You can't have a negative number")); 
            }
            $total = $total + $amt;
        }
        if($total != 0)
        {
            $data['collection_id'] = json_encode($data['collection']);
            $data['amount']        = $total;
            $data['created']       = date("Y-m-d h:i:s");
            if($data['operation'] == 'new')
            {
                $data['payment_id'] = $this->generatePaymentID();
                $data['church_id']  = $_SESSION['church_id_sess'];
                $data['posted_by']  = $_SESSION['username_sess'];
                $church_type          = $this->getitemlabel('church_table','church_id',$data['church_id'],'church_type');
                
                $resp = $this->calRetainedAmount($total,$church_type);
                if($resp['response_code'] == 0)
                {
                    $church_infos              = $this->getItemLabelArr('church_table',array('church_id'),array($data['church_id']),array('church_type','state','church_region'));
                    $data['amount']            = $resp['data']['remit_amt'];
                    $data['state_id']          = $church_infos['state'];
                    $data['church_type']       = $church_infos['church_type'];
                    $data['church_region_id']  = $church_infos['church_region'];
                    $count              = $this->doInsert('collections_basket',$data,array('operation','op','collection'));
                    if($count == 1)
                    {
                        $query_data = array(
                                    'payment_id'=>$data['payment_id'],
                                    'church_id'=>$data['church_id'],
                                    'amount_collected'=>$total,
                                    'retained_amount'=>$resp['data']['retained_amt'],
                                    'remitted_amount'=>$resp['data']['remit_amt'],
                                    'percentage'=>$resp['data']['percentage'],
                                    'pastor_percentage'=>$resp['data']['pastor_percentage'],
                                    'state_percentage'=>$resp['data']['state_percentage'],
                                    'national_percentage'=>$resp['data']['national_percentage'],
                                    'created'=>date('Y-m-d h:i:s'),
                                    'posted_user'=>$_SESSION['username_sess']
                        );
//                        var_dump($query_data);
                        $counter   = $this->doInsert('remittance',$query_data,array('operation','op','collection'));
                        $users = new Users();
                        $msg    = "Good Day Sir/Madam,\n The Accountant has just posted a collection, and needs your approval.\n Kindly login to the portal to approve collection";
                        $users->notifyChurchUsers($data['church_id'],array('004','008'),$msg);
                        
                        return json_encode(array("response_code"=>0,"response_message"=>"successfully saved and awaiting approval"));
                    }
                    else
                    {
                        return json_encode(array("response_code"=>74,"response_message"=>"Did not save nor update any record"));
                    }
                }
                else
                {
                    return json_encode($resp);
                }
            }
        }else
        {
            return json_encode(array("response_code"=>714,"response_message"=>"Total amount cannot be zero"));
        }
        
    }
    public function calRetainedAmount($total,$church_type)
    {
        if($church_type == "3")
        {
            $sql = "SELECT * FROM splitting_state_hq WHERE state_id = '$_SESSION[state_id_sess]'";
            $result = $this->db_query($sql);
            if(count($result) > 0)
            {
                $state_hq_percentage = $result[0]['state_hq_share'];
//                $hq_percentage = $result[0]['hq_share'];
                $retained_amt = ($state_hq_percentage/100) * $total;
                
                
                
                $national_percentage = $result[0]['hq_share'];
                    
                $remit_amt    = $total - $retained_amt;
                return array('response_code'=>0,'response_message'=>'OK','data'=>array('percentage'=>$state_hq_percentage,'retained_amt'=>$retained_amt,'remit_amt'=>$remit_amt,'national_percentage'=>$national_percentage));
            }
            else
            {
                $state_name = $this->getitemlabel('lga','stateid',$_SESSION[state_id_sess],'State');
                return array('response_code'=>84,'response_message'=>'Your church (state headquarters for '.$state_name.' state) has not been setup for collection splitting, kindly contact the administrator');
            }
        }else
        {
            $sql = "SELECT percentage,code FROM splitting WHERE church_type = '$church_type' AND (min_amt <= '$total' AND max_amt >= '$total')";
            $result = $this->db_query($sql);
            if(count($result) > 0)
            {
                $percentage = $result[0]['percentage'];
                $retained_amt = ($percentage/100) * $total;
                
                $code = $result[0]['code'];
                $sql2 = "SELECT church_type,percentage FROM splitting WHERE code = '$code' AND church_type IN ('3','1','5')";
                $rr   = $this->db_query($sql2);
                foreach($rr as $row)
                {
                    if($row['church_type'] == 3)
                        $state_percentage  = $row['percentage'];
                    if($row['church_type'] == 1)
                        $national_percentage = $row['percentage'];
                    if($row['church_type'] == 5)
                        $pastor_percentage = $row['percentage'];
                }
                
                
                $pastor_amt = ($pastor_percentage/100) * $total;
                $remit_amt  = $total - ($retained_amt + $pastor_amt);
                return array('response_code'=>0,'response_message'=>'OK','data'=>array('percentage'=>$percentage,'retained_amt'=>$retained_amt,'remit_amt'=>$remit_amt,'pastor_percentage'=>$pastor_percentage,'state_percentage'=>$state_percentage,'national_percentage'=>$national_percentage));
            }
            else
            {
                return array('response_code'=>84,'response_message'=>$total.' does not fall into any splitting formular, kindly contact the administrator');
            }
        }
         
    }
    function suspendCollection($data)
    {
        $church_id  = $_SESSION['church_id_sess'];
        $username  = $_SESSION['username_sess'];
        $id = $data['collection_id'];
        $why = (isset($data['reason']))?$data['reason']:"";
        $suspension_status = ($data['suspension'] == "1")?0:1;
          $sql = "UPDATE collections_basket SET is_suspended = '$suspension_status',suspension_date = NOW(),suspended_by = '$username',suspension_note='$why' WHERE id = '$id' AND church_id = '$church_id' ";
        $res = $this->db_query($sql,false);
        if($res == 1)
        {
            $message = ($data['suspension'] == "1")?"Collection has been unsuspended":"Collection has been suspended";
            return json_encode(array("response_code"=>0,"response_message"=>$message));
        }else
        {
            return json_encode(array("response_code"=>350,"response_message"=>"Collection status update failed"));
        }
    }
    public function approveCollection($data)
    {
        $collection_id = $data['collection_id'];
        $church_id  = $_SESSION['church_id_sess'];
        
        $outp = $this->validatePartiesToRemittance($_SESSION['state_id_sess']);
        if($outp[response_code] == 0)
        {
            if($_SESSION['role_id_sess'] == "008")
                $sql = "UPDATE collections_basket SET monitoring_unit_approval = '1' WHERE id = '$collection_id' AND church_id = '$church_id'";
            elseif($_SESSION['role_id_sess'] == "004")
                $sql = "UPDATE collections_basket SET head_usher_approval = '1' WHERE id = '$collection_id' AND church_id = '$church_id'";


            $res = $this->db_query($sql,false);
            if($res == 1)
            {
                $ss = "SELECT monitoring_unit_approval,payment_id,created,amount FROM collections_basket WHERE id = '$collection_id' AND church_id = '$church_id' AND monitoring_unit_approval = '1' AND head_usher_approval = '1' AND accountant_approval = '1'";
                $result = $this->db_query($ss);
                if(count($result) == 1)
                {
                        
                    $saw     = "UPDATE collections_basket SET payment_status = '99' WHERE id = '$collection_id' AND church_id = '$church_id'";
                    $result2 = $this->db_query($saw,false);
                    
                    //push to transaction table
                    $church_state = $this->getitemlabel('church_table','church_id',$church_id,'state');
                    $this->collectionAllocation(array('paying_church_id'=>$church_id,'payment_id'=>$result[0][payment_id], 'church_state'=>$church_state));
                    $users = new Users();
                    $date          = date_create($result[0][created]);
                    $creation_date = date_format($date,"l, jS F Y");
                    $msg    = "Good Day Sir/Madam,\n The collection you posted on ".$creation_date." has been approved by all approving parties.\n Kindly use this portal id ".$result[0][payment_id]."  to pay at the bank. \n Amount to remit = NGN ".$result[0][amount].". \n You can login to verify these details";
                    $users->notifyChurchUsers($church_id,array('002'),$msg);
                }

                return json_encode(array("response_code"=>0,"response_message"=>"Approval successful"));
            }else
            {
                return json_encode(array("response_code"=>78,"response_message"=>"Approval Failed"));
            }
        }else
        {
            return json_encode(array("response_code"=>798,"response_message"=>$outp[response_message]));
        }
        
    }
    private function generatePaymentID()
    {
//        return substr(strtoupper(uniqid($_SESSION['church_id_sess'])), 0, -5);
        return date('ymd').$this->getnextid(date('Y_m_d'));
    }
    private function validatePartiesToRemittance($church_state)
    {
        $sql    = "SELECT id FROM church_type WHERE part_of_split = '1' AND part_of_church_creation = '1'";
        $result = $this->db_query($sql);
        foreach($result as $row)
        {
            if($row['id'] == '3')
            {
                $sql = "SELECT church_id FROM church_table WHERE church_type = '3' AND state = '$church_state' LIMIT 1 ";
                $rr  = $this->db_query($sql,false);
                if($rr != 1)
                {
                    return array('response_code'=>25,'response_message'=>'There is no State HeadQuarters Church in your state');
                }
            }elseif($row['id'] == '1')
            {
                $sql = "SELECT church_id FROM church_table WHERE church_type = '$row[id]'  LIMIT 1 ";
                $rr  = $this->db_query($sql,false);
                if($rr != 1)
                {
                    return array('response_code'=>257,'response_message'=>'There is no head quarters church on the system');
                }
            }
            elseif($row['id'] == '2' && $_SESSION['church_type_id_sess'] == '2')
            {
                $sql = "SELECT church_id FROM church_table WHERE church_type = '$row[id]'  LIMIT 1 ";
                $rr  = $this->db_query($sql,false);
                if($rr != 1)
                {
                    return array('response_code'=>257,'response_message'=>'There is no head quarters church on the system');
                }
            }
        }
        return array('response_code'=>0,'response_message'=>'OK');
    }
    public function getRegions($data)
    {
        $state_id = $data['state'];
        $sql = "SELECT * FROM church_table WHERE church_type = '2' AND state = '$state_id'";
        $regions = "";
        $res = $this->db_query($sql);
        if(count($res) > 0)
        {
           foreach($res as $row)
           {
               $regions = $regions."<option value='".$row[church_id]."'>".$row[church_name]."</option>";
           }
        }
        else
        {
            $regions = "<option value=''>NO REGION CHURCH IN THIS STATE".$this->getitemlabel('')."</option>";
        }
        return $regions;
    }
    public function getBranch($data)
    {
        $region = $data['regions'];
        $sql    = "SELECT church_id,church_name FROM church_table WHERE church_type = '4' AND church_region = '$region' AND state = '$_SESSION[state_id_sess]'";
        $regions = "";
        $res = $this->db_query($sql);
        if(count($res) > 0)
        {
            $regions    = $regions.'<option value="">:: ALL BRANCH CHURCH ::</option>';
           foreach($res as $row)
           {
               $regions = $regions."<option value='".$row[church_id]."'>".$row[church_name]."</option>";
           }
        }
        else
        {
            $regions = "<option value=''>NO BRANCH CHURCH IN THIS CHURCH REGION</option>";
        }
        return $regions;
    }
   
    public function getLga($data)
    {
        $state  = $data['state'];
        $sql    = "SELECT Lga,Lgaid FROM lga WHERE stateid = '$state' order by Lga";
        $result = $this->db_query($sql);
        $data   = "";
        foreach($result as $row)
        {
            $data.= "<option value='".$row['Lgaid']."'>".$row['Lga']."</option>";
        }
        return $data;
    }
    public function getAccountName($data)
    {
        $account_number = $data['account_no'];
        $bank_code = $data['bank_code'];
        $token          = json_decode($this->getToken(),true);
        $account        = json_decode($this->validateAccount($token['token'],$account_number,$bank_code),true);
        if(is_array($account['data']))
        {
            echo $account['data']['account_name'];
        }else
        {
            echo "Unable to verify account, kindly confirm the account number and try again.";
        }
        
    }
     public function collectionAllocation($data)//($paying_church_id,$payment_id,$church_state)
    {
        $paying_church_id = $data['paying_church_id'];
        $payment_id = $data['payment_id'];
        $church_state = $data['church_state'];
         $sql = "SELECT state,church_type,church_region,lga FROM church_table WHERE church_id = '$paying_church_id' LIMIT 1";
         $pay_church = $this->db_query($sql);
         $s =$pay_church[0][state];
        $ct =$pay_church[0][church_type];
        $l =$pay_church[0][lga];
        
        $remittance_sql     = "SELECT * FROM remittance WHERE payment_id = '$payment_id' LIMIT 1";
        $remittance_result  = $this->db_query($remittance_sql);
        $total_collected    = $remittance_result[0]['amount_collected'];
        $retained_amount    = $remittance_result[0]['retained_amount'];
        $remitted_amount    = $remittance_result[0]['remitted_amount'];
        $paying_church_type = $this->getitemlabel('church_table','church_id',$paying_church_id,'church_type');
        
        $sql              = "SELECT id FROM church_type WHERE part_of_split = '1' AND id <> '$paying_church_type'";
        $result           = $this->db_query($sql);
         $result[] = array('id'=>'003'); // adding pastor to the splitting
        $transaction_info = array();
        if($paying_church_type == '3') // when the state hq is paying the splitting is just with the HQ
        {
            $details = $this->receivingHqDetails($total_collected);
            if($details != null)
                {
                    $transaction_info[] = array('church_id'=>$details[church_id], 'bank'=>$details[bank_code], 'account_no'=>$details[account_no], 'account_name'=>$details[account_name], 'total_collected'=>$total_collected, 'remitted_amount'=>$remitted_amount, 'church_percentage'=>$details[percentage],'value'=>$details[percentage_value],'church_type'=>1,'payment_id'=>$payment_id,'receiving_state_id'=>$details[state],'receiving_church_type'=>$details[church_type],'receiving_lga'=>$details[lga],'receiving_church_region'=>$details[church_region]);
                }
        }else
        {
            foreach($result as $row)
            {
                $details = $this->receivingChurchSplitDetails($row[id],$total_collected,$paying_church_id,$church_state);
                if($details != null)
                {
                    $transaction_info[] = array('church_id'=>$details[church_id], 'bank'=>$details[bank_code], 'account_no'=>$details[account_no], 'account_name'=>$details[account_name], 'total_collected'=>$total_collected, 'remitted_amount'=>$remitted_amount, 'church_percentage'=>$details[percentage],'value'=>$details[percentage_value],'church_type'=>$row[id],'payment_id'=>$payment_id,'receiving_state_id'=>$details[state],'receiving_church_type'=>$details[church_type],'receiving_lga'=>$details[lga],'receiving_church_region'=>$details[church_region]);
                }
            }
        }
        
        foreach($transaction_info as $r)
        {
            
            $trans_id = $r[church_id].uniqid();
            $ip       = $_SERVER['REMOTE_ADDR'];
             $sql = "INSERT INTO transaction_table(transaction_id,source_acct,destination_acct,transaction_desc,transaction_amount,church_id,church_type,payment_id,destination_bank,account_name,percentage,posted_ip,created,response_code,response_message,payment_mode,posted_user,receiving_state_id,receiving_church_type,receiving_lga_id,paying_state_id,paying_church_type,paying_lga_id) VALUES('$trans_id', '$paying_church_id', '$r[account_no]', 'Collection remittance','$r[value]','$r[church_id]','$r[church_type]','$r[payment_id]','$r[bank]','$r[account_name]', '$r[church_percentage]','$ip',NOW(),'99','initiated','NIBSS','$_SESSION[username_sess]','$r[receiving_state_id]','$r[receiving_church_type]','$r[receiving_lga]','$s','$ct','$l')";
//            file_put_contents('patapa.txt',"[".date('h:i:s')."] ".$sql.PHP_EOL."\r\n",FILE_APPEND);
            $result = $this->db_query($sql,false);
        }
    }
    public function receivingHqDetails($amt)
    {
            $sql                   = "SELECT * FROM church_table WHERE church_type = '1' LIMIT 1";
            $result                = $this->db_query($sql);
            $receiving_church_id   = $result[0]['church_id'];
            $receiving_bank        = $result[0]['bank_code'];
            $receiving_acc_no      = $result[0]['account_no'];
            $receiving_acc_name    = $result[0]['account_name'];
            $receiving_state       = $result[0]['state'];
            $receiving_lga       = $result[0]['lga'];
            $receiving_church_type = $result[0]['church_type'];
            $receiving_region      = $result[0]['church_region'];
            $sql                   = "SELECT hq_share FROM splitting_state_hq WHERE state_id = '$_SESSION[state_id_sess]' LIMIT 1";
            //             Get percentage based on the church type
            $rr                  = $this->db_query($sql);
            if(count($result) > 0) // if it found a church
            {
                $percentage          = $rr[0]['hq_share'];
                $percentage_value    = ($percentage/100)*$amt;
                return array('church_id'=>$receiving_church_id, 'bank_code'=>$receiving_bank, 'account_no'=>$receiving_acc_no, 'account_name'=>$receiving_acc_name,'percentage'=>$percentage,'percentage_value'=>$percentage_value,'state'=>$receiving_state,'lga'=>$receiving_lga,'church_type'=>$receiving_church_type,'church_region'=>$receiving_region);
            }else
            {
                return null;
            }
    }
    public function receivingChurchSplitDetails($church_type,$amt,$paying_church_id,$church_state)
    {
        
        if($church_type == 1) //headquarters
        {
            $sql                 = "SELECT * FROM church_table WHERE church_type = '$church_type' LIMIT 1";
            $result              = $this->db_query($sql);
            $receiving_church_id = $result[0]['church_id'];
            $receiving_bank      = $result[0]['bank_code'];
            $receiving_acc_no    = $result[0]['account_no'];
            $receiving_acc_name  = $result[0]['account_name'];
            $receiving_state       = $result[0]['state'];
            $receiving_lga       = $result[0]['lga'];
            $receiving_church_type = $result[0]['church_type'];
            $receiving_region      = $result[0]['church_region'];
            $sql                 = "SELECT percentage FROM splitting WHERE min_amt <= '$amt' AND max_amt >= '$amt' AND church_type = '$church_type' LIMIT 1";
            //             Get percentage based on the church type
            $rr                  = $this->db_query($sql);
            if(count($result) > 0) // if it found a church
            {
                $percentage          = $rr[0]['percentage'];
                $percentage_value    = ($percentage/100)*$amt;
                return array('church_id'=>$receiving_church_id, 'bank_code'=>$receiving_bank, 'account_no'=>$receiving_acc_no, 'account_name'=>$receiving_acc_name,'percentage'=>$percentage,'percentage_value'=>$percentage_value,'state'=>$receiving_state,'lga'=>$receiving_lga,'church_type'=>$receiving_church_type,'church_region'=>$receiving_region);
            }else
            {
                return null;
            }
        }
        if($church_type == 5) //pastor
        {
            $sql        = "SELECT account_no,account_name,bank_name,church_id  FROM userdata WHERE church_id = '$paying_church_id' AND role_id = '003' LIMIT 1";
            $result     = $this->db_query($sql);
            $receiving_church_id = $result[0]['church_id'];
            $receiving_bank      = $result[0]['bank_name'];
            $receiving_acc_no    = $result[0]['account_no'];
            $receiving_acc_name  = $result[0]['account_name'];
            
            $sql                 = "SELECT * FROM church_table WHERE church_id = '$paying_church_id' LIMIT 1";
            $result              = $this->db_query($sql);
            $receiving_state       = $result[0]['state'];
            $receiving_lga       = $result[0]['lga'];
            $receiving_church_type = $result[0]['church_type'];
            $receiving_region      = $result[0]['church_region'];
            $sql2                 = "SELECT percentage FROM splitting WHERE min_amt <= '$amt' AND max_amt >= '$amt' AND church_type = '$church_type' LIMIT 1";
            
            //             Get percentage based on the church type
            $rr                  = $this->db_query($sql2);
            if(count($result) > 0) // if it found a pastor
            {
                $percentage          = $rr[0]['percentage'];
                $percentage_value    = ($percentage/100)*$amt;
                
                $ko = array('church_id'=>$receiving_church_id, 'bank_code'=>$receiving_bank, 'account_no'=>$receiving_acc_no, 'account_name'=>$receiving_acc_name,'percentage'=>$percentage,'percentage_value'=>$percentage_value,'state'=>$receiving_state,'lga'=>$receiving_lga,'church_type'=>$receiving_church_type,'church_region'=>$receiving_region);
                
                return $ko;
            }else
            {
                return null;
            }
        }
        
        if($church_type == 2) // region
        {
            $sql                 = "SELECT * FROM church_table WHERE church_type = '$church_type' AND church_id <> '$paying_church_id' AND state = '$church_state' LIMIT 1";
            $result              = $this->db_query($sql);
            $receiving_church_id = $result[0]['church_id'];
            $receiving_bank      = $result[0]['bank_code'];
            $receiving_acc_no    = $result[0]['account_no'];
            $receiving_acc_name  = $result[0]['account_name'];
            $receiving_state       = $result[0]['state'];
            $receiving_lga       = $result[0]['lga'];
            $receiving_church_type = $result[0]['church_type'];
            $receiving_region      = $result[0]['church_region'];
            $sql                 = "SELECT percentage FROM splitting WHERE min_amt <= '$amt' AND max_amt >= '$amt' AND church_type = '$church_type' LIMIT 1";
            //             Get percentage based on the church type
            $rr                  = $this->db_query($sql);
            if(count($result) > 0) // if it found a church
            {
                $percentage          = $rr[0]['percentage'];
                $percentage_value    = ($percentage/100)*$amt;
                $ko = array('church_id'=>$receiving_church_id, 'bank_code'=>$receiving_bank, 'account_no'=>$receiving_acc_no, 'account_name'=>$receiving_acc_name,'percentage'=>$percentage,'percentage_value'=>$percentage_value,'state'=>$receiving_state,'lga'=>$receiving_lga,'church_type'=>$receiving_church_type,'church_region'=>$receiving_region);
                
                return $ko;
            }else
            {
                return null;
            }
        }
        if($church_type == 3) // State HQ
        {
            $sql                 = "SELECT * FROM church_table WHERE church_type = '$church_type' AND church_id <> '$paying_church_id' AND state = '$church_state' LIMIT 1";
            $result              = $this->db_query($sql);
            $receiving_church_id = $result[0]['church_id'];
            $receiving_bank      = $result[0]['bank_code'];
            $receiving_acc_no    = $result[0]['account_no'];
            $receiving_acc_name  = $result[0]['account_name'];
            $receiving_state       = $result[0]['state'];
            $receiving_lga       = $result[0]['lga'];
            $receiving_church_type = $result[0]['church_type'];
            $receiving_region      = $result[0]['church_region'];
            $sql                 = "SELECT percentage FROM splitting WHERE min_amt <= '$amt' AND max_amt >= '$amt' AND church_type = '$church_type' LIMIT 1";
            //             Get percentage based on the church type
            $rr                  = $this->db_query($sql);
            if(count($result) > 0) // if it found a church
            {
                $percentage          = $rr[0]['percentage'];
                $percentage_value    = ($percentage/100)*$amt;
                $ko = array('church_id'=>$receiving_church_id, 'bank_code'=>$receiving_bank, 'account_no'=>$receiving_acc_no, 'account_name'=>$receiving_acc_name,'percentage'=>$percentage,'percentage_value'=>$percentage_value,'state'=>$receiving_state,'lga'=>$receiving_lga,'church_type'=>$receiving_church_type,'church_region'=>$receiving_region);
                
                return $ko;
            }else
            {
                return null;
            }
        }
    }
    public function getCollectionTypeFormList($data)
    {
        
        $filter = ($data[category_id] == "")?"":" AND category_id = '$data[category_id]'";
         $sql_collection_type = "SELECT id,name FROM collection_type WHERE 1=1 $filter order by collection_order";
        $collection_type = $this->db_query($sql_collection_type);
        $return_data = "";
        $row_count = 0;
        if(count($collection_type) > 0)
        {
            foreach($collection_type as $row)
            {
               if($row_count == 0)
               {
                   $return_data = $return_data.'<div class="row">';
               }
                $return_data = $return_data.'<div class="col-sm-4">
                           <div class="form-group">
                               <label class="form-label">'.$row[name].'</label>
                               <input type="hidden"  id="k'.$row[id].'" name="collection['.$row[id].']" placeholder="" value="0" class="form-control variable" />
                               <input type="text" autocomplete="off" onkeyup="convert_to_w(this)" id="z'.$row[id].'"  placeholder="" value="" class="form-control" />
                               <div style="font-size:12px" class="hh"></div>
                           </div>
                       </div>';
                if($row_count == 2)
                {
                    $return_data = $return_data.'</div>';
                     $row_count = 0;
                }else
                {
                    $row_count++;
                }
            }
            return json_encode(array('response_code'=>0,'response_message'=>'successful','data'=>array('html'=>$return_data)));
        }else
        {
            return json_encode(array('response_code'=>0,'response_message'=>'successful','data'=>array('html'=>'<h5 align="center">No collection was found in the category</h5>')));
        }
        
    }
}