<?php

abstract class EasyPay_Callback
{
        
        function Check($account)
        {
                return null;
        }
        
        function Payment($orderid, $account, $amount)
        {
                return null;
        }
        
        function Confirm($paymentid)
        {
                return null;
        }
        
        function Cancel($paymentid)
        {
                return null;
        }
}