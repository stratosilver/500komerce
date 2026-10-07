<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Category;

class ViewCategory extends \Apgenic\Classes\ViewTemplate {
var $categoryList1 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($categoryList1) {
$this->categoryList1 = $categoryList1;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=category&task=edit&id_category=<?=$id?>">Info</a>
      </li>
      
        <li class="nav-item">
            <a id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'category') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=category&task=childlist&filters[id_parent]=<?=$id ?>&showTabs=0">Category</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_1" class="form_tabs nav-link <?php if($activeTab == 'productcategory') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=productcategory&task=childlist&filters[id_category]=<?=$id ?>&showTabs=0">Product_category</a>
        </li>
        
    </ul>
    

     
<?php
}
/**
 * Edit form for the table category
 * @parameter ModelCategory $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelCategory $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_category, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=category&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

                        <?php 
                        if(!isset($this->filters['id_parent'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;parent:</label>
            <select  class="form-control" name="id_parent" id="id_parent">
                <option value=""></option>
        <?php
        foreach ($this->categoryList1 as $key => $val){
                ?>
                <option value="<?=$val['id_category']?>" <?php if($data->id_parent == $val['id_category']) echo 'selected="selected"';?>><?=$val['name']?></option>
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
                            
                            <input type="hidden" name="id_parent" id="id_parent" value="<?php if(!isset($data->id_parent) || $data->id_parent < 1) echo $_GET['filters']['id_parent']; else echo $data->id_parent?>"/>
                            <?php
                        }
                        ?>                     
                        

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Name&nbsp;*:</label>
		<input required class="form-control" type="text" name="name" id="name" size="10" value="<?=htmlentities((string)(string)$data->name)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Slug&nbsp;*:</label>
		<input required class="form-control" type="text" name="slug" id="slug" size="10" value="<?=htmlentities((string)(string)$data->slug)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Position:</label>
		<input required="" class="form-control" type="number" name="position" id="position" value="<?=$data->position?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_category" id="id" value="<?=$data->id_category ?>"/>
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
 * Display rows from the table category
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
	<tr>	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_parent"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=id_parent&order=<?php if($orderBy == "id_parent") echo $order; else echo 'asc';?>">Id&nbsp;parent</a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>">Name</a></th>
	<th scope="col" id="slug"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=slug&order=<?php if($orderBy == "slug") echo $order; else echo 'asc';?>">Slug</a></th>
	<th scope="col" id="position"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=position&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>">Position</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=category&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_category']?></td>
		<td ><?php echo $this->categoryList1[$element['id_parent']]['name'] ?? '';?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['slug']?></td>
		<td ><?php echo $element['position']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=view&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>" class="btn">
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
 * Display details of a record from the table category
 *
 * @parameter ModelCategory $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelCategory $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_category != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;category</h5></dt><dd class="col-sm-9"><?php echo $data->id_category?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;parent</h5></dt><dd class="col-sm-9"><?php echo $this->categoryList1[$data->id_parent]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Name</h5></dt><dd class="col-sm-9"><?php echo $data->name?></dd>
		        <dt class="col-sm-3"><h5>Slug</h5></dt><dd class="col-sm-9"><?php echo $data->slug?></dd>
		        <dt class="col-sm-3"><h5>Position</h5></dt><dd class="col-sm-9"><?php echo $data->position?></dd>
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
 * Display rows from the table category
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=<?=$task?>&value=<?php echo $value?>&id_category=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table category with edit and del buttons
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
	<tr>	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=id_category&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_parent"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=id_parent&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_parent") echo $order; else echo 'asc';?>">Id&nbsp;parent</a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=name&<?=$this->filtersGet?>&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>">Name</a></th>
	<th scope="col" id="slug"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=slug&<?=$this->filtersGet?>&order=<?php if($orderBy == "slug") echo $order; else echo 'asc';?>">Slug</a></th>
	<th scope="col" id="position"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=position&<?=$this->filtersGet?>&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>">Position</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=category&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_category']?>">
		<td class="shrink"><?php echo $element['id_category']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=editList&id_parent=<?php echo $element['id_parent']?>">
                                            <?php echo $this->categoryList1[$element['id_parent']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['slug']?></td>
		<td ><?php echo $element['position']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=view&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=edit&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_category']?>" hx-post="<?=BASE_URL?>/htmx.php?component=category&task=logicaldeleteHtmx&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
 * Display rows from the table category with edit and del buttons
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
	<tr>	<th scope="col" id="id_category"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_parent"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=id_parent&order=<?php if($orderBy == "id_parent") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;parent</a></th>
	<th scope="col" id="name"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Name</a></th>
	<th scope="col" id="slug"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=slug&order=<?php if($orderBy == "slug") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Slug</a></th>
	<th scope="col" id="position"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=position&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Position</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_category']?>">
		<td class="shrink"><?php echo $element['id_category']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=editList&id_parent=<?php echo $element['id_parent']?>">
                                            <?php echo $this->categoryList1[$element['id_parent']]['name'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['slug']?></td>
		<td ><?php echo $element['position']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=view&showTabs=0&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=category&task=childList&showTabs=0&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_category']?>" hx-post="<?=BASE_URL?>/htmx.php?component=category&task=delHtmx&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
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
 * Display trashed rows from the table category
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
	<tr>	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>
	<th scope="col" id="id_parent"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=id_parent&order=<?php if($orderBy == "id_parent") echo $order; else echo 'asc';?>">Id&nbsp;parent</a></th>
	<th scope="col" id="name"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=name&order=<?php if($orderBy == "name") echo $order; else echo 'asc';?>">Name</a></th>
	<th scope="col" id="slug"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=slug&order=<?php if($orderBy == "slug") echo $order; else echo 'asc';?>">Slug</a></th>
	<th scope="col" id="position"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=position&order=<?php if($orderBy == "position") echo $order; else echo 'asc';?>">Position</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=category&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_category']?>">
		<td class="shrink"><?php echo $element['id_category']?></td>
		<td ><?php echo $this->categoryList1[$element['id_parent']]['name'] ?? '';?></td>
		<td ><?php echo $element['name']?></td>
		<td ><?php echo $element['slug']?></td>
		<td ><?php echo $element['position']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_category']?>" hx-post="<?=BASE_URL?>/htmx.php?component=category&task=undelHtmx&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
