@component('mail::message')

# One of user transactions is failed due to <b>insufficient SkinsBack balance</b>

User with the email <b>{{ $userEmail }}</b> has tried to buy the following products:

@foreach($productNames as $productName)
- {{ $productName }}
@endforeach

The order total amount is: <b>${{ $orderAmount }}</b>

The transaction is failed because there is no enough money to buy the product on the SkinsBack.

Actual SkinsBack balance is: <b>${{ $actualMarketplaceBalance }}</b>

<i><b>Note</b> that some marketplace funds could be frozen by pending transactions of other users.</i>

# What to do

Please replenish the SkinsBack account with more funds
and contact the user if you want to notify him about the possibility to buy the product now.

Kind regards, <b>CS:GO Store</b> app.

@endcomponent
