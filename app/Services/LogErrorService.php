<?php
namespace App\Services;

class LogErrorService
{
    /**
     * 返回值标准格式
     *
     * @return array
     */
    public static function construct()
    {
        $jsonData['success'] = true;
        $jsonData['error_id'] = 0;
        $jsonData['error_code'] = '';
        $jsonData['error_msg'] = '';
        $jsonData['debug_msg'] = '';
        $jsonData['current_time'] = time();
        return $jsonData;
    }
}