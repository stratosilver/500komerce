<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CustomerOrderItem;

class ViewCustomerOrderItem extends \Apgenic\Classes\ViewTemplate {
var $customerOrderList1 = array();
var $productVariantList1 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($customerOrderList1, $productVariantList1) {
$this->customerOrderList1 = $customerOrderList1;
$this->productVariantList1 = $productVariantList1;
}


/**
* Display tabs above form or detail page of an record
* Tabs can display lists of decords of an other table having a forign key with the 
* displayed record.  
* @param int $id
* @param string $activeTab
* @return void
*/
public static function tabs(int $id, string $activeTab){
?>

    <ul id="tabs_list" class="nav nav-tabs">
      <li class="nav-item">
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=customer_order_item&task=edit&id_customer_order_item=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table customer_order_item
 * @parameter ModelCustomerOrderItem $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelCustomerOrderItem $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    if($showTabs == 1){
	        self::tabs($data->id_customer_order_item, 'info');
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

                        <?php 
                        if(!isset($this->filters['id_customer_order'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;customer&nbsp;order&nbsp;*:</label>
            <select  class="form-control" name="id_customer_order" id="id_customer_order">
                <option value=""></option>
        <?php
        foreach ($this->customerOrderList1 as $key => $val){
                ?>
                <option value="<?=$val['id_customer_order']?>" <?php if($data->id_customer_order == $val['id_customer_order']) echo 'selected="selected"';?>><?=$val['id_user']?></option>
        <?php 
        }
        ?>
       
            </select>
		</div>
		</div>
		
                        <?php
                        }
                        else{
                            ?>
                            
                            <input type="hidden" name="id_customer_order" id="id_customer_order" value="<?php if(!isset($data->id_customer_order) || $data->id_customer_order < 1) echo $_GET['filters']['id_customer_order']; else echo $data->id_customer_order?>"/>
                            <?php
                        }
                        ?>                     
                        

                        <?php 
                        if(!isset($this->filters['id_product_variant'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;product&nbsp;variant:</label>
            <select  class="form-control" name="id_product_variant" id="id_product_variant">
                <option value=""></option>
        <?php
        foreach ($this->productVariantList1 as $key => $val){
                ?>
                <option value="<?=$val['id_product_variant']?>" <?php if($data->id_product_variant == $val['id_product_variant']) echo 'selected="selected"';?>><?=$val['name']?></option>
        <?php 
        }
        ?>
       
            </select>
		</div>
		</div>
		
                        <?php
                        }
                        else{
                            ?>
                            
                            <input type="hidden" name="id_product_variant" id="id_product_variant" value="<?php if(!isset($data->id_product_variant) || $data->id_product_variant < 1) echo $_GET['filters']['id_product_variant']; else echo $data->id_product_variant?>"/>
                            <?php
                        }
                        ?>                     
                        

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Product&nbsp;name&nbsp;*:</label>
		<input required class="form-control" type="text" name="product_name" id="product_name" size="10" value="<?=htmlentities((string)(string)$data->product_name)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Sku&nbsp;*:</label>
		<input required class="form-control" type="text" name="sku" id="sku" size="10" value="<?=htmlentities((string)(string)$data->sku)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Quantity&nbsp;*:</label>
		<input required="required" class="form-control" type="number" name="quantity" id="quantity" value="<?=$data->quantity?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Unit&nbsp;price&nbsp;amount&nbsp;*:</label>
		<input required="required" class="form-control" type="number" name="unit_price_amount" id="unit_price_amount" value="<?=$data->unit_price_amount?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Discount&nbsp;amount:</label>
		<input required="" class="form-control" type="number" name="discount_amount" id="discount_amount" value="<?=$data->discount_amount?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Tax&nbsp;amount:</label>
		<input required="" class="form-control" type="number" name="tax_amount" id="tax_amount" value="<?=$data->tax_amount?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Line&nbsp;total&nbsp;amount&nbsp;*:</label>
		<input required="required" class="form-control" type="number" name="line_total_amount" id="line_total_amount" value="<?=$data->line_total_amount?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_customer_order_item" id="id" value="<?=$data->id_customer_order_item ?>"/>
		<input type="submit" name="Envoyer" title="Envoyer" value="Save" class="btn btn-outline btn-outline-primary">
		</div>
		</div>


    </div>
	</fieldset>
	</form>
	</div>
	</div>
	<?php
    if($showTabs == 1){
        echo '</div>';
    }	

	}

	

/**
 * Display rows from the table customer_order_item
 * @parameter array $data , 
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function viewList(array $data ,  string $orderBy='', string $order='desc', array $message=null){
    $this->message($message);


	?>
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_customer_order_item"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=id_customer_order_item&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order&nbsp;item</a></th>
	<th scope="col" id="id_customer_order"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="product_name"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=product_name&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>">Product&nbsp;name</a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>">Quantity</a></th>
	<th scope="col" id="unit_price_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=unit_price_amount&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>">Unit&nbsp;price&nbsp;amount</a></th>
	<th scope="col" id="discount_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=discount_amount&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>">Discount&nbsp;amount</a></th>
	<th scope="col" id="tax_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=tax_amount&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>">Tax&nbsp;amount</a></th>
	<th scope="col" id="line_total_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=line_total_amount&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>">Line&nbsp;total&nbsp;amount</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_customer_order_item']?></td>
		<td ><?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'] ?? '';?></td>
		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=view&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn">
            <?=self::icon('view')?>		    
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table customer_order_item
 *
 * @parameter ModelCustomerOrderItem $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelCustomerOrderItem $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_customer_order_item != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;customer&nbsp;order&nbsp;item</h5></dt><dd class="col-sm-9"><?php echo $data->id_customer_order_item?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;customer&nbsp;order</h5></dt><dd class="col-sm-9"><?php echo $this->customerOrderList1[$data->id_customer_order]['id_user'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;product&nbsp;variant</h5></dt><dd class="col-sm-9"><?php echo $this->productVariantList1[$data->id_product_variant]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Product&nbsp;name</h5></dt><dd class="col-sm-9"><?php echo $data->product_name?></dd>
		        <dt class="col-sm-3"><h5>Sku</h5></dt><dd class="col-sm-9"><?php echo $data->sku?></dd>
		        <dt class="col-sm-3"><h5>Quantity</h5></dt><dd class="col-sm-9"><?php echo $data->quantity?></dd>
		        <dt class="col-sm-3"><h5>Unit&nbsp;price&nbsp;amount</h5></dt><dd class="col-sm-9"><?php echo $data->unit_price_amount?></dd>
		        <dt class="col-sm-3"><h5>Discount&nbsp;amount</h5></dt><dd class="col-sm-9"><?php echo $data->discount_amount?></dd>
		        <dt class="col-sm-3"><h5>Tax&nbsp;amount</h5></dt><dd class="col-sm-9"><?php echo $data->tax_amount?></dd>
		        <dt class="col-sm-3"><h5>Line&nbsp;total&nbsp;amount</h5></dt><dd class="col-sm-9"><?php echo $data->line_total_amount?></dd>
		        <dt class="col-sm-3"><h5>Created&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
	             </dl></div>
            </div>
	    </div>
	</div>
	<?php
    }
    else{
        $this->message(array('text' => 'No data', 'type' => 'warning'));
    }



}

        
/**
 * Display rows from the table customer_order_item
 * @parameter int $pkVal
 * @parameter string $task
 * @parameter int $value
 * @return void
 */
public function buttonToggle(int $pkVal, string $task, int $value = 0){
    $buttonTyp[0] = 'secondary';
    $buttonTyp[1] = 'success';
    $buttonIcon[0] = self::icon('x-lg');
    $buttonIcon[1] = self::icon('check2');
   
    ?>
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=<?=$task?>&value=<?php echo $value?>&id_customer_order_item=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table customer_order_item with edit and del buttons
 * @parameter array $data , 
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function editList(array $data ,  string $orderBy='',string $order='desc',array $message=null){
    $this->message($message);


	?>
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_customer_order_item"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=id_customer_order_item&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order&nbsp;item</a></th>
	<th scope="col" id="id_customer_order"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=id_customer_order&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="product_name"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=product_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>">Product&nbsp;name</a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=quantity&<?=$this->filtersGet?>&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>">Quantity</a></th>
	<th scope="col" id="unit_price_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=unit_price_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>">Unit&nbsp;price&nbsp;amount</a></th>
	<th scope="col" id="discount_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=discount_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>">Discount&nbsp;amount</a></th>
	<th scope="col" id="tax_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=tax_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>">Tax&nbsp;amount</a></th>
	<th scope="col" id="line_total_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=line_total_amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>">Line&nbsp;total&nbsp;amount</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order_item']?>">
		<td class="shrink"><?php echo $element['id_customer_order_item']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=editList&id_customer_order=<?php echo $element['id_customer_order']?>">
                                            <?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=view&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=edit&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_customer_order_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=delHtmx&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">            
            <?=self::icon('x-lg');?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table customer_order_item with edit and del buttons
 * specificly to be displayed below a tab
 * @parameter array $data , 
 * @parameter array $filters
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function childList(array $data , array $filters, string $orderBy='',string $order='desc',array $message=null){
    $this->message($message);
    $parentPK = array_key_first($filters);
    $parentPKValue = $filters[$parentPK];


	?>
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_customer_order_item"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=id_customer_order_item&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;customer&nbsp;order&nbsp;item</a></th>
	<th scope="col" id="id_customer_order"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="product_name"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=product_name&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Product&nbsp;name</a></th>
	<th scope="col" id="quantity"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Quantity</a></th>
	<th scope="col" id="unit_price_amount"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=unit_price_amount&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Unit&nbsp;price&nbsp;amount</a></th>
	<th scope="col" id="discount_amount"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=discount_amount&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Discount&nbsp;amount</a></th>
	<th scope="col" id="tax_amount"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=tax_amount&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Tax&nbsp;amount</a></th>
	<th scope="col" id="line_total_amount"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=line_total_amount&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Line&nbsp;total&nbsp;amount</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order_item']?>">
		<td class="shrink"><?php echo $element['id_customer_order_item']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=editList&id_customer_order=<?php echo $element['id_customer_order']?>">
                                            <?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=view&showTabs=0&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=childList&showTabs=0&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_customer_order_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=delHtmx&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter" style="text-align: right;">
	    <a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?= BASE_URL ?>/htmx.php?component=<?=COMPONENT?>&task=edit&showTabs=0&<?=$parentPK?>=<?=$parentPKValue?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-success" style="margin-top: 10px;">
        <?=self::icon('plus-lg');?>
    </a>
    </td>
	</tr>
	</tfoot>

	</table>
	
	<?php
}


/**
 * Display trashed rows from the table customer_order_item
 * @parameter array $data , 
 * @parameter string $orderBy
 * @parameter string $order
 * @parameter array $message
 * @return void
 */
public function trashedList(array $data ,  string $orderBy='', string $order='desc', array $message=null){
    $this->message($message);


	?>
	<table class="table table-bordered table-sm">
	<thead>
	<tr>	<th scope="col" id="id_customer_order_item"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=id_customer_order_item&order=<?php if($orderBy == "id_customer_order_item") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order&nbsp;item</a></th>
	<th scope="col" id="id_customer_order"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="product_name"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=product_name&order=<?php if($orderBy == "product_name") echo $order; else echo 'asc';?>">Product&nbsp;name</a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>">Quantity</a></th>
	<th scope="col" id="unit_price_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=unit_price_amount&order=<?php if($orderBy == "unit_price_amount") echo $order; else echo 'asc';?>">Unit&nbsp;price&nbsp;amount</a></th>
	<th scope="col" id="discount_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=discount_amount&order=<?php if($orderBy == "discount_amount") echo $order; else echo 'asc';?>">Discount&nbsp;amount</a></th>
	<th scope="col" id="tax_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=tax_amount&order=<?php if($orderBy == "tax_amount") echo $order; else echo 'asc';?>">Tax&nbsp;amount</a></th>
	<th scope="col" id="line_total_amount"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=line_total_amount&order=<?php if($orderBy == "line_total_amount") echo $order; else echo 'asc';?>">Line&nbsp;total&nbsp;amount</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=customerorderitem&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order_item']?>">
		<td class="shrink"><?php echo $element['id_customer_order_item']?></td>
		<td ><?php echo $this->customerOrderList1[$element['id_customer_order']]['id_user'] ?? '';?></td>
		<td ><?php echo $element['product_name']?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['unit_price_amount']?></td>
		<td ><?php echo $element['discount_amount']?></td>
		<td ><?php echo $element['tax_amount']?></td>
		<td ><?php echo $element['line_total_amount']?></td>
		<td ><?php echo $element['created_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_customer_order_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorderitem&task=undelHtmx&id_customer_order_item=<?php echo $element['id_customer_order_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
            <?=self::icon('undel')?>	    
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
	