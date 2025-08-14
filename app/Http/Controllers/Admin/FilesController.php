<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController as Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Contracts\Filesystem\Filesystem;
use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class FilesController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
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
    
    /**
     * 
     */
    private function getFile($path): array
    {
        $file_path_arr = explode('/',$path);
        $file_path = storage_path('app/public/' . $path);
        $file_name = end($file_path_arr);
        $file_type = Storage::mimeType($path);
        $file_checksum = hash_file('sha256', $file_path);
        $file_date = Carbon::createFromTimestamp(Storage::lastModified($path))->format('d-m-Y h:i');
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
            'path' => $file_path,
            'size' => $this->getFilesize(filesize($file_path))
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
            foreach(new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, 
                \FilesystemIterator::SKIP_DOTS)) as $object){
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
            return view('admin.files.index', [
                'files' => $files,
                'path' => $path
            ]);
        } else {
            $file = $this->getFile($path);
            //dd($file);
            return view('admin.files.view', compact('file'));
        }
    }
    /**
     * 
     */
    public function upload(string $path = '')
    {
        return view('admin.files.upload', ['path' => $path ?: '.']);
    }
    /**
     * 
     */
    public function store(Request $request, string $path = ''): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'files_to_upload'   => 'sometimes|required|array',
            'files_to_upload.*' => 'file|mimes:jpg,jpeg,png,gif,svg,webp|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('files_to_upload')) {
            $optimizerChain = OptimizerChainFactory::create();

            foreach ($request->file('files_to_upload') as $file) {
                $savedRelativePath = $file->storeAs($path, $file->getClientOriginalName(), 'public'); // Save original
                $physicalPath = Storage::disk('public')->path($savedRelativePath);
                $optimizerChain->optimize($physicalPath); // Optimize
                $webpPath = pathinfo($physicalPath, PATHINFO_DIRNAME) . '/' . pathinfo($physicalPath, PATHINFO_FILENAME) . '.webp';
                Image::load($physicalPath)->save($webpPath); // Save as webp
            }

        }
        return response()->json(['success' => true, 'message' => 'Files uploaded and optimized successfully!']);
    }
    /**
     * 
     */
    public function destroy(string $path): \Illuminate\Http\RedirectResponse
    {
        $file_path_arr = explode('/',$path);
        $file_name = end($file_path_arr);
        $file_mime_type = mime_content_type($path);
        if(str_contains($file_mime_type,'image')) {
            $file_name_arr = explode('.',$file_name);
            $file_webp_path = str_replace($file_name,$file_name_arr[0] . '.webp',$path);
            if(file_exists($file_webp_path)){
                File::delete($file_webp_path);
            }
        }
        $redirect_path = str_replace(storage_path(),'',str_replace($file_name,'',$path));
        File::delete($path);
        return redirect()->route('admin.files.index',['path' => $redirect_path])->with('success', 'File ' . $file_name . ' removed');

    }

    /**
     * 
     */
    public function createFolder(string $path): \Illuminate\Http\RedirectResponse
    {
        Storage::makeDirectory($path);
        return redirect()->route('admin.files.index',['path' => $path])->with('success', 'Folder ' . $path . ' created');
    }
}
