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
                echo $time = ((microtime(true) - $start) / 60) . "\n";
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

                if (!empty($cells[5]) && $cells[5] != 'Номер внешней системы' && $cells[5]->getValue() && strpos($cells[3], "Other") === false) {
                    $newEntries[] = [$cells[3], $cells[5], empty($cells[19]) ? NULL : $cells[19]];
                }
            }
        }

        Yii::$app->db2->createCommand()->truncateTable('status_sys_temp')->execute();
        $modelStatus->batchInsert('status_sys_temp', ['req_num', 'ext_sys_num', 'status'], $newEntries);

        $selectDifference = $modelStatus->selectDifference();

        foreach ($selectDifference as $diff) {
            if ($diff['esn'] === NULL) {
                $difference[] = [$diff['req_num'], $diff['ext_sys_num'], empty($diff['status']) ? NULL : $diff['status']];
            }

            if ($diff['status'] != $diff['s1']) {
                $modelStatus->updateStatus($diff['status'], $diff['ext_sys_num']);
            }
        }

        $modelStatus->batchInsert('status_sys', ['req_num', 'ext_sys_num', 'status'], $difference);

        $reader->close();
    }
}
?>