<?php
// Autoloader
// ------------------------------------------------------------------------------------------------
function ApgenicAutoloader($className)
{
    $path = explode('\\', $className);
    if($path[0] != 'Apgenic') return false;

    if ($path[1] == 'Classes') {
        require_once('classes/'.$path[2].'.php');
        return true;
    }
    else{
        require_once ('components/'.strtolower($path[1]).'/'.$path[2].'.php');
    }
    /*
    else {
        if (strtolower(substr($className, 0, 10)) == 'controller') {
            $classBaseName = strtolower(substr($className, 10));
            $fname = 'components/' . $classBaseName . '/Controller' . ucfirst($classBaseName) . '.php';
        } elseif (strtolower(substr($className, 0, 5)) == 'model') {
            $classBaseName = strtolower(substr($className, 5));
            $fname = 'components/' . $classBaseName . '/Model' . ucfirst($classBaseName) . '.php';
        } elseif (strtolower(substr($className, 0, 4)) == 'view') {
            $classBaseName = strtolower(substr($className, 4));
            $fname = 'components/' . $classBaseName . '/View' . ucfirst($classBaseName) . '.php';
        }

        require_once($fname);
        return true;
    }
    */
    return false;
}

spl_autoload_register('ApgenicAutoloader');
