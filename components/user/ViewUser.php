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

/** Page of the back office to open after the sign in (parameter return of the login page), '' = the home page */
public string $loginReturn = '';

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

    <ul id="tabs_list" class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a href="#" id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=user&task=edit&id_user=<?=$id?>"><?=T_TAB_INFO?></a>
      </li>

        <li class="nav-item">
            <a href="#" id="tab_0" class="nav-link <?php if($activeTab == 'post') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=post&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_POST?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_1" class="nav-link <?php if($activeTab == 'product') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=product&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_PRODUCT?></a>
        </li>

        <li class="nav-item">
            <a href="#" id="tab_2" class="nav-link <?php if($activeTab == 'cartitem') echo 'active';?>" hx-target="#core_content" hx-swap="innerHTML" hx-get="/htmx.php?component=cartitem&task=childlist&filters[id_user]=<?=$id ?>&showTabs=0"><?=T_MENU_CARTITEM?></a>
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

		<form class="card card-body shadow-sm p-lg-4 mb-4" method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
		<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
		<fieldset>
        <div class="row">


        <div class="col-lg-4 col-md-6">
        <div class="mb-3">
		<label class="form-label" for="email"><?=T_FIELD_EMAIL?>&nbsp;*</label>
		<input required class="form-control" type="email" name="email" id="email" value="<?=htmlentities((string)$data->email)?>"/>
	    </div>
	    </div>


    <div class="col-lg-4 col-md-6">
    <div class="mb-3">
    <div class="form-label"><?=T_FIELD_EMAIL_VERIFIED?></div>
        <?php
        $checked = '';
        if($data->email_verified ?? '' !== '') $checked = 'checked';
        ?>
        <div class="form-check">
        <input  class="form-check-input" <?php echo $checked;?> type="checkbox" value="1"  name="email_verified" id="email_verified"/>
        <label class="form-check-label" for="email_verified"><?=T_YES?></label>
        </div>
    </div>
    </div>



        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="first_name"><?=T_FIELD_FIRST_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="first_name" id="first_name" size="10" value="<?=htmlentities((string)(string)$data->first_name)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="last_name"><?=T_FIELD_LAST_NAME?>&nbsp;*</label>
		<input required class="form-control" type="text" name="last_name" id="last_name" size="10" value="<?=htmlentities((string)(string)$data->last_name)?>"/>
	    </div>
	    </div>


                        <?php
                        if(!isset($this->filters['id_media'])){ ?>

        <div class="col-lg-4 col-md-6">
        <div class="mb-3">
            <label class="form-label" for="id_media"><?=T_FIELD_ID_MEDIA?></label>
            <select  class="form-select" name="id_media" id="id_media" onchange="var i=document.getElementById('id_media_thumb'),u=this.options[this.selectedIndex].getAttribute('data-thumb')||'';i.src=u;i.classList.toggle('d-none',!u);">
                <option value=""></option>
        <?php
        foreach (self::notTrashed($this->mediaList, $data->id_media) as $key => $val){
                ?>
                <option data-thumb="<?=htmlspecialchars(\Apgenic\Media\MediaImage::thumbnailUrl($val['filename']))?>" value="<?=$val['id_media']?>" <?php if($data->id_media == $val['id_media']) echo 'selected="selected"';?>><?=$val['filename']?></option>
        <?php
        }
        ?>

            </select>
            <?php $thumbUrl = \Apgenic\Media\MediaImage::thumbnailUrl($this->mediaList[$data->id_media]['filename'] ?? ''); ?>
            <div><img id="id_media_thumb" class="rounded border bg-body object-fit-cover mt-2 <?php if($thumbUrl == '') echo 'd-none';?>" width="48" height="48" src="<?=htmlspecialchars($thumbUrl)?>" alt=""></div>
		</div>
		</div>

                        <?php
                        }
                        else{
                            ?>

                            <input type="hidden" name="id_media" id="id_media" value="<?php if(!isset($data->id_media) || $data->id_media < 1) echo ((int)($_GET['filters']['id_media'] ?? 0) ?: ''); else echo $data->id_media?>"/>
                            <?php
                        }
                        ?>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="provider"><?=T_FIELD_PROVIDER?></label>
		<input  class="form-control" type="text" name="provider" id="provider" size="10" value="<?=htmlentities((string)(string)$data->provider)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="provider_user_id"><?=T_FIELD_PROVIDER_USER_ID?></label>
		<input  class="form-control" type="text" name="provider_user_id" id="provider_user_id" size="10" value="<?=htmlentities((string)(string)$data->provider_user_id)?>"/>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
            <label class="form-label" for="status"><?=T_FIELD_STATUS?></label>
            <select required="" class="form-select" name="status" id="status">
                <option value=""></option>

                <option value="active" <?php if($data->status == 'active') echo 'selected="selected"' ?>><?=T_VALUE_ACTIVE?></option>
                <option value="suspended" <?php if($data->status == 'suspended') echo 'selected="selected"' ?>><?=T_VALUE_SUSPENDED?></option>
                <option value="disabled" <?php if($data->status == 'disabled') echo 'selected="selected"' ?>><?=T_VALUE_DISABLED?></option>
            </select>
		</div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="permissions"><?=T_FIELD_PERMISSIONS?> (0-100)</label>
		<?php $ownAccount = (int)$data->id_user === (int)($_SESSION['user']['id_user'] ?? 0); ?>
		<input <?php if($ownAccount) echo 'disabled';?> class="form-control" type="number" min="0" max="100" step="1" name="permissions" id="permissions" value="<?=(int)$data->permissions?>"/>
		<small class="form-text"><?=sprintf(T_PERMISSIONS_HELP, \Apgenic\Classes\Auth::levelEdit(), \Apgenic\Classes\Auth::levelAdmin())?><?php if($ownAccount) echo '. '.T_PERMISSIONS_OWN;?></small>
	    </div>
	    </div>


        <div class="col-lg-4 col-md-6"><div class="mb-3">
		<label class="form-label" for="last_login_at"><?=T_FIELD_LAST_LOGIN_AT?></label>
		<input  class="form-control" type="datetime-local" name="last_login_at" id="last_login_at" value="<?=htmlentities((string)$data->last_login_at)?>"/>
	    </div>
	    </div>

		<div class="col-12">


        <fieldset class="border-top pt-3 mt-2">
            <legend class="fs-6 fw-bold"><?=T_CHANGE_PASSWORD?></legend>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="password_hash"><?=T_FIELD_PASSWORD_HASH?></label>
                        <input  class="form-control" type="password" name="password_hash" id="password_hash" size="20" value=""/>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="confirmation_password_hash"><?=T_FIELD_PASSWORD_HASH_CONFIRMATION?></label>
                        <input  class="form-control" type="password" name="confirmation_password_hash" id="confirmation_password_hash" size="20" value=""/>
                    </div>
                </div>
            </div>
        </fieldset>


		<div class="border-top pt-3">
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_user" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="permissions" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=permissions&order=<?php if($orderBy == "permissions") echo $order; else echo 'asc';?>"><?=T_FIELD_PERMISSIONS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'viewlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="text-nowrap"><?php echo $element['id_user']?></td>
		<td ><?php echo htmlspecialchars((string)$element['email'])?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['permissions']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=view&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-secondary" title="<?=T_VIEW?>" aria-label="<?=T_VIEW?>">
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

    <div class="card shadow-sm">
        <div class="card-body p-lg-4">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row mb-0">

		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID?></dt><dd class="col-sm-9"><?php echo $data->id_user?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_EMAIL?></dt><dd class="col-sm-9"><?php echo htmlspecialchars((string)$data->email)?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_EMAIL_VERIFIED?></dt><dd class="col-sm-9"><?php echo $data->email_verified?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_PASSWORD_HASH?></dt><dd class="col-sm-9"><?php /* the hash itself is never displayed */ echo (string)$data->password_hash != '' ? T_YES : '-';?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_FIRST_NAME?></dt><dd class="col-sm-9"><?php echo $data->first_name?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_LAST_NAME?></dt><dd class="col-sm-9"><?php echo $data->last_name?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_ID_MEDIA?></dt><dd class="col-sm-9"><?=\Apgenic\Media\MediaImage::thumbnail($this->mediaList[$data->id_media]['filename'] ?? '')?> <?php echo $this->mediaList[$data->id_media]['filename'] ?? '';?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_PROVIDER?></dt><dd class="col-sm-9"><?php echo $data->provider?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_PROVIDER_USER_ID?></dt><dd class="col-sm-9"><?php echo $data->provider_user_id?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_STATUS?></dt><dd class="col-sm-9"><?=self::value((string)$data->status)?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_PERMISSIONS?></dt><dd class="col-sm-9"><?php echo (int)$data->permissions?></dd>
		        <dt class="col-sm-3 small text-uppercase fw-semibold text-body-secondary mb-1 mb-sm-2"><?=T_FIELD_LAST_LOGIN_AT?></dt><dd class="col-sm-9"><?php echo $data->last_login_at?></dd>
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
 * Display rows from the table user
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
    <a href="#" hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=<?=$task?>&value=<?php echo $value?>&id_user=<?php echo $pkVal;?>" class="btn btn-sm btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_user" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=id_user&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=email&<?=$this->filtersGet?>&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=email_verified&<?=$this->filtersGet?>&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=first_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=last_name&<?=$this->filtersGet?>&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=status&<?=$this->filtersGet?>&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="permissions" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=permissions&<?=$this->filtersGet?>&order=<?php if($orderBy == "permissions") echo $order; else echo 'asc';?>"><?=T_FIELD_PERMISSIONS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=created_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'editlist')?>?orderBy=updated_at&<?=$this->filtersGet?>&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th><a href="<?=self::url('user', 'trashedlist')?>" class="btn btn-sm btn-outline-secondary" title="<?=T_TRASH?>" aria-label="<?=T_TRASH?>"><?=self::icon('trash3', 16)?></a></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_user']?>">
		<td class="text-nowrap"><?php echo $element['id_user']?></td>
		<td ><?php echo htmlspecialchars((string)$element['email'])?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['permissions']?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=edit&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('pencil', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=logicaldeleteHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>">
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
    $parentPKValue = rawurlencode((string)($filters[$parentPK] ?? ''));


	?>
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childlist&orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_user']?>">
		<td class="text-nowrap"><?php echo $element['id_user']?></td>
		<td ><?php echo htmlspecialchars((string)$element['email'])?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

			<td class="text-nowrap"><a href="#" hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=user&task=childList&showTabs=0&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>" class="btn btn-sm btn-outline-primary" title="<?=T_EDIT?>" aria-label="<?=T_EDIT?>">
            <?=self::icon('screwdriver', 16);?>
            </a></td>
			<td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=delHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="<?=T_CONFIRM_DELETE?>" class="btn btn-sm btn-outline-danger" title="<?=T_DELETE?>" aria-label="<?=T_DELETE?>"><?=self::icon('trash3', 16)?></a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="17" class="text-end">
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
	<div class="table-responsive card shadow-sm mb-3">	<table class="table table-sm table-hover align-middle mb-0">
	<thead class="table-light">
	<tr>	<th scope="col" id="id_user" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=id_user&order=<?php if($orderBy == "id_user") echo $order; else echo 'asc';?>"><?=T_FIELD_ID?></a></th>
	<th scope="col" id="email" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=email&order=<?php if($orderBy == "email") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL?></a></th>
	<th scope="col" id="email_verified" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=email_verified&order=<?php if($orderBy == "email_verified") echo $order; else echo 'asc';?>"><?=T_FIELD_EMAIL_VERIFIED?></a></th>
	<th scope="col" id="first_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=first_name&order=<?php if($orderBy == "first_name") echo $order; else echo 'asc';?>"><?=T_FIELD_FIRST_NAME?></a></th>
	<th scope="col" id="last_name" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=last_name&order=<?php if($orderBy == "last_name") echo $order; else echo 'asc';?>"><?=T_FIELD_LAST_NAME?></a></th>
	<th scope="col" id="status" class="text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=status&order=<?php if($orderBy == "status") echo $order; else echo 'asc';?>"><?=T_FIELD_STATUS?></a></th>
	<th scope="col" id="created_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=created_at&order=<?php if($orderBy == "created_at") echo $order; else echo 'asc';?>"><?=T_FIELD_CREATED_AT?></a></th>
	<th scope="col" id="updated_at" class="tight text-nowrap"><a class="link-body-emphasis link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('user', 'trashedlist')?>?orderBy=updated_at&order=<?php if($orderBy == "updated_at") echo $order; else echo 'asc';?>"><?=T_FIELD_UPDATED_AT?></a></th>

		<th class="tight"></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_user']?>">
		<td class="text-nowrap"><?php echo $element['id_user']?></td>
		<td ><?php echo htmlspecialchars((string)$element['email'])?></td>
		<td ><?php echo $element['email_verified']?></td>
		<td ><?php echo $element['first_name']?></td>
		<td ><?php echo $element['last_name']?></td>
		<td ><?=self::value((string)$element['status'])?></td>
		<td ><?php echo $element['created_at']?></td>
		<td ><?php echo $element['updated_at']?></td>

		    <td class="text-nowrap"><a href="#" hx-target="#row-<?php echo $element['id_user']?>" hx-post="<?=BASE_URL?>/htmx.php?component=user&task=undelHtmx&id_user=<?php echo $element['id_user']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-sm btn-outline-secondary" title="<?=T_RESTORE?>" aria-label="<?=T_RESTORE?>">
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
    * @param array $providers key => label of the configured providers (Google, Facebook, X)
    * @return void
    */
    public function profile(ModelUser $data, array $message = null, int $passwordMinLength = 8, array $providers = array()){
        // An account created with a provider has no password until the user chooses one
        $hasPassword = (string)$data->password_hash != '';
        // The user is recognized by his email: his password, if he has one, and all the providers open the same account
        $signInWith = array_values($providers);
        if($hasPassword){
            array_unshift($signInWith, T_EMAIL_AND_PASSWORD);
        }
        $legend = 'fs-5 fw-bold d-flex align-items-center gap-2 mb-3';
        ?>
        <div class="row g-4">
            <div class="col-lg-4">
                <aside class="card shadow-sm">
                    <div class="card-body p-4 text-center">
                        <span class="d-inline-flex rounded-circle bg-primary-subtle text-primary p-3 mb-3" aria-hidden="true"><?=self::icon('person', 40)?></span>
                        <div class="h5 fw-bold mb-1"><?=$data->first_name?> <?=$data->last_name?></div>
                        <div class="text-body-secondary text-break mb-4"><?=htmlspecialchars((string)$data->email)?></div>
                        <dl class="small text-start mb-4">
                            <div class="d-flex justify-content-between gap-3 border-top py-2"><dt class="fw-normal text-body-secondary"><?=T_SIGN_IN_WITH?></dt><dd class="mb-0 fw-semibold text-end"><?=count($signInWith) ? implode('<br>', $signInWith) : '&mdash;'?></dd></div>
                            <?php if((string)$data->created_at != ''){ ?><div class="d-flex justify-content-between gap-3 border-top py-2"><dt class="fw-normal text-body-secondary"><?=T_MEMBER_SINCE?></dt><dd class="mb-0 fw-semibold text-end"><?=$this->getDate((string)$data->created_at)?></dd></div><?php } ?>
                            <?php if((string)$data->last_login_at != ''){ ?><div class="d-flex justify-content-between gap-3 border-top py-2"><dt class="fw-normal text-body-secondary"><?=T_LAST_SIGN_IN?></dt><dd class="mb-0 fw-semibold text-end"><?=$this->getDate((string)$data->last_login_at)?></dd></div><?php } ?>
                        </dl>
                        <a class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2" href="<?=self::url('shop', 'cart')?>"><?=self::icon('bag', 16)?> <?=T_MY_CART?></a>
                    </div>
                </aside>
            </div>

            <div class="col-lg-8">
                <?php $this->message($message); ?>
                <form method="POST" action="<?=self::url('user', 'profile')?>">
                    <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />

                    <fieldset class="card card-body shadow-sm p-4 mb-4">
                        <legend class="<?=$legend?>"><span class="d-inline-flex text-primary"><?=self::icon('person', 18)?></span> <?=T_PERSONAL_DETAILS?></legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="first_name"><?=T_FIRST_NAME?>&nbsp;*</label>
                                <input required maxlength="100" class="form-control" type="text" name="first_name" id="first_name" value="<?=$data->first_name?>" autocomplete="given-name"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="last_name"><?=T_LAST_NAME?>&nbsp;*</label>
                                <input required maxlength="100" class="form-control" type="text" name="last_name" id="last_name" value="<?=$data->last_name?>" autocomplete="family-name"/>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="email"><?=T_EMAIL?>&nbsp;*</label>
                                <input required maxlength="255" class="form-control" type="email" name="email" id="email" value="<?=htmlspecialchars((string)$data->email)?>" autocomplete="email"<?=count($providers) ? ' aria-describedby="email_help"' : ''?>/>
                                <?php if(count($providers)){ ?>
                                <div class="form-text" id="email_help"><?=sprintf(T_EMAIL_IDENTITY_HINT, implode(', ', $providers))?></div>
                                <?php } ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="card card-body shadow-sm p-4 mb-4">
                        <legend class="<?=$legend?>"><span class="d-inline-flex text-primary"><?=self::icon('lock', 18)?></span> <?=$hasPassword ? T_CHANGE_PASSWORD : T_CHOOSE_PASSWORD?></legend>
                        <p class="small text-body-secondary"><?=$hasPassword ? T_PASSWORD_KEEP_HINT : T_PASSWORD_NONE_HINT?></p>
                        <div class="row g-3">
                            <?php if($hasPassword){ ?>
                            <div class="col-md-4">
                                <label class="form-label" for="current_password"><?=T_CURRENT_PASSWORD?></label>
                                <input class="form-control" type="password" name="current_password" id="current_password" autocomplete="current-password"/>
                            </div>
                            <?php } ?>
                            <div class="<?=$hasPassword ? 'col-md-4' : 'col-md-6'?>">
                                <label class="form-label" for="password"><?=T_NEW_PASSWORD?></label>
                                <input class="form-control" type="password" name="password" id="password" minlength="<?=$passwordMinLength?>" maxlength="72" autocomplete="new-password" aria-describedby="password_help"/>
                                <div class="form-text" id="password_help"><?=sprintf(T_PASSWORD_MIN_HINT, $passwordMinLength)?></div>
                            </div>
                            <div class="<?=$hasPassword ? 'col-md-4' : 'col-md-6'?>">
                                <label class="form-label" for="password_confirmation"><?=T_CONFIRMATION?></label>
                                <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" maxlength="72" autocomplete="new-password"/>
                            </div>
                        </div>
                    </fieldset>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"><?=self::icon('check2', 18)?> <?=T_SAVE?></button>
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
        echo self::head($title);
        ?>
          <body class="bg-body-tertiary">
            <div class="container-fluid">
              <div class="row min-vh-100">
                <aside class="col-lg-5 col-xl-4 d-none d-lg-flex flex-column justify-content-between gap-5 text-bg-primary p-5">
                  <?php self::brand(true); ?>
                  <div>
                    <p class="h2 fw-bold mb-4"><?=T_YOUR_ACCOUNT?></p>
                    <ul class="list-unstyled d-grid gap-3 mb-0">
                      <li class="d-flex align-items-center gap-3"><span class="d-inline-flex rounded-circle bg-white bg-opacity-10 text-warning p-2"><?=self::icon('bag-check', 18)?></span> <?=T_CHECKOUT_FASTER?></li>
                      <li class="d-flex align-items-center gap-3"><span class="d-inline-flex rounded-circle bg-white bg-opacity-10 text-warning p-2"><?=self::icon('geo-alt', 18)?></span> <?=T_ADDRESSES_SAVED?></li>
                    </ul>
                  </div>
                  <div class="small text-white-50">&copy; <?=date('Y')?> <?=self::appName()?></div>
                </aside>
                <main class="col-lg-7 col-xl-8 d-flex flex-column py-4 px-3 px-sm-4 px-lg-5">
                  <div class="d-flex flex-wrap align-items-center gap-3">
                    <?php self::brand(false, 'd-lg-none'); ?>
                    <div class="d-flex align-items-center gap-3 ms-auto">
                      <?php self::languageSwitch(); ?>
                      <a class="icon-link small link-secondary link-underline-opacity-0 link-underline-opacity-75-hover" href="<?=self::url('product', 'viewlist')?>"><?=self::icon('arrow-left', 16)?> <?=T_BACK_TO_SHOP?></a>
                    </div>
                  </div>
                  <div class="row justify-content-center align-items-center flex-grow-1">
                    <div class="col-sm-10 col-md-8 col-xl-7 col-xxl-6 py-4">
                      <div class="card shadow p-4 p-sm-5">
                        <h1 class="h2 fw-bold mb-2"><?=$title?></h1>
                        <?php if($intro != ''){ ?><p class="text-body-secondary mb-4"><?=$intro?></p><?php } ?>
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
                </main>
              </div>
            </div>
            <?=self::scripts()?>
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
        <form method="POST" action="<?=htmlspecialchars(self::url('user', 'login', array('return' => $this->loginReturn)))?>">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="mb-3">
                <label class="form-label" for="email"><?=T_EMAIL?></label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?=htmlspecialchars($email)?>" autocomplete="username" required autofocus>
            </div>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-baseline gap-2">
                    <label class="form-label" for="password"><?=T_PASSWORD?></label>
                    <a class="small" href="<?=self::url('user', 'forgot')?>"><?=T_FORGOT_PASSWORD_Q?></a>
                </div>
                <input type="password" class="form-control form-control-lg" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100"><?=T_SIGN_IN?></button>
        </form>
        <?php $this->socialButtons($providers); ?>
        <p class="text-center text-body-secondary mt-4 mb-0"><?=T_NEW_HERE?> <a class="fw-semibold" href="<?=self::url('user', 'register')?>"><?=T_CREATE_ACCOUNT?></a></p>
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
        <form method="POST" action="<?=self::url('user', 'register')?>">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label" for="first_name"><?=T_FIRST_NAME?></label>
                    <input type="text" class="form-control" id="first_name" name="first_name" maxlength="100" value="<?=$values['first_name'] ?? ''?>" autocomplete="given-name" required autofocus>
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="last_name"><?=T_LAST_NAME?></label>
                    <input type="text" class="form-control" id="last_name" name="last_name" maxlength="100" value="<?=$values['last_name'] ?? ''?>" autocomplete="family-name" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email"><?=T_EMAIL?></label>
                <input type="email" class="form-control" id="email" name="email" maxlength="255" value="<?=htmlspecialchars($values['email'] ?? '')?>" autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password"><?=T_PASSWORD?></label>
                <input type="password" class="form-control" id="password" name="password" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required aria-describedby="password_help">
                <div class="form-text" id="password_help"><?=sprintf(T_PASSWORD_MIN_HINT, $passwordMinLength)?></div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password_confirmation"><?=T_CONFIRM_PASSWORD?></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100"><?=T_CREATE_MY_ACCOUNT?></button>
        </form>
        <?php $this->socialButtons($providers); ?>
        <p class="text-center text-body-secondary mt-4 mb-0"><?=T_ALREADY_CUSTOMER?> <a class="fw-semibold" href="<?=self::url('user', 'login')?>"><?=T_SIGN_IN?></a></p>
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
            $this->message(array('type' => 'warning', 'text' => '<strong>'.T_DEV_MODE.'</strong> (AUTH_SHOW_RESET_LINK): <a class="alert-link" href="'.htmlspecialchars($devResetLink).'">'.T_RESET_LINK.'</a>'));
        }
        ?>
        <form method="POST" action="<?=self::url('user', 'forgot')?>">
            <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
            <div class="mb-4">
                <label class="form-label" for="email"><?=T_EMAIL?></label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" autocomplete="username" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center gap-2"><?=self::icon('envelope', 18)?> <?=T_SEND_LINK?></button>
        </form>
        <p class="text-center mt-4 mb-0"><a class="icon-link" href="<?=self::url('user', 'login')?>"><?=self::icon('arrow-left', 14)?> <?=T_BACK_TO_SIGN_IN?></a></p>
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
            <form method="POST" action="<?=self::url('user', 'reset')?>">
                <input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>" />
                <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>" />
                <div class="mb-3">
                    <label class="form-label" for="password"><?=T_NEW_PASSWORD?></label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required autofocus aria-describedby="password_help">
                    <div class="form-text" id="password_help"><?=sprintf(T_PASSWORD_MIN_HINT, $passwordMinLength)?></div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password_confirmation"><?=T_CONFIRM_PASSWORD?></label>
                    <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" minlength="<?=$passwordMinLength?>" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100"><?=T_SAVE_NEW_PASSWORD?></button>
            </form>
            <?php
        }
        else{
            ?>
            <a class="btn btn-primary btn-lg w-100" href="<?=self::url('user', 'forgot')?>"><?=T_ASK_NEW_LINK?></a>
            <?php
        }
        ?>
        <p class="text-center mt-4 mb-0"><a class="icon-link" href="<?=self::url('user', 'login')?>"><?=self::icon('arrow-left', 14)?> <?=T_BACK_TO_SIGN_IN?></a></p>
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
        <div class="d-flex align-items-center gap-3 small text-body-secondary my-4"><hr class="flex-grow-1 my-0"><span><?=T_OR?></span><hr class="flex-grow-1 my-0"></div>
        <div class="d-grid gap-2">
        <?php
        foreach($providers as $key => $label){
            ?>
            <a class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2" href="<?=htmlspecialchars(self::url('user', 'oauth', array('provider' => $key, 'return' => $this->loginReturn)))?>"><?=isset($icons[$key]) ? self::icon($icons[$key], 18) : ''?> <?=sprintf(T_CONTINUE_WITH, $label)?></a>
            <?php
        }
        ?>
        </div>
        <?php
    }

}
