<?php

namespace console\models;

use Yii;
use yii\base\Model;

class Status extends Model
{
    public function batchInsert($table, $columns, $newEntries) {
        if (!empty($newEntries)) {
            $newEntries = array_chunk($newEntries, 50);

            for ($i = 0; $i < count($newEntries); $i++) { 
                Yii::$app->db2->createCommand()->batchInsert($table, $columns, $newEntries[$i])->execute();
            }
        }
    }

    public function updateStatus($status, $reqNum) {
        Yii::$app->db2->createCommand()->update(
            'status_sys', [
                'status' => empty($status) ? NULL : $status,
                'date_update' => Yii::$app->formatter->asDate('now', 'php:Y-m-d'),
            ],
            ['req_num' => $reqNum],
        )->execute();
    }

    public function selectDifference() {
        return (new \yii\db\Query)
            ->select(['ss.status AS s1', 'sst.status', 'sst.req_num', 'sst.ext_sys_num'])
            ->from('status_sys ss')
            ->join('FULL OUTER JOIN', 'status_sys_temp sst', 'sst.req_num = ss.req_num')
            ->where(
                ['or',
                    ['<>', 'sst.status', 'ss.status'],
                    ['IS', 'ss.req_num', NULL],
                ],
            )
            ->all(Yii::$app->db2);
    }
}