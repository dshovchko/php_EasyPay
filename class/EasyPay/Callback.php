<?php

namespace EasyPay;

interface Callback
{
        public function check($account);
        public function payment($account, $orderid, $amount);
        public function confirm($paymentid);
        public function cancel($paymentid);
}
