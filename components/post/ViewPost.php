<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\Post;

class ViewPost extends \Apgenic\Classes\ViewTemplate {
var $userList1 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($userList1) {
$this->userList1 = $userList1;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=post&task=edit&id_post=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table post
 * @parameter ModelPost $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelPost $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_post, 'info');
	    }
	    if($coreContent){
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=post&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

                        <?php 
                        if(!isset($this->filters['id_user'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;user:</label>
            <select  class="form-control" name="id_user" id="id_user">
                <option value=""></option>
        <?php
        foreach ($this->userList1 as $key => $val){
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
		<label class="control-label">Lang&nbsp;*:</label>
		<input required class="form-control" type="text" name="lang" id="lang" size="10" value="<?=htmlentities((string)(string)$data->lang)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Title&nbsp;*:</label>
		<input required class="form-control" type="text" name="title" id="title" size="10" value="<?=htmlentities((string)(string)$data->title)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Id&nbsp;media:</label>
		<input required="" class="form-control" type="number" name="id_media" id="id_media" value="<?=$data->id_media?>"/>
	    </div>
	    </div>
	    

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Slug&nbsp;*:</label>
		<input required class="form-control" type="text" name="slug" id="slug" size="10" value="<?=htmlentities((string)(string)$data->slug)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Excerpt:</label>
		<input  class="form-control" type="text" name="excerpt" id="excerpt" size="10" value="<?=htmlentities((string)(string)$data->excerpt)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-12 col-md-12"><div class="form-group">
		<label><span>Body&nbsp;*:</span></label>
		<textarea required class="form-control" name="body" id="body" rows="4" cols="49"><?=$data->body?></textarea>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
            <label class="control-label">Status:</label>
            <select required="" class="form-control" name="status" id="status">
                <option value=""></option>
            
                <option value="draft" <?php if($data->status == 'draft') echo 'selected="selected"' ?>>draft</option>
                <option value="published" <?php if($data->status == 'published') echo 'selected="selected"' ?>>published</option>
                <option value="archived" <?php if($data->status == 'archived') echo 'selected="selected"' ?>>archived</option>
            </select>
		</div>
	    </div>
		

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Published&nbsp;at:</label>
		<input  class="form-control" type="datetime-local" name="published_at" id="published_at" value="<?=htmlentities((string)$data->published_at)?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
		<div class="form-group">
		<input type="hidden" name="id_post" id="id" value="<?=$data->id_post ?>"/>
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
 * Display rows from the table post
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
	<tr>	<th scope="col" id="id_post"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=id_post&order=<?php if($orderBy == "id_post") echo $order; else echo 'asc';?>">Id&nbsp;post</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="lang"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=lang&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>">Lang</a></th>
	<th scope="col" id="title"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=title&order=<?php if($orderBy == "title") echo $order; else echo 'asc';?>">Title</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=post&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_post']?></td>
		<td ><?php echo $this->userList1[$element['id_user']]['email'] ?? '';?></td>
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['title']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=view&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="16" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table post
 *
 * @parameter ModelPost $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelPost $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_post != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;post</h5></dt><dd class="col-sm-9"><?php echo $data->id_post?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;user</h5></dt><dd class="col-sm-9"><?php echo $this->userList1[$data->id_user]['email'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Lang</h5></dt><dd class="col-sm-9"><?php echo $data->lang?></dd>
		        <dt class="col-sm-3"><h5>Title</h5></dt><dd class="col-sm-9"><?php echo $data->title?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;media</h5></dt><dd class="col-sm-9"><?php echo $data->id_media?></dd>
		        <dt class="col-sm-3"><h5>Slug</h5></dt><dd class="col-sm-9"><?php echo $data->slug?></dd>
		        <dt class="col-sm-3"><h5>Excerpt</h5></dt><dd class="col-sm-9"><?php echo $data->excerpt?></dd>
		        <dt class="col-sm-3"><h5>Body</h5></dt><dd class="col-sm-9"><?php echo $data->body?></dd>
		        <dt class="col-sm-3"><h5>Status</h5></dt><dd class="col-sm-9"><?php echo $data->status?></dd>
		        <dt class="col-sm-3"><h5>Published&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->published_at?></dd>
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
 * Display rows from the table post
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=<?=$task?>&value=<?php echo $value?>&id_post=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table post with edit and del buttons
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
	<tr>	<th scope="col" id="id_post"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=id_post&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_post") echo $order; else echo 'asc';?>">Id&nbsp;post</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="lang"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=lang&<?=$this->filtersGet?>&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>">Lang</a></th>
	<th scope="col" id="title"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=title&<?=$this->filtersGet?>&order=<?php if($orderBy == "title") echo $order; else echo 'asc';?>">Title</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=post&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_post']?>">
		<td class="shrink"><?php echo $element['id_post']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->userList1[$element['id_user']]['email'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['title']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=view&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=edit&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_post']?>" hx-post="<?=BASE_URL?>/htmx.php?component=post&task=logicaldeleteHtmx&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
	<td colspan="16" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table post with edit and del buttons
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
	<tr>	<th scope="col" id="id_post"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=id_post&order=<?php if($orderBy == "id_post") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;post</a></th>
	<th scope="col" id="id_user"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;user</a></th>
	<th scope="col" id="lang"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=lang&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Lang</a></th>
	<th scope="col" id="title"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=title&order=<?php if($orderBy == "title") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Title</a></th>
	<th scope="col" id="status"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Status</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_post']?>">
		<td class="shrink"><?php echo $element['id_post']?></td>
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=editList&id_user=<?php echo $element['id_user']?>">
                                            <?php echo $this->userList1[$element['id_user']]['email'];?>
                                            </a>
                                            </td>
                                            
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['title']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=view&showTabs=0&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=post&task=childList&showTabs=0&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_post']?>" hx-post="<?=BASE_URL?>/htmx.php?component=post&task=delHtmx&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="16" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table post
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
	<tr>	<th scope="col" id="id_post"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=id_post&order=<?php if($orderBy == "id_post") echo $order; else echo 'asc';?>">Id&nbsp;post</a></th>
	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="lang"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=lang&order=<?php if($orderBy == "lang") echo $order; else echo 'asc';?>">Lang</a></th>
	<th scope="col" id="title"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=title&order=<?php if($orderBy == "title") echo $order; else echo 'asc';?>">Title</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=post&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_post']?>">
		<td class="shrink"><?php echo $element['id_post']?></td>
		<td ><?php echo $this->userList1[$element['id_user']]['email'] ?? '';?></td>
		<td ><?php echo $element['lang']?></td>
		<td ><?php echo $element['title']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_post']?>" hx-post="<?=BASE_URL?>/htmx.php?component=post&task=undelHtmx&id_post=<?php echo $element['id_post']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="16" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
