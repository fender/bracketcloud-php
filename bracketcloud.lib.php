<?php

/**
 * PHP API wrapper for BracketCloud (http://bracketcloud.com)
 * Version: 1.0.0
 * 
 * Copyright 2013 BracketCloud - Nichikare Corporation
 * 
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */
 
/**
 * BracketCloudAPIRequest class
 */
class BracketCloudAPIRequest {
  private $api_key;
  private $api_url;
  private $arguments = array();
  
  public $error;
  public $http_status;
  
  /**
   * Class Contructor
   * 
   * @param string $api_key
   *   The unique API key for interacting with the API.
   */
  public function __construct($api_key) {
    $this->api_url = 'https://bracketcloud.com/api/1.0/';
    $this->api_key = $api_key;
    
    $this->error = FALSE;
  }
  
  /**
   * Sets an argument for the request
   */
  public function addArgument($key, $value) {
    $this->arguments[$key] = $value;
    return $this;
  }
  
  /**
   * Executes a tournament search. e.g. GET tournaments
   * 
   * @return Callback response data
   */
  public function execute() {
    $params = $this->arguments;
    $params['full_objects'] = 'true';
    return $this->request('tournaments', 'GET', $params);
  }
  
  
  /**
   * Make an API http request
   * 
   * @param string $path
   * @param array @params
   * @param string $method
   * 
   * @return API results
   */
  public function request($path = '', $method = 'GET', $params = array()) {
    // Set default values
    $this->http_status = NULL;
    $this->error = FALSE;
   
    // The API key must be sent with every request we make to the API
    $params['api_key'] = $this->api_key;
    
    // Construct the request method callback URL
    $url = $this->api_url . $path;

    // Build our cURL request    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    switch ($method) {
      case 'POST':
      case 'PUT':
      case 'DELETE':
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        $content = json_encode($params);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        break;
        
      case 'GET':
        $url .= '?' . http_build_query($params, '', '&');
    }
    
    $headers = array('Content-type: application/json');
    if (!empty($content)) {
      $headers[] = 'Content-Length: ' . strlen($content);
    }
    
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $url);
    
    // Execute callback
    $result = curl_exec($curl);
    $this->http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if (!in_array($this->http_status, array(200, 204))) {
      // We received an HTTP status code other than the expected 200 or 204
      $this->error = TRUE;
    }
    
    $data = $this->parse($result);
    
    // Return FALSE on error
    if ($this->error && isset($data->error)) {
      return FALSE;
    }
    
    return $data;
  }

  private function parse($data) {
    return json_decode($data);
  }
  
  /**
   * Returns a single tournament
   */
  public function getTournament($tid) {
    return $this->request("tournaments/$tid", 'GET');
  }
  
  /**
   * Creates a new tournament
   */
  public function createTournament($params) {
    return $this->request('tournaments', 'POST', $params);
  }
  
  /**
   * Update a tournament
   */
  public function updateTournament($tid, $params) {
    return $this->request("tournaments/$tid", 'PUT', $params);
  }
  
  /**
   * Delete a tournament
   */
  public function deleteTournament($tid) {
    return $this->request("tournaments/$tid", 'DELETE');
  }
}

?>