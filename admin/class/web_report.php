<?php

class web_report extends dbobject
{
   public function menuList($data)
    {
		$table_name    = "school_menu_group";
		$primary_key   = "school_id";
		$columner = array(
			array( 'db' => 'school_id', 'dt' => 0 ),
			array( 'db' => 'menu_id', 'dt' => 1, 'formatter' => function($d,$row){
                return $this->getitemlabel('school_menu','id',$d,'name');
            } ),
			array( 'db' => 'is_active',  'dt' => 2, 'formatter' => function($d,$row){
                return ($d == 1)?"<span style='display:inline-block;width:10px;height:10px; background:green; border-radius:50%'></span>&nbsp;Active":"<span style='display:inline-block;width:10px;height:10px; background:red; border-radius:50%'></span>&nbsp;Disabled";
                
            } ),
            array( 'db' => 'school_id',  'dt' => 3, 'formatter' => function($d,$row){
                $url = $this->getitemlabel('school_menu','id',$row['menu_id'],'admin_url');
                $edit_icon = '<a  class="" onclick="getModal(\''.$url.'\',\'modal_div\')"  href="javascript:void(0)"  data-toggle="modal" data-target="#defaultModalPrimary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg> Edit</a>';
                $action = "";
                if($row['menu_id'] != 1)
                {
                    $action = ($row['is_active'] == 1)?"| <a href='javascript:action_btn(1,\"".$row[menu_id]."\")'><i class='fas fa-fw fa-lock'></i> Lock Page</a>":"<a href='javascript:action_btn(0,\"".$row[menu_id]."\")'><i class='fas fa-fw fa-lock-open'></i> Enable Page</a>";
                }
                
                return $edit_icon." ".$action;
                
            } ),
			array( 'db' => 'posted_by',   'dt' => 4 ),
			array( 'db' => 'created',     'dt' => 5, 'formatter' => function( $d, $row ) {
						return $d;
					}
				)
			);
		$filter = "";
		$filter = " AND school_id='".$_SESSION[sch_id_sess]."'";
//		$filter = " AND school_id='111'";
       
		echo $this->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
    public function pageList($data)
    {
        $table_name    = "merchant_page_settings";
		$primary_key   = "merchant_id";
		$columner = array(
			array( 'db' => 'merchant_id', 'dt' => 0 ),
			array( 'db' => 'primary_color', 'dt' => 1, 'formatter' => function($d,$row){
                return "<span style='width:20px; border:1px solid #ccc; height:20px;display:inline-block; border-radius:50%; background-color:$d'></span>";
            } ),
			array( 'db' => 'secondary_color',  'dt' => 2, 'formatter' => function($d,$row){
                return "<span style='width:20px; border:1px solid #ccc; height:20px; display:inline-block; border-radius:50%; background-color:$d' ></span>";
            } ),
            array( 'db' => 'menu_font_color',  'dt' => 3, 'formatter' => function($d,$row){
                return "<span style='width:20px; border:1px solid #ccc; height:20px;display:inline-block; border-radius:50%; background-color:$d'></span>";
            }  ),
			array( 'db' => 'text_logo_inline',   'dt' => 4, 'formatter' => function($d,$row){
                return ($d == 1)?"Beside the logo":"Underneath the logo";
            } ),
			array( 'db' => 'show_display_name_logo',   'dt' => 5, 'formatter' => function($d,$row){
                return ($d == "1")?"YES":"NO";
            }  ),
			array( 'db' => 'merchant_id',   'dt' => 6 , 'formatter' => function($d,$row){
                return $this->getitemlabel('merchant_reg','merchant_id',$d,'merchant_name');
            } ),
			array( 'db' => 'logo_max_width',   'dt' => 7, 'formatter' => function($d,$row){
                return $d."px";
            }  ),
			array( 'db' => 'merchant_id',   'dt' => 8, 'formatter' => function($d,$row){
                $img_src = $this->getitemlabel("merchant_reg","merchant_id",$d,"merchant_logo");
                return "<img src='$img_src' style='width:60px; height:60px' />";
            } ),
			array( 'db' => 'merchant_id',   'dt' => 9, 'formatter' => function($d,$row){
                return '<button class="btn btn-success" onclick="getModal(\'setup/settings.php?operation=edit\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit</button>';
            }   ),
			array( 'db' => 'created', 'dt' => 10, 'formatter' => function( $d, $row ) {
						return $d;
					}
				)
			);
		$filter = "";
		$filter = " AND merchant_id='".$_SESSION[merchant_sess_id]."'";
//		$filter = " AND school_id='111'";
        $datatableEngine = new engine();
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);
    }
    public function pageActivation($data)
    {
        $school_id = $_SESSION['sch_id_sess'];
        $state = ($data['state']== 1)?0:1;
        $menu_id = $data['menu_id'];
        $count = $this->db_query('UPDATE school_menu_group SET  is_active = "'.$state.'" WHERE school_id ="'.$school_id.'" AND menu_id = "'.$menu_id.'" ',false);
        if($count == 1)
        {
            $dd = ($data['state']== 1)?"Disabled":"Active";
            return json_encode(array('response_code'=>0,'response_message'=>'Page is now '.$dd));
        }else
        {
            return json_encode(array('response_code'=>44,'response_message'=>'Page status was not updated'));
        }
    }
    
    public function saveHomePage($data)
    {
        $data['school_id']         = $_SESSION['sch_id_sess'];
        $data['intro_message']         = mysql_real_escape_string($data['intro_message']);
        $data['created']        = date('Y-m-d h:i:s');
        $data['posted_by']      = $_SESSION['username_sess'];
        if($data['display_type'] == "carousel")
        {
            $data['show_slider']      = 1;
            $data['show_banner']      = 0;
        }else
        {
            $data['show_slider']      = 0;
            $data['show_banner']      = 1;
        }
        if($data['operation'] == "new")
        {
            $count = $this->doInsert('school_home',$data,array('op','operation','image-display','display_type'));
            
        }else{
            $count = $this->doUpdate('school_home',$data,array('op','operation','image-display','display_type'),array('school_id'=>$data['school_id']));
        }
        return ($count > 0)?"Data saved successfully":"Unable to save record.";
    }
    public function saveFacility($data)
    {
        $data['sch_id']         = $_SESSION['sch_id_sess'];
        $data['created']           = date('Y-m-d h:i:s');
        $data['posted_by']         = $_SESSION['username_sess'];
        if($data['operation'] == "new")
        {
            $count = $this->doInsert('school_facility',$data,array('op','operation'));
            
        }else{
            $count = $this->doUpdate('school_facility',$data,array('op','operation'),array('sch_id'=>$data['sch_id']));
        }
    }
    public function saveGeneralSettings($data)
    {
        $data['merchant_id']         = $_SESSION['merchant_sess_id'];
        $data['created']        = date('Y-m-d h:i:s');
        $data['posted_by']      = $_SESSION['username_sess'];
        if($data['operation'] == "new")
        {
            $count = $this->doInsert('merchant_page_settings',$data,array('op','operation','id','show_display_name_logo_old'));
        }
        else
        {
//            var_dump($data);
            $count = $this->doUpdate('merchant_page_settings',$data,array('op','operation','show_display_name_logo_old'),array('merchant_id'=>$data['merchant_id']));
        }
    }
    public function saveCarousel($data)
    {
        $data['sch_id']         = $_SESSION['sch_id_sess'];
        $data['created']        = date('Y-m-d h:i:s');
        $data['posted_by']      = $_SESSION['username_sess'];
        if($data['operation'] == "new")
        {
            $count = $this->doInsert('home_slider',$data,array('op','operation','id'));
        }
        else
        {
            $count = $this->doUpdate('home_slider',$data,array('op','operation'),array('id'=>$data['id']));
        }
        
        if($count == 1)
        {
            return json_encode(array('response_code'=>0,'response_message'=>'Saved successfully'));
        }else
        {
            return json_encode(array('response_code'=>74,'response_message'=>'Failed to save record'));
        }
    }
    
    function doInsert($table,$arr,$exp_arr)
        {
            $patch1  = "(";
            $patch2  = "(";

            foreach($arr as $key=>$value)
            {
                if(!in_array($key,$exp_arr))
                {
                    $patch1.= $key.",";
                    $patch2.= "'".$value."',";
                }
            }
            $patch1 =  substr($patch1,0,-1).")";
            $patch2 =  substr($patch2,0,-1).")";
            $sql = "insert into ".$table." ".$patch1." VALUES ".$patch2;
            $num_row = $this->db_query($sql,false);
            return $num_row;
        }
        function doUpdate($table,$arr,$exp_arr,$clause)
        {
            $patch1     = "";
            $key_id     = "";
            foreach($arr as $key=>$value)
            {
                if(!in_array($key,$exp_arr))
                {
                    $patch1.= $key."='".$value."',";
                }
            }
            foreach($clause as $key=>$value)
            {
                $key_id.= " ".$key."='".$value."' AND";
            }
            $key_id  =  substr($key_id,0,-3);
            $patch1  =  substr($patch1,0,-1);
            $sql    = "UPDATE ".$table." SET ".$patch1." WHERE ".$key_id;

            $num_row = $this->db_query($sql,false);
            return $num_row;
        }
}