<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController as Controller;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    protected $files;

    public function __construct(\Illuminate\Contracts\Filesystem\Filesystem $files)
    {
        $this->files = $files;
    }
    /**
     * 
     */
    private function getFiles($directory = ''): array
    {
        $file_storage_path = 'app/public/' . $directory;
        $file_path = storage_path($file_storage_path);
        $file_list = scandir($file_path);
        $files = [];
        foreach($file_list as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $item_path = $file_path . '/' . $item;
            $item_storage_path = ltrim($directory, '/') . '/' . $item;
            //dd($item_storage_path);
            if(str_contains($item,'.')){
                if(filetype($item_path) !== 'dir' && $item !== '.gitignore') {
                    $files[] = [
                        'name' => $item,
                        'path' => ltrim($directory, '/') . '/' . $item,
                        'type' => Storage::mimeType($item_storage_path),
                        'size' => $this->getFilesize(Storage::size($item_storage_path)),
                        'date' => Storage::lastModified($item_storage_path)

                    ];
                }
            }else{
                $files[] = [
                    'type' => 'folder',
                    'name' => $item,
                    'path' => ltrim($directory, '/') . '/' . $item,
                    'size' => $this->getFilesize($this->getDirectorySize($item_path))
                ];
            }
            
        }
        return $files;
    }
    private function getFile($path)
    {
        $file_path_arr = explode('/',$path);
        $file_path = storage_path('app/public/' . $path);
        $file_name = end($file_path_arr);
        $file_type = Storage::mimeType($path);
        $file_checksum = hash_file('sha256', $file_path);
        $file_date = Storage::lastModified($path);
        $file_contents = Storage::get($path);
        switch ($file_type) {
            case 'application/json':
                //$file_contents = Storage::json($path);
                break;
            case 'image/png':
            case 'image/webp':
                
                break;
            default:
                //
                break;
        }
        return [
            'name' => $file_name,
            'type' => $file_type,
            'content' => $file_contents,
            'hash' => $file_checksum,
            'date' => $file_date,
            'path' => $file_path
        ];
    }
    /**
     * 
     */
    private function getDirectorySize(string $path): int
    {
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }
    /**
     * 
     */
    private function getFilesize(int $bytes, int $decimals = 2): string 
    {
        $factor = floor((strlen($bytes) - 1) / 3);
        if ($factor > 0) $sz = 'KMGT';
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
    }
    /**
     * 
     */
    public function index(string $path = '')
    {
        if(empty($path) || !str_contains($path, '.')) {
            $files = $this->getFiles($path);
            return view('admin.files.index', compact('files'));
        } else {
            $file = $this->getFile($path);
            //dd($file);
            return view('admin.files.view', compact('file'));
        }
    }
}
