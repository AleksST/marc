<?php

/**
 * This is the model class for table "zservers".
 *
 * The followings are the available columns in table 'zservers':
 * @property integer $id
 * @property string $name
 * @property string $library
 * @property string $host
 * @property integer $port
 * @property string $db
 * @property integer $is_rusmarc
 */
class Zserver extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'zservers';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('port, is_rusmarc', 'numerical', 'integerOnly'=>true),
            array('name, library', 'length', 'max'=>1000),
            array('host', 'length', 'max'=>100),
            array('db', 'length', 'max'=>50),
            // @todo Please remove those attributes that should not be searched.
            array('id, name, library, host, port, db, is_rusmarc', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'library' => 'Library',
            'host' => 'Host',
            'port' => 'Port',
            'db' => 'Db',
            'is_rusmarc' => 'Is Rusmarc',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('library',$this->library,true);
        $criteria->compare('host',$this->host,true);
        $criteria->compare('port',$this->port);
        $criteria->compare('db',$this->db,true);
        $criteria->compare('is_rusmarc',$this->is_rusmarc);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Zservers the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}