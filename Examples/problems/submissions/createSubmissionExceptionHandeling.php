<?php
/**
 * Example presents error handeling for createSubmission() API method
*/

use SphereEngine\Api\ProblemsClientV3;
use SphereEngine\Api\SphereEngineResponseException;

// require library
require_once('../../../autoload.php');

// define access parameters
$accessToken = getenv("SE_ACCESS_TOKEN_PROBLEMS");
$endpoint = getenv("SE_ENDPOINT_PROBLEMS");

// initialization
$client = new ProblemsClientV3($accessToken, $endpoint);

// API usage
$problemCode = 'TEST';
$source = 'int main() { return 0; }';
$nonexisting_compiler = 99999;

try {
	$response = $client->createSubmission($problemCode, $source, $nonexisting_compiler);
	// response['id'] stores the ID of the created submission
} catch (SphereEngineResponseException $e) {
	if ($e->getCode() == 401) {
		echo 'Invalid access token';
	} elseif ($e->getCode() == 404) {
		// agregates three possible reasons of 404 error
		// non existing problem, compiler or user
		echo 'Non existing resource (problem, compiler or user), details available in the message: ' . $e->getMessage();
	} elseif ($e->getCode() == 400) {
		echo 'Empty source code';
	}
}
