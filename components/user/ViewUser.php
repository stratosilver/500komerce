<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\User;

class ViewUser extends \Apgenic\Classes\ViewTemplate {
var $mediaList = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($mediaList) {
$this->mediaList = $mediaList;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=user&task=edit&id_user=<?=$id?>">Info</a>
      </li>
      
        <li class="nav-item">
            <a id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'post') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=post&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0">Post</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_1" class="form_tabs nav-link <?php if($activeTab == 'product') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=product&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0">Product</a>
        </li>
        
        <li class="nav-item">
            <a id="tab_2" class="form_tabs nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0">Cart_item</a>
        </li>
        
    </ul>
    

     
<?php
}
/**
 * Edit form for the table user
 * @parameter ModelUser $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelUser $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    if($showTabs == 1){
	        self::tabs($data->id_user, 'info');
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">
	

        <div class="col-lg-4 col-md-6">
        <div class="form-group">
		<label class="control-label">Email&nbsp;*:</label>
		<input required class="form-control" type="email" name="email" id="email" value="<?=htmlentities((string)$data->email)?>"/>
	    </div>
	    </div>
	    

    <div class="col-lg-4 col-md-6">
    <label class="control-label">Email&nbsp;verified</label>
    <div class="checkbox">
        <?php
        $checked = '';
        if($data->email_verified ?? '' !== '') $checked = 'checked';
        ?>
        <div class="form-check form-check-inline">
        <input  class="form-check-input" <?php echo $checked;?> type="checkbox" value="1"  name="email_verified"/>
        <label>True</label>
        </div>
    </div>
    </div>
	


        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">First&nbsp;name&nbsp;*:</label>
		<input required class="form-control" type="text" name="first_name" id="first_name" size="10" value="<?=htmlentities((string)(string)$data->first_name)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Last&nbsp;name&nbsp;*:</label>
		<input required class="form-control" type="text" name="last_name" id="last_name" size="10" value="<?=htmlentities((string)(string)$data->last_name)?>"/>
	    </div>
	    </div>
	

                        <?php 
                        if(!isset($this->filters['id_media'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;media:</label>
            <select  class="form-control" name="id_media" id="id_media">
                <option value=""></option>
        <?php
        foreach ($this->mediaList as $key => $val){
                ?>
                <option value="<?=$val['id_media']?>" <?php if($data->id_media == $val['id_media']) echo 'selected="selected"';?>><?=$val['filename']?></option>
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
                            
                            <input type="hidden" name="id_media" id="id_media" value="<?php if(!isset($data->id_media) || $data->id_media < 1) echo $_GET['filters']['id_media']; else echo $data->id_media?>"/>
                            <?php
                        }
                        ?>                     
                        

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Provider:</label>
		<input  class="form-control" type="text" name="provider" id="provider" size="10" value="<?=htmlentities((string)(string)$data->provider)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Provider&nbsp;user&nbsp;id:</label>
		<input  class="form-control" type="text" name="provider_user_id" id="provider_user_id" size="10" value="<?=htmlentities((string)(string)$data->provider_user_id)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
            <label class="control-label">Status:</label>
            <select required="" class="form-control" name="status" id="status">
                <option value=""></option>
            
                <option value="active" <?php if($data->status == 'active') echo 'selected="selected"' ?>>active</option>
                <option value="suspended" <?php if($data->status == 'suspended') echo 'selected="selected"' ?>>suspended</option>
                <option value="disabled" <?php if($data->status == 'disabled') echo 'selected="selected"' ?>>disabled</option>
            </select>
		</div>
	    </div>
		

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label">Last&nbsp;login&nbsp;at:</label>
		<input  class="form-control" type="datetime-local" name="last_login_at" id="last_login_at" value="<?=htmlentities((string)$data->last_login_at)?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
        
        <fieldset>
            <legend>Change password</legend>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Password&nbsp;hash:</label>
                        <input  class="form-control" type="password" name="password_hash" id="password_hash" size="20" value=""/>
                    </div>
                </div>    
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Confirmation Password&nbsp;hash:</label>
                        <input  class="form-control" type="password" name="confirmation_password_hash" id="confirmation_password_hash" size="20" value=""/>
                    </div>
                </div>
            </div>
        </fieldset>
        
	
		<div class="form-group">
		<input type="hidden" name="id_user" id="id" value="<?=$data->id_user ?>"/>
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
 * Display rows from the table user
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
	<tr>	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="email"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>">Email</a></th>
	<th scope="col" id="email_verified"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>">Email&nbsp;verified</a></th>
	<th scope="col" id="first_name"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>">First&nbsp;name</a></th>
	<th scope="col" id="last_name"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>">Last&nbsp;name</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $element['id_user']?></td>
		<td ><?php echo $element['email']?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="17" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table user
 *
 * @parameter ModelUser $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelUser $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_user != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;user</h5></dt><dd class="col-sm-9"><?php echo $data->id_user?></dd>
		        <dt class="col-sm-3"><h5>Email</h5></dt><dd class="col-sm-9"><?php echo $data->email?></dd>
		        <dt class="col-sm-3"><h5>Email&nbsp;verified</h5></dt><dd class="col-sm-9"><?php echo $data->email_verified?></dd>
		        <dt class="col-sm-3"><h5>Password&nbsp;hash</h5></dt><dd class="col-sm-9"><?php echo $data->password_hash?></dd>
		        <dt class="col-sm-3"><h5>First&nbsp;name</h5></dt><dd class="col-sm-9"><?php echo $data->first_name?></dd>
		        <dt class="col-sm-3"><h5>Last&nbsp;name</h5></dt><dd class="col-sm-9"><?php echo $data->last_name?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;media</h5></dt><dd class="col-sm-9"><?php echo $this->mediaList[$data->id_media]['filename'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Provider</h5></dt><dd class="col-sm-9"><?php echo $data->provider?></dd>
		        <dt class="col-sm-3"><h5>Provider&nbsp;user&nbsp;id</h5></dt><dd class="col-sm-9"><?php echo $data->provider_user_id?></dd>
		        <dt class="col-sm-3"><h5>Status</h5></dt><dd class="col-sm-9"><?php echo $data->status?></dd>
		        <dt class="col-sm-3"><h5>Last&nbsp;login&nbsp;at</h5></dt><dd class="col-sm-9"><?php echo $data->last_login_at?></dd>
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
 * Display rows from the table user
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=<?=$task?>&value=<?php echo $value?>&id_user=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table user with edit and del buttons
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
	<tr>	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="email"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=email&<?=$this->filtersGet?>&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>">Email</a></th>
	<th scope="col" id="email_verified"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=email_verified&<?=$this->filtersGet?>&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>">Email&nbsp;verified</a></th>
	<th scope="col" id="first_name"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=first_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>">First&nbsp;name</a></th>
	<th scope="col" id="last_name"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=last_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>">Last&nbsp;name</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist" class="btn btn-outline btn-outline-secondary">
check2   
            </a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_user']?>">
		<td class="shrink"><?php echo $element['id_user']?></td>
		<td ><?php echo $element['email']?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=edit&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=logicaldeleteHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">
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
	<td colspan="17" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table user with edit and del buttons
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
	<tr>	<th scope="col" id="id_user"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;user</a></th>
	<th scope="col" id="email"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Email</a></th>
	<th scope="col" id="email_verified"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Email&nbsp;verified</a></th>
	<th scope="col" id="first_name"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">First&nbsp;name</a></th>
	<th scope="col" id="last_name"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Last&nbsp;name</a></th>
	<th scope="col" id="status"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Status</a></th>
	<th scope="col" id="created_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Updated&nbsp;at</a></th>

		<th></th>
		<th></th>
		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_user']?>">
		<td class="shrink"><?php echo $element['id_user']?></td>
		<td ><?php echo $element['email']?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&showTabs=0&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childList&showTabs=0&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn  btn-outline btn-outline-primary">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=delHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="17" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table user
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
	<tr>	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>">Id&nbsp;user</a></th>
	<th scope="col" id="email"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>">Email</a></th>
	<th scope="col" id="email_verified"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>">Email&nbsp;verified</a></th>
	<th scope="col" id="first_name"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>">First&nbsp;name</a></th>
	<th scope="col" id="last_name"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>">Last&nbsp;name</a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>">Status</a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>">Created&nbsp;at</a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>">Updated&nbsp;at</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_user']?>">
		<td class="shrink"><?php echo $element['id_user']?></td>
		<td ><?php echo $element['email']?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?php echo $element['status']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=undelHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="17" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

    // Authentication
    // ------------------------------------------------------------------------------------------------

    /**
    * Header of the authentication pages (no menu, centered card)
    * @return void
    */
    public function authHeader(string $title){
        ?><!doctype html>
        <html lang="fr">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title><?=$title?></title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
            <link href="<?=BASE_URL?>/css/styles.css" rel="stylesheet" media="screen">
          </head>
          <body class="bg-light">
            <div class="container">
              <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                  <div class="text-center my-4">
                    <?=self::icon('robot', 48)?>
                    <div class="h4 mt-2">100Komerce</div>
                  </div>
                  <div class="card shadow-sm">
                    <div class="card-body">
                      <h1 class="h4 mb-4"><?=$title?></h1>
        <?php
    }


    /**
    * Footer of the authentication pages
    * @return void
    */
    public function authFooter(){
        ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </body>
        </html>
        <?php
    }


    /**
    * Login form
    * @return void
    */
    public function login(string $email = '', array $message = null, array $providers = array()){
        $this->authHeader('Sign in');
        $this->message($message);
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=login">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?=htmlspecialchars($email)?>" autocomplete="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
        </form>
        <div class="d-flex justify-content-between mt-3">
            <a href="<?=BASE_URL?>/index.php?component=user&task=forgot">Forgot password?</a>
            <a href="<?=BASE_URL?>/index.php?component=user&task=register">Create an account</a>
        </div>
        <?php
        $this->socialButtons($providers);
        $this->authFooter();
    }


    /**
    * Registration form
    * @return void
    */
    public function register(array $values = array(), array $message = null, int $passwordMinLength = 8, array $providers = array()){
        $this->authHeader('Create an account');
        $this->message($message);
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=register">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="first_name">First name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" maxlength="100" value="<?=$values['first_name'] ?? ''?>" autocomplete="given-name" required autofocus>
                </div>
                <div class="form-group col-sm-6">
                    <label for="last_name">Last name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" maxlength="100" value="<?=$values['last_name'] ?? ''?>" autocomplete="family-name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="255" value="<?=htmlspecialchars($values['email'] ?? '')?>" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
                <small class="form-text text-muted">At least <?=$passwordMinLength?> characters.</small>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create my account</button>
        </form>
        <div class="mt-3">
            <a href="<?=BASE_URL?>/index.php?component=user&task=login">I already have an account</a>
        </div>
        <?php
        $this->socialButtons($providers);
        $this->authFooter();
    }


    /**
    * Form asking a password reset link
    * @return void
    */
    public function forgot(array $message = null, string $devResetLink = ''){
        $this->authHeader('Forgot password');
        $this->message($message);
        if($devResetLink != ''){
            ?>
            <div class="alert alert-warning">
                <strong>Development mode</strong> (AUTH_SHOW_RESET_LINK): <a href="<?=htmlspecialchars($devResetLink)?>">reset link</a>
            </div>
            <?php
        }
        ?>
        <p class="text-muted">Enter your email address, you will receive a link to choose a new password.</p>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=forgot">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" autocomplete="username" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Send the link</button>
        </form>
        <div class="mt-3">
            <a href="<?=BASE_URL?>/index.php?component=user&task=login">Back to sign in</a>
        </div>
        <?php
        $this->authFooter();
    }


    /**
    * Form to choose a new password (reached from the link sent by email)
    * @return void
    */
    public function reset(string $token, bool $tokenValid, array $message = null, int $passwordMinLength = 8){
        $this->authHeader('New password');
        $this->message($message);
        if($tokenValid){
            ?>
            <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=reset">
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>" />
                <div class="form-group">
                    <label for="password">New password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required autofocus>
                    <small class="form-text text-muted">At least <?=$passwordMinLength?> characters.</small>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Save the new password</button>
            </form>
            <?php
        }
        else{
            ?>
            <a class="btn btn-primary btn-block" href="<?=BASE_URL?>/index.php?component=user&task=forgot">Ask a new link</a>
            <?php
        }
        ?>
        <div class="mt-3">
            <a href="<?=BASE_URL?>/index.php?component=user&task=login">Back to sign in</a>
        </div>
        <?php
        $this->authFooter();
    }


    /**
    * Buttons to sign in with an external provider
    * @param array $providers key => label of the configured providers
    * @return void
    */
    public function socialButtons(array $providers){
        if(!count($providers)) return;
        ?>
        <div class="text-center text-muted my-3">or</div>
        <?php
        foreach($providers as $key => $label){
            ?>
            <a class="btn btn-outline-dark btn-block" href="<?=BASE_URL?>/index.php?component=user&task=oauth&provider=<?=$key?>">Continue with <?=$label?></a>
            <?php
        }
    }

}
