<?php
namespace App\Models;


class LotteryModel extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lottery';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 彩票类型: 安徽快3
     *
     * @val
     */
    const TYPE_AHK3 = 'ahk3';

    /**
     * 彩票类型: 北京快3
     *
     * @val
     */
    const TYPE_BJK3 = 'bjk3';

    /**
     * 彩票类型: 福建快3
     *
     * @val
     */
    const TYPE_FJK3 = 'fjk3';

    /**
     * 彩票类型: 甘肃快3
     *
     * @val
     */
    const TYPE_GSK3 = 'gsk3';

    /**
     * 彩票类型: 广西快3
     *
     * @val
     */
    const TYPE_GXK3= 'gxk3';

    /**
     * 彩票类型: 贵州快3
     *
     * @val
     */
    const TYPE_GZK3= 'gzk3';

    /**
     * 彩票类型: 河北快3
     *
     * @val
     */
    const TYPE_HEBK3= 'hebk3';

    /**
     * 彩票类型: 湖北快3
     *
     * @val
     */
    const TYPE_HUBK3= 'hubk3';

    /**
     * 彩票类型: 吉林快3
     *
     * @val
     */
    const TYPE_JLK3= 'jlk3';

    /**
     * 彩票类型: 江苏快3
     *
     * @val
     */
    const TYPE_JSK3= 'jsk3';

    /**
     * 彩票类型: 江西快3
     *
     * @val
     */
    const TYPE_JXK3= 'jxk3';

    /**
     * 彩票类型: 内蒙古快3
     *
     * @val
     */
    const TYPE_NMGK3= 'nmgk3';

    /**
     * 彩票类型: 上海快3
     *
     * @val
     */
    const TYPE_SHK3= 'shk3';

    /**
     * 彩票类型列表
     *
     * @val array
     */
    public static $typeList = [
        self::TYPE_AHK3, // 安徽快3
        self::TYPE_BJK3, // 北京快3
//        self::TYPE_FJK3, // 福建快3
        self::TYPE_GSK3, // 甘肃快3
        self::TYPE_GXK3, // 广西快3
        self::TYPE_GZK3, // 贵州快3
        self::TYPE_HEBK3, // 河北快3
        self::TYPE_HUBK3, // 湖北快3
        self::TYPE_JLK3,// 吉林快3
        self::TYPE_JSK3, // 江苏快3
//        self::TYPE_JXK3, // 江西快3
//        self::TYPE_NMGK3, // 内蒙古快3
        self::TYPE_SHK3, // 上海快3
    ];
}