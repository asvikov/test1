<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Contracts\PaymentContract;

class PaymentController extends Controller
{
    public function index() {

        $transactions = Transaction::orderBy('id', 'desc')->get();
        return view('payments.index', ['transactions' => $transactions]);
    }

    public function create(Request $request, PaymentContract $service) {

        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $amount = $request->input('amount');
        $description = (string)$request->input('description');

        $transaction = Transaction::create([
            'amount' => $amount,
            'description' => $description,
            'user_id' => 1
        ]);

        if($transaction) {
            $res_transaction = $service->createTransaction($amount, $description, [
                'transaction_id' => $transaction->id
            ]);

            if($res_transaction) {
                return redirect()->away($service->getUrlPayment());
            } else {
                return redirect('/')->withErrors('message', 'Payment is not success. Try again.');
            }
        }
    }

    public function callback(Request $request, PaymentContract $service) {

        $transaction_data = $service->callback($request);
        if($service->getStatus() === PaymentStatusEnum::CONFIRMED) {
            $transaction = Transaction::find($transaction_data['transactionId']);
            $transaction->status = PaymentStatusEnum::CONFIRMED;
            $transaction->save();
        }


        //тут лучше записывать в базу конечно а не в кеш
        if(cache()->has('balance')) {
            cache()->forever('balance', (float)cache()->get('balance') + $transaction_data['amount']);
        } else {
            cache()->forever('balance', $transaction_data['amount']);
        }
    }
}
