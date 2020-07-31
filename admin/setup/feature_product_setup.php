<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Feature Setup</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false">
       <input type="hidden" name="op" value="Product.setFeatureProduct">
       <div class="row">
          
           <div class="col-sm-6">
               <div class="form-group">
                    <label class="form-label">Product Search</label>
                    <input style="border: 1px solid #f5911e;" type="text" id="search_menu" placeholder="Search for a product" class="form-control input-lg">
                </div>
           </div>
          
           
       </div>
       
       <div id="err"></div>
<!--        <a href="javascript:void(0)" id="save_facility" onclick="saveRecord()" class="btn btn-primary mb-1">Submit</a>-->
        
    </form>
</div>
<script src="js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="css/bootstrap-multiselect.css">
<link rel="stylesheet" href="css/jquery.auto-complete.css">
<script src="js/jquery.auto-complete.js"></script>
<script>
      $(document).ready(function(){

 $('#search_menu').autoComplete({
			minChars: 3,
			source: function(term, response){
				term = term.toLowerCase();
                 try { xhr.abort(); } catch(e){}
                $.getJSON('utilities.php', { op: "Product.getSearchFilter",item:term }, function(data){                                                               
                        response(data.responseBody.items); 
                });
			},
			renderItem: function (item, search){
                console.log(item)
                if(item.status == 0)
                    {
                        return '<div class="autocomplete-suggestion" data-status="'+item.status+'" data-id="'+item.id+'" data-price="'+item.price+'"  data-label="'+item.name+'" data-quantity="1" data-image="'+item.primaryImage+'"> <img class="img-thumbnail" width="50" height="50" src="'+item.primaryImage+'" /> '+item.name+'  - <span class="card-subtitle text-muted">NGN '+item.price+'</span></div>';
                    }else{
                        return '<div class="autocomplete-suggestion" data-status="'+item.status+'" >NO PRODUCT MATCH!</div>';
                    }
				
			},
			onSelect: function(e, term, item){
                if(item.data('status') == 0)
                {
                    var con = confirm("Are you sure you want to set "+item.data('label')+" as a featured product?");
                    if(con)
                    {
                        $("#err").text("Loading please wait...")
                        $("#search_menu").val(item.data('label'));
                        $("#search_menu").attr("readonly");
                        $.post("utilities.php",{op:"Product.setFeatureProduct",product_id:item.data('id')},function(re){
                            $("#search_menu").removeAttr("readonly");
                            if(re.response_code == 0)
                            {
                                $("#err").css('color','green')
                                $("#err").html(re.response_mesage)
                                getpage('feature_product_list.php','page'); 
                            }
                            else
                            {
                                 $("#err").css('color','red')
                                $("#err").html(re.response_mesage)
                            }
                        },'json');
                    }
                    else{
                        $("#search_menu").val("");
                    }
                }else{
                    $("#search_menu").val("");
                }
			}
		});

    });
    

</script>
<style>
    /* autocomplete tagsinput*/
.label-info {
  background-color: #5bc0de;
  display: inline-block;
  padding: 0.2em 0.6em 0.3em;
  font-size: 75%;
  font-weight: 700;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25em;
}
    .btn-group{
        border: 1px solid #ccc;
        border-radius: 3px
    }
</style>