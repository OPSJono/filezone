<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FolderController extends Controller
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

    //
}
