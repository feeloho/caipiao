<?php
namespace App\Services;

use App\Models\LotteryModel;
use App\Models\LotteryResultModel;
use App\Providers\Helper\Helper;
use Carbon\Carbon;

class LotteryService
{
    /**
     * 根据类型获取彩票信息
     *
     * @param string $type 彩票类型
     * @param string $query 查询字段, 默认全部 *
     * @return array
     */
    public static function getLotteryInfoByType($type, $query = '*')
    {
        $result = LotteryModel::selectRaw($query)->whereRaw(' type = ? ', [$type])->first();
        return $result ? $result->toArray() : null;
    }

    /**
     * 检查彩票类型是否存在
     *
     * @param string $type 彩票类型
     * @return boolean
     */
    public static function checkLotteryType($type)
    {
        return LotteryModel::whereRaw(' type = ? ', [$type])->exists();
    }

    /**
     * 获取开奖信息
     *
     * @param string $type 彩票类型
     * @param string $expect 期号
     * @return array
     */
    public static function getLotteryInfo($type, $expect)
    {
        $result = LotteryResultModel::selectRaw('cp_lottery.type,cp_lottery.type_text,cp_lottery_result.lottery_id,
        cp_lottery_result.expect,cp_lottery_result.lottery_time,cp_lottery_result.open_code,cp_lottery_result.sum,
        cp_lottery_result.odd_even,cp_lottery_result.big_small')
            ->whereRaw(' cp_lottery_result.expect = ? and cp_lottery.type = ? ', [$expect, $type])
            ->join('lottery', 'lottery.id', '=', 'lottery_result.lottery_id')
            ->first();
        return $result ? $result->toArray() : null;
    }

    /**
     * 添加开彩结果
     *
     * @param integer $lotteryId 彩票类型ID
     * @param string $expect 开奖期号
     * @param string $lotteryTime 开奖时间
     * @param string $openCode 开奖号码
     * @param integer $sum 数值总和
     * @param string $oddEven 奇数/偶数
     * @param string $bigSmall 大/小
     * @return int
     */
    public static function addLotteryResult($lotteryId, $expect, $lotteryTime, $openCode,
                                            $sum, $oddEven, $bigSmall)
    {
        $attributes['lottery_id'] = $lotteryId;
        $attributes['expect'] = $expect;
        $attributes['lottery_time'] = $lotteryTime;
        $attributes['open_code'] = $openCode;
        $attributes['sum'] = $sum;
        $attributes['odd_even'] = $oddEven;
        $attributes['big_small'] = $bigSmall;
        return LotteryResultModel::dbInsert($attributes);
    }

    /**
     * 根据彩票ID获取开奖信息列表
     *
     * @param string $lotteryId 彩票ID
     * @param string $pn 页码
     * @param string $ps 页面大小
     * @return array
     */
    public static function getLotteryResultListByLotteryId($lotteryId, $pn, $ps)
    {
        return LotteryResultModel::selectRaw('lottery_id,expect,lottery_time,open_code,sum,odd_even,big_small,created_at')
            ->whereRaw(' lottery_id = ? and lottery_time >= ? ', [$lotteryId, Carbon::today()])
            ->orderByRaw('expect desc')
            ->skip(Helper::getStartLimit($pn, $ps))
            ->take($ps)
            ->get()
            ->toArray();
    }

    /**
     * 获取彩票类型列表
     *
     * @return array
     */
    public static function getLotteryList()
    {
        return LotteryModel::selectRaw('id,type,type_text')
            ->get()
            ->toArray();
    }

    /**
     * 预测下期奇数/偶数
     *
     * @param array $oddEvenArr 奇偶数组
     * @return string odd,even
     */
    public static function predictOddEven(array $oddEvenArr)
    {
        $oldArr = [];
        foreach ($oddEvenArr as $item) {
            $keys = array_keys($oldArr);
            $maxKey = $keys ? max($keys) : 0;
            if (isset($oldArr[$maxKey]['odd_even']) && $oldArr[$maxKey]['odd_even'] == $item) {
                $oldArr[$maxKey]['frequency'] = $oldArr[$maxKey]['frequency'] + 1;
            } else {
                $oldArr[] = ['odd_even' => $item, 'frequency' => 1];
            }
        }
        // 贝叶斯算法
        $allOdd = 1;
        $allEven = 1;
        foreach ($oldArr as $item) {
            if ($item['odd_even'] == LotteryResultModel::ODD_EVEN_ODD) { // 奇数
                $allOdd = $allOdd * $item['frequency'];
            } else { // 偶数
                $allEven = $allEven * $item['frequency'];
            }
        }
        $allOddEvenSum = $allOdd + $allEven;

        // 取最近相同信息做比较
        $latelyOdd = 1;
        $latelyEven = 1;
        if (isset($oldArr[0]['odd_even'])) {
            if ($oldArr[0]['odd_even'] == LotteryResultModel::ODD_EVEN_ODD) { // 奇数
                $latelyOdd = $latelyOdd * $oldArr[0]['frequency'] * (isset($oldArr[1]['frequency'])
                    ? $oldArr[1]['frequency'] : 1) * (isset($oldArr[2]['frequency']) ? $oldArr[2]['frequency'] : 1);
            } else {
                $latelyEven = $latelyEven * $oldArr[0]['frequency'] * (isset($oldArr[1]['frequency'])
                    ? $oldArr[1]['frequency'] : 1) * (isset($oldArr[2]) ? $oldArr[2]['frequency'] : 1);
            }
        }
        $lateOddEvenSum = $latelyOdd + $latelyEven;

        if (($allOdd / $allOddEvenSum) > ($latelyOdd / $lateOddEvenSum) ||
        ($allEven / $allOddEvenSum) > ($latelyEven / $lateOddEvenSum)
        ) {
            // 取反
            return ['odd_predict' =>  1 - (($allOdd / $allOddEvenSum) / 2),
                'even_predict' => 1 - (($allEven / $allOddEvenSum) / 2)];
        } else {
            return ['odd_predict' => ($latelyOdd / $lateOddEvenSum) / 2,
                'even_predict' => ($latelyEven / $lateOddEvenSum) / 2];
        }

    }

    /**
     * 预测下期大/小
     *
     * @param array $bigSmall 大小数组
     * @return string big,small
     */
    public static function predictBigSmall(array $bigSmall)
    {
        $oldArr = [];
        foreach ($bigSmall as $item) {
            $keys = array_keys($oldArr);
            $maxKey = $keys ? max($keys) : 0;
            if (isset($oldArr[$maxKey]['big_small']) && $oldArr[$maxKey]['big_small'] == $item) {
                $oldArr[$maxKey]['frequency'] = $oldArr[$maxKey]['frequency'] + 1;
            } else {
                $oldArr[] = ['big_small' => $item, 'frequency' => 1];
            }
        }
        // 贝叶斯算法
        $allBig = 1;
        $allSmall = 1;
        foreach ($oldArr as $item) {
            if ($item['big_small'] == LotteryResultModel::BIG_SMALL_BIG) { // 大
                $allBig = $allBig * $item['frequency'];
            } else { // 小
                $allSmall = $allSmall * $item['frequency'];
            }
        }
        $allBigSmallSum = $allBig + $allSmall;

        // 取最近相同信息做比较
        $latelyBig = 1;
        $latelySmall = 1;
        if (isset($oldArr[0]['odd_even'])) {
            if ($oldArr[0]['odd_even'] == LotteryResultModel::BIG_SMALL_BIG) { // 大
                $latelyBig = $latelyBig * $oldArr[0]['frequency'] * (isset($oldArr[1]['frequency'])
                    ? $oldArr[1]['frequency'] : 1) * (isset($oldArr[2]['frequency']) ? $oldArr[2]['frequency'] : 1);
            } else {
                $latelySmall = $latelySmall * $oldArr[0]['frequency'] * (isset($oldArr[1]['frequency'])
                    ? $oldArr[1]['frequency'] : 1) * (isset($oldArr[2]['frequency']) ? $oldArr[2]['frequency'] : 1);
            }
        }
        $latelyBigSmallSum = $latelyBig + $latelySmall;

        if ( ($allBig / $allBigSmallSum) > ($latelyBig / $latelyBigSmallSum) ||
            ($allSmall / $allBigSmallSum) >  ($latelySmall / $latelyBigSmallSum)
        ) {
            // 取反
            return ['big_predict' => (($allBig / $allBigSmallSum) / 2),
                'small_predict' =>  (($allSmall / $allBigSmallSum) / 2)];
        } else {
            return ['big_predict' => 1- ($latelyBig / $latelyBigSmallSum) / 2,
                'small_predict' =>  1 - ($latelySmall / $latelyBigSmallSum)/ 2];
        }
    }
}