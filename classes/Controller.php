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

    /** Fields that cannot be used as a filter of a list */
    const NOT_FILTERS = array('password_hash');


    /**
     * Set the order and filter private variable depending of the browser request
     * @return void
     */
    /**
     * Date typed in a field datetime-local (2026-11-26T00:00), as stored in the database (2026-11-26 00:00:00)
     * @param bool $isNew a new entry: today at 00:00 when the date is empty or not valid
     * @return string|null null: no date
     */
    protected static function publishedAt(string $typed, bool $isNew):?string{
        $typed = trim($typed);
        foreach(array('Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i:s', 'Y-m-d H:i') as $format){
            $date = \DateTime::createFromFormat('!'.$format, $typed);
            if($date !== false && $date->format($format) === $typed){
                return $date->format('Y-m-d H:i:s');
            }
        }
        return $isNew ? Model::today().' 00:00:00' : null;
    }


    /**
     * A slug that no other entry has: the slug, then slug-2, slug-3...
     * @param callable $taken function(string $slug): bool, true when another entry has the slug
     * @param int $max longest slug (VARCHAR(200))
     * @return string
     */
    protected static function uniqueSlug(string $slug, callable $taken, int $max = 200):string{
        $candidate = $slug;
        for($i = 2; $taken($candidate) && $i < 1000; $i++){
            $suffix = '-'.$i;
            $candidate = rtrim(substr($slug, 0, $max - strlen($suffix)), '-').$suffix;
        }
        return $candidate;
    }


    protected function setOrderAndFilters($defaultOrderBy = '', $defaultOrder = 'asc'):void{

        $orderBy = $_GET['orderBy'] ?? $defaultOrderBy;
        // Validate field existance
        if(is_string($orderBy) && in_array($orderBy, static::$fieldsNames, true) && !in_array($orderBy, self::NOT_FILTERS, true))
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
                if(is_string($fieldName) && in_array($fieldName, static::$fieldsNames, true) && is_scalar($value)){
                    $this->filters[$fieldName] = $value;
                }
            }
        }

        // The components also add filters of their own (field_search of the search form, foreign keys): only the
        // fields of the table and simple values are kept. They are written in the links and the forms of the pages
        foreach ($this->filters as $key=>$val){
            if(!is_string($key) || !in_array($key, static::$fieldsNames, true) || in_array($key, self::NOT_FILTERS, true)
                || !($val === null || is_scalar($val))){
                unset($this->filters[$key]);
            }
            // The keys (id_...) are numbers
            elseif(strpos($key, 'id_') === 0 && (string)$val !== ''){
                $this->filters[$key] = (int)$val;
            }
        }

        // Used in urls
        foreach ($this->filters as $key=>$val){
            if((string)$val != ''){
                $this->filtersGet .= 'filters['.urlencode($key).']='.urlencode((string)$val).'&';
            }
        }

        $this->page = max(1, (int)filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT));
    }


}
