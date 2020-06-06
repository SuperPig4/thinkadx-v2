<?php
/**
 * 操作日记
 */
namespace app\http\middleware;

class ActionLog
{
    public function handle($request, \Closure $next, $model)
    {
        $response = $next($request);
        //判断是否需要写入操作日志
        if(empty($request->act_log) === false && $model) {
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

            $model = '\\'.$model;
            $adminLog = new $model();
            $adminLog->saveAll($logList);
        }
        return $response;
    }
}
