<?php
namespace App\Models;


class LotteryResultModel extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lottery_result';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 开奖奇/偶数: 奇数
     *
     * @val
     */
    const ODD_EVEN_ODD = 'odd';

    /**
     * 开奖奇/偶数: 偶数
     *
     * @val
     */
    const ODD_EVEN_EVEN = 'even';

    /**
     * 开奖大/小: 大
     *
     * @val
     */
    const BIG_SMALL_BIG = 'big';

    /**
     * 开奖大/小: 小
     *
     * @val
     */
    const BIG_SMALL_SMALL = 'small';
}