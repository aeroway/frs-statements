<?php

namespace console\models;

use yii\base\Model;

class Status extends Model
{
    // public $imageFile;

    public function rules()
    {
        // return [
        //     [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        // ];
    }
    
    // public function upload()
    // {
    //     if ($this->validate()) {
    //         $this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    public function checkActualCompletionDate($processingCompletionDate) {
        if (empty($processingCompletionDate)) {
            return true;
        }

        if (!empty($processingCompletionDate) && $processingCompletionDate >= date('d.m.y', strtotime("-30 days"))) {
            return true;
        }

        return false;
    }
}