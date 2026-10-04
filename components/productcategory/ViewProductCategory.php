<?php 
/**
 * @author ApGenic.com <support@apgenic.com>
 * @author ApGenic generate a boilerplate web app from your database structure
 * @license GPL
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
declare(strict_types=1);
namespace Apgenic\ProductCategory;

class ViewProductCategory extends \Apgenic\Classes\ViewTemplate {
var $productList2 = array();
var $categoryList2 = array();

public string $filtersGet = '';

public $fileters = array();

function __construct($productList2, $categoryList2) {
$this->productList2 = $productList2;
$this->categoryList2 = $categoryList2;
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
        <a id="tab_info" class="nav-link <?php if($activeTab == 'info') echo 'active';?>" hx-target="#main_content" hx-swap="innerHTML"  hx-get="<?=BASE_URL?>/htmx.php?component=product_category&task=edit&id_product=<?=$id?>&id_category=<?=$id?>">Info</a>
      </li>
      
    </ul>
    

     
<?php
}
/**
 * Edit form for the table product_category
 * @parameter ModelProductCategory $data
 * @parameter int $showTabs 
 * @parameter array $message
 * @return void
 */
public function edit(ModelProductCategory $data , int $showTabs = 1, array $message=null, string $task = 'edit'){
	?>
	<div>
	    <?php
	    if($showTabs == 1){
	        self::tabs($data->id_product, 'info');
	        echo '<div id="core_content">';
	    }
	    ?>
		<?php
		$this->message($message);
		?>
		
		<form method="POST" enctype="multipart/form-data" hx-target="#core_content" hx-swap="innerHTML" hx-post="<?=BASE_URL?>/htmx.php?component=productcategory&task=<?=$task?>&showTabs=0&<?=$this->filtersGet?>">
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
        foreach ($this->productList2 as $key => $val){
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
                        if(!isset($this->filters['id_category'])){ ?>
                        
        <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label class="control-label">Id&nbsp;category&nbsp;*:</label>
            <select  class="form-control" name="id_category" id="id_category">
                <option value=""></option>
        <?php
        foreach ($this->categoryList2 as $key => $val){
                ?>
                <option value="<?=$val['id_category']?>" <?php if($data->id_category == $val['id_category']) echo 'selected="selected"';?>><?=$val['name']?></option>
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
                            
                            <input type="hidden" name="id_category" id="id_category" value="<?php if(!isset($data->id_category) || $data->id_category < 1) echo $_GET['filters']['id_category']; else echo $data->id_category?>"/>
                            <?php
                        }
                        ?>                     
                        
		<div class="col-12">
		
		<div class="form-group">
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
 * Display rows from the table product_category
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
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=productcategory&task=viewlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=productcategory&task=viewlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr>
		<td class="shrink"><?php echo $this->productList2[$element['id_product']]['name'] ?? '';?></td>
		<td ><?php echo $this->categoryList2[$element['id_category']]['name'] ?? '';?></td>
	
		    <td class="shrink"><a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=view&id_product=<?php echo $element['id_product']?>&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>" class="btn">
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
	<td colspan="5" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






/**
 * Display details of a record from the table product_category
 *
 * @parameter ModelProductCategory $data 
 * @parameter array $message
 * @return void
 */
public function view(ModelProductCategory $data , array $message=null){
        $this->message($message);
	

	$i=1;

	if ($data->id_product != null){
	?>
	
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
            <div class="col-sm-12">
                <dl class="row">
                    
		        <dt class="col-sm-3"><h5>Id&nbsp;product</h5></dt><dd class="col-sm-9"><?php echo $this->productList2[$data->id_product]['name'] ?? '';?></dd>
		        <dt class="col-sm-3"><h5>Id&nbsp;category</h5></dt><dd class="col-sm-9"><?php echo $this->categoryList2[$data->id_category]['name'] ?? '';?></dd>
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
 * Display rows from the table product_category
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
    <a hx-swap="outerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=<?=$task?>&value=<?php echo $value?>&id_product=<?php echo $pkVal;?>" class="btn btn-outline btn-outline-<?=$buttonTyp[$value]?>"><?php echo $buttonIcon[$value];?></a>        
    <?php
}

/**
 * Display rows from the table product_category with edit and del buttons
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
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=productcategory&task=editlist&orderBy=id_product&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=productcategory&task=editlist&orderBy=id_category&<?=$this->filtersGet?>&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?><?php echo $element['id_category']?>">
		<td class="shrink">
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=editList&id_product=<?php echo $element['id_product']?>">
                                            <?php echo $this->productList2[$element['id_product']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=editList&id_category=<?php echo $element['id_category']?>">
                                            <?php echo $this->categoryList2[$element['id_category']]['name'] ?? '';?>
                                            </a>
                                            </td>
                                            
	<td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?><?php echo $element['id_category']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productcategory&task=delHtmx&id_product=<?php echo $element['id_product']?>&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">            
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
	<td colspan="3" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}

        
/**
 * Display rows from the table product_category with edit and del buttons
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
	<tr>	<th scope="col" id="id_product"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=childlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_category"><a hx-target="#core_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=childlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>&<?=$this->filtersGet?>">Id&nbsp;category</a></th>

		<th>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?><?php echo $element['id_category']?>">
		<td class="shrink">
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=editList&id_product=<?php echo $element['id_product']?>">
                                            <?php echo $this->productList2[$element['id_product']]['name'];?>
                                            </a>
                                            </td>
                                            
		<td >
                                            <a hx-target="#main_content" hx-swap="innerHTML" hx-get="<?=BASE_URL?>/htmx.php?component=productcategory&task=editList&id_category=<?php echo $element['id_category']?>">
                                            <?php echo $this->categoryList2[$element['id_category']]['name'];?>
                                            </a>
                                            </td>
                                            
	    
			<td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?><?php echo $element['id_category']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productcategory&task=delHtmx&id_product=<?php echo $element['id_product']?>&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" hx-confirm="Are you sure?" class="btn btn-outline btn-outline-danger">Del</a></td>
		</tr>
		<?php
	}?>
	</tbody>

	<?php



		?>
	<tfoot>
	<tr>
	<td colspan="3" class="phppistolsTFooter" style="text-align: right;">
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
 * Display trashed rows from the table product_category
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
	<tr>	<th scope="col" id="id_product"><a href="<?=BASE_URL?>/index.php?component=productcategory&task=trashedlist&orderBy=id_product&order=<?php if($orderBy == "id_product") echo $order; else echo 'asc';?>">Id&nbsp;product</a></th>
	<th scope="col" id="id_category"><a href="<?=BASE_URL?>/index.php?component=productcategory&task=trashedlist&orderBy=id_category&order=<?php if($orderBy == "id_category") echo $order; else echo 'asc';?>">Id&nbsp;category</a></th>

		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$i=1;


	foreach ($data as $element){
		?>
		<tr id="row-<?php echo $element['id_product']?><?php echo $element['id_category']?>">
		<td class="shrink"><?php echo $this->productList2[$element['id_product']]['name'] ?? '';?></td>
		<td ><?php echo $this->categoryList2[$element['id_category']]['name'] ?? '';?></td>
	
		    <td class="shrink"><a hx-target="#row-<?php echo $element['id_product']?><?php echo $element['id_category']?>" hx-post="<?=BASE_URL?>/htmx.php?component=productcategory&task=undelHtmx&id_product=<?php echo $element['id_product']?>&id_category=<?php echo $element['id_category']?>&<?=$this->filtersGet?>&csrf_token=<?=$_SESSION['csrf_token']?>" class="btn btn-outline btn-outline-secondary">
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
	<td colspan="5" class="phppistolsTFooter"></td>
	</tr>
	</tfoot>

	</table>
	<?php
}






}
	