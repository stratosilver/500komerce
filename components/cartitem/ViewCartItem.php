<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CartItem;

class ViewCartItem extends \Apgenic\Classes\ViewTemplate {
var $productVariantList = array();
var $userList3 = array();
var $productList3 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($productVariantList, $userList3, $productList3) {
$this->productVariantList = $productVariantList;
$this->userList3 = $userList3;
$this->productList3 = $productList3;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=cart_item&task=edit&id_cart_item=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table cart_item
 * @parameter ModelCartItem $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelCartItem $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    if($showTabs == 1){
	        self::tabs($data->id_cart_item, 'info');
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Id&nbsp;cart&nbsp;item&nbsp;*:</label>
		<input required="required" class="form-control" type="number" name="id_cart_item" id="id_cart_item" value="<?=$data->id_cart_item?>"/>
	    </div>
	    </div>
	    

                        <?php 
                        if(!isset($this->filters['id_user'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;user&nbsp;*:</label>
            <select  class="form-control" name="id_user" id="id_user">
                <option value=""></option>
        <?php
        foreach ($this->userList3 as $key => $val){
                ?>
                <option value="<?=$val['id_user']?>" <?php if($data->id_user == $val['id_user']) echo 'selected="selected"';?>><?=$val['email']?></option>
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
                            
                            <input type="hidden" name="id_user" id="id_user" value="<?php if(!isset($data->id_user) || $data->id_user < 1) echo $_GET['filters']['id_user']; else echo $data->id_user?>"/>
                            <?php
                        }
                        ?>                     
                        

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Cookie&nbsp;id:</label>
		<input required="" class="form-control" type="number" name="cookie_id" id="cookie_id" value="<?=$data->cookie_id?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_cart_item" id="id" value="<?=$data->id_cart_item ?>"/>
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
 * Display rows from the table cart_item
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
	<tr>	<th scope="col" id="id_cart"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=id_cart&order=<?php if($orderBy == "id_cart") echo $order; else echo 'asc';?>">Id&nbsp;cart</a></th>
	<th scope="col" id="id_product_variant"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>">Id&nbsp;product&nbsp;variant</a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>">Quantity</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_cart']?></td>
		<td ><?php echo $this->productVariantList[$element['id_product_variant']]['name'] ?? '';?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=view&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="11" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table cart_item
 *
 * @parameter ModelCartItem $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelCartItem $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_cart_item != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;cart</h5></dt><dd class="col-sm-9"><?php echo $data->id_cart?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;product&nbsp;variant</h5></dt><dd class="col-sm-9"><?php echo $this->productVariantList[$data->id_product_variant]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Quantity</h5></dt><dd class="col-sm-9"><?php echo $data->quantity?></dd>
		        <dt class="col-sm-3"><h5>Created&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
		        <dt class="col-sm-3"><h5>Updated&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->updated_at?></dd>
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
 * Display rows from the table cart_item
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=<?=$task?>&value=<?php echo $value?>&id_cart_item=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table cart_item with edit and del buttons
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
	<tr>	<th scope="col" id="id_cart"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=id_cart&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_cart") echo $order; else echo 'asc';?>">Id&nbsp;cart</a></th>
	<th scope="col" id="id_product_variant"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=id_product_variant&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>">Id&nbsp;product&nbsp;variant</a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=quantity&<?=$this->filtersGet?>&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>">Quantity</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

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
		<tr id="row-<?php echo $element['id_cart_item']?>">
		<td class="shrink"><?php echo $element['id_cart']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=editList&id_product_variant=<?php echo $element['id_product_variant']?>">
                                            <?php echo $this->productVariantList[$element['id_product_variant']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=view&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=edit&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_cart_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=delHtmx&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">            
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
	<td colspan="11" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table cart_item with edit and del buttons
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
	<tr>	<th scope="col" id="id_cart"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=id_cart&order=<?php if($orderBy == "id_cart") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;cart</a></th>
	<th scope="col" id="id_product_variant"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;product&nbsp;variant</a></th>
	<th scope="col" id="quantity"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Quantity</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_cart_item']?>">
		<td class="shrink"><?php echo $element['id_cart']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=editList&id_product_variant=<?php echo $element['id_product_variant']?>">
                                            <?php echo $this->productVariantList[$element['id_product_variant']]['name'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=view&showTabs=0&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=cartitem&task=childList&showTabs=0&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_cart_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=delHtmx&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="11" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table cart_item
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
	<tr>	<th scope="col" id="id_cart"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=id_cart&order=<?php if($orderBy == "id_cart") echo $order; else echo 'asc';?>">Id&nbsp;cart</a></th>
	<th scope="col" id="id_product_variant"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>">Id&nbsp;product&nbsp;variant</a></th>
	<th scope="col" id="quantity"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=quantity&order=<?php if($orderBy == "quantity") echo $order; else echo 'asc';?>">Quantity</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=cartitem&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_cart_item']?>">
		<td class="shrink"><?php echo $element['id_cart']?></td>
		<td ><?php echo $this->productVariantList[$element['id_product_variant']]['name'] ?? '';?></td>
		<td ><?php echo $element['quantity']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_cart_item']?>" hx-post="<?=BASE_URL?>/htmx.php?component=cartitem&task=undelHtmx&id_cart_item=<?php echo $element['id_cart_item']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="11" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
	