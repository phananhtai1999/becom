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

        .page-title {
            padding: 0;
            margin: 0;
            font-size: 1.75rem;
            font-weight: 300;
        }

        .brc-default-l1 {
            border-color: #dce9f0 !important;
        }

        .ml-n1, .mx-n1 {
            margin-left: -.25rem !important;
        }

        .mr-n1, .mx-n1 {
            margin-right: -.25rem !important;
        }

        .mb-4, .my-4 {
            margin-bottom: 1.5rem !important;
        }

        hr {
            margin-top: 1rem;
            margin-bottom: 1rem;
            border: 0;
            border-top: 1px solid rgba(0, 0, 0, .1);
        }

        .text-grey-m2 {
            color: #888a8d !important;
        }

        .text-success-m2 {
            color: #86bd68 !important;
        }

        .font-bolder, .text-600 {
            font-weight: 600 !important;
        }

        .text-110 {
            font-size: 110% !important;
        }

        .text-blue {
            color: #0000ee;
        }

        .pb-25, .py-25 {
            padding-bottom: .75rem !important;
        }

        .pt-25, .py-25 {
            padding-top: .75rem !important;
        }

        .bgc-default-tp1 {
            background-color: #0000ee;
        }

        .bgc-default-l4, .bgc-h-default-l4:hover {
            background-color: #f3f8fa !important;
        }

        .page-header .page-tools {
            -ms-flex-item-align: end;
            align-self: flex-end;
        }

        .btn-light {
            color: #757984;
            background-color: #f5f6f9;
            border-color: #dddfe4;
        }

        .w-2 {
            width: 1rem;
        }

        .text-120 {
            font-size: 120% !important;
        }

        .text-primary-m1 {
            color: #4087d4 !important;
        }

        .text-danger-m1 {
            color: #dd4949 !important;
        }

        .text-blue-m2 {
            color: #68a3d5 !important;
        }

        .text-200 {
            font-size: 200%;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-60 {
            font-size: 60% !important;
        }

        .text-grey-m1 {
            color: #7b7d81 !important;
        }

        .align-bottom {
            vertical-align: bottom !important;
        }

        .text-white {
            color: #fff !important;
        }

        .text-secondary {
            color: #6c757d !important;
        }

        .info, th, td {
            border-collapse: collapse;
            width: 100%;
        }

        .info td, .info th {
            padding: 15px;
        }

        .info td div {
            padding-bottom: 5px;
        }

        .product-data {
            border-collapse: collapse;
            width: 100%;
        }

        .product-data td, .product-data th {
            padding: 8px;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge-pill {
            padding: 0.2em 0.8em;
            border-radius: 10rem;
        }

        .my-2 {
            text-align: start;
        }

        .text-right {
            text-align: right !important;
        }

        .vertical-align-top {
            vertical-align: top;
        }
    </style>
</head>
<body>
<div class="page-content container">
    <div class="page-header">
        <div class="intro">
            <h3>Hi <strong>{{ $billingAddress->name }}</strong>,</h3>
            This is the receipt for a payment of <strong> ${{ $invoice->product_data['price'] }} </strong> (USD) for
            your
            works
        </div>
        <hr/>
        <div class="text-center">
            <div class="text-200">
                <span class="text-default-d3 text-blue">Invoice</span>
            </div>
            <div>
                <span>Invoice ID: #{{ $invoice->uuid }}</span>
            </div>
        </div>
        <hr class="row brc-default-l1 mx-n1"/>
        <table class="info">
            <tr class="text-95 text-secondary">
                <td>

                    <div class="text-grey-m2">
                        <div>
                            <span class="text-grey-m2 align-middle">To:</span>
                            <span class="text-600 text-110 align-middle">{{ $billingAddress->name }}</span>
                        </div>
                        <div>
                            {{ $billingAddress->address }}, {{ $billingAddress->city }}
                        </div>
                        <div>
                            {{ $billingAddress->state }}, {{ $billingAddress->country }}
                        </div>
                        <div>
                            <span class="text-grey-m2 align-middle">Phone:</span>
                            <span class="text-grey-m2 align-middle">{{ $billingAddress->phone }}</span>

                        </div>
                    </div>
                </td>
                <td class="vertical-align-top text-grey-m2 my-2" style="text-align: inherit ">
                    <div>
                        <div>
                            <span>Payment Method:</span> {{ ucfirst($paymentMethod->name) }}
                        </div>
                        <div>
                            <span>Invoice Date:</span> {{ date('Y-M-D H:m:s', $billingAddress->created_date) }}
                        </div>
                        <div>
                            <span>Status:</span>
                            <span class="badge badge-success badge-pill">Complete</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <div class="mt-4">
            <div>
                <table class="product-data">
                    <tr class="text-white bgc-default-tp1 text-600 py-25">
                        <th>Type</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Duration</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
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
                        <td colspan="5">
                            <div>
                                Extra note such as company or payment information...
                            </div>
                        </td>
                        <td class="text-right">
                            <div>
                                Total Amount
                            </div>
                            <div>
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
