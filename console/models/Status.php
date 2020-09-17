<?php

namespace console\models;

use Yii;
use yii\base\Model;

class Status extends Model
{
    public function batchInsert($table, $columns, $newEntries) {
        if (!empty($newEntries)) {
            Yii::$app->db2->createCommand()->batchInsert($table, $columns, $newEntries)->execute();
        }
    }

    public function updateStatus($status, $extSysNum) {
        Yii::$app->db2->createCommand()->update(
            'status_sys', [
                'status' => empty($status) ? NULL : $status,
                'date_update' => Yii::$app->formatter->asDate('now', 'php:Y-m-d'),
            ],
            ['ext_sys_num' => $extSysNum],
        )->execute();
    }

    public function selectDifference() {
        return (new \yii\db\Query)
            ->select(['ss.ext_sys_num AS esn', 'ss.status AS s1', 'sst.req_num', 'sst.ext_sys_num', 'sst.status'])  
            ->from('status_sys ss')
            ->join('FULL OUTER JOIN', 'status_sys_temp sst', 'sst.ext_sys_num = ss.ext_sys_num')
            ->where(
                ['or',
                    ['<>', 'sst.status', 'ss.status'],
                    ['IS', 'ss.ext_sys_num', NULL],
                ],
            )
            ->all(Yii::$app->db2);
    }
}