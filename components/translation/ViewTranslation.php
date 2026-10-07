<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Translation;

class ViewTranslation extends \Apgenic\Classes\ViewTemplate {

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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=edit&id_translation=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table translation
 * @parameter ModelTranslation $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelTranslation $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_translation, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=translation&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Text&nbsp;key&nbsp;*:</label>
		<input required class="form-control" type="text" name="text_key" id="text_key" size="10" value="<?=htmlentities((string)(string)$data->text_key)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Lang&nbsp;*:</label>
		<input required class="form-control" type="text" name="lang" id="lang" size="10" value="<?=htmlentities((string)(string)$data->lang)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-12 col-md-12"><div class="form-group">
		<label><span>Text&nbsp;*:</span></label>
		<textarea required class="form-control" name="text" id="text" rows="4" cols="49"><?=$data->text?></textarea>
	    </div>
	    </div>
	
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_translation" id="id" value="<?=$data->id_translation ?>"/>
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
 * Display rows from the table translation
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
	<tr>	<th scope="col" id="id_translation"><a href="<?=BASE_URL?>/index.php?component=translation&task=viewlist&orderBy=id_translation&order=<?php if($orderBy == "id_translation") echo $order; else echo 'asc';?>">Id&nbsp;translation</a></th>
	<th scope="col" id="text_key"><a href="<?=BASE_URL?>/index.php?component=translation&task=viewlist&orderBy=text_key&order=<?php if($orderBy == "text_key") echo $order; else echo 'asc';?>">Text&nbsp;key</a></th>
	<th scope="col" id="lang"><a href="<?=BASE_URL?>/index.php?component=translation&task=viewlist&orderBy=lang&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>">Lang</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=translation&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=translation&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_translation']?></td>
		<td ><?php echo $element['text_key']?></td>
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=view&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="10" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table translation
 *
 * @parameter ModelTranslation $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelTranslation $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_translation != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;translation</h5></dt><dd class="col-sm-9"><?php echo $data->id_translation?></dd>
		        <dt class="col-sm-3"><h5>Text&nbsp;key</h5></dt><dd class="col-sm-9"><?php echo $data->text_key?></dd>
		        <dt class="col-sm-3"><h5>Lang</h5></dt><dd class="col-sm-9"><?php echo $data->lang?></dd>
		        <dt class="col-sm-3"><h5>Text</h5></dt><dd class="col-sm-9"><?php echo $data->text?></dd>
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
 * Display rows from the table translation
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=<?=$task?>&value=<?php echo $value?>&id_translation=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table translation with edit and del buttons
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
	<tr>	<th scope="col" id="id_translation"><a href="<?=BASE_URL?>/index.php?component=translation&task=editlist&orderBy=id_translation&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_translation") echo $order; else echo 'asc';?>">Id&nbsp;translation</a></th>
	<th scope="col" id="text_key"><a href="<?=BASE_URL?>/index.php?component=translation&task=editlist&orderBy=text_key&<?=$this->filtersGet?>&order=<?php if($orderBy == "text_key") echo $order; else echo 'asc';?>">Text&nbsp;key</a></th>
	<th scope="col" id="lang"><a href="<?=BASE_URL?>/index.php?component=translation&task=editlist&orderBy=lang&<?=$this->filtersGet?>&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>">Lang</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=translation&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=translation&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=translation&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_translation']?>">
		<td class="shrink"><?php echo $element['id_translation']?></td>
		<td ><?php echo $element['text_key']?></td>
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=view&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=edit&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_translation']?>" hx-post="<?=BASE_URL?>/htmx.php?component=translation&task=logicaldeleteHtmx&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
	<td colspan="10" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table translation with edit and del buttons
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
	<tr>	<th scope="col" id="id_translation"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=childlist&orderBy=id_translation&order=<?php if($orderBy == "id_translation") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;translation</a></th>
	<th scope="col" id="text_key"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=childlist&orderBy=text_key&order=<?php if($orderBy == "text_key") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Text&nbsp;key</a></th>
	<th scope="col" id="lang"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=childlist&orderBy=lang&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Lang</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_translation']?>">
		<td class="shrink"><?php echo $element['id_translation']?></td>
		<td ><?php echo $element['text_key']?></td>
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=view&showTabs=0&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=translation&task=childList&showTabs=0&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_translation']?>" hx-post="<?=BASE_URL?>/htmx.php?component=translation&task=delHtmx&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="10" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table translation
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
	<tr>	<th scope="col" id="id_translation"><a href="<?=BASE_URL?>/index.php?component=translation&task=trashedlist&orderBy=id_translation&order=<?php if($orderBy == "id_translation") echo $order; else echo 'asc';?>">Id&nbsp;translation</a></th>
	<th scope="col" id="text_key"><a href="<?=BASE_URL?>/index.php?component=translation&task=trashedlist&orderBy=text_key&order=<?php if($orderBy == "text_key") echo $order; else echo 'asc';?>">Text&nbsp;key</a></th>
	<th scope="col" id="lang"><a href="<?=BASE_URL?>/index.php?component=translation&task=trashedlist&orderBy=lang&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>">Lang</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=translation&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=translation&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_translation']?>">
		<td class="shrink"><?php echo $element['id_translation']?></td>
		<td ><?php echo $element['text_key']?></td>
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_translation']?>" hx-post="<?=BASE_URL?>/htmx.php?component=translation&task=undelHtmx&id_translation=<?php echo $element['id_translation']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="10" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
