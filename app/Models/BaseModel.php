<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static findOrFail(array $array)
 * @method toArray:array
 */
class BaseModel extends Model
{
    public function save(array $options = [])
    {
        try {
            if($this->timestamps) {
                $user_id = optional(app()->make('request')->user())->id;

                if(!isset($this->id) || empty ($this->id)) {
                    $this->created_by = $user_id;
                }

                $this->updated_by = $user_id;
            }
        } catch (\Throwable $e) {}

        return parent::save($options);
    }
}
