<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends BaseModel
{
    use SoftDeletes;

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

    protected $appends = [
        'sub_folder_count',
        'files_count',
    ];

    /**
     * @param $value
     * @void
     */
    public function setParentFolderIdAttribute($value)
    {
        if(empty(trim($value))) {
            $value = null;
        }

        $this->attributes['parent_folder_id'] = $value;
    }

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

    public function scopeRootFolders($query)
    {
        $query->whereNull('parent_folder_id');

    }

    public function getSubFolderCountAttribute()
    {
        if(isset($this->childFolders)) {
            return count($this->childFolders);
        }

        return 0;
    }

    public function getFilesCountAttribute()
    {
        if(isset($this->files)) {
            return count($this->files);
        }

        return 0;
    }
}
