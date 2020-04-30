<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FolderController extends Controller
{

    public function index()
    {
        /**
         * @var $user User
         */
        $user = User::currentUser();

        $user->load([
            'folders',
        ]);

        /**
         * @var $folders Collection
         */
        $folders = $user->listOwnedFolders();

        return response()->json([
            'success' => true,
            'folders' => $folders
        ]);
    }

    public function view($id)
    {
        /**
         * @var $folders Folder
         */
        $folders = Folder::with([
            'childFolders',
            'files'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'folders' => $folders
        ]);
    }

    public function create()
    {
        /**
         * @var $validator Validator
         */
        $validator = app()->make('validator')->make($this->request->input(), [
            'parent_folder_id' => 'exists:folders,id',
            'name' => 'required|min:2',
            'description' => 'min:2',
        ]);

        if($validator->passes()) {
            $folder = new Folder();
            $folder->fill($this->request->input());

            if($folder->save()) {
                return response()->json([
                    'success' => true,
                    'folder' => $folder->toArray()
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
            'parent_folder_id' => 'exists:folders,id',
            'name' => 'required|min:2',
            'description' => 'min:2',
        ]);

        if($validator->passes()) {
            $folder = Folder::findOrFail($id);
            $folder->fill($this->request->input());

            if($folder->save()) {
                return response()->json([
                    'success' => true,
                    'folder' => $folder->toArray()
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
         * @var Folder $folder
         */
        $folder = Folder::findOrFail($id);

        if($folder->delete()) {
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
