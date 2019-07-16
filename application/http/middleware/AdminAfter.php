<?php
namespace app\http\middleware;
use app\admin\model\AdminLog;

class AdminAfter
{
    public function handle($request, \Closure $next) {
        $response = $next($request);
        //判断是否需要写入操作日志

        if(!empty($request->act_log)) {
            $logList = [];
            if(is_array($request->act_log)) {
                foreach($request->act_log as $value) {
                    $logList[] = [
                        'des' => $value
                    ];
                }
            } else {
                $logList[] = [
                    'des' => $request->act_log
                ];
            }

            $adminLog = new AdminLog();
            $adminLog->saveAll($logList);
        }
        return $response;
    }
}
