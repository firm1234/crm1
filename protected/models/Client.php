<?php

/**
 * This is the model class for table "client".
 *
 * The followings are the available columns in table 'client':
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $phone2
 * @property string $phone3
 * @property string $email
 * @property integer $age
 * @property string $job
 * @property integer $user_id
 * @property integer $city_id
 *
 * The followings are the available model relations:
 * @property User $user
 * @property City $city
 * @property Contact[] $contacts
 */
class Client extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'client';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, phone',  'required'),
			array('age, user_id, city_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('phone, phone2, phone3', 'length', 'max'=>20),
			array('email, job', 'length', 'max'=>100),
            array('email','email'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('name, phone', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'city' => array(self::BELONGS_TO, 'City', 'city_id'),
			'contacts' => array(self::HAS_MANY, 'Contact', 'client_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
            'id' => 'ID',
            'name' => 'ФИО',
            'phone' => 'Телефон',
            'phone2' => 'Phone2',
            'phone3' => 'Phone3',
            'email' => 'Email',
            'age' => 'Возраст',
            'job' => 'Место учебы(работы)',
            'user_id' => 'Менеджер',
            'city_id' => 'Город',
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

		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone',$this->phone,true);
        $criteria->compare('phone2',$this->phone2,true);
        $criteria->compare('phone3',$this->phone3,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('age',$this->age);
        $criteria->compare('job',$this->job,true);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('city_id',$this->city_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Client the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    protected function beforeSave()
    {
        if(parent::beforeSave())
        {
            if($this->isNewRecord)
            {
                $this->user_id=Yii::app()->user->id;
                $this->city_id=Yii::app()->user->city;
            }
            return true;
        }
        else
            return false;
    }


    /*public function scopes()
    {
        return array(
            'phone'=>array(
                'condition'=>''
            ),
        );
    }/**/
    public function phone($phone)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition'=>'phone =:phone OR phone2 =:phone OR phone3 =:phone',
            'params'=>array(':phone'=>$phone),
        ));
        return $this;
    }
}
