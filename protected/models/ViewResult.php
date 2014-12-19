<?php

/**
 * This is the model class for table "view_result".
 *
 * The followings are the available columns in table 'view_result':
 * @property integer $id
 * @property integer $result_id
 * @property integer $contact_type_id
 *
 * The followings are the available model relations:
 * @property Result $result
 * @property ContactType $contactType
 */
class ViewResult extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'view_result';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('result_id, contact_type_id', 'required'),
			array('result_id, contact_type_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, result_id, contact_type_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'result' => array(self::BELONGS_TO, 'Result', 'result_id'),
			'contactType' => array(self::BELONGS_TO, 'ContactType', 'contact_type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'result_id' => 'Result',
			'contact_type_id' => 'Contact Type',
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
		$criteria->compare('result_id',$this->result_id);
		$criteria->compare('contact_type_id',$this->contact_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ViewResult the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    /*      */
    public static function checkBoxList($id)
    {
        $criteria=new CDbCriteria;
        $criteria->select='result_id';
        $criteria->condition='contact_type_id=:c_t_id';
        $criteria->params=array(':c_t_id'=>$id);
        $results = ViewResult::model()->findAll($criteria);
        foreach($results as $i)
            $ids[] = (int)$i->result_id;
        /*var_dump($results);
        exit();/**/
        return CHtml::listData(Result::model()->findAllByAttributes(array('id'=>$ids)), 'id', 'name');
    }
}
