<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    /**
     * 是否：是
     */
    const IS_TRUE = 1;

    /**
     * 是否：否
     */
    const IS_FALSE = 0;

    /**
     * 布尔转整数
     *
     * @param $value
     * @return int
     */
    public static function bool2int($value)
    {
        return $value ? 1 : 0;
    }

    /**
     * 封装自己的数据库自增，使用update方法更新。
     *
     * @date 2015-10-27 上午10:42:57
     * @author pengyouchuan<cq.peng@qq.com>
     * @param array $updates 更新数组
     * @param string $field 更新字段
     * @param int $amount 数量，默认1
     * @return array
     */
    public static function modelIncrement(&$updates, $field, $amount = 1)
    {
        $updates[$field] = DB::raw("`$field` + $amount");
        return $updates;
    }

    /**
     * 封装自己的数据库自减，使用update方法更新。
     *
     * @date 2015-10-27 上午10:42:57
     *
     * @author pengyouchuan<cq.peng@qq.com>
     * @param array $updates 更新数组
     * @param string $field 更新字段
     * @param int $amount 数量，默认1
     * @return array
     */
    public static function modelDecrement(&$updates, $field, $amount = 1)
    {
        $updates[$field] = DB::raw("`$field` - $amount");
        return $updates;
    }

    /**
     * 封装插入函数
     *
     * @param array 插入属性
     * @return id 自增id
     */
    public static function dbInsert(array $attributes)
    {
        $model = new static();
        $model->fillable(array_keys($attributes));
        $model->fill($attributes);
        if ($model->save()) {
            return $model->getKey();
        } else {
            return 0;
        }
    }

    /**
     * 封装插入函数
     *
     * @param array 插入属性
     * @return boolean
     */
    public static function add(array $attributes)
    {
        $model = new static();
        $model->fillable(array_keys($attributes));
        $model->fill($attributes);
        return $model->save();
    }

    /**
     * 转换created_at为时间戳
     *
     * @param string $time 时间
     * @return intger
     */
    public function getCreatedAtAttribute($time)
    {
        return strtotime($time);
    }

    /**
     * 转换updated_at为时间戳
     *
     * @param string $time 时间
     * @return intger
     */
    public function getUpdatedAtAttribute($time)
    {
        return strtotime($time);
    }
}