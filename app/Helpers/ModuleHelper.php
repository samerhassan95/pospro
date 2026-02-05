<?php

if (!function_exists('isModuleEnabled')) {
    function isModuleEnabled($moduleName): bool
    {
        $statusFile = base_path('modules_statuses.json');
        
        if (!file_exists($statusFile)) {
            return false;
        }
        
        $moduleStatuses = json_decode(file_get_contents($statusFile), true);
        
        return isset($moduleStatuses[$moduleName]) && $moduleStatuses[$moduleName] === true;
    }
}