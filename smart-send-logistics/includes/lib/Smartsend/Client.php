<?php


namespace Smartsend;

require_once 'Models/Error.php';

use Smartsend\Models\Error;

class Client
{
    const TIMEOUT = 30;

    private $api_host = 'https://app.smartsend.io/api/v1/';
    private $website;
    private $api_token;
    private $demo;
    protected $request_endpoint;
    protected $request_headers;
    protected $request_body;
    protected $response_headers;
    protected $response_body;
    protected $response;
    protected $http_status_code;
    protected $content_type;
    protected $debug;
    protected $meta;
    protected $success;
    protected $data;
    protected $links;
    protected $error;

    public function __construct($api_token, $website, $demo=false)
    {
        $this->setApiToken($api_token);
        $this->setWebsite($website);
        $this->setDemo($demo);

        $this->api_host = apply_filters( 'smart_send_api_endpoint', $this->api_host);
    }

    public function setApiToken($api_token)
    {
        $this->api_token = $api_token;
    }

    public function setWebsite($website)
    {
        // Remove www. from the start of the website
        if (substr($website, 0, strlen('www.')) == 'www.') {
            $website = substr($website, strlen('www.'));
        }

        $this->website = $website;
    }

    public function setDemo($demo)
    {
        $this->demo = $demo;
    }

    public function getApiEndpoint() 
    {
        return $this->getApiHost().($this->getDemo() ? 'demo/' : '')."website/".$this->getWebsite()."/";
    }

    private function getApiHost() 
    {
        return $this->api_host;
    }

    private function getWebsite() 
    {
        return $this->website;
    }

    private function getApiToken() 
    {
        return $this->api_token;
    }

    public function getDemo() 
    {
        return $this->demo;
    }

    public function getModuleVersion()
    {
        return SS_SHIPPING_VERSION;
    }

    public function getUserAgent()
    {
        // Check if get_plugins() function exists. This is required on the front end of the
        // site, since it is in a file that is normally only loaded in the admin.
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        // Find WooCommerce version number
        $wooCommercePluginFolder = get_plugins( '/' . 'woocommerce' );
        $wooCommercePluginFile = 'woocommerce.php';
        if (isset($wooCommercePluginFolder[$wooCommercePluginFile]['Version'])) {
            $wooCommerceVersion = $wooCommercePluginFolder[$wooCommercePluginFile]['Version'];
        } else {
            $wooCommerceVersion = '';
        }

        // Find the HTTP User-agent
        $userAgent = array(
            "WordPress"     => get_bloginfo('version'),
            "WooCommerce"   => $wooCommerceVersion,
            "SmartSend"     => $this->getModuleVersion(),
        );
        $userAgentString = str_replace('=', '/', http_build_query($userAgent, '', ' '));

        return $userAgentString;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getErrorString($delimiter='<br>')
    {
	    // Fetch error:
	    $error = $this->getError();

	    // Print error message
	    $error_string = $error->message;
	    // Print 'Read more here' link to error explenation
	    if (isset($error->links->about)) {
		    $error_string .= $delimiter."- <a href='".$error->links->about."' target='_blank'>Read more here</a>";
	    }

	    // Print unique error ID if one exists
	    if (isset($error->id)) {
		    $error_string .= $delimiter."Unique ID: ".$error->id;
	    }

	    // Print each error
	    if (isset($error->errors)) {
		    foreach ($error->errors as $error_field => $error_details) {
			    if (is_array($error_details)) {
				    if (count($error_details) > 1) {
					    $error_string .= $delimiter . $error_field .':';
					    foreach ($error_details as $error_description) {
						    $error_string .= $delimiter . "- ". $error_description;
					    }
				    } else {
					    foreach ($error_details as $error_description) {
						    $error_string .= $delimiter . "- " . $error_field . ': ' . $error_description;
					    }
				    }
			    } else {
				    $error_string .= $delimiter . "- " . $error_field . ': ' . $error_details;
			    }
		    }
	    }

	    return $error_string;
    }

    /**
     * @return void
     */
    public function printError()
    {
        echo $this->getErrorString('<br>');
    }

    /**
     * @return mixed
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @return mixed
     */
    public function getRequestEndpoint()
    {
        return $this->request_endpoint;
    }

    /**
     * @return mixed
     */
    public function getRequestBody()
    {
        return $this->request_body;
    }

    /**
     * @return mixed
     */
    public function getRequestHeaders()
    {
        return $this->request_headers;
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->response_body;
    }

    /**
     * @return mixed
     */
    public function getResponseHeaders()
    {
        return $this->response_headers;
    }

    /**
     * Was the API response contain link to next page of results
     * @return  boolean
     */
    public function isSuccessful()
    {
        return $this->success;
    }

    /**
     * Return all request and response traces
     * @return  void
     */
    private function clearAll()
    {
        $this->request_endpoint = null;
        $this->request_headers = null;
        $this->request_body = null;
        $this->response_headers = null;
        $this->response_body = null;
        $this->response = null;
        $this->meta = null;
        $this->data = null;
        $this->links = null;
        $this->error = null;
        $this->success = null;
        $this->http_status_code = null;
        $this->content_type = null;
        $this->debug = null;
    }

    /**
     * Make an HTTP DELETE request - for deleting data
     * @param   string $method URL of the API request method
     * @param   array $args Assoc array of arguments (if any)
     * @param   array $headers Assoc array of headers
     * @param   array $body Assoc array of body (will be converted to json)
     * @param   int $timeout Timeout limit for request in seconds
     * @return  object|true|false   Assoc array of API response, decoded from JSON
     */
    public function httpDelete($method, $args = array(), $headers = array(), $body=null, $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('delete', $method, $args, $headers, $body, $timeout);
    }
    /**
     * Make an HTTP GET request - for retrieving data
     * @param   string $method URL of the API request method
     * @param   array $args Assoc array of arguments (usually your data)
     * @param   array $headers Assoc array of headers
     * @param   array $body Assoc array of body (will be converted to json)
     * @param   int $timeout Timeout limit for request in seconds
     * @return  object|true|false   Assoc array of API response, decoded from JSON
     */
    public function httpGet($method, $args = array(), $headers = array(), $body=null, $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('get', $method, $args, $headers, $body, $timeout);
    }
    /**
     * Make an HTTP PATCH request - for performing partial updates
     * @param   string $method URL of the API request method
     * @param   array $args Assoc array of arguments (usually your data)
     * @param   array $headers Assoc array of headers
     * @param   array $body Assoc array of body (will be converted to json)
     * @param   int $timeout Timeout limit for request in seconds
     * @return  object|true|false   Assoc array of API response, decoded from JSON
     */
    public function httpPatch($method, $args = array(), $headers = array(), $body=null, $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('patch', $method, $args, $headers, $body, $timeout);
    }
    /**
     * Make an HTTP POST request - for creating and updating items
     * @param   string $method URL of the API request method
     * @param   array $args Assoc array of arguments (usually your data)
     * @param   array $headers Assoc array of headers
     * @param   array $body Assoc array of body (will be converted to json)
     * @param   int $timeout Timeout limit for request in seconds
     * @return  object|true|false   Assoc array of API response, decoded from JSON
     */
    public function httpPost($method, $args = array(), $headers = array(), $body=null, $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('post', $method, $args, $headers, $body, $timeout);
    }
    /**
     * Make an HTTP PUT request - for creating new items
     * @param   string $method URL of the API request method
     * @param   array $args Assoc array of arguments (usually your data)
     * @param   array $headers Assoc array of headers
     * @param   array $body Assoc array of body (will be converted to json)
     * @param   int $timeout Timeout limit for request in seconds
     * @return  object|true|false   Assoc array of API response, decoded from JSON
     */
    public function httpPut($method, $args = array(), $headers = array(), $body=null, $timeout = self::TIMEOUT)
    {
        return $this->makeRequest('put', $method, $args, $headers, $body, $timeout);
    }
    /**
     * Performs the underlying HTTP request. Not very exciting.
     * @param   string $http_verb The HTTP verb to use: get, post, put, patch, delete
     * @param   string $method The API method to be called
     * @param   array $args Assoc array of query parameters to be passed
     * @param   array $headers Assoc array of headers
     * @param   array $body Assoc array of body (will be converted to json)
     * @param   int $timeout
     * @return  object|true|false   Assoc array of API response, decoded from JSON
     *
     * @throws \Exception
     */
    private function makeRequest($http_verb, $method, $args = array(), $headers=array(), $body=null, $timeout = self::TIMEOUT)
    {
        // If the headers where not set, then use default
        if (empty($headers)) {
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
            );
        }

        // Append API key to the headers
        $args['api_token'] = $this->getApiToken();

        // Clear request and response from previous API call
        $this->clearAll();

        // Set URL (inc parameters $args)
        $this->request_endpoint = $this->getApiEndpoint().$method;

        if (!empty($args) && strpos($this->request_endpoint, '?') !== false) {
            $this->request_endpoint .= '&'.http_build_query($args, '', '&');
        } elseif (!empty($args)) {
            $this->request_endpoint .= '?'.http_build_query($args, '', '&');
        }

        // Set body (if $http_verb not delete)
        if ($http_verb != 'get' && $http_verb != 'delete') {
            $this->request_body = ($body ? json_encode($body) : null);
        }

        if (!isset($headers['referer'])) {
	        $headers['referer'] = $this->getWebsite();
        }

        // Split headers into key-value array
	    $headers_key_value = array();
	    foreach ($headers as $header) {
		    $tmp = explode(': ', $header, 2);
		    if (isset($tmp[1])) {
			    $headers_key_value[$tmp[0]] = $tmp[1];
		    }
	    }

	    // Make request
	    $res = wp_remote_request($this->request_endpoint, array(
		    'method'     => strtoupper($http_verb),
		    'user-agent' => $this->getUserAgent(),
			'headers'    => $headers_key_value,
		    'body'       => $this->request_body,
		    'timeout'    => $timeout,
		    'httpversion' => '1.1',
            'sslverify'  => apply_filters('smart_send_sslverify', true),
	    ));

        // execute request
	    $this->response_body = wp_remote_retrieve_body($res);

        // Save http status code and headers
	    $this->debug = $res;
	    $this->request_headers = wp_remote_retrieve_headers($res);
	    $this->http_status_code = wp_remote_retrieve_response_code($res);
	    $this->content_type = wp_remote_retrieve_header($res, 'content-type');

        if (is_wp_error($res)) {
            $this->success = false;
            $error = new Error();

	        if ($res->get_error_message() == 'cURL error 35: SSL connect error') {
		        $error->links = null;
		        $error->id = null;
		        $error->code = 'curl-35';
		        $error->message =  'Unsupported cURL version. This is a security issue. Please ask your host to update cURL.';
	        } else {
		        $error->links = null;
		        $error->id = null;
		        $error->code = $res->get_error_code();
		        $error->message =  $res->get_error_message();
	        }

            $error->errors = array();
            $this->error = $error;
            return $this->success;
        }

        // If response is JSON, then json_decode
        if (strpos($this->content_type, 'application/json') !== false || strpos($this->content_type, 'text/json') !== false) {
            $this->response = json_decode($this->response_body);
        }

        //Error if response is not 2xx
        if ($this->http_status_code < 200 || $this->http_status_code > 299 ) {
            $this->success = false;
            if (!empty($this->response->message)) {
                $this->error = $this->response;
            } elseif (empty($this->response_body)) {
                $error = new Error();
                $error->links = null;
                $error->id = null;
                $error->code = (int) $this->http_status_code;
                $error->message = 'No API response';
                $error->errors = array();
                $this->error = $error;
            } elseif (empty($this->response)) {
                $error = new Error();
                $error->links = null;
                $error->id = null;
                $error->code = (int) $this->http_status_code;
                $error->message = 'Unknown API response';
                $error->errors = array();
                $this->error = $error;
            } else {
                $error = new Error();
                $error->links = null;
                $error->id = null;
                $error->code = (int) $this->http_status_code;
                $error->message = $this->response;
                $error->errors = array();
                $this->error = $error;
            }

            return $this->success;
        }

        // if no response->data
        if (empty($this->response->data)) {
            if ($http_verb == 'delete') {
                //Return TRUE for DELETE with no BODY
                $this->success = true;
            } elseif (!empty($this->response->message)) {
                $this->error = $this->response;
                $this->success = false;
            } elseif (empty($this->response_body)) {
                $error = new Error();
                $error->links = null;
                $error->id = null;
                $error->code = 'HTTP' . $this->http_status_code;
                $error->message = 'No API response';
                $error->errors = array();
                $this->error = $error;
                $this->success = false;
            } elseif (isset($this->response->data)) {
                $error = new Error();
                $error->links = null;
                $error->id = null;
                $error->code = 'NoResults';
                $error->message = 'No results found';
                $error->errors = array();
                $this->error = $error;
                $this->success = false;
            } else {
                $error = new Error();
                $error->links = null;
                $error->id = null;
                $error->code = 'HTTP'.$this->http_status_code;
                $error->message = $this->response_body;
                $error->errors = array();
                $this->error = $error;
                $this->success = false;
            }
        } else {
            if (isset($this->response->links)) {
                $this->links = $this->response->links;
            }

            $this->success = true;
            $this->data = $this->response->data;
        }

        return $this->success;
    }

}
