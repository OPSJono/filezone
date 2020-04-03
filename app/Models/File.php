<?php

namespace App\Models;

class File extends BaseModel
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
        'folder_id',
        'name',
        'description',
        'type',
        'size',

        'storage_method',
        'storage_region',
        'storage_path',
    ];

    public function folder()
    {
        return $this->hasOne(Folder::class, 'id', 'folder_id');
    }

    public function permissions()
    {
        return $this->hasMany(FilePermission::class, 'file_id', 'id');
    }
}
