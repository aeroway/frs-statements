<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\ReferenceBook;
use common\models\TgbOffset;

class TgbController extends Controller
{
    public function sendRequest($method, $params = [])
    {
        $token = Yii::$app->params['token'];
        $baseUrl = 'https://api.telegram.org/bot' . $token . '/';
        $arrContextOptions = [
            'ssl'  => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ]
        ];

        if (!empty($params)) {
            $url = $baseUrl . $method . '?' . http_build_query($params);
        } else {
            $url = $baseUrl . $method;
        }

        return json_decode(file_get_contents($url, false, stream_context_create($arrContextOptions)), JSON_OBJECT_AS_ARRAY);
    }

    private function weightWords($questions, $textMessage)
    {
        if (!empty($questions)) {
            $words = array();
            $countEntryWords = 0;
            $textMessageArray = explode(" ", $textMessage);

            if (count($textMessageArray) < 3) {
                return 0;
            }

            foreach ($textMessageArray as $text) {
                if (strlen($text) > 2) {
                    if (strlen($text) > 4) {
                        $words[] = substr($text, 0, -4);
                    }
                }
            }

            for ($i = 0; $i < count($questions); $i++) {
                foreach ($words as $word) {
                    $count = substr_count($this->mbStrToLower($questions[$i]["question"]), $word);

                    if ($count > 1) {
                        $count = 1;
                    }

                    $countEntryWords += $count;
                }

                $questions[$i]["weight"] = $countEntryWords;
                $countEntryWords = 0;
            }

            usort($questions, function($a ,$b) {
                return ($b['weight'] - $a['weight']);
            });
        
            return $questions;
        }

        return 0;
    }

    public function actionRun()
    {
        $tgbOffset = TgbOffset::findOne(1);
        $getMessage = $this->sendRequest('getUpdates', ['offset' => $tgbOffset->update_id + 1])["result"];
        if (empty($getMessage)) { exit; }
        for ($i = 0; $i < count($getMessage) ; $i++) {
            $questions = ReferenceBook::find()->select(["question", "answer"])->orderBy(["id" => SORT_ASC])->asArray()->all();
            $chatId = $getMessage[$i]["message"]["chat"]["id"];
            $textMessage = mb_strtolower($getMessage[$i]["message"]["text"]);
            $questions = $this->weightWords($questions, $textMessage);

            if ($textMessage == '/start') {
                continue;
            }

            if ($questions === 0) {
                $responseSendMessage = $this->sendRequest('sendMessage', ['chat_id' => $chatId, 'text' => 'Необходимо уточнить вопрос. Пожалуйста используйте больше слов.']);
            } else {
                $responseSendMessage = $this->sendRequest('sendMessage', ['chat_id' => $chatId, 'text' => $questions[0]["question"] . ' ' . $questions[0]["answer"]]);
            }
            
            $tgbOffset->update_id = $getMessage[$i]["update_id"];
            $tgbOffset->save();

        }
    }

    private function mbStrToLower($str)
    {
        return mb_strtolower($str);
    }
}
?>