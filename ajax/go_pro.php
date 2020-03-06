<?php

if ($yx['loggedin'] == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
} else if (!YX_IsAdmin() && ($yx['config']['go_pro'] != 1 || YX_IsPRO() == true)) {
    $data = array(
        'status' => 400,
        'error' => 'Bad request'
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

$country = base64_decode($_COOKIE['r']);

if ($country == "United Kingdom") {
    $payment_currency = "GBP";
}
else if (in_array($country, ['Austria', 'Belgium', 'Cyrpus', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Portugal', 'Slovakia', 'Slovenia', 'Spain'], true )) {
    $payment_currency = "EUR";
}
else if ($country == "New Zealand") {
    $payment_currency = "NZD";
}
else if ($country == "Australia") {
    $payment_currency = "AUD";
}
else if ($country == "Canada") {
    $payment_currency = "CAD";
}
else{
    $payment_currency = "USD"; //fallack to USD as default
}

$payer = new Payer();
$item = new Item();
$itemList = new ItemList();
$details = new Details();
$amount = new Amount();
$transaction = new Transaction();
$redirectUrls = new RedirectUrls();
$payment = new Payment();
$pkgs = array('pro');
$payer->setPaymentMethod('paypal');
$sum = intval($yx['config']['pro_pkg_price']);


if (!empty($s == 'purchase')) {

    if (!empty($_POST['type']) && $_POST['type'] == 'pro') {

        if ($_POST['payment_via'] == 'stripe') {
            $randSession = generateRandomString();
            $_SESSION['stripe_rand_id'] = $randSession;
            $_SESSION['stripe_amount'] = $sum;
            $session = \Stripe\Checkout\Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                        'name' => 'Yozax Premium Membership Subscription',
                        'description' => 'Purchase pro package',
                        'amount' => $sum * 100,
                        'currency' => $payment_currency,
                        'quantity' => 1,
                            ]],
                        'success_url' => YX_Link("ajax_requests.php?f=go_pro&s=get_paid_stripe&pkg=pro&status=success&session_id=$randSession&userid={$yx['user']['id']}"),
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

        $redirectUrls->setReturnUrl(YX_Link('ajax_requests.php?f=go_pro&s=get_paid&status=success&pkg=pro'))->setCancelUrl(YX_Link(''));
        $item->setName('Yozax Premium Membership Subscription')->setQuantity(1)->setPrice($sum)->setCurrency($payment_currency);
        $itemList->setItems(array($item));
        $details->setSubtotal($sum);
        $amount->setCurrency($payment_currency)->setTotal($sum)->setDetails($details);
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Purchase pro package')->setInvoiceNumber(time());
        $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
            $transaction
        ));

        try {
            $payment->create($paypal);
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

        $data = array(
            'status' => 200,
            'type' => 'SUCCESS',
            'url' => $payment->getApprovalLink()
        );
    }
} else if ($s == 'get_paid') {
    $data['status'] = 500;
    $request = (!empty($_GET['paymentId']) && !empty($_GET['PayerID']) && !empty($_GET['status']) && $_GET['status'] == 'success');
    $pkg = (!empty($_GET['pkg']) && in_array($_GET['pkg'], $pkgs));

    if ($request && $pkg) {
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

        $update = array('is_pro' => 1, 'verified' => 1);
        $go_pro = $db->where('user_id', $yx['user']['id'])->update(T_USERS, $update);

        if ($go_pro === true) {
            $pkg_type = YX_Secure($_GET['pkg']);
            $payment_data = array(
                'user_id' => $yx['user']['id'],
                'type' => $pkg_type,
                'amount' => $sum,
                'date' => date('n') . '/' . date('Y'),
                'expire' => strtotime("+30 days")
            );

            $db->insert(T_PAYMENTS, $payment_data);

            $db->where('user_id', $yx['user']['id'])->update(T_LISTS, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_QUIZZES, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_VIDEOS, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_MUSIC, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_POLLS_PAGES, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_NEWS, array('featured' => 1));

            $_SESSION['upgraded'] = true;
            header('Location: ' . YX_Link('go_pro'));
            exit();
        }
    }
}else if ($s == 'get_paid_stripe') {
    if ($_SESSION['stripe_rand_id'] == $_GET['session_id'] && $_GET['status'] == 'success') {
        //userid

        $amount = $_SESSION['stripe_amount'];
        
        $update = array('is_pro' => 1, 'verified' => 1);
        $go_pro = $db->where('user_id', $yx['user']['id'])->update(T_USERS, $update);

        if ($go_pro === true) {
            $pkg_type = YX_Secure($_GET['pkg']);
            $payment_data = array(
                'user_id' => $yx['user']['id'],
                'type' => $pkg_type,
                'amount' => $sum,
                'date' => date('n') . '/' . date('Y'),
                'expire' => strtotime("+30 days")
            );

            $db->insert(T_PAYMENTS, $payment_data);

            $db->where('user_id', $yx['user']['id'])->update(T_LISTS, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_QUIZZES, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_VIDEOS, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_MUSIC, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_POLLS_PAGES, array('featured' => 1));
            $db->where('user_id', $yx['user']['id'])->update(T_NEWS, array('featured' => 1));

            $_SESSION['upgraded'] = true;
            header('Location: ' . YX_Link('go_pro'));
            exit();
        }
    }
}


if (YX_IsAdmin() && $s == 'remove_expired') {

    $db->where('expire', 0, '<>');

    $data['status'] = 400;
    $expired_subs = $db->where('expire', time(), '<')->get(T_PAYMENTS);
    $update = array('is_pro' => 0, 'verified' => 0);

    foreach ($expired_subs as $subscriber) {
        $db->where('id', $subscriber->id)->update(T_PAYMENTS, array('expire' => 0));
        $db->where('user_id', $subscriber->user_id)->update(T_USERS, $update);
        $db->where('user_id', $subscriber->user_id)->update(T_LISTS, array('featured' => 0));
        $db->where('user_id', $subscriber->user_id)->update(T_QUIZZES, array('featured' => 0));
        $db->where('user_id', $subscriber->user_id)->update(T_VIDEOS, array('featured' => 0));
        $db->where('user_id', $subscriber->user_id)->update(T_MUSIC, array('featured' => 0));
        $db->where('user_id', $subscriber->user_id)->update(T_POLLS_PAGES, array('featured' => 0));
        $db->where('user_id', $subscriber->user_id)->update(T_NEWS, array('featured' => 0));
    }

    $data['status'] = 200;
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