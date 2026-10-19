<?php
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */

declare(strict_types=1);
namespace Apgenic\Media;

class ViewMedia extends \Apgenic\Classes\ViewTemplate {
/** The component has a list of the deleted entries (link Trash of the header) */
protected bool $trash = true;

public string $filtersGet = '';

public $fileters = array();

var $productList = array();

function __construct($productList = array()) {
$this->productList = $productList;
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
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=media&task=edit&id_media=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

        <li class="nav-item">
            <a href="#" id="tab_0" class="nav-link <?php if($activeTab == 'user') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=user&task=childlist&filters[id_media]=<?=$id ?>&showTabs=0"><?=T_MENU_USER?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_1" class="nav-link <?php if($activeTab == 'productmedia') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productmedia&task=childlist&filters[id_media]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCTMEDIA?></a>
        </li>

    </ul>



<?php
}
/**
 * Edit form for the table media
 * @parameter ModelMedia $data
 * @parameter int $showTabs
 * @parameter array $message
 * @return void
 */
public function edit(ModelMedia $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_media, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-encoding="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">


        <?php
        $isNew = !($data->id_media > 0);
        $sizes = array();
        foreach(MediaImage::SIZES as $sizeName => $box){
            $sizes[] = $sizeName.' '.$box[0].'x'.$box[1];
        }
        ?>

        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="file"><?=$isNew ? T_IMAGE.'&nbsp;*' : T_REPLACE_IMAGE?></label>
		<input <?php if($isNew) echo 'required';?> class="form-control" type="file" name="file" id="file" accept="<?=implode(',', array_keys(MediaImage::TYPES))?>"/>
		<small class="form-text"><?=sprintf(T_IMAGE_HELP, round(MediaImage::MAX_FILE_SIZE / 1048576), implode(', ', $sizes))?></small>
	    </div>
	    </div>

        <div class="col-lg-4 col-md-6">
        <div class="mb-3">
            <label class="form-label" for="id_product"><?=T_FIELD_PRODUCT?></label>
            <select  class="form-select" name="id_product" id="id_product">
                <option value=""></option>
        <?php
        foreach ($this->productList as $key => $val){
                ?>
                <option value="<?=$val['id_product']?>"><?=$val['name']?></option>
        <?php
        }
        ?>
            </select>
            <small class="form-text"><?=$isNew ? T_IMAGE_PRODUCT_HELP_NEW : T_IMAGE_PRODUCT_HELP?></small>
		</div>
		</div>

        <?php if(!$isNew){ ?>
        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<div class="form-label"><?=T_FIELD_CURRENT_IMAGE?></div>
		<div>
		<?=MediaImage::thumbnail($data->filename, (string)$data->alt_text)?>
		<?=htmlentities((string)$data->filename)?>
		<small class="form-text"><?=htmlentities((string)$data->mime_type)?><?php if($data->width && $data->height) echo ', '.(int)$data->width.' x '.(int)$data->height.' px';?><?php if($data->size_bytes) echo ', '.round($data->size_bytes / 1024).' KB';?></small>
		</div>
	    </div>
	    </div>
        <?php } ?>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="alt_text"><?=T_FIELD_ALT_TEXT?></label>
		<input  class="form-control" type="text" name="alt_text" id="alt_text" size="10" value="<?=htmlentities((string)(string)$data->alt_text)?>"/>
	    </div>
	    </div>


        <div class="col-12"><div class="mb-3">
		<label class="form-label" for="caption"><?=T_FIELD_CAPTION?></label>
		<textarea  class="form-control" name="caption" id="caption" rows="4" cols="49"><?=$data->caption?></textarea>
	    </div>
	    </div>

		<div class="col-12">

		<div class="border-top pt-3">
		<input type="hidden" name="id_media" id="id" value="<?=$data->id_media ?>"/>
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
 * Display rows from the table media
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
	<tr>	<th scope="col" id="id_media" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="filename" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=filename&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>"><?=T_FIELD_FILENAME?></a></th>
	<th scope="col" id="mime_type" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=mime_type&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>"><?=T_FIELD_MIME_TYPE?></a></th>
	<th scope="col" id="size_bytes" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=size_bytes&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>"><?=T_FIELD_SIZE_BYTES?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="text-nowrap"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=view&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
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
 * Display details of a record from the table media
 *
 * @parameter ModelMedia $data
 * @parameter array $message
 * @return void
 */
public function view(ModelMedia $data , array $message=null){
        $this->message($message);


	$i=1;

	if ($data->id_media != null){
	?>

    <div class="card shadow-sm">
        <div class="card-body p-lg-4">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row mb-0">

		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID?></dt><dd class="col-sm-9"><?php echo $data->id_media?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_FILENAME?></dt><dd class="col-sm-9"><?=MediaImage::thumbnail($data->filename, (string)$data->alt_text)?> <?php echo $data->filename?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_MIME_TYPE?></dt><dd class="col-sm-9"><?php echo $data->mime_type?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_SIZE_BYTES?></dt><dd class="col-sm-9"><?php echo $data->size_bytes?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_WIDTH?></dt><dd class="col-sm-9"><?php echo $data->width?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_HEIGHT?></dt><dd class="col-sm-9"><?php echo $data->height?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ALT_TEXT?></dt><dd class="col-sm-9"><?php echo $data->alt_text?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_CAPTION?></dt><dd class="col-sm-9"><?php echo $data->caption?></dd>
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
 * Display rows from the table media
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=<?=$task?>&value=<?php echo $value?>&id_media=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
    <?php
}

/**
 * Display rows from the table media with edit and del buttons
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
	<tr>	<th scope="col" id="id_media" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=id_media&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="filename" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=filename&<?=$this->filtersGet?>&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>"><?=T_FIELD_FILENAME?></a></th>
	<th scope="col" id="mime_type" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=mime_type&<?=$this->filtersGet?>&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>"><?=T_FIELD_MIME_TYPE?></a></th>
	<th scope="col" id="size_bytes" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=size_bytes&<?=$this->filtersGet?>&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>"><?=T_FIELD_SIZE_BYTES?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
		<th><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist" class="btn btn-sm btn-outline-secondary" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3', 16)?></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_media']?>">
		<td class="text-nowrap"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=view&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
			<?=self::icon('view', 16);?></a></td>
			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=edit&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=logicaldeleteHtmx&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
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
 * Display rows from the table media with edit and del buttons
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
	<tr>	<th scope="col" id="id_media" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="filename" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=filename&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_FILENAME?></a></th>
	<th scope="col" id="mime_type" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=mime_type&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_MIME_TYPE?></a></th>
	<th scope="col" id="size_bytes" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=size_bytes&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_SIZE_BYTES?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

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
		<tr id="row-<?php echo $element['id_media']?>">
		<td class="text-nowrap"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=view&showTabs=0&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
	        <?=self::icon('view', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childList&showTabs=0&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=delHtmx&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="14" class="text-end">
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
 * Display trashed rows from the table media
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
	<tr>	<th scope="col" id="id_media" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="filename" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=filename&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>"><?=T_FIELD_FILENAME?></a></th>
	<th scope="col" id="mime_type" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=mime_type&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>"><?=T_FIELD_MIME_TYPE?></a></th>
	<th scope="col" id="size_bytes" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=size_bytes&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>"><?=T_FIELD_SIZE_BYTES?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_media']?>">
		<td class="text-nowrap"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=undelHtmx&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
