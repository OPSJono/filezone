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
}
