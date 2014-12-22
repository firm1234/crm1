<?php

/**
 * This is the model class for table "contact".
 *
 * The followings are the available columns in table 'contact':
 * @property integer $id
 * @property integer $client_id
 * @property string $datetime
 * @property integer $contact_type_id
 * @property integer $contract_id
 * @property string $comment
 * @property string $recall
 * @property integer $event_id
 *
 * The followings are the available model relations:
 * @property Client $client
 * @property ContactType $contactType
 * @property Contract $contract
 * @property Event $event
 * @property Purpose[] $Purposes
 * @property Result[] $Results
 * @property StudyForm[] $StudyForms
 */
class Contact extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'contact';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, contact_type_id, contract_id, event_id', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>255),
			array('recall', 'safe'),
            array('studyFormArray', 'safe'),
            array('purposeArray', 'safe'),
            array('resultArray', 'safe'),
            //array('client', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
            // Необходимо для вкладки Interview
            array('datetime, comment, client.phone,
			        client.name,
			        client.email,
			        client.age,
			        client.job,
			        ', 'safe', 'on'=>'search'),
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
			'client' => array(self::BELONGS_TO, 'Client', 'client_id'),
			'contactType' => array(self::BELONGS_TO, 'ContactType', 'contact_type_id'),
			'contract' => array(self::BELONGS_TO, 'Contract', 'contract_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'purposes' => array(self::MANY_MANY, 'Purpose', 'contact_purpose(contact_id, purpose_id)'),
            'results' => array(self::MANY_MANY, 'Result', 'contact_result(contact_id, result_id)'),
            'studyForms' => array(self::MANY_MANY, 'StudyForm', 'contact_study_form(contact_id, study_form_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'client_id' => 'Client',
			'datetime' => 'Datetime',
			'contact_type_id' => 'Contact Type',
			'contract_id' => 'Contract',
			'comment' => 'Comment',
			'recall' => 'Recall',
			'event_id' => 'Event',
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
        $criteria->with = array('client');

        $criteria->compare('datetime',$this->datetime,true);
		$criteria->compare('comment',$this->comment,true);
        $criteria->compare('client.name', $this->clientName, true);
        $criteria->compare('client.phone',$this->clientPhone, true);
        // Необходимо для вкладки Interview
        $criteria->compare('client.email',$this->clientEmail, true);
        $criteria->compare('client.age',$this->clientAge, true);
        $criteria->compare('client.job',$this->clientJob, true);
        $criteria->compare('client.user_id',$this->clientUser, true);
        $criteria->compare('client.city_id',$this->clientCity, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Contact the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /*сохранить клиента*/
    protected function beforeSave()
    {
        if(parent::beforeSave())
        {
            $this->client->save();
            $this->contact_type_id = Yii::app()->session['tab'];
            $this->client_id=$this->client->id;
            return true;
        }
        else
            return false;
    }
    /*scopes*/
    public function fromToday($days)
    {
        $cr = new CDbCriteria();
        $cr->with = array('client', 'studyForms', 'purposes', 'results');
        $cr->order = 'datetime DESC';
        if($days != -1)
        {
            $cr->condition='contact_type_id=:c_t_id AND DATEDIFF(CURDATE(), datetime)<=:days';
            $cr->params=array(':c_t_id'=>Yii::app()->session['tab'], ':days'=>$days);
        }
        $this->getDbCriteria()->mergeWith($cr);
        return $this;
    }
    /**/
    protected $purpose_string = '';
    protected $result_string = '';
    protected $study_form_string = '';

    protected $study_form_array;
    protected $purpose_array;
    protected $result_array;

    protected $days;
    /*propeties*/
    public function getClientName()
    {
        return $this->client->name;
    }
    public function getClientPhone()
    {
        return $this->client->phone;
    }
    // Необходимо для вкладки Interview
    public function getClientEmail()
    {
        return $this->client->email;
    }
    public function getClientAge()
    {
        return $this->client->age;
    }
    public function getClientJob()
    {
        return $this->client->job;
    }
    public function getClientUser()
    {
        return $this->client->user_id;
    }
    public function getClientCity()
    {
        return $this->client->city_id;
    }
    public function setClientName($value)
    {
        $this->client->name = $value;
    }
    public function setClientPhone($value)
    {
        $this->client->phone = $value;
    }
    public function setClientEmail($value)
    {
        $this->client->email = $value;
    }
    public function setClientAge($value)
    {
        $this->client->age = $value;
    }
    public function setClientJob($value)
    {
        $this->client->job = $value;
    }
    public function setClientUser($value)
    {
        $this->client->user_id = $value;
    }
    public function setClientCity($value)
    {
        $this->client->city_id = $value;
    }
    public function getPurposeString()
    {
        if ($this->purpose_string===''){
            foreach($this->purposes as $p)
            {
                $this->purpose_string.= '[';
                $this->purpose_string.= $p->name;
                $this->purpose_string.= ']';
            }
        }
        return $this->purpose_string;
    }
    public function getResultString()
    {
        if ($this->result_string===''){
            foreach($this->results as $r)
            {
                $this->result_string.= '[';
                $this->result_string.= $r->name;
                $this->result_string.= ']';
            }
        }
        return $this->result_string;
    }
    public function getStudyFormString()
    {
        if ($this->study_form_string===''){
            foreach($this->studyForms as $sf)
            {
                $this->study_form_string.= '[';
                $this->study_form_string.= $sf->name;
                $this->study_form_string.= ']';
            }
        }
        return $this->study_form_string;
    }
    /*property studyform*/
    public function getStudyFormArray()
    {
        if ($this->study_form_array===null)
            $this->study_form_array=CHtml::listData($this->studyForms, 'id', 'id');
        return $this->study_form_array;
    }
    public function setStudyFormArray($value)
    {
        $this->study_form_array=$value;
    }
    /*property purpose*/
    public function getPurposeArray()
    {
        if ($this->purpose_array===null)
            $this->purpose_array=CHtml::listData($this->purposes, 'id', 'id');
        return $this->purpose_array;
    }
    public function setPurposeArray($value)
    {
        $this->purpose_array=$value;
    }
    /*property result*/
    public function getResultArray()
    {
        if ($this->result_array===null)
            $this->result_array=CHtml::listData($this->results, 'id', 'id');
        return $this->result_array;
    }
    public function setResultArray($value)
    {
        $this->result_array=$value;
    }

    /*after save*/
    protected function afterSave()
    {
        $this->refreshResult();
        $this->refreshStudyForm();
        $this->refreshPurpose();
        parent::afterSave();
    }
    protected function refreshPurpose()
    {
        $purpose = $this->purposeArray;

        ContactPurpose::model()->deleteAllByAttributes(array('contact_id'=>$this->id));

        if (is_array($purpose))
        {
            foreach ($purpose as $id)
            {
                if (Purpose::model()->exists('id=:id', array(':id'=>$id)))
                {
                    $contactPurpose = new ContactPurpose();
                    $contactPurpose->contact_id = $this->id;
                    $contactPurpose->purpose_id = $id;
                    $contactPurpose->save();
                }
            }
        }
    }
    protected function refreshResult()
    {
        $result = $this->resultArray;

        ContactResult::model()->deleteAllByAttributes(array('contact_id'=>$this->id));

        if (is_array($result))
        {
            foreach ($result as $id)
            {
                if (Result::model()->exists('id=:id', array(':id'=>$id)))
                {
                    $contactResult = new ContactResult();
                    $contactResult->contact_id = $this->id;
                    $contactResult->result_id = $id;
                    $contactResult->save();
                }
            }
        }
    }
    protected function refreshStudyForm()
    {
        $studyForm = $this->StudyFormArray;

        ContactStudyForm::model()->deleteAllByAttributes(array('contact_id'=>$this->id));

        if (is_array($studyForm))
        {
            foreach ($studyForm as $id)
            {
                if (StudyForm::model()->exists('id=:id', array(':id'=>$id)))
                {
                    $contactStudyForm = new ContactStudyForm();
                    $contactStudyForm->contact_id = $this->id;
                    $contactStudyForm->study_form_id = $id;
                    $contactStudyForm->save();
                }
            }
        }
    }
}
