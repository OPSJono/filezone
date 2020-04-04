<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int folder_id
 * @property string name
 * @property string description
 * @property string extension
 * @property string type
 * @property int size
 * @property string storage_method
 * @property string storage_region
 * @property string storage_path
 * @property string file_hash
 *
 * Class File
 * @package App\Models
 */
class File extends BaseModel
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
        'name',
        'description',
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
