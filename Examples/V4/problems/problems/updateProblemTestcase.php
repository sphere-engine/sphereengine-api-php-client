<?php
/**
 * Example presents usage of the successful updateProblemTestcase() API method  
 */

use SphereEngine\Api\ProblemsClientV4;

// require library
require_once('../../../../vendor/autoload.php');

// define access parameters
$accessToken = '<access_token>';
$endpoint = '<endpoint>';

// initialization
$client = new ProblemsClientV4($accessToken, $endpoint);

// API usage
$problemCode = 'TEST';
$testcaseNumber = 0;
$newInput = 'New testcase input';

$response = $client->updateProblemTestcase($problemCode, $testcaseNumber, $newInput);
