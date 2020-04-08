<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class FileController extends Controller
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * Create a new controller instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'folder_id' => 'required|exists:folders,id',
        ]);

        /**
         * @var $files Builder
         */
        $query = File::query();

        if($validator->passes()) {
            $query = $query->where('folder_id', $this->request->input('folder_id'));

            /**
             * @var $files Collection
             */
            $files = $query->get();

            return response()->json([
                'success' => true,
                'files' => $files
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray()
        ])->setStatusCode(400);

    }

    public function view($id)
    {
        /**
         * @var File $file
         */
        $file = File::findOrFail($id);

        $file->last_accessed_by = User::currentUser()->id;
        $file->last_accessed_at = Carbon::now();

        $file->save();

        return response()->json([
            'success' => true,
            'file' => $file->toArray()
        ]);
    }

    public function create()
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'file' => 'file',
            'file_b64' => 'string',
            'extension' => 'required_with:file_b64',
            'mimetype' => 'required_with:file_b64',
            'size' => 'required_with:file_b64',
            'folder_id' => 'required|exists:folders,id',
            'name' => 'required|min:5',
            'description' => 'min:5',
//            'storage_region' => 'required',
        ]);

        if($validator->passes()) {

            $fileData = $this->saveFileToDisk();

            $file = new File();
            $file->fill($this->request->input());

            $file->folder_id = $this->request->input('folder_id');

            $file->guid = $fileData->guid;

            $file->extension = $fileData->extension;
            $file->mimetype = $fileData->mimetype;
            $file->size = $fileData->size;

            $file->storage_method = 'DISK';
            $file->storage_region = 'uploads';
            $file->storage_path = $fileData->path_to_file;

            $file->file_hash = $fileData->file_hash;

            if($file->save()) {
                return response()->json([
                    'success' => true,
                    'file' => $file->toArray()
                ]);
            } else {
                $validator->getMessageBag()->add('general', 'Failed to save record.');
            }
        }

        return response()->json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray()
        ])->setStatusCode(400);
    }

    public function update($id)
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'name' => 'required|min:5',
            'description' => 'min:5',
        ]);

        if($validator->passes()) {
            /**
             * @var $file File
             */
            $file = File::findOrFail($id);
            $file->fill($this->request->input());


            if($file->save()) {
                return response()->json([
                    'success' => true,
                    'file' => $file->toArray()
                ]);
            } else {
                $validator->getMessageBag()->add('general', 'Failed to save record.');
            }
        }

        return response()->json([
            'success' => false,
            'errors' => $validator->getMessageBag()->toArray()
        ])->setStatusCode(400);
    }

    public function delete($id)
    {
        /**
         * @var File $file
         */
        $file = File::findOrFail($id);

        if($file->delete()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => [
                'general' => 'Failed to delete record'
            ]
        ]);
    }

    public function download($id)
    {
        /**
         * @var File $file
         */
        $file = File::findOrFail($id);

        // TODO: Actually download file from disk, with correct headers etc...
        return response()->json([
            'success' => true,
            'file' => $file->toArray()
        ]);
    }

    /**
     * @return object
     */
    private function saveFileToDisk()
    {
        $tempFilePath = false;

        try {
            // Generate a ResourceGuid to work out what the filename on disk should be.
            $guid = Str::orderedUuid()->toString();

            $folderString = explode('-',$guid)[0];
            $folderPrefixes = str_split($folderString, 2);
            $baseFolder = storage_path('uploads');
            $folderPath = $baseFolder.'/'.implode('/', $folderPrefixes).'/';
            $thumbnailPath = 'thumbnail/'.$folderPath.'/';

            $name_on_disk = $guid;
            $ext = null;

            // Save the file to disk first
            if ($this->request->hasFile('file')) {
                /**
                 * @var $file UploadedFile
                 */
                $file = $this->request->file('file');
                // If we have a file object, get the size direct from the file rather than trusting the user input.
                $size = $file->getSize();
                $ext = $file->getClientOriginalExtension();
                $mimetype =  $file->getMimeType();

                $tempFilePath = $file->getRealPath();

                if(!empty($ext)) {
                    $name_on_disk .= '.'.$ext;
                }

                // Move the file to our transient storage folder
                $file->move($folderPath, $name_on_disk);
            } else {
                $ext = $this->request->input('extension', null);
                if(!empty($ext)) {
                    $name_on_disk .= '.'.$ext;
                }

                $size = $this->request->input('size', 0);
                $mimetype = $this->request->input('mimetype', 0);

                // fallback to a base64 encoded string as a file
                $fileContents = $this->request->input('file_b64', null);
                if(!empty($fileContents)) {
                    file_put_contents($folderPath.$name_on_disk, base64_decode($fileContents));
                }
            }

            if(!file_exists($folderPath.$name_on_disk)) {
                throw new RuntimeException("File failed to save to disk", 500);
            }

            // Attempt to create a thumbnail for the file
            $thumbnail = $this->makeThumbnail($folderPath.$name_on_disk, $thumbnailPath.$name_on_disk);

            if($thumbnail == true) {
                $thumbnail = $thumbnailPath.$name_on_disk;
            }

            $fileHash = md5_file($folderPath.$name_on_disk);

            // Return data so we can create a record of the file in the database
            return $fileData = (object) [
                'guid' => $guid,
                'size' => $size,
                'mimetype' => $mimetype,
                'extension' => $ext,
                'name_on_disk' => $name_on_disk,
                'path_to_file' => $folderPath.$name_on_disk,
                'thumbnail' => $thumbnail,
                'file_hash' => $fileHash,
            ];
        } catch (\Throwable $e) {
            if($tempFilePath !== false && file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }

            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * @author Pedro Pinheiro: https://gist.github.com/pedroppinheiro/7a039da05fd9a1bc4182
     * @author Jonathan Marshall
     *
     * @param $filepath string path to the original image
     * @param $thumbpath string path to where the thumbnail should be saved
     * @param $thumbnail_width int width of thumbnail image
     * @param $thumbnail_height int height of thumbnail image
     * @param $background string|array|bool background of the thumbnail
     *
     * @return bool
     *
     * Modified the original Ref slightly to wrap in a try-catch and log any errors.
     * Also to unlink the thumbnail if it already exists before trying to create it.
     * And to abort early on if the file passed isn't an image. (has a width|height of 0)
     * */
    private function makeThumbnail($filepath, $thumbpath, $thumbnail_width = 80, $thumbnail_height = 80, $background = 'transparent')
    {
        try {
            if (is_file($thumbpath)) {
                unlink($thumbpath);
            }

            list($original_width, $original_height, $original_type) = getimagesize($filepath);
            if ($original_width == 0 || $original_height == 0) {
                return false;
            }
            if ($original_width > $original_height) {
                $new_width = $thumbnail_width;
                $new_height = intval($original_height * $new_width / $original_width);
            } else {
                $new_height = $thumbnail_height;
                $new_width = intval($original_width * $new_height / $original_height);
            }
            $dest_x = intval(($thumbnail_width - $new_width) / 2);
            $dest_y = intval(($thumbnail_height - $new_height) / 2);

            if ($original_type === 1) {
                $imgt = "ImageGIF";
                $imgcreatefrom = "ImageCreateFromGIF";
            } else if ($original_type === 2) {
                $imgt = "ImageJPEG";
                $imgcreatefrom = "ImageCreateFromJPEG";
            } else if ($original_type === 3) {
                $imgt = "ImagePNG";
                $imgcreatefrom = "ImageCreateFromPNG";
            } else {
                return false;
            }

            $old_image = $imgcreatefrom($filepath);
            $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background

            // figuring out the color for the background
            if (is_array($background) && count($background) === 3) {
                list($red, $green, $blue) = $background;
                $color = imagecolorallocate($new_image, $red, $green, $blue);
                imagefill($new_image, 0, 0, $color);
                // apply transparent background only if is a png image
            } else if ($background === 'transparent' && $original_type === 3) {
                imagesavealpha($new_image, true);
                $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagefill($new_image, 0, 0, $color);
            }

            imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
            $imgt($new_image, $thumbpath);
        } catch (\Exception $e) {
            Log::error('FileUploadServer::makeThumbnail failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }

        return file_exists($thumbpath);
    }
}
