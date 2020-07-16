<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-14 19:58:10
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 10:15:00
# Descripttion: 操作日记
#============================================================================= */
namespace app\http\middleware;

class ActionLog
{
    public function handle($request, \Closure $next, $logic)
    {
        
        $response = $next($request);

        //判断是否需要写入操作日志
        if(empty($request->act_log) === false) {
            $logList = [];
            $field = $logic::getField();
            
            if(is_array($request->act_log)) {
                foreach($request->act_log as $value) {
                    if(is_array($value)) {
                        $logList[] = $value;
                    } else {
                        $logList[] = [
                            $field => $value
                        ];
                    }
                }
            } else {
                $logList[] = [
                    $field => $request->act_log
                ];
            }

            $model = $logic::getModel();
            $adminLog = new $model;
            $adminLog->saveAll($logList);
        }

        return $response;
    }
}
