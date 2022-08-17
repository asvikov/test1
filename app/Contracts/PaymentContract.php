<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface PaymentContract {

    /**
     * @param float $amount
     * @param string $description
     * @param array $options
     * @return bool
     */
    public function createTransaction($amount, $description, $options) : bool;


    /**
     * @return string url for payment
     */
    public function getUrlPayment() : string;

    /**
     * @return string
     */
    public function getStatus() : string;

    /**
     * @param Illuminate\Http\Request $request
     * @return array data about transaction. required: /transactionId/amount/...
     */
    public function callback(Request $request) : array;
}
