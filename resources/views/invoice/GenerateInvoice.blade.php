<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Send Email Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style type="text/css">

        body {
            margin-top: 20px;
            color: #484b51;
        }

        .text-secondary-d1 {
            color: #728299 !important;
        }

        .page-header {
            margin: 0 0 1rem;
            padding-bottom: 1rem;
            padding-top: .5rem;
            border-bottom: 1px dotted #e2e2e2;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -ms-flex-align: center;
            align-items: center;
        }

        hr {
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, .1);
        }


        .text-110 {
            font-size: 110% !important;
        }

        .bgc-default-tp1 {
            background-color: #888a8d;
            text-transform: uppercase;
        }

        .page-header .page-tools {
            -ms-flex-item-align: end;
            align-self: flex-end;
        }

        .text-120 {
            font-size: 120% !important;
            font-weight: bold;
        }

        .text-200 {
            font-size: 200%;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-white {
            color: #fff !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .header-table {
            border-collapse: collapse;
            text-align: center;
            width: 100%;
            padding-bottom: 60px;
            font-weight: bolder;
        }
        .header-table td, .header-table th {

        }
        .info {
            border-collapse: collapse;
            width: 100%;
            font-weight: bolder;
            padding-bottom: 20px;
        }

        .info td, .info th {
            vertical-align: top;
        }

        .product-data {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #000000;
        }

        .product-data td, .product-data th {
            padding: 8px;
            border: 1px solid #000000;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .vertical-align-top {
            vertical-align: top;
        }
        .text-95 {
            font-size: 95%;
        }
    </style>
</head>
<body>
<div class="page-content container">
    <div class="page-header">
{{--        <div class="intro">--}}
{{--            <h3>Hi <strong>{{ $billingAddress->name }}</strong>,</h3>--}}
{{--            This is the receipt for a payment of <strong> ${{ $invoice->product_data['price'] }} </strong> (USD) for--}}
{{--            your--}}
{{--            works--}}
{{--        </div>--}}
{{--        <hr/>--}}
        <table class="header-table">
            <tr>
                <td style="width: 25%;vertical-align: top;"><img style="padding-bottom: 10px; width: 80%"  src="{{ $logo->value }}"> <br> {{ $companyWebsite->value }}</td>
                <td style="width: 50%;">
                    <div class="text-center">
                        <div class="text-200">
                            <span class="text-default-d3">Invoice</span>
                        </div>
                        <br>
                        <div>
                            <span>Invoice ID #{{ $invoice->uuid }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-left">
                    {{ $companyName->value }} <br>
                    {{ $companyAddress->value }} <br>
                    {{ $supportEmail->value }}
                </td>
            </tr>
        </table>
        <table class="info">
            <tr class="text-95 text-secondary text-left">
                <td><span class="text-600 text-110">Date</span></td>
                <td><span class="text-600 text-110">: {{ $billingAddress->created_date }}</span></td>
                <td style="width: 20%"><span class="text-600 text-110 ">Payment Method</span></td>
                <td><span class="text-600 text-110 ">: {{ ucfirst($paymentMethod->name) }}</span></td>
            </tr>
            <tr class="text-95 text-secondary">
                <td style="width: 20%">Invoice number</td>
                <td style="width: 40%;">: {{ $invoice->uuid }}</td>
                <td><span class=""></span>Initial Charge</td>
                <td>: ${{ $invoice->product_data['price'] }}</td>
            </tr>
            <tr class="text-95 text-secondary">
                <td><span class=""></span>Phone</td>
                <td>: {{ $billingAddress->phone }}</td>
                <td><span class="text-600 text-110 align-middle">Final Cost</span></td>
                <td>: ${{ $invoice->product_data['price'] }}</td>
            </tr>
            <tr class="text-95 text-secondary">
                <td><span class=""></span>Email</td>
                <td>: {{ $billingAddress->email }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr class="text-95 text-secondary">
                <td><span class=""></span>Address</td>
                <td>
                    : {{ $billingAddress->address }}, {{ $billingAddress->city }} <br>
                    {{ $billingAddress->state }}, {{ $billingAddress->country }}
                </td>
                <td>
                    <span class="text-600 text-110 align-middle">Total Refund</span> <br>
                Refund To
                </td>
                <td>: N/A <br>: N/A</td>
            </tr>
        </table>
        <div class="mt-4">
            <div>
                <table class="product-data">
                    <tr class="text-white bgc-default-tp1">
                        <th>Type</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Duration</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                    <tr style="text-align: center">
                        <td>{{ $invoice->product_data['type'] }}</td>
                        <td>{{ $invoice->product_data['name'] }}</td>
                        <td>{{ $invoice->product_data['quantity'] }}</td>
                        <td>{{ $invoice->product_data['duration'] }}</td>
                        <td>${{ $invoice->product_data['price'] }}</td>
                        <td>${{ $invoice->product_data['price'] }}</td>
                    </tr>
                    <tr class="text-95 text-secondary vertical-align-top ">
                        <td colspan="5" class="text-right text-120">
                            <div>
                                <b> Total Amount </b>
                            </div>
                        </td>
                        <td class="text-center">
                            <div >
                                <span class="text-120 text-secondary-d1">${{ $invoice->product_data['price'] }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <hr/>
            <div>
                <span class="text-105">Thank you so much for your using!</span>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>
</body>
</html>
