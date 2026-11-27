<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\ProductVariant;

class ViewProductVariant extends \Apgenic\Classes\ViewTemplate {
/** The component has a list of the deleted entries (link Trash of the header) */
protected bool $trash = true;

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

    <ul id="tabs_list" class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=product_variant&task=edit&id_product_variant=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

        <li class="nav-item">
            <a href="#" id="tab_0" class="nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_product_variant]=<?=$id ?>&showTabs=0"><?=T_MENU_CARTITEM?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_1" class="nav-link <?php if($activeTab == 'customerorderitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=customerorderitem&task=childlist&filters[id_product_variant]=<?=$id ?>&showTabs=0"><?=T_MENU_CUSTOMERORDERITEM?></a>
        </li>

    </ul>



<?php
}
/**
 * Edit form for the table product_variant
 * @parameter ModelProductVariant $data
 * @parameter int $showTabs
 * @parameter array $message
 * @return void
 */
public function edit(ModelProductVariant $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_product_variant, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=productvariant&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="name"><?=T_FIELD_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="name" id="name" size="10" value="<?=htmlentities((string)(string)$data->name)?>"/>
	    </div>
	    </div>

		<div class="col-12">

		<div class="border-top pt-3">
		<input type="hidden" name="id_product_variant" id="id" value="<?=$data->id_product_variant ?>"/>
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
 * Display rows from the table product_variant
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
	<tr>	<th scope="col" id="id_product_variant" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'viewlist')?>?orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'viewlist')?>?orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'viewlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'viewlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="text-nowrap"><?php echo $element['id_product_variant']?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=view&id_product_variant=<?php echo $element['id_product_variant']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
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
 * Display details of a record from the table product_variant
 *
 * @parameter ModelProductVariant $data
 * @parameter array $message
 * @return void
 */
public function view(ModelProductVariant $data , array $message=null){
        $this->message($message);


	$i=1;

	if ($data->id_product_variant != null){
	?>

    <div class="card shadow-sm">
        <div class="card-body p-lg-4">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row mb-0">

		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID?></dt><dd class="col-sm-9"><?php echo $data->id_product_variant?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_NAME?></dt><dd class="col-sm-9"><?php echo $data->name?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_CREATED_AT?></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_UPDATED_AT?></dt><dd class="col-sm-9"><?php echo $data->updated_at?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_DELETED_AT?></dt><dd class="col-sm-9"><?php echo $data->deleted_at?></dd>
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
 * Display rows from the table product_variant
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=<?=$task?>&value=<?php echo $value?>&id_product_variant=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
    <?php
}

/**
 * Display rows from the table product_variant with edit and del buttons
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
	<tr>	<th scope="col" id="id_product_variant" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'editlist')?>?orderBy=id_product_variant&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'editlist')?>?orderBy=name&<?=$this->filtersGet?>&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'editlist')?>?orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'editlist')?>?orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th><a href="<?=self::url('productvariant', 'trashedlist')?>" class="btn btn-sm btn-outline-secondary" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3', 16)?></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product_variant']?>">
		<td class="text-nowrap"><?php echo $element['id_product_variant']?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=edit&id_product_variant=<?php echo $element['id_product_variant']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_product_variant']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productvariant&task=logicaldeleteHtmx&id_product_variant=<?php echo $element['id_product_variant']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
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
 * Display rows from the table product_variant with edit and del buttons
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
    $parentPKValue = rawurlencode((string)($filters[$parentPK] ?? ''));


	?>
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_product_variant" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=childlist&orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=childlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product_variant']?>">
		<td class="text-nowrap"><?php echo $element['id_product_variant']?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productvariant&task=childList&showTabs=0&id_product_variant=<?php echo $element['id_product_variant']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_product_variant']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productvariant&task=delHtmx&id_product_variant=<?php echo $element['id_product_variant']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="8" class="text-end">
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
 * Display trashed rows from the table product_variant
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
	<tr>	<th scope="col" id="id_product_variant" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'trashedlist')?>?orderBy=id_product_variant&order=<?php if($orderBy == "id_product_variant") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'trashedlist')?>?orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>"><?=T_FIELD_NAME?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'trashedlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('productvariant', 'trashedlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product_variant']?>">
		<td class="text-nowrap"><?php echo $element['id_product_variant']?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_product_variant']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productvariant&task=undelHtmx&id_product_variant=<?php echo $element['id_product_variant']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
