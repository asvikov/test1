<?php

namespace App\Http\Controllers;

use App\Mail\CertificateShipped;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Product;
use Illuminate\Support\Carbon;
use App\Services\GenerateIdentityCodeService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{
    public function index() {

        $certificates = Certificate::activatedWithUserAndProduct()
            ->limit(2)
            ->get();
        $products = Product::all();

        return view('home', ['certificates' => $certificates, 'products' => $products]);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'product_id' => 'required|numeric',
            'number_of_trees' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response($validator->errors()->all(), 400);
        }

        if(User::where('email', $request->input('email'))->doesntExist()) {
            User::create([
                'name' => $request->input('name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => 'standardPassword0_'
            ]);
        }

        $product = Product::find($request->input('product_id'));
        $product_price = $product ? $product->price : 0;
        $total_price = $product_price * $request->input('number_of_trees');

        if(!$total_price) {
            return response('bad request', 400);
        }

        //производить оплату

        $user = User::where('email', $request->input('email'))->first();
        $certificate = Certificate::create([
            'identity' => GenerateIdentityCodeService::generateCode(),
            'user_id' => $user->id,
            'total_price' => $total_price,
            'currency_code' => 'EUR',
            'product_id' => $request->input('product_id'),
            'product_count' => $request->input('number_of_trees')
        ]);

        //Mail::to($user)->send(new CertificateShipped($certificate, $product));

        if($certificate) {
            return response('The certificate has been created. You will receive a message', 201);
        } else {
            return response('bad request', 400);
        }
    }

    public function update(Request $request) {

        $validator = Validator::make($request->all(), [
            'identity' => 'required|string'
        ]);

        if($validator->fails()) {
            return response($validator->errors()->all(), 400);
        }

        $certificate = Certificate::where('identity', '=', $request->input('identity'))->first();

        if(!$certificate) {
            return response('this certificate is not exists', 400);
        } elseif ($certificate->status == 'active') {
            return response('This certificate has been already activated');
        } else {
            $certificate->update([
                'status' => 'active',
                'activation_at' => Carbon::now()
            ]);

            return response('Your certificate #' . $certificate->identity . ' has been activated!');
        }
    }
}
