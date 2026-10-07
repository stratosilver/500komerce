<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\CustomerOrder;

class ViewCustomerOrder extends \Apgenic\Classes\ViewTemplate {
var $productList4 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($productList4) {
$this->productList4 = $productList4;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=customer_order&task=edit&id_customer_order=<?=$id?>">Info</a>
      </li>
      
        <li class="nav-item">
            <a id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'customerorderitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=customerorderitem&task=childlist&filters[id_customer_order]=<?=$id ?>&showTabs=0">Customer_order_item</a>
        </li>
        
    </ul>
    

     
<?php
}
/**
 * Edit form for the table customer_order
 * @parameter ModelCustomerOrder $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelCustomerOrder $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_customer_order, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

                        <?php 
                        if(!isset($this->filters['id_user'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;user&nbsp;*:</label>
            <select  class="form-control" name="id_user" id="id_user">
                <option value=""></option>
        <?php
        foreach ($this->productList4 as $key => $val){
                ?>
                <option value="<?=$val['id_user']?>" <?php if($data->id_user == $val['id_user']) echo 'selected="selected"';?>><?=$val['name']?></option>
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
                        
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_customer_order" id="id" value="<?=$data->id_customer_order ?>"/>
		<input type="submit" name="Envoyer" title="Envoyer" value="Save" class="btn btn-outline btn-outline-primary">
		</div>
		</div>


    </div>
	</fieldset>
	</form>
	<?php
	// End of #core_content
	if($coreContent){
	    echo '</div>';
	}
	?>
	</div>
	<?php

	}

	

/**
 * Display rows from the table customer_order
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
	<tr>	<th scope="col" id="id_customer_order"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=viewlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=viewlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_customer_order']?></td>
		<td ><?php echo $this->productList4[$element['id_user']]['name'] ?? '';?></td>
		<td ><?php echo $element['created_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=view&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="6" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table customer_order
 *
 * @parameter ModelCustomerOrder $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelCustomerOrder $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_customer_order != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;customer&nbsp;order</h5></dt><dd class="col-sm-9"><?php echo $data->id_customer_order?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;user</h5></dt><dd class="col-sm-9"><?php echo $this->productList4[$data->id_user]['name'] ?? '';?></dd>
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
 * Display rows from the table customer_order
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=<?=$task?>&value=<?php echo $value?>&id_customer_order=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table customer_order with edit and del buttons
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
	<tr>	<th scope="col" id="id_customer_order"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=editlist&orderBy=id_customer_order&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>

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
		<tr id="row-<?php echo $element['id_customer_order']?>">
		<td class="shrink"><?php echo $element['id_customer_order']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->productList4[$element['id_user']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['created_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=view&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=edit&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_customer_order']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=delHtmx&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">            
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
	<td colspan="6" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table customer_order with edit and del buttons
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
	<tr>	<th scope="col" id="id_customer_order"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="id_user"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;user</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order']?>">
		<td class="shrink"><?php echo $element['id_customer_order']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->productList4[$element['id_user']]['name'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['created_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=view&showTabs=0&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childList&showTabs=0&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_customer_order']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=delHtmx&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="6" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table customer_order
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
	<tr>	<th scope="col" id="id_customer_order"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=trashedlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>">Id&nbsp;customer&nbsp;order</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=customerorder&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order']?>">
		<td class="shrink"><?php echo $element['id_customer_order']?></td>
		<td ><?php echo $this->productList4[$element['id_user']]['name'] ?? '';?></td>
		<td ><?php echo $element['created_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_customer_order']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=undelHtmx&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="6" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
