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

    <ul id="tabs_list" class="nav nav-tabs">
      <li class="nav-item">
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=media&task=edit&id_media=<?=$id?>">Info</a>
      </li>
      
        <li class="nav-item">
            <a id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'user') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=user&task=childlist&filters[id_media]=<?=$id ?>&showTabs=0">User</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_1" class="form_tabs nav-link <?php if($activeTab == 'productmedia') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productmedia&task=childlist&filters[id_media]=<?=$id ?>&showTabs=0">Product_media</a>
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
		
		<form method="POST" enctype="multipart/form-data" hx-encoding="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
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

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=$isNew ? 'Image&nbsp;*:' : 'Replace&nbsp;the&nbsp;image:'?></label>
		<input <?php if($isNew) echo 'required';?> class="form-control-file" type="file" name="file" id="file" accept="<?=implode(',', array_keys(MediaImage::TYPES))?>"/>
		<small class="form-text text-muted">JPEG, PNG, GIF or WebP, <?=round(MediaImage::MAX_FILE_SIZE / 1048576)?> MB maximum. Sizes created: <?=implode(', ', $sizes)?>.</small>
	    </div>
	    </div>

        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Product:</label>
            <select  class="form-control" name="id_product" id="id_product">
                <option value=""></option>
        <?php
        foreach ($this->productList as $key => $val){
                ?>
                <option value="<?=$val['id_product']?>"><?=$val['name']?></option>
        <?php 
        }
        ?>
            </select>
            <small class="form-text text-muted">The file is named after the product<?php if($isNew) echo ' and the image is added to its images';?>. Without product: after the alt text, else the name of the file.</small>
		</div>
		</div>

        <?php if(!$isNew){ ?>
        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Current&nbsp;image:</label>
		<div>
		<?=MediaImage::thumbnail($data->filename, (string)$data->alt_text)?>
		<?=htmlentities((string)$data->filename)?>
		<small class="form-text text-muted"><?=htmlentities((string)$data->mime_type)?><?php if($data->width && $data->height) echo ', '.(int)$data->width.' x '.(int)$data->height.' px';?><?php if($data->size_bytes) echo ', '.round($data->size_bytes / 1024).' KB';?></small>
		</div>
	    </div>
	    </div>
        <?php } ?>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Alt&nbsp;text:</label>
		<input  class="form-control" type="text" name="alt_text" id="alt_text" size="10" value="<?=htmlentities((string)(string)$data->alt_text)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-12 col-md-12"><div class="form-group">
		<label><span>Caption:</span></label>
		<textarea  class="form-control" name="caption" id="caption" rows="4" cols="49"><?=$data->caption?></textarea>
	    </div>
	    </div>
	
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_media" id="id" value="<?=$data->id_media ?>"/>
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
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_media"><a href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>">Id&nbsp;media</a></th>
	<th scope="col" id="filename"><a href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=filename&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>">Filename</a></th>
	<th scope="col" id="mime_type"><a href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=mime_type&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>">Mime&nbsp;type</a></th>
	<th scope="col" id="size_bytes"><a href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=size_bytes&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>">Size&nbsp;bytes</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=media&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=view&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn">
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
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;media</h5></dt><dd class="col-sm-9"><?php echo $data->id_media?></dd>
		        <dt class="col-sm-3"><h5>Filename</h5></dt><dd class="col-sm-9"><?=MediaImage::thumbnail($data->filename, (string)$data->alt_text)?> <?php echo $data->filename?></dd>
		        <dt class="col-sm-3"><h5>Mime&nbsp;type</h5></dt><dd class="col-sm-9"><?php echo $data->mime_type?></dd>
		        <dt class="col-sm-3"><h5>Size&nbsp;bytes</h5></dt><dd class="col-sm-9"><?php echo $data->size_bytes?></dd>
		        <dt class="col-sm-3"><h5>Width</h5></dt><dd class="col-sm-9"><?php echo $data->width?></dd>
		        <dt class="col-sm-3"><h5>Height</h5></dt><dd class="col-sm-9"><?php echo $data->height?></dd>
		        <dt class="col-sm-3"><h5>Alt&nbsp;text</h5></dt><dd class="col-sm-9"><?php echo $data->alt_text?></dd>
		        <dt class="col-sm-3"><h5>Caption</h5></dt><dd class="col-sm-9"><?php echo $data->caption?></dd>
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
 * Display rows from the table media
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=<?=$task?>&value=<?php echo $value?>&id_media=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
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
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_media"><a href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=id_media&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>">Id&nbsp;media</a></th>
	<th scope="col" id="filename"><a href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=filename&<?=$this->filtersGet?>&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>">Filename</a></th>
	<th scope="col" id="mime_type"><a href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=mime_type&<?=$this->filtersGet?>&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>">Mime&nbsp;type</a></th>
	<th scope="col" id="size_bytes"><a href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=size_bytes&<?=$this->filtersGet?>&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>">Size&nbsp;bytes</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=media&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_media']?>">
		<td class="shrink"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=view&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=edit&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=logicaldeleteHtmx&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_media"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;media</a></th>
	<th scope="col" id="filename"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=filename&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Filename</a></th>
	<th scope="col" id="mime_type"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=mime_type&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Mime&nbsp;type</a></th>
	<th scope="col" id="size_bytes"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=size_bytes&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Size&nbsp;bytes</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_media']?>">
		<td class="shrink"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=view&showTabs=0&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=media&task=childList&showTabs=0&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=delHtmx&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
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
	<table class="table table-bordered table-sm">
	<thead>
	<tr>	<th scope="col" id="id_media"><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>">Id&nbsp;media</a></th>
	<th scope="col" id="filename"><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=filename&order=<?php if($orderBy == "filename") echo $order; else echo 'asc';?>">Filename</a></th>
	<th scope="col" id="mime_type"><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=mime_type&order=<?php if($orderBy == "mime_type") echo $order; else echo 'asc';?>">Mime&nbsp;type</a></th>
	<th scope="col" id="size_bytes"><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=size_bytes&order=<?php if($orderBy == "size_bytes") echo $order; else echo 'asc';?>">Size&nbsp;bytes</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=media&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_media']?>">
		<td class="shrink"><?php echo $element['id_media']?></td>
		<td ><?=MediaImage::thumbnail($element['filename'], (string)($element['alt_text'] ?? ''))?> <?php echo $element['filename']?></td>
		<td ><?php echo $element['mime_type']?></td>
		<td ><?php echo $element['size_bytes']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=media&task=undelHtmx&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
