<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
            'folder_id' => 'required|exists:Folders,id',
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

    public function create()
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'folder_id' => 'required|exists:Folders,id',
            'name' => 'required|min:2',
            'description' => 'min:2',
            'storage_region' => 'required',
        ]);

        if($validator->passes()) {
            $file = new File();
            $file->fill($this->request->input());

            $file->folder_id = $this->request->input('folder_id');

            $file->extension = '';
            $file->type = '';
            $file->size = '';

            $file->storage_method = '';
            $file->storage_region = $this->request->input('storage_region');
            $file->storage_path = '';

            $file->file_hash = '';

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
            'name' => 'required|min:2',
            'description' => 'min:2',
        ]);

        if($validator->passes()) {
            $file = new File();
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
}
