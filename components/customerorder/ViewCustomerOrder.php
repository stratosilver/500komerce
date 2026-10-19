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

    <ul id="tabs_list" class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=customer_order&task=edit&id_customer_order=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

        <li class="nav-item">
            <a href="#" id="tab_0" class="nav-link <?php if($activeTab == 'customerorderitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=customerorderitem&task=childlist&filters[id_customer_order]=<?=$id ?>&showTabs=0"><?=T_MENU_CUSTOMERORDERITEM?></a>
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

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">


                        <?php
                        if(!isset($this->filters['id_user'])){ ?>

        <div class="col-lg-4 col-md-6">
        <div class="mb-3">
            <label class="form-label" for="id_user"><?=T_FIELD_ID_USER?>&nbsp;*</label>
            <select  class="form-select" name="id_user" id="id_user">
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

		<div class="border-top pt-3">
		<input type="hidden" name="id_customer_order" id="id" value="<?=$data->id_customer_order ?>"/>
		<input type="submit" name="Envoyer" title="<?=T_SAVE?>" value="<?=T_SAVE?>" class="btn btn-primary">
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_customer_order" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=viewlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=viewlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="text-nowrap"><?php echo $element['id_customer_order']?></td>
		<td ><?php echo $this->productList4[$element['id_user']]['name'] ?? '';?></td>
		<td ><?php echo $element['created_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=view&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
            <?=self::icon('view', 16)?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>

	</table>
	</div>
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

    <div class="card shadow-sm">
        <div class="card-body p-lg-4">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row mb-0">

		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID?></dt><dd class="col-sm-9"><?php echo $data->id_customer_order?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID_USER?></dt><dd class="col-sm-9"><?php echo $this->productList4[$data->id_user]['name'] ?? '';?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_CREATED_AT?></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
	             </dl></div>
            </div>
	    </div>
	</div>
	<?php
    }
    else{
        $this->message(array('text' => T_NO_DATA, 'type' => 'warning'));
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
    $buttonIcon[0] = self::icon('x-lg', 16);
    $buttonIcon[1] = self::icon('check2', 16);

    ?>
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=<?=$task?>&value=<?php echo $value?>&id_customer_order=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_customer_order" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=editlist&orderBy=id_customer_order&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order']?>">
		<td class="text-nowrap"><?php echo $element['id_customer_order']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->productList4[$element['id_user']]['name'] ?? '';?>
                                            </a>
                                            </td>

		<td ><?php echo $element['created_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=view&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
			<?=self::icon('view', 16);?></a></td>
			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=edit&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_customer_order']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=delHtmx&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
            <?=self::icon('x-lg', 16);?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>

	</table>
	</div>
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_customer_order" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order']?>">
		<td class="text-nowrap"><?php echo $element['id_customer_order']?></td>
		<td >
                                            <a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->productList4[$element['id_user']]['name'];?>
                                            </a>
                                            </td>

		<td ><?php echo $element['created_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=view&showTabs=0&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
	        <?=self::icon('view', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=customerorder&task=childList&showTabs=0&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_customer_order']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=delHtmx&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="6" class="text-end">
	    <a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?= BASE_URL ?>/htmx.php?component=<?=COMPONENT?>&task=edit&showTabs=0&<?=$parentPK?>=<?=$parentPKValue?>&<?=$this->filtersGet?>" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
        <?=self::icon('plus-lg', 16);?> <?=T_ADD?>
    </a>
    </td>
	</tr>
	</tfoot>

	</table>
	</div>

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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-sm table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_customer_order" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=trashedlist&orderBy=id_customer_order&order=<?php if($orderBy == "id_customer_order") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID_USER?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=customerorder&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_customer_order']?>">
		<td class="text-nowrap"><?php echo $element['id_customer_order']?></td>
		<td ><?php echo $this->productList4[$element['id_user']]['name'] ?? '';?></td>
		<td ><?php echo $element['created_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_customer_order']?>" hx-post="<?=BASE_URL?>/htmx.php?component=customerorder&task=undelHtmx&id_customer_order=<?php echo $element['id_customer_order']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
            <?=self::icon('undel', 16)?>
            </a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>

	</table>
	</div>
	<?php
}






}
