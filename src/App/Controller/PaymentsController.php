<?php
namespace CodeExperts\App\Controller;

use CodeExperts\App\Entity\Product;
use CodeExperts\DB\Connection;
use CodeExperts\MVC\BaseController;
use CodeExperts\MVC\View;
use CodeExperts\Tools\Session;

class PaymentsController extends BaseController
{
	private function initPagSeguro()
	{
		(new Session())->sessionStart();
//unset($_SESSION['session_pagseguro']);
		if(!isset($_SESSION['session_pagseguro'])) {
			$this->getConfig('pagseguro');

			//Init PagSeguro
			\PagSeguroLibrary::init();
			\PagSeguroConfig::init();
			\PagSeguroResources::init();

			//Session ID
			$credentitals = \PagSeguroConfig::getAccountCredentials();
			$sessionToken = \PagSeguroSessionService::getSession($credentitals);

			$_SESSION['session_pagseguro'] = $sessionToken;
		}

	}

	public function index()
	{
		$this->initPagSeguro();

		$view = new View(VIEWS_FOLDER . 'site/payments.phtml');

		return $view->render();
	}

	public function proccess()
	{
//		$customer = [
//			'name' => filter_input(INPUT_POST, 'name'),
//			'email' => filter_input(INPUT_POST, 'email'),
//			'hash' => filter_input(INPUT_POST, 'hash'),
//			'phoneAreaCode' => filter_input(INPUT_POST, 'phoneAreaCode'),
//			'phoneNumber' => filter_input(INPUT_POST, 'phoneNumber'),
//			'documentsValue'=>filter_input(INPUT_POST, 'cpf')
//		];
//		$shipping = [
//			'street' => 'Av. PagSeguro',
//			'number' => 99,
//			'complement' => '99o andar',
//			'district' => 'Jardim Internet',
//			'postalCode' => 99999999,
//			'city' => 'Cidade Exemplo',
//			'state' => 'SP',
//			'country' => 'BRA'
//		];
//		$payment = [
//			'token' => filter_input(INPUT_POST, 'token'),
//			'name'  => filter_input(INPUT_POST, 'name'),
//			'birthDate'=>'20/12/1990',
//			'documents'=>'97998185325',
//			'phoneAreaCode' => '11',
//			'phoneNumber' => '999999999',
//		];
//
////		https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/ -d\
//$data['email'] = "suporte@lojamodelo.com.br"
//$data['token'] = "57BE455F4EC148E5A54D9BB91C5AC12C";
//$data['receiverEmail'] = "suporte@lojamodelo.com.br";
//$data['extraAmount'] = "1.00";
//$data['itemId1'] = "0001";
//$data['itemDescription1'] ="Produto PagSeguroI";
//$data['itemAmount1'] ="99999.99";
//$data['itemQuantity1'] ="1";
//$data['itemId1'] ="0002";
//$data['itemDescription1'] ="Produto PagSeguroII";
//$data['itemAmount1'] ="99999.98";
//$data['itemQuantity1'] ="2";
//$data['notificationURL'] ="https://sualoja.com.br/notifica.html";
//$data['reference'] ="REF1234";
//$data['senderName'] ="Jose Comprador";
//$data['senderCPF'] ="11475714734";
//$data['senderAreaCode'] ="99";
//$data['senderPhone'] ="99999999";
//$data['senderEmail'] ="comprador@uol.com.br";
//$data['senderHash'] ="52578d5d3336ec7a43ff1dae4794d0c5625feddcc8fbc0e80bcb0cb46c9947d4";
//$data['shippingAddressStreet'] ="Av. PagSeguro";
//$data['shippingAddressNumber'] ="9999";
//$data['shippingAddressComplement'] ="99o andar";
//$data['shippingAddressDistrict'] ="Jardim Internet";
//$data['shippingAddressPostalCode'] ="99999999";
//$data['shippingAddressCity'] ="Cidade Exemplo";
//$data['shippingAddressState'] ="SP";
//$data['shippingAddressCountry'] ="ATA";
//$data['shippingType'] ="1";
//$data['shippingCost'] ="21.50";
//$data['creditCardToken'] ="1e358d39e26448dc8a28d0f1815f08c5";
//$data['installmentQuantity'] ="1";
//$data['installmentValue'] ="300021.45";
//$data['noInterestInstallmentQuantity'] ="2";
//$data['creditCardHolderName'] ="Jose Comprador";
//$data['creditCardHolderCPF'] ="11475714734";
//$data['creditCardHolderBirthDate'] ="01/01/1900";
//$data['creditCardHolderAreaCode'] ="99";
//$data['creditCardHolderPhone'] ="99999999";
//$data['billingAddressStreet'] ="Av. PagSeguro";
//$data['billingAddressNumber'] ="9999";
//$data['billingAddressComplement'] ="99o andar";
//$data['billingAddressDistrict'] ="Jardim Internet";
//$data['billingAddressPostalCode'] ="99999999";
//$data['billingAddressCity'] ="Cidade Exemplo";
//$data['billingAddressState'] ="SP";
//$data['billingAddressCountry']="ATA";
		$paymentDirect = new \PagSeguroDirectPaymentRequest();
		$paymentDirect->setPaymentMode('DEFAULT');
		$paymentDirect->setPaymentMethod('creditCard');
		$paymentDirect->setCurrency('BRL');

		//gerar order
		$order = 1;

		foreach($products as $prod) {
			$aProd = (new Product(Connection::getInstance($this->getConfig('database'))))->find($prod);

			$paymentDirect->addItem($order, $aProd['name'], 1, $aProd['price']);
		}

		$paymentDirect->setSender(
				'Jose Comprador',
				'joao@sandbox.pagseguro.com.br',
				'11',
				'984283645',
				'CPF',
				'000.000.000-00'
		);

		$paymentDirect->getSenderHash($hash);

	}
}