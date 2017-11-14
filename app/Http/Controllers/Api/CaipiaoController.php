<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LogErrorService;
use App\Services\LotteryService;

class CaipiaoController extends Controller
{
    /**
     * 获取推荐快3彩票数据
     */
    public function lotteryList()
    {
        $data = LogErrorService::construct();

        $data['list'] = [];
        $lotteryList = LotteryService::getLotteryList();
        foreach ($lotteryList as $lottery) {
            $lotteryResultList = LotteryService::getLotteryResultListByLotteryId($lottery['id'], 1, 25);
            $resData['type'] = $lottery['type'];
            $resData['type_text'] = $lottery['type_text'];
            $resData['expect'] = isset($lotteryResultList[0]['expect']) ? $lotteryResultList[0]['expect'] + 1 : '';
            $resData['odd_even'] = LotteryService::predictOddEven(array_column($lotteryResultList,'odd_even'));
            $resData['big_small'] = LotteryService::predictBigSmall(array_column($lotteryResultList,'big_small'));
            $data['list'][] = $resData;
        }
        // 推荐奇数/偶数
        $data['recommend_odd_even'] = [];
        // 推荐大/小
        $data['recommend_big_small'] = [];
        foreach ($data['list'] as $item) {
            $oddEven = $item['odd_even']['odd_predict'] >= $item['odd_even']['even_predict'] ? '单数' : '双数';
            $bigSmall = $item['big_small']['big_predict'] >= $item['big_small']['small_predict'] ? '大' : '小';
            if (!isset($data['recommend_odd_even']['odd_even'])) {
                // 推荐单数/双数
                $data['recommend_odd_even'] = ['type_text' => $item['type_text'], 'expect' => $item['expect'], 'odd_even' => $oddEven,
                    'predict' => $oddEven == '单数' ? $item['odd_even']['odd_predict'] : $item['odd_even']['even_predict']];
                // 推荐大/小
                $data['recommend_big_small'] = ['type_text' => $item['type_text'], 'expect' => $item['expect'], 'big_small' => $bigSmall,
                    'predict' => $bigSmall == '大' ? $item['big_small']['big_predict'] : $item['big_small']['small_predict']];
            } else {
                // 推荐单数/双数
                if ($item['odd_even']['odd_predict'] > $data['recommend_odd_even']['predict']) {
                    $data['recommend_odd_even'] = ['type_text' => $item['type_text'], 'expect' => $item['expect'], 'odd_even' => $oddEven,
                        'predict' => $oddEven == '单数' ? $item['odd_even']['odd_predict'] : $item['odd_even']['even_predict']];
                } elseif ($item['odd_even']['even_predict'] > $data['recommend_odd_even']['predict']) {
                    $data['recommend_odd_even'] = ['type_text' => $item['type_text'], 'expect' => $item['expect'], 'odd_even' => $oddEven,
                        'predict' => $oddEven == '单数' ? $item['odd_even']['odd_predict'] : $item['odd_even']['even_predict']];
                }
                // 推荐大/小
                if ($item['big_small']['big_predict'] > $data['recommend_big_small']['predict']) {
                    $data['recommend_big_small'] = ['type_text' => $item['type_text'], 'expect' => $item['expect'], 'big_small' => $bigSmall,
                        'predict' => $bigSmall == '大' ? $item['big_small']['big_predict'] : $item['big_small']['small_predict']];
                } elseif ($item['big_small']['small_predict'] > $data['recommend_big_small']['predict']) {
                    $data['recommend_big_small'] = ['type_text' => $item['type_text'], 'expect' => $item['expect'], 'big_small' => $bigSmall,
                        'predict' => $bigSmall == '大' ? $item['big_small']['big_predict'] : $item['big_small']['small_predict']];
                }
            }
        }
        unset($data['list']);
        return response()->json($data);
    }
}