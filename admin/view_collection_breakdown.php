<?php
    include_once("libs/dbfunctions.php");
    $dbobject     = new dbobject();
    $id           = $_REQUEST['id'];
    $sql          = "SELECT collection_id,amount,payment_id FROM collections_basket WHERE id = '$id' LIMIT 1";
    $collection1   = $dbobject->db_query($sql);
    $collection   = json_decode($collection1[0]['collection_id'],TRUE);
//echo "payment_id ".$collection1[0]['payment_id'];
    $total_amount = $dbobject->getitemlabel('remittance','payment_id',$collection1[0]['payment_id'],'amount_collected');
?>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Collection Breakdown</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Collection</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
           <?php
            foreach($collection as $key=>$value)
            {
            ?>
                <tr>
                    <td><?php echo $dbobject->getitemlabel('collection_type','id',$key,'name'); ?></td>
                    <td><?php echo $value ?></td>
                </tr>
            <?php
            }
            ?>
                <tr>
                    <td style="color:red;">Total</td>
                    <td style="color:red"><?php echo $total_amount; ?></td>
                </tr>
        </tbody>
    </table>
</div>