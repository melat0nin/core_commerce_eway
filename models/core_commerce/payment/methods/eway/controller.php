<?php

defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');

class CoreCommerceEWayPaymentMethodController extends CoreCommercePaymentController {

    public function method_form() {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('PAYMENT_METHOD_EWAY_API_LOGIN', $pkg->config('PAYMENT_METHOD_EWAY_API_LOGIN'));
        $this->set('PAYMENT_METHOD_EWAY_API_TYPE', $pkg->config('PAYMENT_METHOD_EWAY_API_TYPE'));
        $this->set('PAYMENT_METHOD_EWAY_LIVE', $pkg->config('PAYMENT_METHOD_EWAY_LIVE'));
        $this->set('PAYMENT_METHOD_EWAY_MODE', $pkg->config('PAYMENT_METHOD_EWAY_MODE'));
    }

    public function validate() {
        $e = parent::validate();
        $ve = Loader::helper('validation/strings');

        if ($this->post('PAYMENT_METHOD_EWAY_API_LOGIN') == '') {
            $e->add(t('You must specify your E-Way Login ID.'));
        }

        return $e;
    }

    public function save() {
        $pkg = Package::getByHandle('core_commerce');
        $pkg->saveConfig('PAYMENT_METHOD_EWAY_API_LOGIN', $this->post('PAYMENT_METHOD_EWAY_API_LOGIN'));
        $pkg->saveConfig('PAYMENT_METHOD_EWAY_API_TYPE', $this->post('PAYMENT_METHOD_EWAY_API_TYPE'));
        $pkg->saveConfig('PAYMENT_METHOD_EWAY_LIVE', $this->post('PAYMENT_METHOD_EWAY_LIVE'));
        $pkg->saveConfig('PAYMENT_METHOD_EWAY_MODE', $this->post('PAYMENT_METHOD_EWAY_MODE'));
    }

    public function set_base_fields($pkg, $u, $ui, $o) {
        $pkg = Package::getByHandle('core_commerce');

        if ($pkg->config('PAYMENT_METHOD_EWAY_API_TYPE') == 'HOSTED') {
            $eWayClass = 'EwayPaymentHosted';
        } else {
            $eWayClass = 'EwayPaymentLive';
        }

        Loader::library($eWayClass, 'core_commerce_eway');

        $eway = new $eWayClass($pkg->config('PAYMENT_METHOD_EWAY_API_LOGIN'), $pkg->config('PAYMENT_METHOD_EWAY_MODE'), $pkg->config('PAYMENT_METHOD_EWAY_TEST'));

        $invoiceRef = date('ymdGis') . $u->getUserID();
        $eway->setTransactionData("TotalAmount", $o->getOrderTotal());
        $eway->setTransactionData("CustomerInvoiceDescription", t('Purchase from %s', SITE));
        $eway->setTransactionData("CustomerInvoiceRef", $invoiceRef);
        $eway->setTransactionData("TrxnNumber", $o->getOrderID());

        if ($ui && $ui->getUserEmail()) {
            $eway->setTransactionData("CustomerEmail", $ui->getUserEmail());
        }

        $eway->setTransactionData("CustomerFirstName", $o->getAttribute('billing_first_name'));
        $eway->setTransactionData("CustomerLastName", $o->getAttribute('billing_last_name'));

        $address = $o->getAttribute('billing_address')->getAddress1() . "\n" .
                $o->getAttribute('billing_address')->getAddress2() . "\n" .
                $o->getAttribute('billing_address')->getCity() . ", " .
                $o->getAttribute('billing_address')->getStateProvince() . "\n" .
                $o->getAttribute('billing_address')->getCountry();

        $eway->setTransactionData("CustomerAddress", $address);
        $eway->setTransactionData("CustomerPostcode", $o->getAttribute('billing_address')->getPostalCode());

        $eway->setTransactionData("Option1", $invoiceRef);
        $eway->setTransactionData("Option2", "");
        $eway->setTransactionData("Option3", "");

        return $eway;
    }

    public function form() {
        $pkg = Package::getByHandle('core_commerce');
        Loader::model('order/current', 'core_commerce');

        $o = CoreCommerceCurrentOrder::get();
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $eway = $this->set_base_fields($pkg, $u, $ui, $o);

        if ($pkg->config('PAYMENT_METHOD_EWAY_API_TYPE') == 'HOSTED') {
            $eway->setTransactionData('URL', $this->action('hosted_complete'));

            $this->set('action', $eway->getTransactionURL());
            $this->set('fields', $eway->getTransactionData());
            $this->set('form', 'hosted');
        } else {
            $this->set('action', $this->action('submit'));
            $this->set('form', 'xml');
        }
    }

    public function action_hosted_complete() {
        $pkg = Package::getByHandle('core_commerce');
        Loader::model('order/current', 'core_commerce');

        $o = CoreCommerceCurrentOrder::get();
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());

        $this->processResponse($_REQUEST, $o, $u, $ui);
    }

    public function action_submit() {

        $pkg = Package::getByHandle('core_commerce');
        Loader::model('order/current', 'core_commerce');

        $o = CoreCommerceCurrentOrder::get();
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $eway = $this->set_base_fields($pkg, $u, $ui, $o);

        $eway->setTransactionData("CardHoldersName", $this->post('x_card_name'));
        $eway->setTransactionData("CardNumber", $this->post('x_card_num'));
        $eway->setTransactionData("CardExpiryMonth", $this->post('x_exp_date_mon'));
        $eway->setTransactionData("CardExpiryYear", $this->post('x_exp_date_year'));
        $eway->setTransactionData("CVN", $this->post('x_card_code'));

        $response = $eway->doPayment();

        $this->processResponse(array('ewayTrxnStatus' => $response['EWAYTRXNSTATUS'],
            'ewayTrxnNumber' => $response['EWAYTRXNNUMBER'],
            'ewayTrxnReference' => $response['EWAYTRXNREFERENCE'],
            'eWAYresponseCode' => null,
            'eWAYresponseText' => $response['EWAYTRXNERROR'],
            'eWAYauthCode' => $response['EWAYAUTHCODE'],
            'eWAYoption1' => $response['EWAYTRXNOPTION1']), $o, $u, $ui);
    }

    public function processResponse($data, $o, $u, $ui) {

        if ($data['ewayTrxnStatus'] == 'True') {
            $o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);
            $finData = array('Invoice' => $data['eWAYoption1'],
                'Auth Code' => $data['eWAYauthCode'],
                'Transaction ID' => $data['ewayTrxnNumber']);
            parent::finishOrder($o, 'E-Way', $finData);
            $this->redirect('/checkout/finish');
        } else {

            if (empty($data['eWAYresponseCode'])) {
                if (substr($data['eWAYresponseText'], 2, 1) == ',') {
                    list($errcode, $desc) = split(',', $data['eWAYresponseText']);
                    $data['eWAYresponseCode'] = $errcode;
                    $data['eWAYresponseText'] = $desc;
                } else {
                    $data['eWAYresponseCode'] = '-1';
                }
            }

            $generr = array('04', '06', '07', '12', '13', '19', '21', '22', '23', '25', '30', '31', '33', '34', '35', '36', '37', '38', '40',
                '42', '43', '57', '58', '59', '60', '63', '64', '66', '67', '92', '93', '96');
            if (in_array($errcode, $generr)) {
                $err = array('orderID' => $o->orderID, 'responseCode' => $data['eWAYresponseCode'],
                    'responseReasonCode' => $data['eWAYresponseCode'], 'responseReasonText' => $data['eWAYresponseText']);
                Log::addEntry('E Way transaction Error: ' . var_export($err, true));
                $err = "This transaction could not be completed at this time.  Please try again later.";
            } else {
                $err = $data['eWAYresponseText'];
            }
            $this->redirect('/checkout/payment/form?error=' . urlencode($err));
        }
    }

}
