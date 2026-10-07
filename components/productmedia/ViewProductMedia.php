<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\ProductMedia;

class ViewProductMedia extends \Apgenic\Classes\ViewTemplate {
var $mediaList1 = array();
var $productList1 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($mediaList1, $productList1) {
$this->mediaList1 = $mediaList1;
$this->productList1 = $productList1;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=product_media&task=edit&id_product=<?=$id?>&id_media=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table product_media
 * @parameter ModelProductMedia $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelProductMedia $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_product, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=productmedia&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

                        <?php 
                        if(!isset($this->filters['id_product'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;product&nbsp;*:</label>
            <select  class="form-control" name="id_product" id="id_product">
                <option value=""></option>
        <?php
        foreach ($this->productList1 as $key => $val){
                ?>
                <option value="<?=$val['id_product']?>" <?php if($data->id_product == $val['id_product']) echo 'selected="selected"';?>><?=$val['name']?></option>
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
                            
                            <input type="hidden" name="id_product" id="id_product" value="<?php if(!isset($data->id_product) || $data->id_product < 1) echo $_GET['filters']['id_product']; else echo $data->id_product?>"/>
                            <?php
                        }
                        ?>                     
                        

                        <?php 
                        if(!isset($this->filters['id_media'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;media&nbsp;*:</label>
            <select  class="form-control" name="id_media" id="id_media" onchange="var i=document.getElementById('id_media_thumb'),u=this.options[this.selectedIndex].getAttribute('data-thumb')||'';i.src=u;i.style.display=u?'':'none';">
                <option value=""></option>
        <?php
        foreach ($this->mediaList1 as $key => $val){
                ?>
                <option data-thumb="<?=htmlspecialchars(\Apgenic\Media\MediaImage::thumbnailUrl($val['filename']))?>" value="<?=$val['id_media']?>" <?php if($data->id_media == $val['id_media']) echo 'selected="selected"';?>><?=$val['filename']?></option>
        <?php 
        }
        ?>
       
            </select>
            <?php $thumbUrl = \Apgenic\Media\MediaImage::thumbnailUrl($this->mediaList1[$data->id_media]['filename'] ?? ''); ?>
            <div><img id="id_media_thumb" class="media-thumb mt-2" src="<?=htmlspecialchars($thumbUrl)?>" alt="" <?php if($thumbUrl == '') echo 'style="display:none"';?>></div>
		</div>
		</div>
		
                        <?php
                        }
                        else{
                            ?>
                            
                            <input type="hidden" name="id_media" id="id_media" value="<?php if(!isset($data->id_media) || $data->id_media < 1) echo $_GET['filters']['id_media']; else echo $data->id_media?>"/>
                            <?php
                        }
                        ?>                     
                        

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Position:</label>
		<input required="" class="form-control" type="number" name="position" id="position" value="<?=$data->position?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
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
 * Display rows from the table product_media
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
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=viewlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_media"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=viewlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>">Id&nbsp;media</a></th>
	<th scope="col" id="position"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=viewlist&orderBy=position&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>">Position</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $this->productList1[$element['id_product']]['name'] ?? '';?></td>
		<td ><?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList1[$element['id_media']]['filename'] ?? '')?> <?php echo $this->mediaList1[$element['id_media']]['filename'] ?? '';?></td>
		<td ><?php echo $element['position']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=view&id_product=<?php echo $element['id_product']?>&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>" class="btn">
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
 * Display details of a record from the table product_media
 *
 * @parameter ModelProductMedia $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelProductMedia $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_product != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;product</h5></dt><dd class="col-sm-9"><?php echo $this->productList1[$data->id_product]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;media</h5></dt><dd class="col-sm-9"><?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList1[$data->id_media]['filename'] ?? '')?> <?php echo $this->mediaList1[$data->id_media]['filename'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Position</h5></dt><dd class="col-sm-9"><?php echo $data->position?></dd>
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
 * Display rows from the table product_media
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=<?=$task?>&value=<?php echo $value?>&id_product=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table product_media with edit and del buttons
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
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=editlist&orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_media"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=editlist&orderBy=id_media&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>">Id&nbsp;media</a></th>
	<th scope="col" id="position"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=editlist&orderBy=position&<?=$this->filtersGet?>&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>">Position</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?><?php echo $element['id_media']?>">
		<td class="shrink">
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=editList&id_product=<?php echo $element['id_product']?>">
                                            <?php echo $this->productList1[$element['id_product']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=editList&id_media=<?php echo $element['id_media']?>">
                                            <?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList1[$element['id_media']]['filename'] ?? '')?> <?php echo $this->mediaList1[$element['id_media']]['filename'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['position']?></td>
	<td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?><?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productmedia&task=delHtmx&id_product=<?php echo $element['id_product']?>&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">            
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
	<td colspan="4" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table product_media with edit and del buttons
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
	<tr>	<th scope="col" id="id_product"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_media"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=childlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;media</a></th>
	<th scope="col" id="position"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=childlist&orderBy=position&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Position</a></th>

		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?><?php echo $element['id_media']?>">
		<td class="shrink">
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=editList&id_product=<?php echo $element['id_product']?>">
                                            <?php echo $this->productList1[$element['id_product']]['name'];?>
                                            </a>
                                            </td>
                                            
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productmedia&task=editList&id_media=<?php echo $element['id_media']?>">
                                            <?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList1[$element['id_media']]['filename'] ?? '')?> <?php echo $this->mediaList1[$element['id_media']]['filename'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['position']?></td>
	    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?><?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productmedia&task=delHtmx&id_product=<?php echo $element['id_product']?>&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="4" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table product_media
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
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=trashedlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_media"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=trashedlist&orderBy=id_media&order=<?php if($orderBy == "id_media") echo $order; else echo 'asc';?>">Id&nbsp;media</a></th>
	<th scope="col" id="position"><a href="<?=BASE_URL?>/index.php?component=productmedia&task=trashedlist&orderBy=position&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>">Position</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?><?php echo $element['id_media']?>">
		<td class="shrink"><?php echo $this->productList1[$element['id_product']]['name'] ?? '';?></td>
		<td ><?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList1[$element['id_media']]['filename'] ?? '')?> <?php echo $this->mediaList1[$element['id_media']]['filename'] ?? '';?></td>
		<td ><?php echo $element['position']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?><?php echo $element['id_media']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productmedia&task=undelHtmx&id_product=<?php echo $element['id_product']?>&id_media=<?php echo $element['id_media']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
