<?php

namespace App\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class PageFormOptionsService
{   
    /**
     * Get a list of all Controllers and their methods
     */
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
    private function getViews(string $path, bool $fullPath = true): array
    {
        $views = [];
        $resourcePath = resource_path('views/' . $path);
        if (!File::isDirectory($resourcePath)) {
            return [];
        }
    
        foreach (File::files($resourcePath) as $file) {
            $viewName = basename($file->getFilename(), '.blade.php');
            $views[] = $fullPath ? str_replace('/', '.', $path) . '.' . $viewName : $viewName;
        }
        
        return $views; 
    }
    /**
     * Get a list of all page views 
     */
    public function getPageViews(): array
    {
        return $this->getViews('pages');
    }
    /**
     * Get a list of alle sections
     */
    public function getSections(): array
    {
        return $this->getViews('components/sections', false);
    }
    
    public function getSortedParts(array $pageParts = []): array
    {
        $allSections = $this->getSections();
        $selected = array_intersect($pageParts, $allSections);
        $remainder = array_diff($allSections, $pageParts);
        return array_merge($selected, $remainder);
    }
}