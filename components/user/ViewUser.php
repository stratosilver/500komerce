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
/** The component has a list of the deleted entries (link Trash of the header) */
protected bool $trash = true;
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
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=user&task=edit&id_user=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>
      
        <li class="nav-item">
            <a href="#" id="tab_0" class="form_tabs nav-link <?php if($activeTab == 'post') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=post&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_POST?></a>
        </li>
        
        <li class="nav-item">
            <a href="#" id="tab_1" class="form_tabs nav-link <?php if($activeTab == 'product') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=product&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCT?></a>
        </li>
        
        <li class="nav-item">
            <a href="#" id="tab_2" class="form_tabs nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_CARTITEM?></a>
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
	    // #core_content is the zone replaced by HTMX: content of a tab, form after a save.
	    // There must be exactly one in the page. It is created here: under the tabs, or around
	    // the form of a full page. A form loaded by HTMX without tabs is already inside it.
	    $coreContent = $showTabs == 1 || $_SERVER['PHP_SELF'] != '/htmx.php';
	    if($showTabs == 1){
	        self::tabs($data->id_user, 'info');
	    }
	    if($coreContent){
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
		<label class="control-label"><?=T_FIELD_EMAIL?>&nbsp;*</label>
		<input required class="form-control" type="email" name="email" id="email" value="<?=htmlentities((string)$data->email)?>"/>
	    </div>
	    </div>
	    

    <div class="col-lg-4 col-md-6">
    <label class="control-label"><?=T_FIELD_EMAIL_VERIFIED?></label>
    <div class="checkbox">
        <?php
        $checked = '';
        if($data->email_verified ?? '' !== '') $checked = 'checked';
        ?>
        <div class="form-check form-check-inline">
        <input  class="form-check-input" <?php echo $checked;?> type="checkbox" value="1"  name="email_verified"/>
        <label><?=T_YES?></label>
        </div>
    </div>
    </div>
	


        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_FIRST_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="first_name" id="first_name" size="10" value="<?=htmlentities((string)(string)$data->first_name)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_LAST_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="last_name" id="last_name" size="10" value="<?=htmlentities((string)(string)$data->last_name)?>"/>
	    </div>
	    </div>
	

                        <?php 
                        if(!isset($this->filters['id_media'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label"><?=T_FIELD_ID_MEDIA?></label>
            <select  class="form-control" name="id_media" id="id_media" onchange="var i=document.getElementById('id_media_thumb'),u=this.options[this.selectedIndex].getAttribute('data-thumb')||'';i.src=u;i.style.display=u?'':'none';">
                <option value=""></option>
        <?php
        foreach ($this->mediaList as $key => $val){
                ?>
                <option data-thumb="<?=htmlspecialchars(\Apgenic\Media\MediaImage::thumbnailUrl($val['filename']))?>" value="<?=$val['id_media']?>" <?php if($data->id_media == $val['id_media']) echo 'selected="selected"';?>><?=$val['filename']?></option>
        <?php 
        }
        ?>
       
            </select>
            <?php $thumbUrl = \Apgenic\Media\MediaImage::thumbnailUrl($this->mediaList[$data->id_media]['filename'] ?? ''); ?>
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
		<label class="control-label"><?=T_FIELD_PROVIDER?></label>
		<input  class="form-control" type="text" name="provider" id="provider" size="10" value="<?=htmlentities((string)(string)$data->provider)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_PROVIDER_USER_ID?></label>
		<input  class="form-control" type="text" name="provider_user_id" id="provider_user_id" size="10" value="<?=htmlentities((string)(string)$data->provider_user_id)?>"/>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
            <label class="control-label"><?=T_FIELD_STATUS?></label>
            <select required="" class="form-control" name="status" id="status">
                <option value=""></option>
            
                <option value="active" <?php if($data->status == 'active') echo 'selected="selected"' ?>><?=T_VALUE_ACTIVE?></option>
                <option value="suspended" <?php if($data->status == 'suspended') echo 'selected="selected"' ?>><?=T_VALUE_SUSPENDED?></option>
                <option value="disabled" <?php if($data->status == 'disabled') echo 'selected="selected"' ?>><?=T_VALUE_DISABLED?></option>
            </select>
		</div>
	    </div>
		

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_PERMISSIONS?> (0-100)</label>
		<?php $ownAccount = (int)$data->id_user === (int)($_SESSION['user']['id_user'] ?? 0); ?>
		<input <?php if($ownAccount) echo 'disabled';?> class="form-control" type="number" min="0" max="100" step="1" name="permissions" id="permissions" value="<?=(int)$data->permissions?>"/>
		<small class="form-text text-muted"><?=sprintf(T_PERMISSIONS_HELP, \Apgenic\Classes\Auth::levelEdit(), \Apgenic\Classes\Auth::levelAdmin())?><?php if($ownAccount) echo '. '.T_PERMISSIONS_OWN;?></small>
	    </div>
	    </div>
	

        <div class="col-lg-4 col-md-6"><div class="form-group">
		<label class="control-label"><?=T_FIELD_LAST_LOGIN_AT?></label>
		<input  class="form-control" type="datetime-local" name="last_login_at" id="last_login_at" value="<?=htmlentities((string)$data->last_login_at)?>"/>
	    </div>
	    </div>
	    
		<div class="col-12">
		
        
        <fieldset>
            <legend><?=T_CHANGE_PASSWORD?></legend>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?=T_FIELD_PASSWORD_HASH?></label>
                        <input  class="form-control" type="password" name="password_hash" id="password_hash" size="20" value=""/>
                    </div>
                </div>    
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?=T_FIELD_PASSWORD_HASH_CONFIRMATION?></label>
                        <input  class="form-control" type="password" name="confirmation_password_hash" id="confirmation_password_hash" size="20" value=""/>
                    </div>
                </div>
            </div>
        </fieldset>
        
	
		<div class="form-group">
		<input type="hidden" name="id_user" id="id" value="<?=$data->id_user ?>"/>
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
	<div class="table-responsive k-table-card">	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="permissions"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=permissions&order=<?php if($orderBy == "permissions") echo $order; else echo 'asc';?>"><?=T_FIELD_PERMISSIONS?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=user&task=viewlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

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
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['permissions']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
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
	</div>
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
                    
		        <dt class="col-sm-3"><h5><?=T_FIELD_ID?></h5></dt><dd class="col-sm-9"><?php echo $data->id_user?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_EMAIL?></h5></dt><dd class="col-sm-9"><?php echo $data->email?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_EMAIL_VERIFIED?></h5></dt><dd class="col-sm-9"><?php echo $data->email_verified?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_PASSWORD_HASH?></h5></dt><dd class="col-sm-9"><?php echo $data->password_hash?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_FIRST_NAME?></h5></dt><dd class="col-sm-9"><?php echo $data->first_name?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_LAST_NAME?></h5></dt><dd class="col-sm-9"><?php echo $data->last_name?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_ID_MEDIA?></h5></dt><dd class="col-sm-9"><?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList[$data->id_media]['filename'] ?? '')?> <?php echo $this->mediaList[$data->id_media]['filename'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_PROVIDER?></h5></dt><dd class="col-sm-9"><?php echo $data->provider?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_PROVIDER_USER_ID?></h5></dt><dd class="col-sm-9"><?php echo $data->provider_user_id?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_STATUS?></h5></dt><dd class="col-sm-9"><?=self::value((string)$data->status)?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_PERMISSIONS?></h5></dt><dd class="col-sm-9"><?php echo (int)$data->permissions?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_LAST_LOGIN_AT?></h5></dt><dd class="col-sm-9"><?php echo $data->last_login_at?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_CREATED_AT?></h5></dt><dd class="col-sm-9"><?php echo $data->created_at?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_UPDATED_AT?></h5></dt><dd class="col-sm-9"><?php echo $data->updated_at?></dd>
		        <dt class="col-sm-3"><h5><?=T_FIELD_DELETED_AT?></h5></dt><dd class="col-sm-9"><?php echo $data->deleted_at?></dd>
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=<?=$task?>&value=<?php echo $value?>&id_user=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
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
	<div class="table-responsive k-table-card">	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=email&<?=$this->filtersGet?>&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=email_verified&<?=$this->filtersGet?>&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=first_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=last_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="permissions"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=permissions&<?=$this->filtersGet?>&order=<?php if($orderBy == "permissions") echo $order; else echo 'asc';?>"><?=T_FIELD_PERMISSIONS?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=user&task=editlist&orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th></th>
		<th></th>
		<th><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist" class="btn btn-outline btn-outline-secondary k-btn-icon" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3')?></a></th>
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
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['permissions']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
			<?=self::icon('view');?></a></td>
			<td class="shrink"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=edit&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary k-btn-icon" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil');?>
            </a></td>
			<td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=logicaldeleteHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-outline btn-outline-danger k-btn-icon" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
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
	</div>
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
	<div class="table-responsive k-table-card">	<table class="table table-bordered">
	<thead>
	<tr>	<th scope="col" id="id_user"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

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
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
			<td class="shrink"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&showTabs=0&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn k-btn-icon" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
	        <?=self::icon('view');?>		
            </a></td>
			<td class="shrink"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childList&showTabs=0&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn btn-outline btn-outline-primary k-btn-icon" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver');?>
            </a></td>    
			<td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=delHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-outline btn-outline-danger k-btn-icon" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3')?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="17" class="phppistolsTFooter" style="text-align: right;">
	    <a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?= BASE_URL ?>/htmx.php?component=<?=COMPONENT?>&task=edit&showTabs=0&<?=$parentPK?>=<?=$parentPKValue?>&<?=$this->filtersGet?>" class="btn btn-primary btn-sm">
        <?=self::icon('plus-lg');?> <?=T_ADD?>
    </a>
    </td>
	</tr>
	</tfoot>

	</table>
	</div>
	
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
	<div class="table-responsive k-table-card">	<table class="table table-bordered table-sm">
	<thead>
	<tr>	<th scope="col" id="id_user"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at"><a href="<?=BASE_URL?>/index.php?component=user&task=trashedlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

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
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>
	
		    <td class="shrink"><a href="#" hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=undelHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary k-btn-icon" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
	</div>
	<?php
}

    /**
    * Header of the page: no links of the list of the users above the profile
    * @return void
    */
    function mainHeader($title){
        if(($_GET['task'] ?? '') == 'profile'){
            $this->listLinks = false;
            $this->eyebrow = T_SECTION_ACCOUNT;
        }
        parent::mainHeader($title);
    }


    /**
    * Profile of the logged user: his name, email and password
    * @return void
    */
    public function profile(ModelUser $data, array $message = null, int $passwordMinLength = 8){
        $isLocal = $data->provider == 'local';
        $initials = mb_strtoupper(mb_substr(html_entity_decode((string)$data->first_name), 0, 1).mb_substr(html_entity_decode((string)$data->last_name), 0, 1));
        ?>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <aside class="k-card k-profile-card">
                    <span class="k-avatar k-avatar-lg" aria-hidden="true"><?=htmlspecialchars($initials)?></span>
                    <div class="k-profile-name"><?=$data->first_name?> <?=$data->last_name?></div>
                    <div class="k-profile-email"><?=$data->email?></div>
                    <dl class="k-meta-list k-meta-list-compact">
                        <div><dt><?=T_SIGN_IN_WITH?></dt><dd><?=$isLocal ? T_EMAIL_AND_PASSWORD : ucfirst((string)$data->provider)?></dd></div>
                        <?php if((string)$data->created_at != ''){ ?><div><dt><?=T_MEMBER_SINCE?></dt><dd><?=$this->getDate((string)$data->created_at)?></dd></div><?php } ?>
                        <?php if((string)$data->last_login_at != ''){ ?><div><dt><?=T_LAST_SIGN_IN?></dt><dd><?=$this->getDate((string)$data->last_login_at)?></dd></div><?php } ?>
                    </dl>
                    <a class="btn btn-outline-primary btn-block" href="<?=BASE_URL?>/index.php?component=shop&task=cart"><?=self::icon('bag', 16)?> <?=T_MY_CART?></a>
                </aside>
            </div>

            <div class="col-lg-8">
                <?php $this->message($message); ?>
                <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=profile">
                    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />

                    <fieldset class="k-card k-fieldset">
                        <legend class="k-fieldset-legend"><?=self::icon('person', 18)?> <?=T_PERSONAL_DETAILS?></legend>
                        <div class="row">
                            <div class="col-md-6"><div class="form-group">
                                <label class="control-label" for="first_name"><?=T_FIRST_NAME?>&nbsp;*</label>
                                <input required maxlength="100" class="form-control" type="text" name="first_name" id="first_name" value="<?=$data->first_name?>" autocomplete="given-name"/>
                            </div></div>
                            <div class="col-md-6"><div class="form-group">
                                <label class="control-label" for="last_name"><?=T_LAST_NAME?>&nbsp;*</label>
                                <input required maxlength="100" class="form-control" type="text" name="last_name" id="last_name" value="<?=$data->last_name?>" autocomplete="family-name"/>
                            </div></div>
                            <div class="col-12"><div class="form-group mb-0">
                                <label class="control-label" for="email"><?=T_EMAIL?>&nbsp;*</label>
                                <input <?=$isLocal ? 'required' : 'disabled'?> maxlength="255" class="form-control" type="email" name="email" id="email" value="<?=$data->email?>" autocomplete="email"/>
                                <?php if(!$isLocal){ ?>
                                <small class="form-text text-muted"><?=sprintf(T_EMAIL_GIVEN_BY, ucfirst((string)$data->provider))?></small>
                                <?php } ?>
                            </div></div>
                        </div>
                    </fieldset>

                    <?php if($isLocal){ ?>
                    <fieldset class="k-card k-fieldset">
                        <legend class="k-fieldset-legend"><?=self::icon('lock', 18)?> <?=T_CHANGE_PASSWORD?></legend>
                        <p class="k-fieldset-hint"><?=T_PASSWORD_KEEP_HINT?></p>
                        <div class="row">
                            <div class="col-md-4"><div class="form-group mb-md-0">
                                <label class="control-label" for="current_password"><?=T_CURRENT_PASSWORD?></label>
                                <input class="form-control" type="password" name="current_password" id="current_password" autocomplete="current-password"/>
                            </div></div>
                            <div class="col-md-4"><div class="form-group mb-md-0">
                                <label class="control-label" for="password"><?=T_NEW_PASSWORD?></label>
                                <input class="form-control" type="password" name="password" id="password" minlength="<?=$passwordMinLength?>" maxlength="72" autocomplete="new-password" aria-describedby="password_help"/>
                                <small class="form-text text-muted" id="password_help"><?=sprintf(T_PASSWORD_MIN_HINT, $passwordMinLength)?></small>
                            </div></div>
                            <div class="col-md-4"><div class="form-group mb-0">
                                <label class="control-label" for="password_confirmation"><?=T_CONFIRMATION?></label>
                                <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" maxlength="72" autocomplete="new-password"/>
                            </div></div>
                        </div>
                    </fieldset>
                    <?php } ?>

                    <div class="k-form-footer justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg"><?=self::icon('check2', 18)?> <?=T_SAVE?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }


    // Authentication
    // ------------------------------------------------------------------------------------------------

    /**
    * Header of the authentication pages: no menu, the form next to a panel of the brand
    * @param string $intro sentence under the title
    * @return void
    */
    public function authHeader(string $title, string $intro = ''){
        ?><!doctype html>
        <html lang="<?=LANG?>">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="theme-color" content="<?=self::THEME_COLOR?>">
            <title><?=$title?> &middot; <?=self::appName()?></title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
            <link href="<?=BASE_URL?>/css/theme.css" rel="stylesheet">
            <link href="<?=BASE_URL?>/css/styles.css" rel="stylesheet">
          </head>
          <body class="k-layout-auth">
            <div class="k-auth">
              <aside class="k-auth-aside" aria-hidden="true">
                <div class="k-auth-aside-inner">
                  <?php self::brand('k-brand-inverse'); ?>
                  <div class="k-auth-aside-text">
                    <p class="k-auth-aside-title"><?=T_YOUR_ACCOUNT?></p>
                    <ul class="k-auth-aside-list">
                      <li><?=self::icon('bag-check', 18)?> <?=T_CHECKOUT_FASTER?></li>
                      <li><?=self::icon('geo-alt', 18)?> <?=T_ADDRESSES_SAVED?></li>
                    </ul>
                  </div>
                </div>
              </aside>
              <main class="k-auth-main">
                <div class="k-auth-top">
                  <?php self::brand('d-lg-none'); ?>
                  <?php self::languageSwitch('k-lang-auth'); ?>
                  <a class="k-link-muted" href="<?=BASE_URL?>/index.php?component=product&task=viewlist"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_SHOP?></a>
                </div>
                <div class="k-auth-box">
                  <h1 class="k-auth-title"><?=$title?></h1>
                  <?php if($intro != ''){ ?><p class="k-auth-intro"><?=$intro?></p><?php } ?>
        <?php
    }


    /**
    * Footer of the authentication pages
    * @return void
    */
    public function authFooter(){
        ?>
                </div>
              </main>
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
        $this->authHeader(T_SIGN_IN, T_SIGN_IN_INTRO);
        $this->message($message);
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=login">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="form-group">
                <label for="email"><?=T_EMAIL?></label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?=htmlspecialchars($email)?>" autocomplete="username" required autofocus>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-baseline">
                    <label for="password"><?=T_PASSWORD?></label>
                    <a class="k-link-small" href="<?=BASE_URL?>/index.php?component=user&task=forgot"><?=T_FORGOT_PASSWORD_Q?></a>
                </div>
                <input type="password" class="form-control form-control-lg" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block"><?=T_SIGN_IN?></button>
        </form>
        <?php $this->socialButtons($providers); ?>
        <p class="k-auth-switch"><?=T_NEW_HERE?> <a href="<?=BASE_URL?>/index.php?component=user&task=register"><?=T_CREATE_ACCOUNT?></a></p>
        <?php
        $this->authFooter();
    }


    /**
    * Registration form
    * @return void
    */
    public function register(array $values = array(), array $message = null, int $passwordMinLength = 8, array $providers = array()){
        $this->authHeader(T_CREATE_ACCOUNT, T_REGISTER_INTRO);
        $this->message($message);
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=register">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="form-row">
                <div class="form-group col-sm-6">
                    <label for="first_name"><?=T_FIRST_NAME?></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" maxlength="100" value="<?=$values['first_name'] ?? ''?>" autocomplete="given-name" required autofocus>
                </div>
                <div class="form-group col-sm-6">
                    <label for="last_name"><?=T_LAST_NAME?></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" maxlength="100" value="<?=$values['last_name'] ?? ''?>" autocomplete="family-name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email"><?=T_EMAIL?></label>
                <input type="email" class="form-control" id="email" name="email" maxlength="255" value="<?=htmlspecialchars($values['email'] ?? '')?>" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password"><?=T_PASSWORD?></label>
                <input type="password" class="form-control" id="password" name="password" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required aria-describedby="password_help">
                <small class="form-text text-muted" id="password_help"><?=sprintf(T_PASSWORD_MIN_HINT, $passwordMinLength)?></small>
            </div>
            <div class="form-group">
                <label for="password_confirmation"><?=T_CONFIRM_PASSWORD?></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block"><?=T_CREATE_MY_ACCOUNT?></button>
        </form>
        <?php $this->socialButtons($providers); ?>
        <p class="k-auth-switch"><?=T_ALREADY_CUSTOMER?> <a href="<?=BASE_URL?>/index.php?component=user&task=login"><?=T_SIGN_IN?></a></p>
        <?php
        $this->authFooter();
    }


    /**
    * Form asking a password reset link
    * @return void
    */
    public function forgot(array $message = null, string $devResetLink = ''){
        $this->authHeader(T_FORGOT_PASSWORD, T_FORGOT_INTRO);
        $this->message($message);
        if($devResetLink != ''){
            ?>
            <div class="alert alert-warning k-alert">
                <?=self::icon('exclamation-triangle', 20)?>
                <div><strong><?=T_DEV_MODE?></strong> (AUTH_SHOW_RESET_LINK): <a href="<?=htmlspecialchars($devResetLink)?>"><?=T_RESET_LINK?></a></div>
            </div>
            <?php
        }
        ?>
        <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=forgot">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="form-group">
                <label for="email"><?=T_EMAIL?></label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" autocomplete="username" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block"><?=self::icon('envelope', 18)?> <?=T_SEND_LINK?></button>
        </form>
        <p class="k-auth-switch"><a href="<?=BASE_URL?>/index.php?component=user&task=login"><?=self::icon('arrow-left', 14)?> <?=T_BACK_TO_SIGN_IN?></a></p>
        <?php
        $this->authFooter();
    }


    /**
    * Form to choose a new password (reached from the link sent by email)
    * @return void
    */
    public function reset(string $token, bool $tokenValid, array $message = null, int $passwordMinLength = 8){
        $this->authHeader(T_NEW_PASSWORD, $tokenValid ? T_RESET_INTRO : '');
        $this->message($message);
        if($tokenValid){
            ?>
            <form method="POST" action="<?=BASE_URL?>/index.php?component=user&task=reset">
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>" />
                <div class="form-group">
                    <label for="password"><?=T_NEW_PASSWORD?></label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required autofocus aria-describedby="password_help">
                    <small class="form-text text-muted" id="password_help"><?=sprintf(T_PASSWORD_MIN_HINT, $passwordMinLength)?></small>
                </div>
                <div class="form-group">
                    <label for="password_confirmation"><?=T_CONFIRM_PASSWORD?></label>
                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block"><?=T_SAVE_NEW_PASSWORD?></button>
            </form>
            <?php
        }
        else{
            ?>
            <a class="btn btn-primary btn-lg btn-block" href="<?=BASE_URL?>/index.php?component=user&task=forgot"><?=T_ASK_NEW_LINK?></a>
            <?php
        }
        ?>
        <p class="k-auth-switch"><a href="<?=BASE_URL?>/index.php?component=user&task=login"><?=self::icon('arrow-left', 14)?> <?=T_BACK_TO_SIGN_IN?></a></p>
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
        $icons = array('google' => 'google', 'facebook' => 'facebook', 'x' => 'twitter-x');
        ?>
        <div class="k-divider"><span><?=T_OR?></span></div>
        <div class="k-social-buttons">
        <?php
        foreach($providers as $key => $label){
            ?>
            <a class="btn btn-social btn-block" href="<?=BASE_URL?>/index.php?component=user&task=oauth&provider=<?=$key?>"><?=isset($icons[$key]) ? self::icon($icons[$key], 18) : ''?> <?=sprintf(T_CONTINUE_WITH, $label)?></a>
            <?php
        }
        ?>
        </div>
        <?php
    }

}
