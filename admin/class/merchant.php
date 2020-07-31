<?php

class Merchant extends dbobject
{
   public function merchantList($data)
    {
		$table_name    = "merchant_reg";
		$primary_key   = "merchant_id";
		$columner = array(
			array( 'db' => 'merchant_id', 'dt' => 0 ),
			array( 'db' => 'merchant_id', 'dt' => 1),
			array( 'db' => 'merchant_logo', 'dt' => 2,'formatter'=>function($d,$row){
				return "<img src='$d' class='img-thumbnail' width='50px' height='50px' />";
			  }),
            array( 'db' => 'merchant_name', 'dt' => 3),
            array( 'db' => 'merchant_phone','dt' => 4),
            array( 'db' => 'merchant_email', 'dt' => 5),
            array( 'db' => 'active_merchant', 'dt' => 6, 'formatter'=>function($d,$row){
              return ($d == 1)?"Yes":"No";
            }),
            array( 'db' => 'main_url', 'dt' => 7),
			array( 'db' => 'merchant_id', 'dt' => 8,'formatter'=>function($d,$row){
				return "NGN ".$this->getitemlabel('customer_balance','username',$d,'current_balance');
			  }),
            array( 'db' => 'account_no', 'dt' => 9),
            array( 'db' => 'merchant_id',     'dt' => 10, 'formatter' => function( $d,$row ) {
                $split_dist = "<button class='btn btn-success' onclick=\"getModal('setup/merchant_setup.php?merchant_id=$d&op=edit','modal_div')\" href='javascript:void(0)' data-toggle='modal' data-target='#defaultModalPrimary'>Edit</button>";
						return $split_dist;
					}
				),
            array( 'db' => 'created',  'dt' => 11 )
			);
		$filter = "";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
	}
	public function saveMerchant($data)
	{
		$validation = $this->validate($data,
                    array(
                        'merchant_name'=>'required|unique:merchant_reg.merchant_name',
                        'merchant_email'=>'required|email',
                        'merchant_address'=>'required',
                        'merchant_phone'=>'required|int',
                        'bank_code'=>'required',
                        'account_no'=>'int',
                        'merchant_details'=>'required',
                        'industry'=>'required'
                    ),
                    array('merchant_name'=>'Business Name','merchant_email'=>'Email')
                   );
        if(!$validation['error'])
        {
			$data['created'] = date("Y-m-d h:i:s");
            if($data['operation'] == 'new')
            {
				$data['merchant_id'] = "VUV-".date('mdhisy');
				$count = $this->doInsert('merchant_reg',$data,array('operation','op','id','_files','merchant_password','merchant_confirm_password'));
				if($count == 1)
				{
//					$this->createWallet($data['merchant_email']);
                    $this->createMerchantFolder($data['merchant_id']);
                    $ff = $this->saveMerchantImage($data['_files'],$data['merchant_id'],$data['merchant_id']);
//                    file_put_contents('track_img.txt',$ff);
                    $ff = json_decode($ff,true);
                    if($ff['response_code'] == "0")
                    {
                        $full_path = $ff['data'];
                        $sql = "UPDATE merchant_reg SET merchant_logo = '$full_path' WHERE merchant_id='$data[merchant_id]' LIMIT 1";
                        $this->db_query($sql,false);
                        $this->createUser($data);
                    }
					
				}
            }else
            {
                $count = $this->doUpdate('merchant_reg',$data,array('operation','op','id','_files'),array('merchant_id'=>$data['id']));
            }
            if($count == 1)
            {
				

                return json_encode(array("response_code"=>0,"response_message"=>"Merchant details saved successfully"));
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
	public function createUser($merch)
	{
		$data['day_1'] = "on";
		$data['day_2'] = "on";
		$data['day_3'] = "on";
		$data['day_4'] = "on";
		$data['day_5'] = "on";
		$data['day_6'] = "on";
		$data['day_7'] = "on";
		$data['passchg_logon'] = "on";

		$data['operation'] = "new";
		$data['firstname'] = $merch['merchant_name'];
		$data['lastname'] = $merch['merchant_name'];
		$data['mobile_phone'] = $merch['merchant_phone'];
		$data['merchant_id'] = $merch['merchant_id'];
		$data['sex'] = "male";
		$data['role_id'] = "002";
		$data['username'] = $merch['merchant_email'];
		$data['password'] = $merch['merchant_password'];
		$data['confirm_password'] = $merch['merchant_confirm_password'];
		$userObj = new Users();
		$resp    = $userObj->register($data);
	}
    public function createMerchantFolder($merchant_id)
    {
        if(mkdir('uploads/'.$merchant_id))
        {
            if(mkdir('uploads/'.$merchant_id.'/logo'))
            {
                if(mkdir('uploads/'.$merchant_id.'/products'))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        
        
    }
	public function saveMerchantImage($data,$user_id,$image_id="")
    {
        $_FILES = $data;
        if (
            !isset($_FILES['upfile']['error']) ||
            is_array($_FILES['upfile']['error'])
        ) {
            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Invalid parameter.')));
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['upfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'No file sent.')));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.')));
            default:
                throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Unknown errors.')));
        }

        // You should also check filesize here.
        if ($_FILES['upfile']['size'] > 1000000) {
            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Exceeded filesize limit.')));
        }

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
    //    $finfo = new finfo(FILEINFO_MIME_TYPE);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            finfo_file($finfo,$_FILES['upfile']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png'
            ),
            true
        )) {
            throw new RuntimeException(json_encode(array('response_code'=>'74','response_mesage'=>'Invalid file format.')));
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
       $email = ($image_id == "")?date('mdhis'):$image_id;
        $path = './uploads/'.$user_id.'/logo/';
        if (!move_uploaded_file(
            $_FILES['upfile']['tmp_name'],
            sprintf($path.'%s.%s',
                $email,
                $ext
            )
        )) {
            throw new RuntimeException(json_encode(array('response_code'=>'50','response_mesage'=>'Failed to move uploaded file.')));
        }
        $full_path = $path.$email.'.'.$ext;
        return json_encode(array('response_code'=>'0','response_message'=>'success','data'=>$full_path));
        
    }
	public function getFileDetails($data)
	{
		// $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
		// echo finfo_file($finfo, "./uploads/merchant_logos/0526081608.jpg");
		// finfo_close($finfo);
		 $size = filesize('./uploads/merchant_logos/0526081608.jpg');
		return json_encode(array('name'=>'0526081608','path'=>'./uploads/merchant_logos/0526081608.jpg','size'=>$size));
        
	}
	public function createWallet($merchant_id)
	{
		$sql = "INSERT INTO customer_balance (username,previous_balance,current_balance,created) VALUES('$merchant_id','0','0',NOW())";
		$count = $this->db_query($sql,false);
		return $count;
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
            echo "Unable to verify account, try again.";
        }
        
    }
    
}