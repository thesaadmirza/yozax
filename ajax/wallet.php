<?php

if ($yx['loggedin'] == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
}

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

$payment_currency = "USD";
$payer = new Payer();
$item = new Item();
$itemList = new ItemList();
$details = new Details();
$amount = new Amount();
$transaction = new Transaction();
$redirectUrls = new RedirectUrls();
$payment = new Payment();
$payer->setPaymentMethod('paypal');

if ($s == 'upto') {

    $data = array('status' => 400);
    $request = (!empty($_POST['amount']) && is_numeric($_POST['amount']));
    $rep_amount = $_POST['amount'];

    if ($request === true) {
        if ($_POST['payment_via'] == 'stripe') {
            $randSession = generateRandomString();
            $_SESSION['stripe_rand_id'] = $randSession;
            $_SESSION['stripe_amount'] = $rep_amount; 
            $session = \Stripe\Checkout\Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                        'name' => 'Replenish your balance',
                        'description' => 'Replenish your balance',
                        'images' => [YX_Link('themes/default/img/logo.png')],
                        'amount' => $rep_amount*100,
                        'currency' => $payment_currency,
                        'quantity' => 1,
                            ]],
                        'success_url' => YX_Link("ajax_requests.php?f=wallet&s=get_paid_stripe&status=success&session_id=$randSession&userid={$_GET['user_id']}"),
                        'cancel_url' => $_GET['urlc'],
            ]);

            $_SESSION['stripe_payment_details'] = $session;
            $data = array(
                'status' => 200,
                'type' => 'SUCCESS',
                'url' => "stripe",
                'key' => $session->id
            );
            echo json_encode($data);
            exit();
        }



        $redirectUrl = YX_Link("ajax_requests.php?f=wallet&s=get_paid&status=success&amount=$rep_amount");
        $redirectUrls->setReturnUrl($redirectUrl);
        $redirectUrls->setCancelUrl(YX_Link(''));

        $item->setName('Replenish your balance');
        $item->setQuantity(1);
        $item->setPrice($rep_amount);
        $item->setCurrency($payment_currency);

        $itemList->setItems(array($item));
        $details->setSubtotal($rep_amount);

        $amount->setCurrency($payment_currency);
        $amount->setTotal($rep_amount);
        $amount->setDetails($details);

        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);
        $transaction->setDescription('Replenish your balance');
        $transaction->setInvoiceNumber(time());

        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrls);
        $payment->setTransactions(array($transaction));

        try {
            $payment->create($paypal);
        } catch (Exception $e) {
            $data = array(
                'status' => 500,
                'details' => json_decode($e->getData())
            );

            if (empty($data['details'])) {
                $data['details'] = json_decode($e->getCode());
            }
            echo json_encode($data);
            exit();
        }

        $data = array(
            'status' => 200,
            'type' => 'SUCCESS',
            'url' => $payment->getApprovalLink()
        );
    }
}


if ($s == 'get_paid_stripe') {
    if ($_SESSION['stripe_rand_id'] == $_GET['session_id'] && $_GET['status'] == 'success') {
        //userid

        $amount = $_SESSION['stripe_amount'];
        $update = array('wallet' => ($yx['user']['wallet'] += $amount));

        $db->where('user_id', $yx['user']['id'])->update(T_USERS, $update);
        $_SESSION['refilled_balance'] = $amount;

        $payment_data = array(
            'user_id' => $yx['user']['id'],
            'type' => 'wallet',
            'amount' => $amount,
            'date' => date('n') . '/' . date('Y'),
            'expire' => 0
        );

        $db->insert(T_PAYMENTS, $payment_data);

        $url = YX_Link('settings/wallet');
        header("Location: $url");
        exit();
    }
}

if ($s == 'get_paid') {
    $data['status'] = 500;

    $request = array();
    $request[] = (!empty($_GET['paymentId']) && !empty($_GET['PayerID']));
    $request[] = (!empty($_GET['status']) && $_GET['status'] == 'success');
    $request[] = (!empty($_GET['amount']) && is_numeric($_GET['amount']));
    $request = (!in_array(false, $request) === true);


    if ($request === true) {

        $paymentId = YX_Secure($_GET['paymentId']);
        $PayerID = YX_Secure($_GET['PayerID']);
        $payment = Payment::get($paymentId, $paypal);
        $execute = new PaymentExecution();
        $execute->setPayerId($PayerID);

        try {
            $result = $payment->execute($execute, $paypal);
        } catch (Exception $e) {
            $data = array(
                'type' => 'ERROR',
                'details' => json_decode($e->getData())
            );

            if (empty($data['details'])) {
                $data['details'] = json_decode($e->getCode());
            }

            echo json_encode($data);
            exit();
        }

        $amount = $_GET['amount'];
        $update = array('wallet' => ($yx['user']['wallet'] += $amount));

        $db->where('user_id', $yx['user']['id'])->update(T_USERS, $update);
        $_SESSION['refilled_balance'] = $amount;

        $payment_data = array(
            'user_id' => $yx['user']['id'],
            'type' => 'wallet',
            'amount' => $amount,
            'date' => date('n') . '/' . date('Y'),
            'expire' => 0
        );

        $db->insert(T_PAYMENTS, $payment_data);

        $url = YX_Link('settings/wallet');
        header("Location: $url");
        exit();
    }
}

function generateRandomString($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
