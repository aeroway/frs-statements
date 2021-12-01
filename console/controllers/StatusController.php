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
                    $newEntries[] = [trim($cells[3]->getValue()), trim($cells[5]->getValue()), empty($cells[23]->getValue()) ? 'В работе' : trim($cells[23]->getValue())];
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
        $contextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $email = $this->emailRead();

        $email["url"] = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $email["url"]);
        $email["url"] = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $email["url"]);

        $content = file_get_contents($email["url"], false, stream_context_create($contextOptions));
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
        $inbox = imap_open(Yii::$app->params['host'], Yii::$app->params['email'], Yii::$app->params['password']);
        $emails = imap_search($inbox, 'UNSEEN');

        if ($emails) {
            $output = '';
            rsort($emails);

            foreach ($emails as $email_number) {
                $overview = imap_fetch_overview($inbox,$email_number, 0);
                $structure = imap_fetchstructure($inbox, $email_number);
                $message = imap_body($inbox, $email_number);

                $posMessageStart = stripos($message, 'base64') + 10;
                $posMessageStop = (stripos($message, '=', $posMessageStart)) - ($posMessageStart - 1);
                $message = base64_decode(substr($message, $posMessageStart, $posMessageStop));


                $posUrlStart = stripos($message, 'https://query');
                $posSubjectStart = stripos($message, 'EGRN_VP_INCCA');

                if ($posUrlStart !== false && $posSubjectStart !== false) {
                    $posUrlStop = (stripos($message, '>', $posUrlStart)) - $posUrlStart;
                    $posSubjectStop = (stripos($message, ' ', $posSubjectStart)) - $posSubjectStart;
                    $url = substr($message, $posUrlStart, $posUrlStop);
                    $subject = substr($message, $posSubjectStart, $posSubjectStop);

                    return ["imap" => $inbox, "num" => $email_number, "url" => $url, "subject" => $subject];
                }
            }
        }
    }
}
?>