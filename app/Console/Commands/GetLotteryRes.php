<?php
namespace App\Console\Commands;

use App\Models\LotteryModel;
use App\Providers\Helper\Helper;
use App\Providers\HttpClient\HttpClient;
use App\Services\LotteryService;
use Illuminate\Console\Command;

/**
 * 自动获取彩票中奖信息存入数据库
 */
class GetLotteryRes extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'GetLotteryRes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动获取彩票中奖信息存入数据库';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $params['showapi_appid'] = env('SHOWAPI_APPID');
        $params['showapi_sign'] = env('SHOWAPI_SECRET');
        $params['code'] = implode('|', LotteryModel::$typeList);
        $result = HttpClient::doRequest('http://route.showapi.com/44-1', 'get', $params);
        $result = Helper::jsonDecode($result);
        if (!isset($result['showapi_res_body']['result'])) return;
        $lotteryResList = $result['showapi_res_body']['result'];
        foreach ($lotteryResList as $item) {
            $lotteryInfo = LotteryService::getLotteryInfoByType($item['code'], 'id,type_text');
            if (empty($lotteryInfo)) { // 无效彩票类型跳过
                continue;
            }
            $lotteryResInfo = LotteryService::getLotteryInfo($item['code'], $item['expect']);
            if ($lotteryResInfo) { // 存在则跳过
                continue;
            }
            $openCodeArr = explode(',', $item['openCode']);
            $openCodeSum = array_sum($openCodeArr);
            $oddEven = Helper::isOdd($openCodeSum) ? 'odd' : 'even';
            $bigSmall = $openCodeSum <= 10 ? 'small' : 'big';
            // 添加开彩结果
            $res = LotteryService::addLotteryResult($lotteryInfo['id'], $item['expect'], $item['time'], $item['openCode'],
                $openCodeSum,$oddEven, $bigSmall);
            $jsonData['info'] = 'expect='. $item['expect']. '|code=' . $item['code'].
                '|type_text='.$lotteryInfo['type_text'].$item['openCode'];
            if ($res) {
                $jsonData['success'] = true;
                echo Helper::jsonEncode($jsonData) . "\n";
            } else {
                $jsonData['success'] = false;
                echo Helper::jsonEncode($jsonData) . "\n";
            }
        }
}

}