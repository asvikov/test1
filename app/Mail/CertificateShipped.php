<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificate;

class CertificateShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;
    public $product;
    /**
     * Create a new message instance.
     * @param Certificate
     * @param Product
     * @return void
     */
    public function __construct(Certificate $certificate, Product $product)
    {
        $this->certificate = $certificate;
        $this->product = $product;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.certificate');
    }
}
