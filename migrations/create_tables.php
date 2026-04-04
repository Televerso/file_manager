<?php

use yii\db\Migration;

class m260401_142047_create_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->getTableSchema('users') === null) {
            $this->createTable('users', 
            [
                'userID' => $this->primaryKey(),
                'user_name' => $this->string()->notNull(),
                'pass_hash' => $this->string()->notNull(),
                'auth_key' =>$this->string()->notNull(),
            ]);

            $this->createIndex(
                'idx_user_id_name', 
                'users', 
                ['userID','user_name']
            );
        }


        if ($this->db->getTableSchema('files') === null) {
            $this->createTable('files', [
                'fileID' => $this->primaryKey(),
                'file_true_name' => $this->string()->notNull(),
                'file_name' => $this->string()->notNull(),
                'file_path' => $this->string()->notNull(),
                'user_name' => $this->string(),
                'userID' => $this->integer(),
                'time_modify' => $this->dateTime(),
            ]);

            $this->addForeignKey(
                'user_id_name',
                'files',
                ['userID', 'user_name'],
                'users', 
                ['userID', 'user_name'], 
                'SET NULL'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('user_id_name', 'files');

        $this->dropIndex('idx_user_id_name', 'users');
        $this->dropIndex('user_id_name', 'files');

        $this->dropTable('files');
        $this->dropTable('users');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260401_142047_create_tables cannot be reverted.\n";

        return false;
    }
    */
}
