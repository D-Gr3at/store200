<?php
include_once("response.php");
class Category extends dbobject
{
    private $response   = "";
    public function __construct()
    {
        $this->response = new Response();
    }
   
    public function saveCategory($data)
    {
        $data['created'] = date("Y-m-d h:i:s");
        $data['merchant_id'] = $_SESSION['merchant_sess_id'];
        if($data['operation'] == "new")
        {
            
            $count   = $this->doInsert("product_categories",$data,array('operation','op','id'));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Category Created Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'Category Creation Failed'));
            }
        }else
        {
            $count   = $this->doUpdate("product_categories",$data,array('operation','op','id'),array('id'=>$data['id']));
            if($count > 0)
            {
                return json_encode(array('response_code'=>0,'response_message'=>'Category Updated Successfully'));
            }else
            {
                return json_encode(array('response_code'=>47,'response_message'=>'No update made'));
            }
        }
    }
    
    public function deleteCategory($data)
    {
        $menu_id = $data['menu_id'];
        $sql     = "DELETE FROM menu WHERE menu_id = '$menu_id'";
        $this->db_query($sql,false);
        $sql     = "DELETE FROM menu_group WHERE menu_id = '$menu_id'";
        $this->db_query($sql,false);
        return $this->response->publishResponse("0","Deleted successfully","");
    }
    
    public function categoryList($data)
    {
        $table_name    = "product_categories";
		$primary_key   = "id";
		$columner = array(
			array( 'db' => 'id', 'dt' => 0 ),
			array( 'db' => 'name',  'dt' => 1 ),
			array( 'db' => 'description',  'dt' => 2 ),
			array( 'db' => 'id',  'dt' => 3,'formatter' => function( $d,$row ) {
                
						return '<a class="btn btn-warning" onclick="getModal(\'setup/category_setup.php?op=edit&cat_id='.$d.'\',\'modal_div\')"  href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">Edit Category</a>';
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
    
    public function getCategory($data)
    {
        $merchant_id = $_SESSION['merchant_sess_id'];
        $sql    = "SELECT * FROM product_categories WHERE merchant_id='$merchant_id'";
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