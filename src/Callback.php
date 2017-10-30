<?php

/**
 *      Interface for callback class
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay;

interface Callback
{
        public function check($account);
        public function payment($account, $orderid, $amount);
        public function confirm($paymentid);
        public function cancel($paymentid);
}
