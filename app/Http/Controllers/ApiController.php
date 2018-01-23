<?php

namespace App\Http\Controllers;

use App\StatusCodes;
use Illuminate\Http\Request;

use App\Http\Requests;

class ApiController extends Controller
{
	use StatusCodes;

	/**
	 * Required inputs to be overriden be the child class.
	 * @var array
	 */
    protected $requiredInputs = [];

    /**
     * Inputs for this request, whether they be raw or form encoded, will be set in the constructor.
     * @var array
     */
    protected $inputs = [];

    /**
     * Constructor to do some housekeeping stuff.
     * 1. Set the inputs property.
     */
    public function __construct(Request $request) {

    	$this->setUpInputs($request);

    }


    /**
     * If the required inputs are present.
     * returns true if all required inputs are present.
     * returns a response with the missing inputs if an input is not present.
     * @return mixed
     */
	protected function requiredInputsPresent() {
		// a flag to indicate if all inputs are present or not.
		$allInputsPresent = true;
		// an array to include the missing inputs during the traversal.
		$missingInputs = [];
		foreach ($this->requiredInputs as $index => $requiredInput) {
			// if the inputs dont have the requiredInput
			if( !isset($this->inputs[$requiredInput]) || empty($this->inputs[$requiredInput]) ) {
				// if so, then set the flag to false.
				$allInputsPresent = false;
				// put the missing input in the missingInputs array.
				array_push($missingInputs, $requiredInput);
			}
		}
		// if All inputs present flag still indicates true then yup return true.
		if($allInputsPresent) {
			return true;
		}
		
		// if not then return a non 400 response with the missing inputs imploded into it as a comma seperated string.


		return $this->respond($this->MISSING_REQUIRED_INPUTS, [
			'status' => $this->MISSING_REQUIRED_INPUTS,
			'message' => 'Required inputs (' . implode(',', $missingInputs) . ') are missing'
		], $this->MISSING_REQUIRED_INPUTS);

	}


	/**
	 * Sets the inputs property to the inputs received.
	 * @param Illuminate\Http\Request $request
	 */
	protected function setUpInputs(Request $request) {

		$this->inputs = $request->input();

	}

	/**
	 * Send some response and die. use it for errorneous stuff.
	 * @param int $status HTTP response code
	 * @param array $response the response to send along.
	 */
	protected function respond($status, $response) {

		header('Content-Type: Application/JSON');

		http_response_code($status);
		echo json_encode($response);
		
		flush();
	}

	/**
	 * Send an errorneous response.
	 */
	protected function sendUnknownError($response = 'An unknown error occured.') {
		$this->respond($this->BAD_REQUEST, [
			'status' => $this->BAD_REQUEST,
			'response' => $response
		]);
	}


    /**
     * Get Reason from validation errors
     * @param $messages
     * @return string
     */
    public static function getReason($messages){

        $reason = '';

        foreach ($messages->toArray() as $message) {
            $reason .= ' ' . $message['0'];
        }


        return $reason;
    }
}
