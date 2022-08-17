<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;
use App\Contracts\PaymentContract;

class PaymentService implements PaymentContract {

    public $status = PaymentStatusEnum::CREATED;
    public $url_payment = '/';

    public function getClient(): Client {

        $client = new Client();
        $client->setAuth(config('yookassa.shop_id'), config('yookassa.secret_key'));

        return $client;
    }

    /**
     * @param float $amount
     * @param string $description
     * @param array $options
     * @return string
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
    //по хорошему обработать ошибки которые могут проброситься, а также проверить что это действительно ссылка.
    public function createTransaction($amount, $description, $options) : bool {

        $client = $this->getClient();
        $payment = $client->createPayment([
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB'
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('payment.index')
            ],
            'capture' => false,
            'metadata' => [
                'transaction_id' => $options['transaction_id']
            ],
            'description' => $description
        ], uniqid('', true));

        if($payment) {
            $this->url_payment = $payment->getConfirmation()->getConfirmationUrl();
            return true;
        } else {
            return false;
        }
    }

    public function callback(Request $request) : array {

        $transaction_data = [
            'transactionId' => 0,
            'amount' => 0
        ];

        $source = file_get_contents('php://input');
        $requestBody = json_decode($source, true);

        $notification = (isset($requestBody['event']) && $requestBody['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
            ? new NotificationSucceeded($requestBody)
            : new NotificationWaitingForCapture($requestBody);
        $payment = $notification->getObject();

        if(isset($payment->status) && $payment->status === 'waiting_for_capture') {
            $this->getClient()->capturePayment(['amount' => $payment->amount], $payment->id, uniqid('', true));
        } elseif(isset($payment->status) && ($payment->status === 'succeeded') && ((bool)$payment->paid === true)) {
            $this->status = PaymentStatusEnum::CONFIRMED;
            $metadata = (object)$payment->metadata;

            if (isset($metadata->transaction_id)) {
                $transaction_data['transactionId'] = (int)$metadata->transaction_id;
                $transaction_data['amount'] = $payment->amount->value ? (float)$payment->amount->value : 0;
            }
        }

        return $transaction_data;
    }

    public function getStatus() : string {

        return $this->status;
    }

    public function getUrlPayment() : string {

        return $this->url_payment;
    }
}
