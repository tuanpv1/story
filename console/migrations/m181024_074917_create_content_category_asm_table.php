<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content_category_asm`.
 */
class m181024_074917_create_content_category_asm_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('content_category_asm', [
            'id' => $this->primaryKey(),
            'content_id' => $this->integer(),
            'category_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('content_category_asm');
    }
}
