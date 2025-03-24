@component('mail::message')

# One of the transactions from the order for {{ $productPrice }}€ did not go through

Unfortunately, the transaction to buy the product <b>{{ $productName }}</b> is failed due to the reason below:

{{ $reason }}

@endcomponent
