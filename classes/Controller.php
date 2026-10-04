<?php

namespace Apgenic\Classes;

abstract class Controller
{
    protected string $order = 'asc';
    protected string $orderBy = '';
    protected string $revertOrder = '';
    protected int $page = 1;
    protected array $filters = array();
    protected string $filtersGet = '';
    protected array $message = array();
    protected int $showTabs = 1;
    protected int $itemsByPage = 10;


    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{

        $orderBy = $_GET['orderBy'] ?? $defaultOrderBy;
        // Validate field existance
        if(array_search($orderBy, static::$fieldsNames) !== false)
            $this->orderBy = $orderBy;

        // Set order in sort link
        $this->order = $_GET['order'] ?? $defaultOrder;
        if($this->order != 'asc'){
            $this->order = 'desc';
        }

        $this->revertOrder = 'desc';
        if($this->order == 'desc'){
            $this->revertOrder = 'asc';
        }

        // Get filters passed by GET
        if(isset($_GET['filters']) && is_array($_GET['filters'])){
            foreach($_GET['filters'] as $fieldName => $value){
                if(array_search($fieldName, static::$fieldsNames) !== false){
                    $this->filters[$fieldName] = $value;
                }
            }
        }

        // Used in urls
        foreach ($this->filters as $key=>$val){
            if($val != ''){
                $this->filtersGet .= 'filters['.$key.']='.urlencode($val).'&';
            }
        }

        $this->page = intval($_GET['page'] ?? 1);
    }


}
