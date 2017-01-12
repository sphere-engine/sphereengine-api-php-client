<?php

use SphereEngine\Api\Mock\CompilersClientV3;

class CompilersClientV3Test extends PHPUnit_Framework_TestCase
{
	protected static $client;
	
	public static function setUpBeforeClass()
	{
		$access_token = 'correctAccessToken';
		$endpoint = 'unittest';
		self::$client = new CompilersClientV3(
				$access_token,
				$endpoint);
	}

    public function testAutorizationSuccess()
    {
        $this->assertEquals(true, array_key_exists('pi', self::$client->test()));
    }

    public function testTestMethodSuccess()
    {        
        $this->assertEquals(3.14, self::$client->test()['pi']);
    }

    public function testCompilersMethodSuccess()
    {
        $this->assertEquals('C++', self::$client->getCompilers()['items'][0]['name']);
    }

    public function testGetSubmissionMethodSuccess()
    {
        $s = self::$client->getSubmission(2, true);
        $this->assertEquals("abc", $s['source'], 'Submission source');
        $this->assertEquals(11, $s['compiler']['id'], 'Submission compiler');
    }
    
    public function testGetSubmissionsMethodSuccess()
    {
    	$response = self::$client->getSubmissions([4,9]);
    
    	$this->assertEquals(True, isset($response['items']));
    	$this->assertEquals(2, count($response['items']));
    	$this->assertEquals(4, $response['items'][0]['id']);
    	$this->assertEquals(9, $response['items'][1]['id']);
    }
    
    public function testGetSubmissionsMethodNonexistingSubmission()
    {
    	$response = self::$client->getSubmissions([9999,10000]);
    	$this->assertEquals(True, isset($response['items']));
    	$this->assertEquals(0, count($response['items']));
    }
    
    public function testGetSubmissionsMethodValidParams()
    {
    	$response = self::$client->getSubmissions(1);
    	$response = self::$client->getSubmissions([1]);
    }

    public function testGetSubmissionStreamMethodSuccess()
    {
        $this->assertEquals("abc", self::$client->getSubmissionStream(2, 'source'), 'Submission source');
        $this->assertEquals("input", self::$client->getSubmissionStream(2, 'input'), 'Submission input');
        $this->assertEquals("output", self::$client->getSubmissionStream(2, 'output'), 'Submission output');
        $this->assertEquals("error", self::$client->getSubmissionStream(2, 'error'), 'Submission error');
        $this->assertEquals("cmpinfo", self::$client->getSubmissionStream(2, 'cmpinfo'), 'Submission cmpinfo');
    }

    public function testCreateSubmissionMethodSuccess()
    {
    	$submission_source = "unittest";
		$submission_compiler = 11;
		$submission_input = "unittestinput";
		$response = self::$client->createSubmission($submission_source, $submission_compiler, $submission_input);
		$submission_id = $response['id']; 
        
		$this->assertTrue($submission_id > 0, 'New submission id should be greater than 0');
    }
}
