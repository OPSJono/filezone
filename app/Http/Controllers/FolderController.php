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
            'folders' => function($query) {
                $query->rootFolders();
            },
        ]);

        /**
         * @var $folders Collection
         */
        $folders = $user->listOwnedFolders();

        $this->apiResponse->setSuccess(['folders' => $folders]);

        return $this->apiResponse->returnResponse();
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

        $this->apiResponse->setSuccess(['folders' => $folders]);

        return $this->apiResponse->returnResponse();
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
                $this->apiResponse->setSuccess(['folder' => $folder->toArray()]);
            } else {
                $this->apiResponse->setGeneralError('Failed to save record');
            }
        } else {
            $this->apiResponse->handleErrors($validator);
        }

        return $this->apiResponse->returnResponse();
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
                $this->apiResponse->setSuccess(['folder' => $folder->toArray()]);
            } else {
                $this->apiResponse->setGeneralError('Failed to save record');
            }
        } else {
            $this->apiResponse->handleErrors($validator);
        }

        return $this->apiResponse->returnResponse();
    }

    public function delete($id)
    {
        /**
         * @var Folder $folder
         */
        $folder = Folder::findOrFail($id);

        if(!$folder->delete()) {
            $this->apiResponse->setGeneralError('Failed to delete record.');
        }

        return $this->apiResponse->returnResponse();
    }
}
