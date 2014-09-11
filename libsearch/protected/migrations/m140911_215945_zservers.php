<?php

class m140911_215945_zservers extends CDbMigration
{
	public function up()
	{
        $this->createTable('zservers', [
            'id' => 'pk',
            'name' => 'nvarchar(1000)',
            'library' => 'nvarchar(1000)',
            'host' => 'nvarchar(100)',
            'port' => 'integer',
            'db' => 'nvarchar(50)',
            'is_rusmarc' => 'boolean',
        ]);


	}

	public function down()
	{
        $this->dropTable('zservers');
	}
}