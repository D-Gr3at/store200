<?php

class Role extends dbobject
{
   public function role_list($data)
    {
		$table_name    = "role";
		$primary_key   = "role_id";
		$columner = array(
			array( 'db' => 'role_id', 'dt' => 0 ),
			array( 'db' => 'role_id', 'dt' => 1 ),
			array( 'db' => 'role_name',  'dt' => 2 ),
//			array( 'db' => 'role_id',   'dt' => 3, 'formatter' => function( $d,$row ) {
//						return "<a href='javascript:void(0)' class='btn btn-primary'>Edit Role</a>";
//					} ),
			array( 'db' => 'created',     'dt' => 3, 'formatter' => function( $d,$row ) {
						return $d;
					}
				)
			);
		$filter = "";
//		$filter = " AND role_id='001'";
		$datatableEngine = new engine();
	
		echo $datatableEngine->generic_table($data,$table_name,$columner,$filter,$primary_key);

    }
}