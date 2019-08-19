<?php
class ControllerExtensionPaymentAzericard extends Controller {
	
	private function get_web_page($url, $data_in){
		
		$options = array(
			//CURLOPT_SSLVERSION     => 3,
			CURLOPT_RETURNTRANSFER => true,		// return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_ENCODING       => "",       // handle all encodings
			//CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			CURLOPT_TIMEOUT        => 120,      // timeout on response
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			//-------to post-------------
			CURLOPT_POST		   => true,
			CURLOPT_POSTFIELDS	   => $data_in,	//$data,
			CURLOPT_SSL_VERIFYPEER => false,    // DONT VERIFY		
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_CAINFO		   => "a.cer",
		);

		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
			$content = curl_exec( $ch );
			$err     = curl_errno( $ch );
			$errmsg  = curl_error( $ch );
			$header  = curl_getinfo( $ch );
		curl_close( $ch );
			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $content;

		return $header;
		
	}
	
	public function index() {
		
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		// fields
		
		$trtype = '0';
		$country = 'AZ';
		$merch_gmt = '+4';
		$backref = $this->url->link('extension/payment/azericard/callback', '', true);
		$desc = $this->config->get('azericard_desc');
		$merch_name = $this->config->get('azericard_merch_name');
		$merch_url = $this->config->get('azericard_merch_url');
		$terminal = $this->config->get('azericard_terminal');
		$email = $this->config->get('azericard_email');
		$key = $this->config->get('azericard_key');
		
		$amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$currency = $order_info['currency_code'];
		$order_id = '00000' . $this->session->data['order_id'];
		$oper_time = gmdate("YmdHis");
		$nonce = substr(md5(rand()),0,16);
		
		$action = 'https://mpi.3dsecure.az/cgi-bin/cgi_link';
		if((int)$this->config->get('azericard_testing')){
			$action = 'https://testmpi.3dsecure.az/cgi-bin/cgi_link';
		}
		
		$to_sign = "".strlen($amount).$amount
			.strlen($currency).$currency
			.strlen($order_id).$order_id
			.strlen($desc).$desc
			.strlen($merch_name).$merch_name
			.strlen($merch_url).$merch_url
			.strlen($terminal).$terminal
			.strlen($email).$email
			.strlen($trtype).$trtype
			.strlen($country).$country
			.strlen($merch_gmt).$merch_gmt
			.strlen($oper_time).$oper_time
			.strlen($nonce).$nonce
			.strlen($backref).$backref;

		$p_sign = hash_hmac('sha1', $to_sign, hex2bin($key));
		
		$lang = strtoupper($this->language->get('code'));
		
		$data_add = array(
			'action' => $action,
			'trtype' => $trtype,
			'country' => $country,
			'merch_gmt' => $merch_gmt,
			'backref' => $backref,
			'desc' => $desc,
			'merch_name' => $merch_name,
			'merch_url' => $merch_url,
			'terminal' => $terminal,
			'email' => $email,
			'p_sign' => $p_sign,
			'amount' => $amount,
			'currency' => $currency,
			'order_id' => $order_id,
			'oper_time' => $oper_time,
			'nonce' => $nonce,
			'lang' => $lang
		);
		$data = array_merge($data_add, $data);
		
		return $this->load->view('extension/payment/azericard', $data);
		
	}

	public function callback() {
		
		// check if success
		
		if(isset($this->request->post['ACTION'])){
			$ACTION = $this->request->post['ACTION'];
			if($ACTION != "0"){
				die("ACTION Required!");
			}
		}
		else{
			die("ACTION Required!");
		}
		
		// get variables
		
		$oper_time = gmdate("YmdHis");		
		$nonce = substr(md5(rand()), 0, 16);
		
		$AMOUNT = $this->request->post['AMOUNT'];
		$CURRENCY = $this->request->post['CURRENCY'];
		$ORDER = $this->request->post['ORDER'];
		$RRN = $this->request->post['RRN'];
		$INT_REF = $this->request->post['INT_REF'];
		$TERMINAL = $this->config->get('azericard_terminal');
		$TRTYPE = '21';
		
		$action = 'https://mpi.3dsecure.az/cgi-bin/cgi_link';
		if((int)$this->config->get('azericard_testing')){
			$action = 'https://testmpi.3dsecure.az/cgi-bin/cgi_link';
		}
		
		// set post
		
		$Post_Data = array();

		$Post_Data["AMOUNT"] = $AMOUNT;
		$Post_Data["CURRENCY"] = $CURRENCY;
		$Post_Data["ORDER"] = $ORDER;
		$Post_Data["RRN"] = $RRN;
		$Post_Data["INT_REF"] = $INT_REF;
		$Post_Data["TERMINAL"] = $TERMINAL;
		$Post_Data["TRTYPE"] = $TRTYPE;
		$Post_Data["TIMESTAMP"] = $oper_time;
		$Post_Data["NONCE"] = $nonce;

		$to_sign = "" . strlen($ORDER) . $ORDER .
					strlen($AMOUNT) . $AMOUNT .
					strlen($CURRENCY) . $CURRENCY .
					strlen($RRN) . $RRN .
					strlen($INT_REF) . $INT_REF .
					strlen($TRTYPE) . $TRTYPE .
					strlen($TERMINAL) . $TERMINAL .
					strlen($oper_time) . $oper_time .
					strlen($nonce) . $nonce;

		$key_for_sign = $this->config->get('azericard_key');
		$p_sign = hash_hmac('sha1', $to_sign, hex2bin($key_for_sign));

		$Post_Data["P_SIGN"] = $p_sign;

		$Post = array();
		foreach($Post_Data as $key => $value){
			$Post[] = "$key=$value";
		}
		$Post = implode("&",$Post);
		
		// get curl response

		$result = $this->get_web_page($action, $Post);
		
		// change status
		
		if($result['content'] == '0'){
			$this->load->model('checkout/order');
			$order_id = substr((string)$ORDER, 5);
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('azericard_order_status_id'), '', true);
		}
		
	}
	
	public function reversal() {
		
		$this->request->post['AMOUNT'] = (float)549.00;
		$this->request->post['CURRENCY'] = 944;
		$this->request->post['ORDER'] = '00000144';
		$this->request->post['RRN'] = '906476728002';
		$this->request->post['INT_REF'] = '9B488B910C46930A';
		
		// get fields required
		
		if(isset($this->request->post['AMOUNT'])){
			$AMOUNT = $this->request->post['AMOUNT'];
		}
		else{
			die("AMOUNT Required!");
		}
		
		if(isset($this->request->post['CURRENCY'])){
			$CURRENCY = $this->request->post['CURRENCY'];
		}
		else{
			die("CURRENCY Required!");
		}
		
		if(isset($this->request->post['ORDER'])){
			$ORDER = $this->request->post['ORDER'];
		}
		else{
			die("ORDER Required!");
		}
		
		if(isset($this->request->post['RRN'])){
			$RRN = $this->request->post['RRN'];
		}
		else{
			die("RRN Required!");
		}
		
		if(isset($this->request->post['INT_REF'])){
			$INT_REF = $this->request->post['INT_REF'];
		}
		else{
			die("INT_REF Required!");
		}
		
		$TERMINAL = $this->config->get('azericard_terminal');
		$TRTYPE = '22';
		
		$oper_time = gmdate("YmdHis");
		$nonce = substr(md5(rand()), 0, 16);
		
		$action = 'https://mpi.3dsecure.az/cgi-bin/cgi_link';
		if((int)$this->config->get('azericard_testing')){
			$action = 'https://testmpi.3dsecure.az/cgi-bin/cgi_link';
		}
		
		echo "
			<form ACTION=\"{$action}\" METHOD=\"POST\">
				<input name=\"AMOUNT\" value=\"{$AMOUNT}\" type=\"hidden\">
				<input name=\"CURRENCY\" value=\"{$CURRENCY}\" type=\"hidden\">
				<input name=\"ORDER\" value=\"{$ORDER}\" type=\"hidden\">
				<input name=\"RRN\" value=\"{$RRN}\" type=\"hidden\">
				<input name=\"INT_REF\" value=\"{$INT_REF}\" type=\"hidden\">	
				<input name=\"TERMINAL\" value=\"{$TERMINAL}\" type=\"hidden\">
				<input name=\"TRTYPE\" value=\"{$TRTYPE}\" type=\"hidden\">    
				<input name=\"TIMESTAMP\" value=\"$oper_time\" type=\"hidden\">
				<input name=\"NONCE\" value=\"$nonce\" type=\"hidden\">
		";
		
		$to_sign = "" . strlen($ORDER) . $ORDER
					.strlen($AMOUNT) . $AMOUNT
					.strlen($CURRENCY) . $CURRENCY
					.strlen($RRN) . $RRN
					.strlen($INT_REF) . $INT_REF
					.strlen($TRTYPE) . $TRTYPE
					.strlen($TERMINAL) . $TERMINAL
					.strlen($oper_time) . $oper_time
					.strlen($nonce) . $nonce;

		$key_for_sign = $this->config->get('azericard_key');
		$p_sign = hash_hmac('sha1', $to_sign, hex2bin($key_for_sign));
		
		echo "
				<input name=\"P_SIGN\" value=\"$p_sign\" type=\"hidden\">
				<input alt=\"Submit\" type=\"submit\">
			</form>
		";
		
	}
	
}