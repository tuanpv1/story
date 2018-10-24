<?php

use yii\db\Migration;

/**
 * Handles the creation of table `content`.
 */
class m181014_052344_create_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('content', [
            'id' => $this->primaryKey(),
            'display_name' => $this->string(),
            'ascii_name' => $this->string(),
            'image' => $this->string(),
            'author' => $this->string(),
            'short_description' => $this->string(2000),
            'description' => $this->text(),
            'total_like' => $this->integer(),
            'total_view' => $this->integer(),
            'approved_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'tags' => $this->string(500),
            'created_user_id' => $this->integer(),
            'is_series' => $this->integer(3),
            'status' => $this->integer(3),
            'episode_order' => $this->integer(),
            'total_episode' => $this->integer(),
            'parent_id' => $this->integer(),
            'admin_note' => $this->string(4000),
            'country' => $this->string(),
            'rating' => $this->integer(),
            'rating_count' => $this->integer(),
            'total_dislike' => $this->integer(),
            'total_favorite' => $this->integer(),
            'type' => $this->integer(3),
            'honor' => $this->integer(3),
            'code' => $this->string(20),
            'language' => $this->string(10),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('content');
    }
}
