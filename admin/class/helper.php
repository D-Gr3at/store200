<?php

class Helper extends dbobject
{
    public function getLga($data)
    {
        $state  = $data['state'];
        
        $sql    = "SELECT Lga,Lgaid FROM lga WHERE state_code = '$state' order by Lga";
        $result = $this->db_query($sql);
        $output   = "";
        foreach($result as $row)
        {
            $output.= "<option value='".$row['Lgaid']."'>".$row['Lga']."</option>";
        }
      
        
        return json_encode(array('state'=>$output));
    }
    public function shippingRegionsgetLga($data)
    {
        $state  = $data['state'];
        $merchant_id = $_SESSION['merchant_sess_id'];
        
        $sql = "SELECT lga_list FROM shipping_regions WHERE state = '$state' AND merchant_id = '$merchant_id'";
        $result = $this->db_query($sql);
        if(count($result) > 0)
        {
            $str = "";
            foreach($result as $row)
            {
                $str = $str.$row['lga_list'].",";
            }
            $str = substr($str,0,-1);
            $str_filter = "AND Lgaid NOT IN ($str)";
        }else
        {
            $str_filter = "";
        }
        
        $sql    = "SELECT Lga,Lgaid FROM lga WHERE state_code = '$state' $str_filter  order by Lga";
        $result = $this->db_query($sql);
        $output   = "";
        if(count($result) > 0)
        {
            foreach($result as $row)
            {
                $output.= "<option value='".$row['Lgaid']."'>".$row['Lga']."</option>";
            }
        }else
        {
            $output = "<option value=''>NO LGA AVALAIBLE</option>";
        }
        
      
        
        return json_encode(array('state'=>$output));
    }
}