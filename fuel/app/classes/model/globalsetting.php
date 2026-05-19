<?php

class Model_Globalsetting extends \Orm\Model
{
	protected static $_properties = array(
		'id',
		'linkexpirationdate' => array(
            'data_type' => 'datetime',
            'label' => 'Temporary Link Expiration Date',
            'validation' => array('required')
        ),
		'alllinksopen' => array(
            'data_type' => 'int',
            'label' => 'Open All Links',
            'validation' => array('required','numeric_max' => array(1))
        ),
        'overridelinkexpirationdate' => array(
            'data_type' => 'int',
            'label' => 'Override Link Expiration Date With Temporary',
            'validation' => array('required')
        ),


        'emailpriority' => array(
            'data_type' => 'varchar',
            'label' => 'Email Priority',
            'form' => array(
                'type' => 'select', 'options' => array('5 (Lowest)' => '5 (Lowest)','4 (Low)' => '4 (Low)', '3 (Normal)' =>'3 (Normal)', '2 (High)' =>'2 (High)', '1 (Highest)' =>'1 (Highest)')
            ),
            //'validation' => array('required')
        ),
		'emaildriver' => array(
            'data_type' => 'varchar',
            'label' => 'Email Driver',
            //'validation' => array('required','max_length' => array(50))
        ),
		'emailhtml' => array(
            'data_type' => 'int',
            'label' => 'Email Html Content',
            //'validation' => array('required','numeric_max' => array(1))
        ),
		'emailcharset' => array(
            'data_type' => 'varchar',
            'label' => 'Email Charset',
            //'validation' => array('required','max_length' => array(20))
        ),
		'emailencoding' => array(
            'data_type' => 'varchar',
            'label' => 'Email Encoding',
            'form' => array(
                'type' => 'select', 'options' => array('8bit' => '8bit', 'base64' => 'base64', 'quoted-printable' => 'quoted-printable')
            ),
            //'validation' => array('required','numeric_max' => array(1))
        ),
		'emailfrom' => array(
            'data_type' => 'varchar',
            'label' => 'Email From',
            //'validation' => array('required','valid_email','max_length' => array(250))
        ),
		'emailname' => array(
            'data_type' => 'varchar',
            'label' => 'Email Name',
            //'validation' => array('required','max_length' => array(250))
        ),
		'emailreturn' => array(
            'data_type' => 'varchar',
            'label' => 'Email Return Path',
            //'validation' => array('required','max_length' => array(250))
        ),
		'emailpathtosendmail' => array(
            'data_type' => 'varchar',
            'label' => 'Email Path to sendmail',
            //'validation' => array('required','max_length' => array(250))
        ),
		'emailsmtphost' => array(
            'data_type' => 'varchar',
            'label' => 'Email SMTP host',
            //'validation' => array('required','max_length' => array(250))
        ),
		'emailsmtpport' => array(
            'data_type' => 'varchar',
            'label' => 'Email SMTP port',
            //'validation' => array('required','max_length' => array(20))
        ),
		'emailsmtpusername' => array(
            'data_type' => 'varchar',
            'label' => 'Email SMTP username',
            //'validation' => array('required','max_length' => array(250))
        ),
		'emailsmtppassword' => array(
            'data_type' => 'varchar',
            'label' => 'Email SMTP password',
            //'validation' => array('required','max_length' => array(250))
        ),
		'emailsmtptimeout' => array(
            'data_type' => 'varchar',
            'label' => 'Email SMTP timeout',
            //'validation' => array('required','max_length' => array(20))
        ),
        'newline' => array(
            'data_type' => 'varchar',
            'label' => 'Newline',
            //'validation' => array('required','max_length' => array(10))
        ),
		'created_at'=> array(
            'data_type' => 'int',
            'label' => 'Created At',
            'form' => array(
                'type' => false, // this prevents this field from being rendered on a form
            ),
        ),
		'updated_at'=> array(
            'data_type' => 'int',
            'label' => 'Updated At',
            'form' => array(
                'type' => false, // this prevents this field from being rendered on a form
            ),
        ),
	);

    public static function validate($factory)
    {
        $val = Validation::forge($factory);
        $val->add_field('linkexpirationdate' ,'Link Expiration Date','required');
        $val->add_field('alllinksopen','Open All Links','required|numeric_max[1]');
        $val->add_field('overridelinkexpirationdate','Override Link Expiration Date','required|numeric_max[1]');

        /*
        $val->add_field('emaildriver','Email Driver','required|max_length[50]');
        $val->add_field('emailhtml','Email Html ON','required|numeric_max[1]');
        $val->add_field('emailcharset','Email Charset','required|max_length[20]');
        $val->add_field('emailencoding','Email Encoding','required');
        $val->add_field('emailfrom','Email From','required|valid_email|max_length[250]');
        $val->add_field('emailname','Email Name','required|max_length[250]');
        $val->add_field('emailreturn','Email Return Path','required|max_length[250]');
        $val->add_field('emailpathtosendmail' ,'Email Path to sendmail','required|max_length[250]');
        $val->add_field('emailsmtphost','Email SMTP host','required|max_length[250]');
        $val->add_field('emailsmtpport','Email SMTP port','required|max_length[20]');
        $val->add_field('emailsmtpusername','Email SMTP username','required|max_length[250]');
        $val->add_field('emailsmtppassword','Email SMTP password','required|max_length[250]');
        $val->add_field('emailsmtptimeout','Email SMTP timeout','required|max_length[20]');
        $val->add_field('newline','Newline','required|max_length[10]');
        $val->add_field('emailpriority','Email Priority','required');
        */
        return $val;
    }

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => false,
		),
	);
	protected static $_table_name = 'globalsettings';

}
