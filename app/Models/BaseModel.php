<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static find(int|string $id)
 * @method static findOrFail(int $id)
 * @method static create(array $array)
 * @method static where(string $column, string $operator = null, string $value = null)
 * @method get
 * @method toArray
 * @method delete
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
