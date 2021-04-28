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
        $searchHead = false;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();

                if (!empty($cells[3]) && $cells[3] == 'Номер обращения' && $cells[5] == 'Номер внешней системы' && $cells[23] == 'Статус') {
                    $searchHead = true;
                }

                if (!empty($cells[3]) && $cells[3] != 'Номер обращения' && $cells[3]->getValue() && strpos($cells[3], "Other") === false && !empty($cells[23])) {
                    $newEntries[] = [trim($cells[3]->getValue()), trim($cells[5]->getValue()), empty($cells[23]->getValue()) ? NULL : trim($cells[23]->getValue())];
                }
            }
        }

        if (!$searchHead) {
            die('Head false');
        }

        Yii::$app->db2->createCommand()->truncateTable('status_sys_temp')->execute();
        $modelStatus->batchInsert('status_sys_temp', ['req_num', 'ext_sys_num', 'status'], $newEntries);
        $selectDifference = $modelStatus->selectDifference();

        foreach ($selectDifference as $diff) {
            if ($diff['s1'] === NULL) {
                $difference[] = [$diff['req_num'], $diff['ext_sys_num'], empty($diff['status']) ? NULL : $diff['status']];
            }

            if ($diff['s1'] !== NULL && $diff['status'] != $diff['s1']) {
                $modelStatus->updateStatus($diff['status'], $diff['req_num']);
            }
        }

        $modelStatus->batchInsert('status_sys', ['req_num', 'ext_sys_num', 'status'], $difference);

        $reader->close();
    }

    public function actionFileLoad() {
        $email = $this->emailRead();
        $content = file_get_contents($email["url"]);
        file_put_contents(\Yii::$app->basePath . '/uploads/' . $email["subject"] . '.zip', $content);
        $this->actionUpload();
        $this->actionEmailDelete();
    }

    public function actionEmailDelete() {
        $email = $this->emailRead();
        imap_delete($email["imap"], $email["num"]);
        imap_expunge($email["imap"]);
        imap_close($email["imap"]);
    }

    public function actionClearFolderUploads() {
        $files = glob(\Yii::$app->basePath . '/uploads/*');

        foreach($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function actionClear() {
        sleep(3600);
        $this->actionClearFolderUploads();
        $this->actionEmailDelete();
    }

    private function emailRead() {
        $imap = imap_open(Yii::$app->params['host'], Yii::$app->params['email'], Yii::$app->params['password']);
        $mails_id = imap_search($imap, 'UNSEEN');

        foreach ($mails_id as $num) {
            $body = imap_body($imap, $num);
            $body = quoted_printable_decode($body);
            $posUrlStart = stripos($body, 'https://query');
            $posSubjectStart = stripos($body, 'EGRN_VP_INCCA');

            if ($posUrlStart !== false && $posSubjectStart !== false) {
                $posUrlStop = (stripos($body, '"', $posUrlStart)) - $posUrlStart;
                $posSubjectStop = (stripos($body, ' ', $posSubjectStart)) - $posSubjectStart;
                $url = substr($body, $posUrlStart, $posUrlStop);
                $subject = substr($body, $posSubjectStart, $posSubjectStop);

                return ["imap" => $imap, "num" => $num, "url" => $url, "subject" => $subject];
            }
        }
    }
}
?>