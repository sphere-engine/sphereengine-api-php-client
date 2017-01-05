<?php

use SphereEngine\Api\Mock\ProblemsClientV3;
use SphereEngine\Api\SphereEngineResponseException;

class ProblemsClientV3Test extends PHPUnit_Framework_TestCase
{
	protected static $client;
	
	public static function setUpBeforeClass()
	{
		$access_token = 'correctAccessToken';
		$endpoint = 'unittest';
		self::$client = new ProblemsClientV3(
				$access_token,
				$endpoint);
	}

    public function testAutorizationSuccess()
    {
    	self::$client->test();
    	$this->assertTrue(true);
    }

    public function testTestMethodSuccess()
    {
        $this->assertTrue(count(self::$client->test()) > 0);
    }
    
    public function testCompilersMethodSuccess()
    {
    	$this->assertEquals("C++", self::$client->getCompilers()['items'][0]['name']);
    }
    
    public function testGetProblemsMethodSuccess()
    {
		$problems = self::$client->getProblems();
    	$this->assertEquals(10, $problems['paging']['limit']);
		$this->assertEquals(0, $problems['paging']['offset']);
		$this->assertEquals(false, isset($problems['items'][0]['shortBody']));
		$this->assertEquals(true, isset($problems['items'][0]['lastModifiedBody']));
		$this->assertEquals(true, isset($problems['items'][0]['lastModifiedSettings']));
    	$this->assertEquals(11, self::$client->getProblems(11)['paging']['limit']);
		$this->assertEquals(false, isset(self::$client->getProblems(10, 0, false)['items'][0]['shortBody']));
		$this->assertEquals(true, isset(self::$client->getProblems(10, 0, true)['items'][0]['shortBody']));
    }
    
    public function testGetProblemMethodSuccess()
    {
		$problem = self::$client->getProblem('TEST');
    	$this->assertEquals('TEST', $problem['code']);
		$this->assertEquals(false, isset($problem['shortBody']));
		$this->assertEquals(true, isset($problem['lastModifiedBody']));
		$this->assertEquals(true, isset($problem['lastModifiedSettings']));
		$this->assertEquals(false, isset(self::$client->getProblem('TEST', false)['shortBody']));
		$this->assertEquals(true, isset(self::$client->getProblem('TEST', true)['shortBody']));
    }
    
    public function testCreateProblemMethodSuccess()
    {
    	$problem_code = 'CODE';
    	$problem_name = 'Name';
    	$problem_body = 'Body';
    	$problem_type = 'maximize';
    	$problem_interactive = 1;
    	$problem_masterjudgeId = 1000;
    	$this->assertEquals(
    			$problem_code, 
    			self::$client->createProblem(
    					$problem_code, 
    					$problem_name,
    					$problem_body,
    					$problem_type,
    					$problem_interactive,
    					$problem_masterjudgeId
    				)['code'],
    			'Creation method should return new problem code');
    }
    
    public function testUpdateProblemMethodSuccess()
    {
    	$problem_code = 'CODE';	
    	$new_problem_name = 'Updated name';
    	$new_problem_body = 'update';
    	$new_problem_type = 'maximize';
    	$new_problem_interactive = 1;
    	$new_problem_masterjudgeId = 1000;
    	self::$client->updateProblem(
    			$problem_code,
    			$new_problem_name,
    			$new_problem_body,
    			$new_problem_type,
    			$new_problem_interactive,
    			$new_problem_masterjudgeId);
		// there should be no exceptions during these operations
    }
    
    public function testUpdateProblemActiveTestcasesMethodSuccess()
    {
    	self::$client->updateProblemActiveTestcases('TEST', []);
    	$this->assertEquals("", self::$client->getProblem('TEST')['seq']); 
    	self::$client->updateProblemActiveTestcases('TEST', [0]);
    	$this->assertEquals("#0", self::$client->getProblem('TEST')['seq']);
    }
    
    public function testGetProblemTestcasesMethodSuccess()
    {
    	$this->assertEquals(0, self::$client->getProblemTestcases('TEST')['testcases'][0]['number']);
    }
    
    public function testGetProblemTestcaseMethodSuccess()
    {
    	$this->assertEquals(0, self::$client->getProblemTestcase('TEST', 0)['number']);
    }
    
    public function testCreateProblemTestcaseMethodSuccess()
    {
    	$r = rand(1000000,9999999) . rand(1000000,9999999); // 14-digits random string
    	// create problem to create testcases
    	$problem_code = 'UT' . $r;
    	$problem_name = 'UT' . $r;
    	self::$client->createProblem($problem_code, $problem_name);
    	 
    	self::$client->createProblemTestcase($problem_code, "in0", "out0", 10, 2, 0);
    	$ptc = self::$client->getProblemTestcase($problem_code, 0);
    	$this->assertEquals(0, $ptc['number'], 'Testcase number');
    	$this->assertEquals(false, $ptc['active'], 'Testcase active');
    	$this->assertEquals(10, $ptc['limits']['time'], 'Testcase timelimit');
    	$this->assertEquals(3, $ptc['input']['size'], 'Testcase input size');
    	$this->assertEquals(4, $ptc['output']['size'], 'Testcase output size');
    	$this->assertEquals(2, $ptc['judge']['id'], 'Testcase judge');
    }

    public function testUpdateProblemTestcaseMethodSuccess()
    {
    	$r = rand(1000000,9999999) . rand(1000000,9999999); // 14-digits random string
    	// create problem and testcase to update the testcase
    	$problem_code = 'UT' . $r;
    	$problem_name = 'UT' . $r;
    	self::$client->createProblem($problem_code, $problem_name);
    	self::$client->createProblemTestcase($problem_code, "in0", "out0", 1, 1, 1);
    	
    	$new_testcase_input = "in0updated";
    	$new_testcase_output = "out0updated";
    	$new_testcase_timelimit = 10;
    	$new_testcase_judge = 2;
    	$new_testcase_active = 0;
    	
    	self::$client->updateProblemTestcase(
    			$problem_code,
    			0,
    			$new_testcase_input, 
    			$new_testcase_output, 
    			$new_testcase_timelimit, 
    			$new_testcase_judge, 
    			$new_testcase_active);
    	
    	$ptc = self::$client->getProblemTestcase($problem_code, 0);
    	$this->assertEquals(0, $ptc['number'], 'Testcase number');
    	$this->assertEquals(false, $ptc['active'], 'Testcase active');
    	$this->assertEquals($new_testcase_timelimit, $ptc['limits']['time'], 'Testcase timelimit');
    	$this->assertEquals(strlen($new_testcase_input), $ptc['input']['size'], 'Testcase input size');
    	$this->assertEquals(strlen($new_testcase_output), $ptc['output']['size'], 'Testcase output size');
    	$this->assertEquals($new_testcase_judge, $ptc['judge']['id'], 'Testcase judge');
    }
    
    public function testDeleteProblemTestcaseMethodSuccess()
    {
    	$r = rand(1000000,9999999) . rand(1000000,9999999); // 14-digits random string
    	// create problem and testcase to delete the testcase
    	$problem_code = 'UT' . $r;
    	$problem_name = 'UT' . $r;
    	self::$client->createProblem($problem_code, $problem_name);
    	self::$client->createProblemTestcase($problem_code, "in0", "out0", 1, 1, 1);
    	self::$client->createProblemTestcase($problem_code, "in1", "out1", 1, 1, 1);
    	self::$client->deleteProblemTestcase($problem_code, 0);
    	 
    	$p = self::$client->getProblem($problem_code);
		$this->assertEquals(1, count($p['testcases']));
		
		self::$client->deleteProblemTestcase($problem_code, 1);
		
		$p = self::$client->getProblem($problem_code);
		$this->assertEquals(0, count($p['testcases']));
    }

    public function testGetProblemTestcaseFileMethodSuccess()
    {
    	$r = rand(1000000,9999999) . rand(1000000,9999999); // 14-digits random string
    	// create problem and testcase to retrieve file
    	$problem_code = 'UT' . $r;
    	$problem_name = 'UT' . $r;
    	self::$client->createProblem($problem_code, $problem_name);
    	self::$client->createProblemTestcase($problem_code, "in0", "out0", 1, 1, 1);
    	
    	$this->assertEquals("in0", self::$client->getProblemTestcaseFile($problem_code, 0, 'input'));
    	$this->assertEquals("out0", self::$client->getProblemTestcaseFile($problem_code, 0, 'output'));
    }
    
    public function testGetJudgesMethodSuccess()
    {
    	$this->assertEquals(10, self::$client->getJudges()['paging']['limit']);
    	$this->assertEquals(11, self::$client->getJudges(11)['paging']['limit']);
    }
    
    public function testGetJudgeMethodSuccess()
    {
    	$this->assertEquals(1, self::$client->getJudge(1)['id']);
    }
    
	public function testCreateJudgeMethodSuccess()
	{
		$judge_source = 'source';
		$judge_compiler = 1;
		$judge_type = 'testcase';
		$judge_name = 'UT judge';
		
		$response = self::$client->createJudge(
						$judge_source,
						$judge_compiler,
						$judge_type,
						$judge_name
						);
		$judge_id = $response['id'];
		$this->assertTrue($judge_id > 0, 'Creation method should return new judge ID');
		$j = self::$client->getJudge($judge_id);
		$this->assertEquals($judge_source, $j['source'], 'Judge source');
		$this->assertEquals($judge_compiler, $j['compiler']['id'], 'Judge compiler ID');
		$this->assertEquals($judge_type, $j['type'], 'Judge type');
		$this->assertEquals($judge_name, $j['name'], 'Judge name');
	}
	
	public function testUpdateJudgeMethodSuccess()
	{
		$response = self::$client->createJudge('source', 1, 'testcase', 'UT judge');
		$judge_id = $response['id'];
		 
		$new_judge_source = 'updated source';
		$new_judge_compiler = 11;
		$new_judge_name = 'UT judge updated';
		
		self::$client->updateJudge(
				$judge_id,
				$new_judge_source,
				$new_judge_compiler,
				$new_judge_name);
		
		$j = self::$client->getJudge($judge_id);
		$this->assertEquals($new_judge_source, $j['source'], 'Judge source');
		$this->assertEquals($new_judge_compiler, $j['compiler']['id'], 'Judge compiler ID');
		$this->assertEquals($new_judge_name, $j['name'], 'Judge name');
	}
	
	public function testGetSubmissionMethodSuccess()
	{
		$this->assertEquals(1, self::$client->getSubmission(1)['id']);
	}
	
	public function testCreateSubmissionMethodSuccess()
	{
		$submission_problem_code = 'TEST';
		$submission_source = 'source';
		$submission_compiler = 1;
		
		$response = self::$client->createSubmission(
				$submission_problem_code,
				$submission_source,
				$submission_compiler);
		$submission_id = $response['id'];
		$this->assertTrue($submission_id > 0, 'Creation method should return new submission ID');
		$s = self::$client->getSubmission($submission_id);
		$this->assertEquals($submission_problem_code, $s['problem']['code'], 'Submission problem code');
		$this->assertEquals($submission_source, $s['source'], 'Submission source');
		$this->assertEquals($submission_compiler, $s['compiler']['id'], 'Submission compiler ID');
	}
}
