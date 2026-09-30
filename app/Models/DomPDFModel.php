<?php

namespace App\Models;

use Dompdf\Dompdf;

class DomPDFModel
{
   //to generate order invoice
    public function createInvoice($invoiceData)
    {
        $order            = $invoiceData['order'];
        $invoiceId        = $invoiceData['invoice_id'];
        $invoiceDate      = $invoiceData['invoice_date'] ?? date('Y-m-d H:i:s');
        $billingAddress   = $invoiceData['billing_address'] ?? [];
        $shippingAddress  = $invoiceData['shipping_address'] ?? [];
        $products         = $invoiceData['products'] ?? [];

        //initialise

        $dompdf = new Dompdf();

        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isPhpEnabled', true);


        // helper functions

        $esc = function ($value) {
            return htmlspecialchars(
                (string) $value,
                ENT_QUOTES,
                'UTF-8'
            );
        };


        // Convert local image to base64
        $imageToDataUri = function ($path) {

            if (!file_exists($path)) {
                return '';
            }

            $extension = strtolower(
                pathinfo($path, PATHINFO_EXTENSION)
            );

            switch ($extension) {

                case 'jpg':
                case 'jpeg':
                    $type = 'image/jpeg';
                    break;

                case 'png':
                    $type = 'image/png';
                    break;

                case 'gif':
                    $type = 'image/gif';
                    break;

                case 'webp':
                    $type = 'image/webp';
                    break;

                default:
                    return '';
            }

            $data = file_get_contents($path);

            if ($data === false) {
                return '';
            }

            return 'data:' . $type . ';base64,' . base64_encode($data);
        };


        //local images

        $publicPath = FCPATH . 'Images/';

        $logoImage = $imageToDataUri(
            $publicPath . 'logo.png'
        );

        $goodMusicImage = $imageToDataUri(
            $publicPath . 'email/email1.png'
        );

        $happyShoppingImage = $imageToDataUri(
            $publicPath . 'email/email4.png'
        );

        $signatureImage = $imageToDataUri(
            $publicPath . 'signature.png'
        );


        //date formatting
        $formattedInvoiceDate = date(
            'd M Y',
            strtotime($invoiceDate)
        );

        $formattedOrderDate = '';
        if (!empty($order['order_date'])) {

            $formattedOrderDate = date(
                'd M Y',
                strtotime($order['order_date'])
            );
        }

        $formattedDeliveryStart = date('d M Y',strtotime($order['delivery_start']));
        $formattedDeliveryEnd = date('d M Y',strtotime($order['delivery_end']));
        $deliveryDate = $formattedDeliveryStart." - ".$formattedDeliveryEnd;

        //address
        $formatAddress = function ($address) use ($esc) {
            if (empty($address)) {
                return '';
            }

            $html = '';

            // FIRST + LAST NAME ON SAME LINE
            $name = trim(($address['first_name'] ?? '') . ' ' .($address['last_name'] ?? ''));

            if ($name !== '') {
                $html .= '<div class="address-line name">'
                       . $esc($name)
                       . '</div>';
            }

            if (!empty($address['address_line1'])) {
                $html .= '<div class="address-line">'
                       . $esc($address['address_line1'])
                       . '</div>';
            }

            if (!empty($address['address_line2'])) {
                $html .= '<div class="address-line">'
                       . $esc($address['address_line2'])
                       . '</div>';
            }

            $cityState = trim(
                ($address['city'] ?? '') .
                (
                    !empty($address['state'])
                    ? ', ' . $address['state']
                    : ''
                )
            );

            if ($cityState !== '') {
                $html .= '<div class="address-line">'
                       . $esc($cityState)
                       . '</div>';
            }
            
            $counZip = trim(
                ($address['country'] ?? '') .
                (
                    !empty($address['zip'])
                    ? ', ' . $address['zip']
                    : ''
                )
            );
            
            if ($counZip !== '') {
                $html .= '<div class="address-line">'
                       . $esc($counZip)
                       . '</div>';
            }

            if (!empty($address['contact_no'])) {
                $html .= '<div class="address-line">'
                       . $esc($address['contact_no'])
                       . '</div>';
            }

            return $html;
        };


        $billingHtml = $formatAddress($billingAddress);
        $shippingHtml = $formatAddress($shippingAddress);

        // payment method
        $paymentType = strtolower(
            trim($order['payment_type'] ?? '')
        );

        if ($paymentType === 'cod') {
            $paymentMethod = 'Cash on Delivery';
        } elseif ($paymentType !== '') {
            $paymentMethod = ucwords(
                str_replace(
                    ['_', '-'],
                    ' ',
                    $paymentType
                )
            );
        } else {
            $paymentMethod = 'N/A';
        }


        // order tatus
        $orderStatus = strtolower(
            trim(
                $order['order_status'] ??
                $order['status'] ??
                ''
            )
        );

        $hideShippingSection = in_array(
            $orderStatus,
            [
                'processing',
                'fulfilled',
                'cancelled'
            ],
            true
        );

        // amount
        $subtotal = (float) (
            $order['sub_total_amount'] ?? 0
        );

        $shippingFee = (float) (
            $order['shipping_charged_amount'] ?? 0
        );

        $grandTotal = (float) (
            $order['grand_total_amount'] ?? 0
        );

        $gst = (float) (
            $order['total_taxed_amount'] ?? 0
        );

        $discountedTotal = (float) (
            $order['discounted_total_amount'] ?? 0
        );

        $storeCredit = (float) (
            $order['store_credit'] ?? 0
        );

        $codCharges = (float) (
            $order['cod_charges'] ?? 0
        );


        // Discount
        $discount = $discountedTotal;

        if ($discount < 0) {
            $discount = 0;
        }

        // amount in words
        $numberToWords = function ($number) {

            $number = (int) round($number);

            if ($number === 0) {
                return 'Zero';
            }

            $ones = [
                '',
                'One',
                'Two',
                'Three',
                'Four',
                'Five',
                'Six',
                'Seven',
                'Eight',
                'Nine',
                'Ten',
                'Eleven',
                'Twelve',
                'Thirteen',
                'Fourteen',
                'Fifteen',
                'Sixteen',
                'Seventeen',
                'Eighteen',
                'Nineteen'
            ];

            $tens = [
                '',
                '',
                'Twenty',
                'Thirty',
                'Forty',
                'Fifty',
                'Sixty',
                'Seventy',
                'Eighty',
                'Ninety'
            ];

            $convert = function ($num) use (&$convert, $ones, $tens) {

                $result = '';

                if ($num >= 10000000) {

                    $result .= $convert(
                        intdiv($num, 10000000)
                    ) . ' Crore ';

                    $num %= 10000000;
                }

                if ($num >= 100000) {

                    $result .= $convert(
                        intdiv($num, 100000)
                    ) . ' Lakh ';

                    $num %= 100000;
                }

                if ($num >= 1000) {

                    $result .= $convert(
                        intdiv($num, 1000)
                    ) . ' Thousand ';

                    $num %= 1000;
                }

                if ($num >= 100) {

                    $result .= $convert(
                        intdiv($num, 100)
                    ) . ' Hundred ';

                    $num %= 100;
                }

                if ($num >= 20) {

                    $result .= $tens[
                        intdiv($num, 10)
                    ];

                    $num %= 10;

                    if ($num > 0) {
                        $result .= ' ' . $ones[$num];
                    }

                } elseif ($num > 0) {

                    $result .= $ones[$num];
                }

                return trim($result);
            };

            return $convert($number);
        };

        $amountInWords =$numberToWords($grandTotal) . ' Indian Rupees Only.';

        //remote images helper
        $remoteImageToDataUri = function ($url) {

            if (empty($url)) {
                return '';
            }

            $data = @file_get_contents($url);

            if ($data === false) {
                return '';
            }

            $extension = strtolower(
                pathinfo(
                    parse_url($url, PHP_URL_PATH),
                    PATHINFO_EXTENSION
                )
            );

            switch ($extension) {

                case 'jpg':
                case 'jpeg':
                    $type = 'image/jpeg';
                    break;

                case 'png':
                    $type = 'image/png';
                    break;

                case 'gif':
                    $type = 'image/gif';
                    break;

                case 'webp':
                    $type = 'image/webp';
                    break;

                default:
                    return '';
            }

            return 'data:' . $type . ';base64,' . base64_encode($data);
        };
        
        //product rows
        $productRows = '';
        $counter = 1;

        foreach ($products as $product) {

            $productName = $product['product_name'] ?? '';

            $price = (float) (
                $product['product_price'] ?? 0
            );

            $qty = (int) (
                $product['product_qty'] ?? 0
            );

            $total = $price * $qty;

            $productImage = '';

            if (!empty($product['main_image'])) {

                $productImage = $remoteImageToDataUri(
                    $product['main_image']
                );
            }


            $imageHtml = '';

            if ($productImage !== '') {

                $imageHtml =
                    '<img
                        src="' . $esc($productImage) . '"
                        class="product-image"
                    >';
            }


            $productRows .= '
                <tr>

                    <td class="center">
                        ' . $counter . '
                    </td>

                    <td class="product-cell">

                        <div class="product-image-wrapper">
                            ' . $imageHtml . '
                        </div>

                        <div class="product-name">
                            ' . $esc($productName) . '
                        </div>

                    </td>

                    <td class="center">
                        ₹' . number_format(
                            $price,
                            0
                        ) . '
                    </td>

                    <td class="center">
                        ' . $qty . '
                    </td>

                    <td class="right">
                        ₹' . number_format(
                            $total,
                            0
                        ) . '
                    </td>

                </tr>
            ';

            $counter++;
        }


        // cod row
        $codChargesRow = '';
        if ( $paymentType === 'cod' && $codCharges > 0 ) {

            $codChargesRow = '
                <div class="summary-row">
                    <span>COD Charges</span>
                    <span>₹' .
                        number_format(
                            $codCharges,
                            0
                        ) .
                    '</span>
                </div>
            ';
        }


        //discount row
        $discountRow = '';
        if ($discount > 0) {
            $discountRow = '
                <div class="summary-row discount">
                    <span>Discount</span>
                    <span>−₹' .
                        number_format(
                            $discount,
                            0
                        ) .
                    '</span>
                </div>
            ';
        }

        // store credit row
        $storeCreditRow = '';
        if ($storeCredit > 0) {
            $storeCreditRow = '
                <div class="summary-row discount">
                    <span>Store Credit Used</span>
                    <span>−₹' .
                        number_format(
                            $storeCredit,
                            0
                        ) .
                    '</span>
                </div>
            ';
        }

        //shipping section
        $shippingSection = '';
        if (!$hideShippingSection) {
            $shippingSection = '
                <div class="info-box">
                    <div class="box-title">
                        Shipping Details
                    </div>
                    <div class="info-content">
                        <div>
                            <strong>Courier Partner</strong>
                            <span>:</span>
                            ' .
                            $esc(
                                $order['courier_partner']
                                ?? ''
                            )
                            . '
                        </div>
                        <div>
                            <strong>Tracking Number</strong>
                            <span>:</span>
                            ' .
                            $esc(
                                $order['tracking_id']
                                ?? ''
                            )
                            . '
                        </div>
                        <div>
                            <strong>Estimated Delivery Date</strong>
                            <span>:</span>
                            ' .
                            $esc(
                                $deliveryDate
                                ?? ''
                            )
                            . '
                        </div>

                    </div>

                </div>
            ';
        }

        //order notes
        $orderNotes = trim(
            $order['order_comment'] ?? ''
        );

        $orderNotesSection = '';
        if ($orderNotes !== '') {
            $orderNotesSection = '
                <div class="full-box order-notes">

                    <div class="box-title">
                        Order Notes
                    </div>

                    <div class="notes-content">
                        ' . nl2br(
                            $esc($orderNotes)
                        ) . '
                    </div>

                </div>
            ';
        }

        //email html
        $html = '
        <!DOCTYPE html>

            <html>

                <head>

                    <meta charset="UTF-8">

                <style>

                    /* general */

                    table {
                        border-collapse: collapse;
                    }

                    img {
                        border: 0;
                    }

                    .center {
                        text-align: center;
                    }

                    .right {
                        text-align: right;
                    }

                    @page {
                        margin: 35px;
                    }

                    body {
                        font-family: DejaVu Sans, sans-serif;
                        color: #1F1F1F;
                        font-size: 11px;
                        margin: 0;
                        padding: 0;
                    }

                    .invoice-title {
                        width: 100%;
                        background: #B57BE8;
                        color: #1F1F1F;
                        text-align: center;
                        font-size: 22px;
                        font-weight: bold;
                        padding: 10px 0;
                        margin-bottom: 12px;
                    }

                    /* =========================================
                       TOP INFORMATION AREA
                       ========================================= */

                    .top-section {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 14px;
                    }

                    .top-left {
                        width: 50%;
                        vertical-align: top;
                        padding-right: 8px;
                    }

                    .top-right {
                        width: 50%;
                        vertical-align: top;
                        padding-left: 8px;
                    }

                    /* =========================================
                       BUSINESS HEADER
                       ========================================= */

                    .business-header {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 7px;
                    }

                    .business-logo-cell {
                        width: 72px;
                        vertical-align: middle;
                    }

                    .business-logo {
                        width: 65px;
                        height: 65px;
                        object-fit: contain;
                    }

                    .business-name-cell {
                        vertical-align: middle;
                        padding-left: 8px;
                    }

                    .business-name {
                        font-size: 17px;
                        font-weight: bold;
                        color: #1F1F1F;
                    }

                    .business-details {
                        line-height: 1.6;
                        color: #1F1F1F;
                    }

                    /* =========================================
                       RIGHT TOP - 30 / 70
                       ========================================= */

                    .invoice-info {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .good-music-cell {
                        width: 30%;
                        vertical-align: middle;
                        text-align: center;
                        padding-right: 8px;
                    }

                    .good-music {
                        width: 75px;
                        height: auto;
                        object-fit: contain;
                    }

                    .invoice-details-cell {
                        width: 70%;
                        vertical-align: middle;
                        padding-left: 5px;
                    }

                    .invoice-details {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    .invoice-details td {
                        padding: 3px 0;
                        vertical-align: top;
                    }

                    .invoice-details .label {
                        font-weight: bold;
                        width: 42%;
                        white-space: nowrap;
                    }

                    .invoice-details .colon {
                        width: 10px;
                        text-align: center;
                    }

                    .invoice-details .value {
                        width: auto;
                    }



                    /* address section */

                    .address-table {
                        width: 100%;
                        margin-bottom: 14px;
                    }

                    .address-column {
                        width: 50%;
                        vertical-align: top;

                        padding-right: 6px;
                    }

                    .address-column:last-child {
                        padding-right: 0;
                        padding-left: 6px;
                    }

                    .info-box {
                        border: 1px solid #E9DDF7;

                        border-radius: 6px;

                        overflow: hidden;

                        margin-bottom: 12px;

                        background: #FFFFFF;
                    }

                    .box-title {
                        background: #B57BE8;

                        color: #1F1F1F;

                        font-size: 11px;
                        font-weight: bold;

                        padding: 7px 10px;
                    }

                    .address-content {
                        color: #666666;

                        padding: 8px 11px;

                        min-height: 95px;

                        line-height: 1.5;
                    }

                    .address-line {
                        margin-bottom: 1px;
                    }

                    .address-line.name {
                        color: #1F1F1F;

                        font-weight: bold;

                        margin-bottom: 3px;
                    }


                    /* product table */

                    .products-table {
                        width: 100%;

                        border: 1px solid #E9DDF7;

                        margin-bottom: 14px;

                        background: #FFFFFF;
                    }

                    .products-table th {
                        background: #B57BE8;

                        color: #1F1F1F;

                        font-size: 10px;
                        font-weight: bold;

                        padding: 8px 5px;

                        border-right: 1px solid #E9DDF7;
                    }

                    .products-table th:last-child {
                        border-right: none;
                    }

                    .products-table td {
                        color: #666666;

                        border-top: 1px solid #E9DDF7;
                        border-right: 1px solid #E9DDF7;

                        padding: 7px 5px;

                        vertical-align: middle;
                    }

                    .products-table td:last-child {
                        border-right: none;
                    }


                    /* product section */

                    .product-cell {
                        height: 48px;
                    }

                    .product-image-wrapper {
                        width: 45px;
                        height: 45px;

                        float: left;

                        margin-right: 9px;

                        text-align: center;
                    }

                    .product-image {
                        max-width: 45px;
                        max-height: 45px;
                    }

                    .product-name {
                        color: #1F1F1F;

                        padding-top: 8px;

                        line-height: 1.35;
                    }


                    /* payment section */

                    .bottom-table {
                        width: 100%;
                    }

                    .bottom-left {
                        width: 50%;

                        vertical-align: top;

                        padding-right: 7px;
                    }

                    .bottom-right {
                        width: 50%;

                        vertical-align: top;

                        padding-left: 7px;
                    }

                    .info-content {
                        color: #666666;

                        padding: 9px 11px;

                        line-height: 1.7;
                    }

                    .info-content strong {
                        display: inline-block;

                        width: 105px;

                        color: #1F1F1F;
                    }

                    .info-content span {
                        padding-right: 5px;
                    }


                    /* order summary */

                    .summary-box {
                        border: 1px solid #E9DDF7;

                        border-radius: 6px;

                        overflow: hidden;

                        padding: 9px 11px 0 11px;

                        background: #FFFFFF;
                    }

                    .summary-row {
                        width: 100%;

                        color: #666666;

                        padding: 3px 0;

                        font-size: 10.5px;
                    }

                    .summary-row span:last-child {
                        float: right;

                        color: #1F1F1F;
                    }

                    .summary-row.discount span:last-child {
                        color: #9C5FE0;
                    }


                    /* total */

                    .total-row {
                        margin: 6px -11px 0 -11px;

                        padding: 9px 11px;

                        background: #B57BE8;

                        color: #1F1F1F;

                        font-size: 15px;
                        font-weight: bold;
                    }

                    .total-row span:last-child {
                        color: #9C5FE0;
                    }


                    /* amount in words */

                    .amount-words {
                        color: #666666;

                        font-size: 9.5px;

                        padding: 7px 0 8px 0;

                        line-height: 1.4;
                    }

                    .amount-words strong {
                        display: block;

                        color: #1F1F1F;

                        margin-bottom: 2px;
                    }


                    /* order notes */

                    .full-box {
                        border: 1px solid #E9DDF7;

                        border-radius: 6px;

                        overflow: hidden;

                        margin-top: 12px;

                        background: #FFFFFF;
                    }

                    .notes-content {
                        color: #666666;

                        padding: 9px 11px;

                        line-height: 1.5;
                    }

                    .order-notes {
                        margin-top: 12px;
                    }


                    /* shipping */

                    .info-content strong {
                        color: #1F1F1F;
                    }


                    /* footer */

                    .footer-table {
                        width: 100%;

                        margin-top: 15px;
                    }

                    .footer-left {
                        width: 62%;

                        vertical-align: bottom;

                        padding-right: 10px;
                    }

                    .footer-right {
                        width: 38%;

                        vertical-align: bottom;

                        text-align: center;
                    }


                    /* left bottom image */

                    .happy-shopping {
                        width: 350px;
                        height: auto;

                        display: block;
                    }


                    /* signature */

                    .signature {
                        width: 145px;
                        height: auto;

                        margin-bottom: 2px;
                    }

                    .signature-line {
                        border-top: 1px solid #1F1F1F;

                        width: 150px;

                        margin: 0 auto 5px auto;
                    }

                    .signature-title {
                        color: #1F1F1F;

                        font-size: 9px;
                        font-weight: bold;
                    }

                    .signature-company {
                        color: #666666;

                        font-size: 9px;

                        margin-top: 5px;
                    }


                    /* small test */

                    .muted {
                        color: #999999;
                    }

                    .secondary {
                        color: #666666;
                    }


                    /*purple colors */

                    .accent {
                        color: #B57BE8;
                    }

                    .accent-dark {
                        color: #9C5FE0;
                    }

                    .accent-background {
                        background: #B57BE8;
                    }

                    .section-background {
                        background: #FAF8FC;
                    }


                    /* page break */

                    .info-box,
                    .summary-box,
                    .full-box,
                    .products-table {
                        page-break-inside: avoid;
                    }

                    .footer-table {
                        page-break-inside: avoid;
                    }

                </style>

            </head>

            <body>

                <!-- top section -->
                   <div class="invoice-title">
                       TAX INVOICE
                   </div>

                   <!-- business details -->
                   <table class="top-section">
                       <tr>
                           <!-- left -->
                           <td class="top-left">
                               <table class="business-header">
                                   <tr>
                                       <td class="business-logo-cell">

                                           <img
                                               src="' . $logoImage . '"
                                               class="business-logo"
                                           >
                                       </td>
                                       <td class="business-name-cell">

                                           <div class="business-name">
                                               K-Pop Merch
                                           </div>

                                       </td>
                                   </tr>
                               </table>

                               <div class="business-details">
                                   <strong>Bits To Byte</strong><br>
                                   GSTIN : 08AARFB3495M1ZO<br>
                                   123, Hiran Magri Sec. 11, Udaipur, Raj. 313001, India<br>
                                   Email : support@kpopmerch.in
                               </div>
                           </td>

                           <!-- right -->
                           <td class="top-right">
                               <table class="invoice-info">
                                   <tr>
                                       <!-- GOOD MUSIC -->
                                       <td class="good-music-cell">
                                           <img
                                               src="' . $goodMusicImage . '"
                                               class="good-music"
                                           >
                                       </td>

                                       <!-- INVOICE DETAILS -->
                                       <td class="invoice-details-cell">
                                           <table class="invoice-details">
                                               <tr>
                                                   <td class="label">
                                                       Invoice No.
                                                   </td>

                                                   <td class="colon">
                                                       :
                                                   </td>

                                                   <td class="value">
                                                       ' . $esc($invoiceId) . '
                                                   </td>

                                               </tr>

                                               <tr>

                                                   <td class="label">
                                                       Invoice Date
                                                   </td>

                                                   <td class="colon">
                                                       :
                                                   </td>

                                                   <td class="value">
                                                       ' . $esc($formattedInvoiceDate) . '
                                                   </td>

                                               </tr>

                                               <tr>

                                                   <td class="label">
                                                       Order No.
                                                   </td>

                                                   <td class="colon">
                                                       :
                                                   </td>

                                                   <td class="value">
                                                       ' . $esc($order['id']) . '
                                                   </td>

                                               </tr>

                                               <tr>

                                                   <td class="label">
                                                       Order Date
                                                   </td>

                                                   <td class="colon">
                                                       :
                                                   </td>

                                                   <td class="value">
                                                       ' . $esc($formattedOrderDate) . '
                                                   </td>

                                               </tr>
                                           </table>
                                       </td>
                                   </tr>
                               </table>
                           </td>
                       </tr>
                   </table> 
                    
                    <!-- addresses -->
                    <table class="address-table">

                        <tr>

                        <td class="address-column">

                            <div class="info-box">

                                <div class="box-title">
                                    Billing Address
                                </div>

                                <div class="address-content">
                                    ' . $billingHtml . '
                                </div>

                            </div>

                        </td>


                        <td class="address-column">

                            <div class="info-box">

                                <div class="box-title">
                                    Shipping Address
                                </div>

                                <div class="address-content">
                                    ' . $shippingHtml . '
                                </div>

                            </div>

                        </td>

                        </tr>

                    </table>

                    <!-- products -->
                    <table class="products-table">

                        <thead>

                            <tr>

                                <th width="6%">
                                    #
                                </th>

                                <th width="54%">
                                    Item
                                </th>

                                <th width="13%">
                                    Price (₹)
                                </th>

                                <th width="10%">
                                    Qty
                                </th>

                                <th width="17%">
                                    Total (₹)
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                        ' . $productRows . '

                        </tbody>

                    </table>

                    <!-- payment summary -->

                    <table class="bottom-table">

                        <tr>

                        <td class="bottom-left">

                            <div class="info-box">

                                <div class="box-title">
                                    Payment Method
                                </div>

                                <div class="info-content">

                                    ' . $esc($paymentMethod) . '

                                </div>

                            </div>

                            ' . $shippingSection . '
                        </td>


                        <td class="bottom-right">

                            <div class="summary-box">
                                <div class="summary-row">
                                    <span>
                                        Subtotal
                                    </span>

                                    <span>
                                        ₹' .
                                        number_format(
                                            $subtotal,
                                            0
                                        )
                                        . '
                                    </span>
                                </div>

                                <div class="summary-row">
                                    <span>
                                        Shipping Fee
                                    </span>

                                    <span>
                                        ₹' .
                                        number_format(
                                            $shippingFee,
                                            0
                                        )
                                        . '
                                    </span>
                                </div>

                                ' . $codChargesRow . '

                                ' . $discountRow . '

                                ' . $storeCreditRow . '

                                <div class="summary-row">
                                    <span>
                                        GST (18%)
                                    </span>
                                    <span>
                                        ₹' .
                                        number_format(
                                            $gst,
                                            0
                                        )
                                        . '
                                    </span>
                                </div>

                                <div class="total-row">
                                    <span>
                                        Total Amount
                                    </span>

                                    <span style="float:right;">
                                        ₹' .
                                        number_format(
                                            $grandTotal,
                                            0
                                        )
                                        . '
                                    </span>

                                    <div style="clear:both;"></div>
                                </div>

                                <div class="amount-words">
                                    <strong>
                                        Amount in Words
                                    </strong>

                                    ' .
                                    $esc($amountInWords)
                                    . '
                                </div>
                            </div>
                        </td>

                        </tr>

                    </table>

                    <!-- order notes -->
                    ' . $orderNotesSection . '

                    <!-- footer -->
                    <table class="footer-table">

                        <tr>

                        <td class="footer-left">
                            ' .
                            (
                                $happyShoppingImage !== ''
                                ? '<img
                                        src="' . $happyShoppingImage . '"
                                        class="happy-shopping"
                                   >'
                                : ''
                            )
                            . '
                        </td>

                        <td class="footer-right">
                            ' .
                            (
                                $signatureImage !== ''
                                ? '<img
                                        src="' . $signatureImage . '"
                                        class="signature"
                                   >'
                                : ''
                            )
                            . '
                            <div class="signature-line"></div>

                            <div class="signature-title">
                                Authorised Signatory
                            </div>

                            <div class="signature-company">
                                For K-Pop Merch (Bits To Byte)
                            </div>

                        </td>

                        </tr>

                    </table>

                </body>

            </html>
        ';

        //generate pdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper(
            'A4',
            'portrait'
        );
        $dompdf->render();

        return $dompdf->output();
    }
}