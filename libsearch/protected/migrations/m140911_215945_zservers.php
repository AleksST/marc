<?php

class m140911_215945_zservers extends CDbMigration
{
	public function up()
	{
        $this->createTable('zservers', [
            'id' => 'pk',
            'name' => 'varchar(1000)',
            'library' => 'varchar(1000)',
            'host' => 'varchar(100)',
            'port' => 'integer',
            'db' => 'varchar(50)',
            'format' => 'varchar(50)',
	        'encode' => 'varchar(20)',
	        'is_active' => 'boolean',
        ]);


	}

	public function down()
	{
        $this->dropTable('zservers');
	}
}
