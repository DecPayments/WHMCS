<?php
 function decpayments_config() {
  return array(
   'FriendlyName' => array('Type' => 'System', 'Value' => 'DecPayments'),
   'token' => array('FriendlyName' => 'Token', 'Type' => 'text', 'Size' => '50'),
   'cryptocurrencies' => array('FriendlyName' => 'Which cryptocurrencies you want to accept?', 'Type' => 'dropdown', 'Options' => 'Decred/Vcash,Decred,Vcash'),
   'language' => array('FriendlyName' => 'Language', 'Type' => 'dropdown', 'Options' => 'English (US),Português (BR)'),
   'fee' => array('FriendlyName' => 'Who will pay the fee?', 'Type' => 'dropdown', 'Options' => 'Me,My client'),
  );
 }
 function decpayments_link($params) {
  $cryptocurrencies = array('Decred/Vcash', 'Decred', 'Vcash');
  if(!in_array($params['cryptocurrencies'], $cryptocurrencies)) {
    $crypto_pref = '<option value="DCR">Decred</option><option value="XVC">Vcash</option>';
  } else {
    if($params['cryptocurrencies'] === 'Decred/Vcash') {
      $crypto_pref = '<option value="DCR">Decred</option><option value="XVC">Vcash</option>';
    }
    if($params['cryptocurrencies'] === 'Decred') {
      $crypto_pref = '<option value="DCR">Decred</option>';
    }
    if($params['cryptocurrencies'] === 'Vcash') {
      $crypto_pref = '<option value="XVC">Vcash</option>';
    }
  }
  $languages = array('English (US)', 'Português (BR)');
  if(!in_array($params['language'], $languages)) {
    $language = 'en_US';
  } else {
    if($params['language'] === 'English (US)') {
      $language = 'en_US';
    }
    if($params['language'] === 'Português (BR)') {
      $language = 'pt_BR';
    }
  }
  $fee_options = array('Me', 'My client');
  if(!in_array($params['fee'], $fee_options)) {
    $fee_option = 'U';
  } else {
    if($params['fee'] === 'Me') {
      $fee_option = 'U';
    }
    if($params['fee'] === 'My client') {
      $fee_option = 'C';
    }
  }
  $result = '<form action="https://decpayments.com/invoice" method="POST">' . "\n";
  $result .= '    <input type="hidden" name="token" value="'.$params['token'].'">' . "\n";
  $result .= '    <input type="hidden" name="language" value="'.$language.'">' . "\n";
  $result .= '    <input type="hidden" name="currency" value="'.$params['currency'].'">' . "\n";
  $result .= '    <input type="hidden" name="amount" value="'.$params['amount'].'">' . "\n";
  $result .= '    <select name="crypto">'.$crypto_pref.'</select>' . "\n";
  $result .= '    <input type="hidden" name="description" value="'.$params['invoiceid'].'">' . "\n";
  $result .= '    <input type="hidden" name="failcallback" value="'.$params['systemurl'].'/viewinvoice.php?id='.$params['invoiceid'].'">' . "\n";
  $result .= '    <input type="hidden" name="successcallback" value="'.$params['systemurl'].'/viewinvoice.php?id='.$params['invoiceid'].'">' . "\n";
  $result .= '    <input type="hidden" name="notificationurl" value="'.$params['systemurl'].'/modules/gateways/'.basename(__FILE__).'">' . "\n";
  $result .= '    <input type="hidden" name="fee" value="'.$fee_option.'">' . "\n";
  $result .= '    <input type="submit" value="'.$params['langpaynow'].'">' . "\n";
  $result .= '</form>' . "\n";
  return $result;
 }
 if(isset($_POST['cryptocurrency']) AND isset($_POST['address']) AND isset($_POST['TX']) AND isset($_POST['required']) AND isset($_POST['paid']) AND isset($_POST['amount']) AND isset($_POST['description'])) {
  require("../../init.php");
  require("../../includes/invoicefunctions.php");
  require("../../includes/gatewayfunctions.php");
  $GATEWAY = getGatewayVariables('decpayments');
  $invoiceid = $_POST['description'];
  $required = $_POST['required'];
  $paid = $_POST['paid'];
  $amount = $_POST['amount'];
  $TX = $_POST['TX'];
  if($required === $paid) {
    logTransaction($GATEWAY['name'], 'ID: '.$invoiceid.'<br>Required: '.$required.'<br>Paid: '.$paid.'', 'Successful');
    addInvoicePayment($invoiceid, $TX, $amount, 0, 'decpayments');
  }
  if($required > $paid) {
    logTransaction($GATEWAY['name'], 'ID: '.$invoiceid.'<br>Required: '.$required.'<br>Paid: '.$paid.'', 'Incomplete');
    addInvoicePayment($invoiceid, $TX, $amount, 0, 'decpayments');
  }
  if($required < $paid) {
    logTransaction($GATEWAY['name'], 'ID: '.$invoiceid.'<br>Required: '.$required.'<br>Paid: '.$paid.'', 'Alert');
    addInvoicePayment($invoiceid, $TX, $amount, 0, 'decpayments');
  }
 }
?>
