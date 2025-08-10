<?php

namespace App\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class PageFormOptionsService
{
    public function getControllers(): array
    {
        $controllers = [];
        $path = app_path('Http/Controllers');
        foreach (File::files($path) as $file) {
            $controllerName = $file->getFilenameWithoutExtension();
            if ($controllerName === 'Controller') continue;

            $className = 'App\\Http\\Controllers\\' . $controllerName;
            $reflection = new \ReflectionClass($className);
            $methods = [];

            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getDeclaringClass()->getName() !== $className) continue;
                
                $returnType = $method->getReturnType();
                if ($returnType && $returnType->getName() === View::class) {
                    $methods[] = $method->getName();
                }
            }

            if (!empty($methods)) {
                $controllers[$controllerName] = $methods;
            }
        }
        return $controllers;
    }
    /**
     * 
     */
    public function getPageViews(): array
    {
        $views = [];
        $path = resource_path('views/pages');
    
        if (!File::isDirectory($path)) {
            return [];
        }
    
        foreach (File::files($path) as $file) {
            $viewName = basename($file->getFilename(), '.blade.php');
            $views[] = 'pages.' . $viewName;
        }
        
        return $views;
    }

    public function getSections(): array
    {
        $sections = [];
        $path = resource_path('views/components/sections');
        foreach (File::files($path) as $file) {
            $sections[] = basename($file->getFilename(), '.blade.php');
        }
        return $sections;
    }
    
    public function getSortedParts(array $pageParts = []): array
    {
        $allSections = $this->getSections();
        $selected = array_intersect($pageParts, $allSections);
        $remainder = array_diff($allSections, $pageParts);
        return array_merge($selected, $remainder);
    }
}