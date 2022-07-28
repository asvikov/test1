<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatusEnum;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;

class PaymentController extends Controller
{
    public function index() {

        $transactions = Transaction::orderBy('id', 'desc')->get();
        return view('payments.index', ['transactions' => $transactions]);
    }

    /**
     * @param Request $request
     * @param PaymentService $service
     * @throws \YooKassa\Common\Exceptions\ApiException
     * @throws \YooKassa\Common\Exceptions\BadApiRequestException
     * @throws \YooKassa\Common\Exceptions\ExtensionNotFoundException
     * @throws \YooKassa\Common\Exceptions\ForbiddenException
     * @throws \YooKassa\Common\Exceptions\InternalServerError
     * @throws \YooKassa\Common\Exceptions\NotFoundException
     * @throws \YooKassa\Common\Exceptions\ResponseProcessingException
     * @throws \YooKassa\Common\Exceptions\TooManyRequestsException
     * @throws \YooKassa\Common\Exceptions\UnauthorizedException
     */
    public function create(Request $request, PaymentService $service) {

        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $amount = $request->input('amount');
        $description = (string)$request->input('description');

        $transaction = Transaction::created([
            'amount' => $amount,
            'description' => $description
        ]);

        if($transaction) {
            $link = $service->createPayment($amount, $description, [
                'transaction_id' => $transaction->id
            ]);

            //по хорошему обработать ошибки которые могут прийти из $service->createPayment, а также проверить что это действительно ссылка.
            return redirect()->away($link);
        }
    }

    public function callback(Request $request, PaymentService $service) {

        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);
        $notification = (isset($requestBody['event']) && $requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
            ? new NotificationSucceeded($requestBody)
            : new NotificationWaitingForCapture($requestBody);
        $payment = $notification->getObject();

        if(isset($payment->status) && $payment->status === 'waiting_for_capture') {
            $service->getClient()->capturePayment(['amount' => $payment->amount], $payment->id, uniqid('', true));
        }

        if(isset($payment->status) && $payment->status === 'succeeded') {
            if((bool)$payment->paid === true) {
                $metadata = (object)$payment->metadata;
                if(isset($metadata->transaction_id)) {
                    $transactionId = (int)$metadata->transaction_id;
                    $transaction = Transaction::find($transactionId);
                    $transaction->status = PaymentStatusEnum::CONFIRMED;
                    $transaction->save();

                    //тут лучше записывать в базу конечно а не в кеш
                    if(cache()->has('balance')) {
                        cache()->forever('balance', (float)cache()->get('balance') + (float)$payment->amount->value);
                    } else {
                        cache()->forever('balance', (float)$payment->amount->value);
                    }
                }
            }
        }
    }
}
