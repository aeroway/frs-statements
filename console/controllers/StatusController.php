<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use frontend\models\StatusSys;
use console\models\Status;

class StatusController extends Controller
{
    public function actionUpload() {
        $zip = new \ZipArchive;
        $pathDir = getcwd() . '/console/uploads/';
        $pathZip = glob($pathDir . "*.zip");
        $resZip = $zip->open($pathZip[0]);

        if ($resZip === TRUE) {
            $zip->extractTo($pathDir);
            $zip->close();
            if (empty($pathZip[1])) {
                unlink($pathZip[0]);
            }
            $pathExcel = glob($pathDir . "*.xlsx");
            if (empty($pathExcel[1])) {
                $start = microtime(true);
                $this->importPkpvdXlsx($pathExcel[0]);
                echo $time = (microtime(true) - $start) / 60;
                unlink($pathExcel[0]);
            }
        }
    }

    private function importPkpvdXlsx($pathExcel) {
        $reader = ReaderEntityFactory::createReaderFromFile($pathExcel);
        $reader->open($pathExcel);
        $modelStatus = new Status();

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();

                if (!empty($cells[5])
                    && $cells[5] != 'Номер внешней системы'
                    && $cells[5]->getValue()
                    && strpos($cells[3], "Other") === false
                    && $modelStatus->checkActualCompletionDate($cells[23])) {

                    $select = Yii::$app->db2->createCommand("SELECT req_num, ext_sys_num, status FROM status_sys WHERE ext_sys_num = '$cells[5]'")->queryOne();

                    if (empty($select['ext_sys_num'])) {
                        Yii::$app->db2->createCommand()->insert('status_sys', [
                            'req_num' => $cells[3],
                            'ext_sys_num' => $cells[5],
                            'status' => empty($cells[19]) ? NULL : $cells[19],
                        ])->execute();
                    } elseif ($select['ext_sys_num'] == $cells[5] && $select['status'] != $cells[19]) {
                        Yii::$app->db2->createCommand()->update(
                            'status_sys', [
                                'status' => empty($cells[19]) ? NULL : $cells[19],
                                'date_update' => Yii::$app->formatter->asDate('now', 'php:Y-m-d'),
                            ],
                            ['ext_sys_num' => $cells[5]],
                        )->execute();
                    }
                }
            }
        }

        $reader->close();
    }
}
?>