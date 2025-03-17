@component('mail::message')

# One of your transactions is failed with the status "<b>{{ $status }}</b>"

Unfortunately, the transaction to buy the product <b>{{ $productName }}</b> is failed due to the reason below:

{{ $reason }}

# Refund procedure

Please contact the Support team via the "Contact Us" form with details about the transactions to initiate the
money refund procedure.

We apologize for the inconvenience.

Kind regards, <b>CS:GO Store</b> team.

@endcomponent
