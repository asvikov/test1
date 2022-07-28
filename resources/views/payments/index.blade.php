@extends('payments/main')
@section('main_content')
    <h2>Add cash to account</h2>
    <form method="post" action="{{route('payment.create')}}">
        @csrf
        <label for="amount">amount RUB</label><br>
        <input type="number" name="amount"><br>
        <label for="description">description</label><br>
        <input type="text" name="description"><br>
        <input type="submit" value="Add">
    </form>
    <div>
        <div>current balance: @if(cache()->has('balance')) {{cache()->get('balance')}} @else 0 @endif</div>
        <div>transaction list</div>
        <table class="table mw-600">
            <thead>
            <tr>
                <th>id</th>
                <th>amount</th>
                <th>description</th>
                <th>status</th>
                <th>date</th>
            </tr>
            </thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{$transaction->id}}</td>
                    <td>{{$transaction->amount}}</td>
                    <td>{{$transaction->description}}</td>
                    <td>{{$transaction->status}}</td>
                    <td>{{$transaction->updatet_at->format('d-m-Y H:i')}}</td>
                </tr>
            @empty
                <tr>
                    <td>no transaction</td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>
@stop
