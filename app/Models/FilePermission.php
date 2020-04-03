<?php

namespace App\Models;

class FilePermission extends BaseModel
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
        'user_id',

        'read',
        'write',
        'download',
    ];

    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function canRead()
    {
        if($this->read == 1) {
            return true;
        }

        return false;
    }

    public function canWrite()
    {
        if($this->write == 1) {
            return true;
        }

        return false;
    }

    public function canDownload()
    {
        if($this->download == 1) {
            return true;
        }

        return false;
    }
}
