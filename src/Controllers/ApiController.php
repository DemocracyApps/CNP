<?php namespace DemocracyApps\CNP\Controllers;

use \DemocracyApps\CNP\Entities as DAEntity;
use \Illuminate\Http\Response as IResponse;

class ApiController extends BaseController {


	/**
	 * @var integer
	 */
	protected $statusCode = IResponse::HTTP_OK;

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function setStatusCode($statusCode)
	{
		$this->statusCode = $statusCode;
		return $this;
	}

	public function respond($data, $headers = [])
	{
		return \Response::json($data, $this->getStatusCode(), $headers);
	}

	public function respondWithError($message) 
	{
		return $this->respond([
				'error' => [
					'message' 		=> $message,
					'status_code'	=> $this->getStatusCode()
					]
				]);
	}

	public function respondNotFound ($message = 'Not Found')
	{
		return $this->setStatusCode(IResponse::HTTP_NOT_FOUND)->respondWithError($message);
	}

	public function respondFormatError ($message = "Bad format") 
	{
		return $this->setStatusCode(IResponse::HTTP_BAD_REQUEST)->respondWithError($message);
	}

	public function respondInternalError ($message = 'Internal Error')
	{
		return $this->setStatusCode(IResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
	}

	public function respondFailedValidation($message = 'Failed validation')
	{
		return $this->setStatusCode(IResponse::HTTP_UNPROCESSABLE_ENTITY)->respondWithError($message);
	}

	public function respondOK($message = 'Operation succeeded', $data)
	{
		return $this->setStatusCode(IResponse::HTTP_OK)->respond([
											'message' => $message,
											'status_code'	=> $this->getStatusCode(),
											'data' => $data
											]);
	}

	public function respondCreated($message = 'Successfully created', $data) 
	{
		return $this->setStatusCode(IResponse::HTTP_CREATED)->respond([
											'message' => $message,
											'status_code'	=> $this->getStatusCode(),
											'data' => $data
											]);
	}

	public function respondIndex($message = 'Success', $data)
	{
		return $this->setStatusCode(IResponse::HTTP_OK)->respond([
											'message' => $message,
											'status_code'	=> $this->getStatusCode(),
											'data' => $data
											]);
	}
}
