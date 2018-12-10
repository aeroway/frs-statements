<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m181206_071043_change_auth_assignment_user_id
 */
class m181206_071043_change_auth_assignment_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%auth_assignment}}', 'user_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('fk_auth_assignment_user_id', '{{%auth_assignment}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_auth_assignment_user_id', '{{%auth_assignment}}');
        $this->alterColumn('{{%auth_assignment}}', 'user_id', Schema::TYPE_STRING . '(64)');
    }
}
