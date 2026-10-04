<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Discount;

class ViewDiscount extends \Apgenic\Classes\ViewTemplate {

public string $filtersGet = '';

public $fileters = array();

function __construct() {
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=edit&id_discount=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table discount
 * @parameter ModelDiscount $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelDiscount $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    if($showTabs == 1){
	        self::tabs($data->id_discount, 'info');
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Date&nbsp;from:</label>
		<input  class="form-control" type="datetime-local" name="date_from" id="date_from" value="<?=htmlentities((string)$data->date_from)?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Date&nbsp;to:</label>
		<input  class="form-control" type="datetime-local" name="date_to" id="date_to" value="<?=htmlentities((string)$data->date_to)?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Id&nbsp;category:</label>
		<input required="" class="form-control" type="number" name="id_category" id="id_category" value="<?=$data->id_category?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Id&nbsp;product:</label>
		<input required="" class="form-control" type="number" name="id_product" id="id_product" value="<?=$data->id_product?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Amount:</label>
		<input required="" class="form-control" type="number" name="amount" id="amount" value="<?=$data->amount?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Percentage:</label>
		<input required="" class="form-control" type="number" name="percentage" id="percentage" value="<?=$data->percentage?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_discount" id="id" value="<?=$data->id_discount ?>"/>
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
 * Display rows from the table discount
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
	<tr>	<th scope="col" id="id_discount"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=id_discount&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>">Id&nbsp;discount</a></th>
	<th scope="col" id="date_from"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=date_from&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>">Date&nbsp;from</a></th>
	<th scope="col" id="date_to"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=date_to&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>">Date&nbsp;to</a></th>
	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="amount"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=amount&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>">Amount</a></th>
	<th scope="col" id="percentage"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=percentage&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>">Percentage</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=discount&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=view&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="13" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table discount
 *
 * @parameter ModelDiscount $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelDiscount $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_discount != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;discount</h5></dt><dd class="col-sm-9"><?php echo $data->id_discount?></dd>
		        <dt class="col-sm-3"><h5>Date&nbsp;from</h5></dt><dd class="col-sm-9"><?php echo $data->date_from?></dd>
		        <dt class="col-sm-3"><h5>Date&nbsp;to</h5></dt><dd class="col-sm-9"><?php echo $data->date_to?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;category</h5></dt><dd class="col-sm-9"><?php echo $data->id_category?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;product</h5></dt><dd class="col-sm-9"><?php echo $data->id_product?></dd>
		        <dt class="col-sm-3"><h5>Amount</h5></dt><dd class="col-sm-9"><?php echo $data->amount?></dd>
		        <dt class="col-sm-3"><h5>Percentage</h5></dt><dd class="col-sm-9"><?php echo $data->percentage?></dd>
		        <dt class="col-sm-3"><h5>Created&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
		        <dt class="col-sm-3"><h5>Updated&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->updated_at?></dd>
		        <dt class="col-sm-3"><h5>Deleted&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->deleted_at?></dd>
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
 * Display rows from the table discount
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=<?=$task?>&value=<?php echo $value?>&id_discount=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table discount with edit and del buttons
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
	<tr>	<th scope="col" id="id_discount"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=id_discount&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>">Id&nbsp;discount</a></th>
	<th scope="col" id="date_from"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=date_from&<?=$this->filtersGet?>&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>">Date&nbsp;from</a></th>
	<th scope="col" id="date_to"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=date_to&<?=$this->filtersGet?>&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>">Date&nbsp;to</a></th>
	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=id_category&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="amount"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=amount&<?=$this->filtersGet?>&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>">Amount</a></th>
	<th scope="col" id="percentage"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=percentage&<?=$this->filtersGet?>&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>">Percentage</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=discount&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_discount']?>">
		<td class="shrink"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=view&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=edit&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_discount']?>" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=logicaldeleteHtmx&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
	<td colspan="13" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table discount with edit and del buttons
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
	<tr>	<th scope="col" id="id_discount"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=id_discount&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;discount</a></th>
	<th scope="col" id="date_from"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=date_from&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Date&nbsp;from</a></th>
	<th scope="col" id="date_to"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=date_to&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Date&nbsp;to</a></th>
	<th scope="col" id="id_category"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_product"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;product</a></th>
	<th scope="col" id="amount"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=amount&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Amount</a></th>
	<th scope="col" id="percentage"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=percentage&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Percentage</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_discount']?>">
		<td class="shrink"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=view&showTabs=0&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=discount&task=childList&showTabs=0&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_discount']?>" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=delHtmx&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="13" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table discount
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
	<tr>	<th scope="col" id="id_discount"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=id_discount&order=<?php if($orderBy == "id_discount") echo $order; else echo 'asc';?>">Id&nbsp;discount</a></th>
	<th scope="col" id="date_from"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=date_from&order=<?php if($orderBy == "date_from") echo $order; else echo 'asc';?>">Date&nbsp;from</a></th>
	<th scope="col" id="date_to"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=date_to&order=<?php if($orderBy == "date_to") echo $order; else echo 'asc';?>">Date&nbsp;to</a></th>
	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="amount"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=amount&order=<?php if($orderBy == "amount") echo $order; else echo 'asc';?>">Amount</a></th>
	<th scope="col" id="percentage"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=percentage&order=<?php if($orderBy == "percentage") echo $order; else echo 'asc';?>">Percentage</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=discount&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_discount']?>">
		<td class="shrink"><?php echo $element['id_discount']?></td>
		<td ><?php echo $element['date_from']?></td>
		<td ><?php echo $element['date_to']?></td>
		<td ><?php echo $element['id_category']?></td>
		<td ><?php echo $element['id_product']?></td>
		<td ><?php echo $element['amount']?></td>
		<td ><?php echo $element['percentage']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_discount']?>" hx-post="<?=BASE_URL?>/htmx.php?component=discount&task=undelHtmx&id_discount=<?php echo $element['id_discount']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="13" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
	