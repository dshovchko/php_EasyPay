<?php

interface EasyPay_Callback
{
        public function check($account);
        public function payment($orderid, $account, $amount);
        public function confirm($paymentid);
        public function cancel($paymentid);
}
