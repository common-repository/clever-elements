<?php

/**
 * Class CE_SOAPAuth
 *
 * @author Panayot Balkandzhiyski
 */
class CE_SOAPAuth
{
    /**
     * @var int
     */
    public $userid;
    /**
     * @var string
     */
    public $apikey;
    /**
     * @var
     */
    public $version;
    /**
     * @var string
     */
    public $mode;
    /**
     * @var SoapClient
     */
    public $client;
    /**
     * @var array
     */
    public $params;

    /**
     * @var
     */
    private $user;

    /**
     * @var bool
     */
    private $is_active = false;
    /**
     * @var array
     */
    private $errors = array();
    /**
     * @var
     */
    private $notices;

    /**
     * @param $userid
     * @param $apikey
     * @param $version
     */
    public function __construct($userid, $apikey, $version)
    {
        $this->userid = $userid;
        $this->apikey = $apikey;
        $this->version = $version;

        // get if user is set test mode
        $mode = (get_option('cl_wp_pl_api_is_test_mode') == 1) ? 'test' : 'live';
        $this->mode = $mode;

        $this->client = new SoapClient('http://api.sendcockpit.com/server.php?wsdl', array('trace' => 1));
        $this->params = array($this->userid, $this->apikey, $this->version, $this->mode);

        // this is for local testing
        /*$this->client = new SoapClient('http://cleverelements:8888/appl/ce/api/server_local.php?wsdl',
        $this->client = new SoapClient("http://cleverelements:8888/appl/ce/api/server_local.php?wsdl",
            array('trace' => 1, 'uri' => 'localhost', 'cache_wsdl' => WSDL_CACHE_NONE));
        $this->params = array('191708', 'Nx91Hva1fLwbAJAK',$this->version, $this->mode);*/

        $header = new SOAPHeader('sendcockpit', 'validate', $this->params);

        // send soap headers
        $this->client->__setSoapHeaders($header);

        // check if user has at last 1 record and made the validation

        $this->cewp_get_user_subscr_list();
    }

    /**
     * @return bool|array
     */
    public function cewp_get_user_subscr_list()
    {
        try {
            $subscribers_list = $this->client->apiGetList()->listResponse;
        } catch (SoapFault $error) {
            $this->is_active = false;
            $this->errors[] = $error->getMessage();
            return false;
        }

        // user has more than one subscribers list
        if (!is_array($subscribers_list)) {
            $this->errors[] = 'No lists available';
            return false;
        } else {
            $this->is_active = true;
        }

        return $subscribers_list;
    }


    /**
     * @param $subscriber
     */
    public function cewp_subscribe_to_all($subscriber)
    {
        $subscriber_lists_db = unserialize(get_option('cl_wp_pl_api_subscriber_lists'));
        $doi_is_active = get_option('cl_wp_pl_api_doi_is_active');

        if (is_array($subscriber_lists_db)) {
            foreach ($subscriber_lists_db as $list_id => $is_set) {
                if ($is_set) {
                    if ( !$this->cewp_add_subscriber($subscriber, $list_id, array(), $doi_is_active)) {
                        //@TODO set error messages
                        //var_dump($this->errors);
                    } else {
                        //@TODO set error nitices
                        //var_dump($this->notices);
                    }
                }
            }
        }
    }

    /**
     * @param $subscriber
     * @param $custom_fields_array
     */
    public function cewp_subscribe_to_form($subscriber, $custom_fields_array)
    {
        $subscriber_lists_db = unserialize(get_option('cl_wp_pl_api_subscriber_lists_form'));
        $doi_is_active = get_option('cl_wp_pl_api_doi_is_active_form');

        if (is_array($subscriber_lists_db)) {
            foreach ($subscriber_lists_db as $list_id => $is_set) {
                //exit($this->cewp_add_subscriber($subscriber, $list_id, $custom_fields_array));
                if ($is_set) {
                    if ( !$this->cewp_add_subscriber($subscriber, $list_id, $custom_fields_array, $doi_is_active)) {
                        //@TODO set error messages
                        //var_dump($this->errors);
                    } else {
                        //@TODO set error nitices
                        //var_dump($this->notices);
                    }
                }
            }
        }
    }

    /**
     * @param       $subscriber
     * @param       $list_id
     * @param array $custom_fields_array
     *
     * @return bool
     */
    public function cewp_add_subscriber($subscriber, $list_id, $custom_fields_array = array(), $doi_is_active = '')
    {
        $custom_fields = array();

        if (count($custom_fields_array)) {
            foreach ($custom_fields_array as $_custom_field => $_data) {
                $custom_fields[] = array(
                    'customFieldID'    => $_custom_field,
                    'customFieldValue' => $_data,
                );
            }
        }

        $subscriber_api_data[0]['listID'] = $list_id;
        $subscriber_api_data[0]['email'] = $subscriber['email'];
        $subscriber_api_data[0]['customFields'] = $custom_fields;

        try {

            if ($doi_is_active == 1)
            {
                $response = $this->client->apiAddSubscriberDoi(array('subscriberList' => $subscriber_api_data));
            }
            else {
                $response = $this->client->apiAddSubscriber(array('subscriberList' => $subscriber_api_data));
            }

            $this->notices[] = array('response' => $response, $subscriber_api_data);

            return true;
        } catch (SoapFault $exception) {
            $this->errors[] = $exception;

            return false;
        }
    }

    /**
     * @return mixed
     */
    public function cewp_get_custom_fields()
    {
        try {
            $response = $this->client->apiGetSubscriberFields();
        } catch (SoapFault $exception) {
            echo esc_html($exception->getMessage());
            return array(); // Return an empty array in case of error
        }

        return $response->responseSubsriberCustomFields;
    }


    /**
     * @return bool
     */
    public function cewp_is_connected()
    {
        return $this->is_active;
    }

    /**
     * @return array
     */
    public function cewp_get_errors()
    {
        return $this->errors;
    }
}
