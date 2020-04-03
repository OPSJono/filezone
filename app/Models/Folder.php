<?php

namespace App\Models;

class Folder extends BaseModel
{
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_folder_id',
        'name',
        'description',
    ];

    public function parentFolder()
    {
        return $this->hasOne(Folder::class, 'id', 'parent_folder_id');
    }

    public function childFolders()
    {
        return $this->hasMany(Folder::class, 'parent_folder_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'folder_id', 'id');
    }
}
