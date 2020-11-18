<?php

namespace frontend\models;

use Yii;
use yii\base\Model;


class InsertHelper extends Model
{

    /**
     * Create Query INSERT ... ON DUPLICATE KEY UPDATE ...
     *
     * @param array $dataInsert
     * @param null $columns
     *
     * @return bool
     */
    public function insertUpdate($tableName, $columns, $dataInsert, $uniqueColumns = 'ref_num')
    {
        if (!$dataInsert) {
            return false;
        }

        $onDuplicateKeyValues = [];

        foreach ($columns as $itemColumn) {
            $column = Yii::$app->db->getSchema()->quoteColumnName($itemColumn);
            $onDuplicateKeyValues[] = $column . ' = excluded.' . $column;
        }

        $sql = Yii::$app->db->queryBuilder->batchInsert($tableName, $columns, $dataInsert);
        $sql .= ' ON CONFLICT (' . $uniqueColumns . ') DO UPDATE SET' . implode(', ', $onDuplicateKeyValues);
        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * Create Query INSERT IGNORE
     *
     * @param array $dataInsert
     * @param null $columns
     *
     * @return bool
     */
    public function insertIgnore($tableName, $columns, $dataInsert)
    {
        if (!$dataInsert) {
            return false;
        }

        $sql = Yii::$app->db->queryBuilder->batchInsert($tableName, $columns, $dataInsert);
        $sql = str_replace('INSERT INTO', 'INSERT IGNORE', $sql);
        $db->createCommand($sql)->execute();
    }

}