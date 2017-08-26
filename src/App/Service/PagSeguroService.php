<?php
namespace CodeExperts\App\Service;

use GuzzleHttp\Client;

class PagSeguroService
{
	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var array
	 */
	private $data;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	public function makePurchaseRequest()
	{
		$response = $this->client->request('POST', 'v2/transactions',
			[
				'form_data' => $this->data,
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded; charset=ISO-8859-1'
				]
		    ]);
	}
}