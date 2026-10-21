<?php

namespace App\Models;

use CodeIgniter\Model;

class BrevoEmail extends Model
{

    private $apiKey = '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';
    
    //to form address html
    private function buildAddress($billingAddress){
        //helper
        $e = function ($value) {
            return htmlspecialchars(
                (string)$value,
                ENT_QUOTES,
                'UTF-8'
            );
        };
        
        $billingHtml='';
        
        $billingName = trim(
            ($billingAddress['first_name'] ?? '') . ' ' . ($billingAddress['last_name'] ?? '')
        );

        if ($billingName) {
            $billingHtml .=
                '<div>' . $e($billingName) . '</div>';
        }

        if (!empty($billingAddress['company_name'])) {
            $billingHtml .=
                '<div>' .
                $e($billingAddress['company_name']) .
                '</div>';
        }

        if (!empty($billingAddress['address_line1'])) {
            $billingHtml .=
                '<div>' .
                $e($billingAddress['address_line1']) .
                '</div>';
        }

        if (!empty($billingAddress['address_line2'])) {
            $billingHtml .=
                '<div>' .
                $e($billingAddress['address_line2']) .
                '</div>';
        }

        if (!empty($billingAddress['city'])) {

            $cityState = $billingAddress['city'];
            if (!empty($billingAddress['state'])) {
                $cityState .=
                    ', ' .
                    $billingAddress['state'];
            }

            $billingHtml .=
                '<div>' .
                $e($cityState) .
                '</div>';
        }

        if (!empty($billingAddress['country'])) {

            $countryZip = $billingAddress['country'];
            if (!empty($billingAddress['zip'])) {
                $countryZip .=
                    ', ' .
                    $billingAddress['zip'];
            }

            $billingHtml .=
                '<div>' .
                $e($countryZip) .
                '</div>';
        }

        if (!empty($billingAddress['contact_no'])) {
            $billingHtml .=
                '<div>' .
                $e($billingAddress['contact_no']) .
                '</div>';
        }
        
        return $billingHtml;
    }
    
    //to build product rows 
    private function buildProductRows($products){
        //helper
        $e = function ($value) {
            return htmlspecialchars(
                (string)$value,
                ENT_QUOTES,
                'UTF-8'
            );
        };
        
        $productRows = '';
        $counter = 1;
        foreach ($products as $product) {

            $productName =$product['product_name'] ?? 'Product';
            $productImage =$product['main_image'] ?? base_url('Images/icons/ic_product.png');
            $productPrice = (float)($product['product_price'] ?? 0);
            $productQty =(int)($product['product_qty'] ?? 0);
            $lineTotal = (float)($product['line_total'] ?? 0);
            $productRows .= '
                <tr>

                    <!-- NUMBER -->
                    <td
                        width="7%"
                        valign="middle"
                        align="center"
                        style="
                            border:1px solid #e5d9f4;
                            padding:10px 6px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:13px;
                            color:#29205c;
                        "
                    >
                        ' . $counter . '
                    </td>


                    <!-- PRODUCT -->
                    <td
                        width="53%"
                        valign="middle"
                        style="
                            border:1px solid #e5d9f4;
                            padding:8px 10px;
                        "
                    >

                        <table
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                        >
                            <tr>

                                <td
                                    width="55"
                                    valign="middle"
                                >
                                    <img
                                        src="' . $e($productImage) . '"
                                        alt="' . $e($productName) . '"
                                        width="45"
                                        height="45"
                                        style="
                                            width:45px;
                                            height:45px;
                                            object-fit:contain;
                                            display:block;
                                            border-radius:5px;
                                        "
                                    >
                                </td>

                                <td
                                    valign="middle"
                                    style="
                                        padding-left:8px;
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:13px;
                                        line-height:18px;
                                        color:#29205c;
                                    "
                                >
                                    ' . $e($productName) . '
                                </td>

                            </tr>
                        </table>

                    </td>


                    <!-- PRICE -->
                    <td
                        width="14%"
                        valign="middle"
                        align="center"
                        style="
                            border:1px solid #e5d9f4;
                            padding:10px 5px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:13px;
                            color:#29205c;
                        "
                    >
                        ₹' .
                        number_format( $productPrice,  0  ) .
                    '</td>


                    <!-- QTY -->
                    <td
                        width="8%"
                        valign="middle"
                        align="center"
                        style="
                            border:1px solid #e5d9f4;
                            padding:10px 5px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:13px;
                            color:#29205c;
                        "
                    >
                        ' . $productQty . '
                    </td>


                    <!-- TOTAL -->
                    <td
                        width="18%"
                        valign="middle"
                        align="right"
                        style="
                            border:1px solid #e5d9f4;
                            padding:10px 10px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:13px;
                            color:#29205c;
                        "
                    >
                        ₹' .
                        number_format( $lineTotal, 0 ) .
                    '</td>

                </tr>
            ';

            $counter++;
        }
        
        return $productRows;
    }
    
    //to build summary rows 
    private function buildSummary($order){
         //helper
        $e = function ($value) {
            return htmlspecialchars(
                (string)$value,
                ENT_QUOTES,
                'UTF-8'
            );
        };
        
        $subtotal = (float)($order['sub_total_amount'] ?? 0);
        $discountedTotal =  (float)($order['discounted_total_amount'] ?? 0);
        $discount = $discountedTotal;
        $shippingFee = (float)($order['shipping_charged_amount'] ?? 0);
        $storeCredit = (float)($order['store_credit'] ?? 0);
        $gst = (float)($order['total_taxed_amount'] ?? 0);
        $codCharges = (float)($order['cod_charges'] ?? 0);
        
        //summary rows
        $summaryRows = '';
        $summaryRows .= '
            <tr>
                <td
                    style="
                        padding:5px 0;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;
                        color:#29205c;
                    "
                >
                    Subtotal
                </td>

                <td
                    align="right"
                    style="
                        padding:5px 0;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;
                        color:#29205c;
                    "
                >
                    ₹' .
                    number_format(
                        $subtotal,
                        0
                    ) .
                '</td>
            </tr>
        ';

        $summaryRows .= '
            <tr>
                <td
                    style="
                        padding:5px 0;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;
                        color:#29205c;
                    "
                >
                    Shipping Fee
                </td>

                <td
                    align="right"
                    style="
                        padding:5px 0;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;
                        color:#29205c;
                    "
                >
                    ₹' .
                    number_format(
                        $shippingFee,
                        0
                    ) .
                '</td>
            </tr>
        ';

        $summaryRows .= '
                <tr>
                    <td
                        style="
                            padding:5px 0;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:14px;
                            color:#29205c;
                        "
                    >
                        Discount
                    </td>

                    <td
                        align="right"
                        style="
                            padding:5px 0;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:14px;
                            color:#2e9b52;
                        "
                    >
                        -₹' .
                        number_format(
                            $discount,
                            0
                        ) .
                    '</td>
                </tr>
            ';

        $summaryRows .= '
                <tr>
                    <td
                        style="
                            padding:5px 0;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:14px;
                            color:#29205c;
                        "
                    >
                        Store Credit Used
                    </td>

                    <td
                        align="right"
                        style="
                            padding:5px 0;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:14px;
                            color:#2e9b52;
                        "
                    >
                        -₹' .
                        number_format(
                            $storeCredit,
                            0
                        ) .
                    '</td>
                </tr>
        ';

        $summaryRows .= '
                <tr>
                    <td
                        style="
                            padding:5px 0;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:14px;
                            color:#29205c;
                        "
                    >
                        COD Charges
                    </td>

                    <td
                        align="right"
                        style="
                            padding:5px 0;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:14px;
                            color:#29205c;
                        "
                    >
                        ₹' .
                        number_format(
                            $codCharges,
                            0
                        ) .
                    '</td>
                </tr>
        ';

        $summaryRows .= '
            <tr>
                <td
                    style="
                        padding:5px 0;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;
                        color:#29205c;
                    "
                >
                    GST (18%)
                </td>

                <td
                    align="right"
                    style="
                        padding:5px 0;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:14px;
                        color:#29205c;
                    "
                >
                    ₹' .
                    number_format(
                        $gst,
                        0
                    ) .
                '</td>
            </tr>
        ';
        
        return $summaryRows;
        
    }
    
    //send test email
    public function sendTestEmail($email)
    {
        $htmlContent =
            $this->emailHeader() .
            '
            <tr>
                <td style="
                    padding:60px 40px;
                    text-align:center;
                    font-family:Arial,Helvetica,sans-serif;
                ">
                    <h1 style="
                        margin:0 0 15px;
                        color:#171a31;
                        font-size:30px;
                    ">
                        Email Template Test
                    </h1>

                    <p style="
                        margin:0;
                        color:#666666;
                        font-size:15px;
                        line-height:24px;
                    ">
                        If you can see this message, the K-Pop Merch
                        email header and footer are working correctly.
                    </p>
                </td>
            </tr>
            ' .
            $this->emailFooter();

        $data = [
            'sender' => [
                'name'  => 'Support K-Pop Merch',
                'email' => 'support@kpopmerch.in'
            ],
            'to' => [
                [
                    'email' => $email
                ]
            ],
            'subject' => 'K-Pop Merch Email Template Test',
            'htmlContent' => $htmlContent
        ];

        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email'  );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt(  $ch,  CURLOPT_POST, true  );
        curl_setopt(  $ch,  CURLOPT_POSTFIELDS,  json_encode($data) );
        curl_setopt(  $ch, CURLOPT_HTTPHEADER,
            [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json'
            ]
        );

       $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        return [
            "http_code" => $httpCode,
            "curl_error" => $curlError,
            "response" => $result
        ];

    }
    
    //to generate otp
    public function generateOtp()
    {
        $chars ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $otp='';

        for($i=0;$i<6;$i++)
        {
            $otp .= $chars[
                random_int(  0,  strlen($chars)-1 )
            ];
        }

        return $otp;
    }
    
    public function sendOtpEmail($email)
    {
        $otp = $this->generateOtp();
        $baseUrl = rtrim(base_url(), '/');

        // Email images
        $otpIcon = $baseUrl . '/Images/email/ic_email3.png';

        // Split OTP into individual digits
        $otpBoxes = '';

        foreach (str_split((string) $otp) as $digit) {
            $otpBoxes .= '
                <span style="
                    display:inline-block;
                    width:72px;
                    height:72px;
                    line-height:72px;
                    margin:0 5px;
                    text-align:center;
                    font-family:Inter,Arial,sans-serif;
                    font-size:42px;
                    font-weight:700;
                    color:#25156b;
                    background:#faf7ff;
                    border:2px dashed #c9a7ff;
                    border-radius:14px;
                    box-sizing:border-box;
                ">
                    ' . htmlspecialchars($digit) . '
                </span>
            ';
        }

        // Remove final spacer
        $otpBoxes = preg_replace(
            '/<td style="width:8px;"><\/td>\s*$/',
            '',
            $otpBoxes
        );

        $html =
        $this->emailHeader()
        . '
            <!-- OTP EMAIL BODY -->

            <div style="text-align:center; padding:40px 20px 30px;">

                <img
                    src="' . base_url('Images/email/ic_email3.png') . '"
                    alt="Verification"
                    style="
                        width:180px;
                        max-width:100%;
                        height:auto;
                        display:block;
                        margin:0 auto 25px;
                    "
                >

                <h1 style="
                    margin:0 0 15px;
                    font-family:Poppins,Arial,sans-serif;
                    font-size:38px;
                    line-height:1.2;
                    font-weight:700;
                    color:#2b176b;
                ">
                    Your Verification Code is Here!
                </h1>

                <p style="
                    margin:0 0 8px;
                    font-family:Inter,Arial,sans-serif;
                    font-size:20px;
                    color:#21145f;
                ">
                    Hi there,
                </p>

                <p style="
                    margin:0 auto 8px;
                    font-family:Inter,Arial,sans-serif;
                    font-size:19px;
                    line-height:1.5;
                    color:#21145f;
                    max-width:700px;
                ">
                    Thanks for creating an account with K-Pop Merch!
                </p>

                <p style="
                    margin:0 auto 30px;
                    font-family:Inter,Arial,sans-serif;
                    font-size:19px;
                    line-height:1.5;
                    color:#21145f;
                    max-width:700px;
                ">
                    Please use the 6-digit code below to verify your email address:
                </p>

                <div style="
                    text-align:center;
                    margin:0 auto 35px;
                    white-space:nowrap;
                ">
                    ' . $otpBoxes . '
                </div>

                <div style="
                    max-width:650px;
                    margin:0 auto 30px;
                    padding:20px 25px;
                    background:#f3e8ff;
                    border-radius:16px;
                    text-align:left;
                ">

                    <div style="
                        font-family:Inter,Arial,sans-serif;
                        font-size:18px;
                        line-height:1.5;
                        color:#21145f;
                    ">
                        <strong>
                            This code will expire in 10 minutes.
                        </strong>
                        <br>
                        For your security, please do not share this code with anyone.
                    </div>

                </div>

                <p style="
                    margin:0 auto 30px;
                    font-family:Inter,Arial,sans-serif;
                    font-size:17px;
                    line-height:1.5;
                    color:#21145f;
                    max-width:650px;
                ">
                    If you didn’t request this code, you can safely ignore this email
                    or contact our support team.
                </p>

            </div>

        '
        . $this->emailFooter();

        $data = [
            'sender' => [
                'name'  => 'K-Pop Merch',
                'email' => 'support@kpopmerch.in'
            ],

            'to' => [
                [
                    'email' => $email
                ]
            ],

            'subject' => 'Your K-Pop Merch Verification Code',

            'htmlContent' => $html
        ];


        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            'https://api.brevo.com/v3/smtp/email'
        );

        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            true
        );

        curl_setopt(
            $ch,
            CURLOPT_POST,
            true
        );

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode($data)
        );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'accept: application/json',
                'api-key: '.$this->apiKey,
                'content-type: application/json'
            ]
        );

        $result = curl_exec($ch);

        curl_close($ch);


        if ($result) {

            return [
                "success" => 'true',
                "otp"     => $otp
            ];

        } else {

            return [
                "success" => 'false'
            ];

        }
    }
    
    //to send email for forgot password
    public function sendPasswordEmail($email, $firstName, $tempPassword)
    {
        $body = '
            <!-- FORGOT PASSWORD BODY -->
            <tr>
                <td
                    style="
                        padding:20px 45px 18px;
                        background:#ffffff;
                        text-align:center; " >
                    <!-- LOCK IMAGE -->
                    <img
                        src="' . base_url('Images/email/ic_email1.png') . '"
                        alt=""
                        width="145"
                        style="
                            width:145px;
                            height:auto;
                            margin:0 auto 10px; " >

                    <!-- TITLE -->
                    <h1
                        style="
                            margin:0 0 12px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:30px;
                            line-height:38px;
                            font-weight:700;
                            color:#2d1b62; " >
                        Here&rsquo;s Your Temporary Password!
                    </h1>

                    <!-- GREETING -->
                    <p
                        style="
                            margin:0 0 10px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:18px;
                            line-height:26px;
                            color:#29205c; " >
                        Hi ' . htmlspecialchars($firstName) . ',
                    </p>

                    <!-- DESCRIPTION -->
                    <p
                        style="
                            margin:0 auto 18px;
                            max-width:550px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:16px;
                            line-height:25px;
                            color:#29205c; " >
                        We received a request to reset your password for your
                        K-Pop Merch account. Here&rsquo;s your temporary password:
                    </p>


                    <!-- TEMPORARY PASSWORD -->
                    <table
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        align="center"
                        style="margin:0 auto 25px;"  >
                        <tr>
                            <td
                                style="
                                    background:#f8f1ff;
                                    border:2px dashed #c5a3ed;
                                    border-radius:14px;
                                    padding:10px 24px; " >
                                <table
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0" >
                                    <tr>
                                        <td
                                            valign="middle"
                                            style="padding-right:14px;" >
                                            <img
                                                src="' . base_url('Images/email/ic_email2.png') . '"
                                                alt=""
                                                width="45"
                                                style="
                                                    width:45px;
                                                    height:auto;  "  >
                                        </td>
                                        <td
                                            valign="middle"
                                            style="
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:30px;
                                                line-height:40px;
                                                font-weight:700;
                                                letter-spacing:2px;
                                                color:#35206f;
                                                white-space:nowrap; "  >
                                            ' . htmlspecialchars($tempPassword) . '
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>


                    <!-- STEP 1 -->
                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="margin-bottom:14px;"  >
                        <tr>
                            <td
                                width="80"
                                valign="middle"
                                align="center" >
                                <table
                                    width="55"
                                    height="55"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        width:55px;
                                        height:55px;
                                        background:#f4eaff;
                                        border-radius:50%;  " >
                                    <tr>
                                        <td align="center" valign="middle">

                                            <img
                                                src="' . base_url('Images/icons/ic_login.png') . '"
                                                alt=""
                                                width="32"
                                                height="32"
                                                style="
                                                    width:32px;
                                                    height:32px;  "  >
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td
                                valign="middle"
                                align="left"
                                style="padding-left:8px;"   >
                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:17px;
                                        line-height:23px;
                                        font-weight:700;
                                        color:#29205c;  "  >
                                    1.&nbsp; Login to your account
                                </div>
                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:15px;
                                        line-height:22px;
                                        color:#29205c;  "  >
                                    Use the temporary password above to log in to your account.
                                </div>
                            </td>
                        </tr>
                    </table>

                    <!-- STEP 2 -->
                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="margin-bottom:14px;"  >
                        <tr>

                            <td
                                width="80"
                                valign="middle"
                                align="center"  >
                                <table
                                    width="55"
                                    height="55"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        width:55px;
                                        height:55px;
                                        background:#f4eaff;
                                        border-radius:50%; " >
                                    <tr>
                                        <td align="center" valign="middle">

                                            <img
                                                src="' . base_url('Images/icons/ic_setting.png') . '"
                                                alt=""
                                                width="32"
                                                height="32"
                                                style="
                                                    width:32px;
                                                    height:32px;  "  >
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td
                                valign="middle"
                                align="left"
                                style="padding-left:8px;"  >

                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:17px;
                                        line-height:23px;
                                        font-weight:700;
                                        color:#29205c; "  >
                                    2.&nbsp; Go to My Account
                                </div>

                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:15px;
                                        line-height:22px;
                                        color:#29205c;  " >
                                    Once logged in, navigate to the Account Settings section.
                                </div>

                            </td>

                        </tr>
                    </table>


                    <!-- STEP 3 -->
                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="margin-bottom:18px;"  >
                        <tr>
                            <td
                                width="80"
                                valign="middle"
                                align="center" >
                                <table
                                    width="55"
                                    height="55"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        width:55px;
                                        height:55px;
                                        background:#f4eaff;
                                        border-radius:50%; "  >
                                    <tr>
                                        <td align="center" valign="middle">

                                            <img
                                                src="' . base_url('Images/icons/ic_lock1.png') . '"
                                                alt=""
                                                width="32"
                                                height="32"
                                                style="
                                                    width:32px;
                                                    height:32px; "  >
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td
                                valign="middle"
                                align="left"
                                style="padding-left:8px;" >
                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:17px;
                                        line-height:23px;
                                        font-weight:700;
                                        color:#29205c;  " >
                                    3.&nbsp; Change Your Password
                                </div>

                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:15px;
                                        line-height:22px;
                                        color:#29205c; " >
                                    Set a new password of your choice to keep your account secure.
                                </div>
                            </td>
                        </tr>
                    </table>

                    <!-- SECURITY MESSAGE -->
                    <p
                        style="
                            margin:0 auto 5px;
                            max-width:540px;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:15px;
                            line-height:23px;
                            color:#29205c; " >
                        If you didn&rsquo;t request a password reset, please ignore this email
                        or contact our support team.
                    </p>


                    <!-- HAPPY SHOPPING -->
                    <img
                        src="' . base_url('Images/email/email4.png') . '"
                        alt="Happy Shopping"
                        width="190"
                        style="
                            width:190px;
                            height:auto;
                            margin:4px auto 0; " >
                </td>
            </tr>

        ';


        $htmlContent = $this->emailHeader() . $body .  $this->emailFooter();


        $data = [
            'sender' => [
                'name'  => 'K-Pop Merch',
                'email' => 'support@kpopmerch.in'
            ],
            'to' => [
                [
                    'email' => $email
                ]
            ],
            'subject' => 'Your Temporary Password - K-Pop Merch',
            'htmlContent' => $htmlContent
        ];


        $ch = curl_init();

        curl_setopt(  $ch,  CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email' );
        curl_setopt( $ch,  CURLOPT_RETURNTRANSFER,  true );
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS,  json_encode($data) );

        curl_setopt(  $ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json'
            ]
        );

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);


        if($httpCode == 201)
        {
            return [
                'success' => true,
                'response' => $result
            ];
        }


        return [
            'success' => false,
            'response' => $result,
            'curl_error' => $curlError
        ];
    }

    //send email for order placed
    public function sendOrderPlacedEmail($emailData)
    {
        $order           = $emailData['order'] ?? [];
        $billingAddress  = $emailData['billing_address'] ?? [];
        $shippingAddress = $emailData['shipping_address'] ?? [];
        $products        = $emailData['products'] ?? [];
        $customerEmail   = trim($emailData['customer_email'] ?? '');

        if (!$customerEmail || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'success'  => false,
                'response' => 'A valid customer email is required.'
            ];
        }

        $e = static function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };

        $firstName = trim((string) (
            $order['customer_first_name'] ?? $billingAddress['first_name'] ?? ''
        ));

        if ($firstName === '') {
            $firstName = 'there';
        }

        $orderId = (int) ($order['id'] ?? 0);

        if ($orderId <= 0) {
            return [
                'success'  => false,
                'response' => 'A valid order ID is required.'
            ];
        }

        // Order date
        $orderDateRaw = $order['order_date'] ?? date('Y-m-d');
        $orderTimestamp = strtotime($orderDateRaw);

        if ($orderTimestamp === false) {
            $orderTimestamp = time();
        }

        $orderDate = date('d M Y', $orderTimestamp);
        $estimatedDeliveryDate = date('d M Y',strtotime($order['estimated_delivery_date']));

        $billingHtml  = $this->buildAddress($billingAddress);
        $shippingHtml = $this->buildAddress($shippingAddress);
        $productRows  = $this->buildProductRows($products);
        $summaryHtml  = $this->buildSummary($order);

        $grandTotal = (float) ($order['grand_total_amount'] ?? 0);
        $paymentMethod = $order['payment_type'] ?? 'Unknown';
        $paymentMethod = ucwords(str_replace('_', ' ', $paymentMethod));

        $baseUrl = rtrim(base_url(), '/');

        // Absolute URL required for email images
        $orderPlacedIcon = $baseUrl . '/Images/order/order_placed.png';

        $orderNumber = '#' . $orderId;

        $html = $this->emailHeader() . '
            <tr>
                <td style="padding:25px 30px 30px;background:#ffffff;
                           font-family:Arial,Helvetica,sans-serif;color:#21145f;">

                    <!-- Order placed icon -->
                    <div style="text-align:center;padding:0 0 5px;">
                        <img src="' . $e($orderPlacedIcon) . '"
                             alt="Order placed"
                             width="110"
                             style="display:block;width:110px;max-width:100%;
                                    height:auto;margin:0 auto 8px;border:0;">
                    </div>

                    <!-- Greeting -->
                    <div style="text-align:center;">
                        <h1 style="margin:0 0 6px;color:#21145f;
                                   font-size:34px;line-height:1.2;">
                            Thank You for Your Order!
                        </h1>

                        <p style="margin:0 0 5px;font-size:17px;">
                            Hi ' . $e($firstName) . ',
                        </p>

                        <p style="margin:0 auto 3px;font-size:17px;line-height:24px;">
                            We’ve received your order and it’s now being processed.
                        </p>

                        <p style="margin:0 auto 24px;font-size:17px;line-height:24px;">
                            We’ll notify you once it’s shipped!
                        </p>
                    </div>

                    <!-- Order information -->
                    <table role="presentation" width="100%" cellpadding="0"
                           cellspacing="0"
                           style="width:100%;background:#f8effc;
                                  border-radius:8px;margin-bottom:12px;">
                        <tr>
                            <td width="33.33%" align="center"
                                style="padding:14px 5px;color:#21145f;
                                       border-right:1px solid #dfcff3;">
                                <div style="font-size:14px;margin-bottom:7px;">
                                    Order Number
                                </div>
                                <div style="font-size:22px;font-weight:bold;">
                                    ' . $e($orderNumber) . '
                                </div>
                            </td>

                            <td width="33.33%" align="center"
                                style="padding:14px 5px;color:#21145f;
                                       border-right:1px solid #dfcff3;">
                                <div style="font-size:14px;margin-bottom:7px;">
                                    Order Date
                                </div>
                                <div style="font-size:16px;">
                                    ' . $e($orderDate) . '
                                </div>
                            </td>

                            <td width="33.33%" align="center"
                                style="padding:14px 5px;color:#21145f;">
                                <div style="font-size:14px;margin-bottom:7px;">
                                    Order Status
                                </div>
                                <span style="display:inline-block;background:#eee0ff;
                                             color:#7542b7;font-size:14px;
                                             font-weight:bold;padding:6px 12px;
                                             border-radius:20px;">
                                    Processing
                                </span>
                            </td>
                        </tr>
                    </table>

                    <!-- Billing and shipping addresses -->
                    <table role="presentation" width="100%" cellpadding="0"
                           cellspacing="0"
                           style="width:100%;margin-bottom:12px;">
                        <tr>
                            <td width="50%" valign="top"
                                style="padding:0 8px 0 0;">
                                <table role="presentation" width="100%"
                                       cellpadding="0" cellspacing="0"
                                       style="border:1px solid #dfd5fa;
                                              border-radius:6px;">
                                    <tr>
                                        <td style="background:#f0e8fc;padding:9px 12px;
                                                   font-weight:bold;color:#21145f;">
                                            Billing Address
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;color:#21145f;
                                                   font-size:14px;line-height:21px;">
                                            ' . $billingHtml . '
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td width="50%" valign="top"
                                style="padding:0 0 0 8px;">
                                <table role="presentation" width="100%"
                                       cellpadding="0" cellspacing="0"
                                       style="border:1px solid #dfd5fa;
                                              border-radius:6px;">
                                    <tr>
                                        <td style="background:#f0e8fc;padding:9px 12px;
                                                   font-weight:bold;color:#21145f;">
                                            Shipping Address
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:12px;color:#21145f;
                                                   font-size:14px;line-height:21px;">
                                            ' . $shippingHtml . '
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Products -->
                    <table role="presentation" width="100%" cellpadding="0"
                           cellspacing="0"
                           style="width:100%;border-collapse:collapse;
                                  margin-bottom:12px;">
                        <thead>
                            <tr style="background:#f0e8fc;color:#21145f;">
                                <th style="border:1px solid #dfd5fa;padding:9px 5px;">#</th>
                                <th style="border:1px solid #dfd5fa;padding:9px;
                                           text-align:left;">Item</th>
                                <th style="border:1px solid #dfd5fa;padding:9px 5px;">Price</th>
                                <th style="border:1px solid #dfd5fa;padding:9px 5px;">Qty</th>
                                <th style="border:1px solid #dfd5fa;padding:9px 5px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $productRows . '
                        </tbody>
                    </table>

                    <!-- Summary and payment/shipping details -->
                    <table role="presentation" width="100%" cellpadding="0"
                           cellspacing="0" style="width:100%;margin-bottom:12px;">
                        <tr>
                            <!-- Order summary -->
                            <td width="50%" valign="top"
                                style="padding:0 8px 0 0;">
                                <table role="presentation" width="100%"
                                       cellpadding="0" cellspacing="0"
                                       style="border:1px solid #dfd5fa;
                                              border-radius:6px;background:#faf7ff;">
                                    <tr>
                                        <td style="background:#f0e8fc;padding:10px 12px;
                                                   font-weight:bold;color:#21145f;">
                                            Order Summary
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:5px 12px 12px;">
                                            <table role="presentation" width="100%"
                                                   cellpadding="0" cellspacing="0">
                                                ' . $summaryHtml . '

                                                <tr>
                                                    <td colspan="2"
                                                        style="border-top:1px solid #d8c4ef;
                                                               padding-top:12px;"></td>
                                                </tr>

                                                <tr>
                                                    <td style="padding:7px 0;
                                                               font-size:18px;font-weight:bold;
                                                               color:#21145f;">
                                                        Total Amount
                                                    </td>
                                                    <td align="right"
                                                        style="padding:7px 0;
                                                               font-size:18px;font-weight:bold;
                                                               color:#21145f;white-space:nowrap;">
                                                        ₹' . number_format($grandTotal, 2) . '
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <!-- Payment and delivery -->
                            <td width="50%" valign="top"
                                style="padding:0 0 0 8px;">

                                <table role="presentation" width="100%"
                                       cellpadding="0" cellspacing="0"
                                       style="border:1px solid #dfd5fa;
                                              border-radius:6px;margin-bottom:10px;">
                                    <tr>
                                        <td width="45" valign="top"
                                            style="padding:12px 8px 10px 12px;
                                                   color:#8d50df;font-size:23px;">
                                            &#128179;
                                        </td>
                                        <td style="padding:12px 10px 10px 0;
                                                   color:#21145f;font-size:14px;
                                                   line-height:21px;">
                                            <strong>Payment Method</strong><br>
                                            ' . $e($paymentMethod) . '
                                        </td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%"
                                       cellpadding="0" cellspacing="0"
                                       style="border:1px solid #dfd5fa;
                                              border-radius:6px;margin-bottom:10px;">
                                    <tr>
                                        <td width="45" valign="top"
                                            style="padding:12px 8px 10px 12px;
                                                   color:#8d50df;font-size:23px;">
                                            &#128666;
                                        </td>
                                        <td style="padding:12px 10px 10px 0;
                                                   color:#21145f;font-size:14px;
                                                   line-height:21px;">
                                            <strong>Shipping Details</strong><br>
                                            Courier Partner: To be confirmed<br>
                                            Tracking Number: Shared once shipped
                                        </td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%"
                                       cellpadding="0" cellspacing="0"
                                       style="border:1px solid #dfd5fa;
                                              border-radius:6px;">
                                    <tr>
                                        <td width="45" valign="top"
                                            style="padding:12px 8px 10px 12px;
                                                   color:#8d50df;font-size:23px;">
                                            &#128197;
                                        </td>
                                        <td style="padding:12px 10px 10px 0;
                                                   color:#21145f;font-size:14px;
                                                   line-height:21px;">
                                            <strong>Estimated Delivery</strong><br>
                                            <strong>' . $e($estimatedDeliveryDate) . '</strong><br>
                                            <span style="font-size:12px;">
                                                You will receive a shipping confirmation
                                                email with tracking details once your
                                                order is on the way.
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        ' . $this->emailFooter();

        $data = [
            'sender' => [
                'name'  => 'K-Pop Merch',
                'email' => 'support@kpopmerch.in'
            ],
            'to' => [
                ['email' => $customerEmail]
            ],
            'cc' => [
                ['email' => 'orders@kpopmerch.in']
            ],
            'subject' => 'Your K-Pop Merch Order ' . $orderNumber . ' Has Been Placed!',
            'htmlContent' => $html
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'api-key: ' . $this->apiKey,
            'content-type: application/json'
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 201) {
            return [
                'success'  => true,
                'response' => $result
            ];
        }

        return [
            'success'    => false,
            'response'   => $result,
            'http_code'  => $httpCode,
            'curl_error' => $curlError
        ];
    }

    
    // Send order cancelled email   
    public function sendCancelOrderEmail($emailData)
    {
        $order = $emailData['order'] ?? [];
        $billingAddress = $emailData['billing_address'] ?? [];
        $shippingAddress = $emailData['shipping_address'] ?? [];
        $products = $emailData['products'] ?? [];
        $customerEmail = trim($emailData['customer_email'] ?? '');

        if (!$customerEmail) {
            return [
                'success' => false,
                'response' => 'Customer email is missing.'
            ];
        }

        //helper
        $e = function ($value) {
            return htmlspecialchars(
                (string)$value,
                ENT_QUOTES,
                'UTF-8'
            );
        };

        //customer name
        $firstName = trim(
            $order['customer_first_name']
            ?? $order['first_name']
            ?? $billingAddress['first_name']
            ?? ''
        );

        if (!$firstName) {
            $firstName = 'there';
        }

        //order details
        $orderId = (int)($order['id'] ?? 0);
        $orderNumber = '#' . $orderId;
        $orderDate = !empty($order['order_date'])
            ? date(
                'd M Y',
                strtotime($order['order_date'])
            )
            : '—';

        //billing address
        $billingHtml = $this->buildAddress($billingAddress);
        $shippingHtml = $this->buildAddress($shippingAddress);

        //product box
        $productRows = $this->buildProductRows($products);
       

        //order summary
        $grandTotal = (float)($order['grand_total_amount'] ?? 0);

        //summary rows
        $summaryRows = $this->buildSummary($order);
       

        //email body
        $body = '

            <!-- CANCELLATION BODY -->

            <tr>
                <td
                    style="
                        padding:35px 40px 30px;
                        background:#ffffff;
                    "
                >

                    <!-- MAIN ICON -->

                    <img
                        src="' .
                        base_url(
                            'Images/order/order_cancelled.png'
                        ) .
                        '"
                        alt="Order Cancelled"
                        width="145"
                        style="
                            width:145px;
                            height:auto;
                            margin:0 auto 12px;
                        "
                    >


                    <!-- TITLE -->

                    <h1
                        style="
                            margin:0 0 10px;
                            text-align:center;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:30px;
                            line-height:38px;
                            font-weight:700;
                            color:#25145f;
                        "
                    >
                        Your Order Has Been Cancelled
                    </h1>


                    <!-- GREETING -->

                    <p
                        style="
                            margin:0 0 6px;
                            text-align:center;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:17px;
                            line-height:25px;
                            color:#29205c;
                        "
                    >
                        Hi ' . $e($firstName) . ',
                    </p>


                    <p
                        style="
                            margin:0 auto 20px;
                            max-width:620px;
                            text-align:center;
                            font-family:Arial,Helvetica,sans-serif;
                            font-size:16px;
                            line-height:24px;
                            color:#29205c;
                        "
                    >
                        We&rsquo;re sorry, but your order
                        <strong>' . $e($orderNumber) . '</strong>
                        has been cancelled.<br>
                        If you have any questions, feel free to contact our support team.
                    </p>


                    <!-- ORDER META -->

                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="
                            background:#fff1f5;
                            border-radius:8px;
                            margin-bottom:20px;
                        "
                    >
                        <tr>

                            <!-- ORDER NUMBER -->

                            <td
                                width="33.33%"
                                align="center"
                                valign="middle"
                                style="
                                    padding:13px 8px;
                                    border-right:1px solid #e5c9d6;
                                "
                            >
                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:13px;
                                        color:#29205c;
                                        margin-bottom:5px;
                                    "
                                >
                                    Order Number
                                </div>

                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:20px;
                                        font-weight:700;
                                        color:#17134d;
                                    "
                                >
                                    ' . $e($orderNumber) . '
                                </div>
                            </td>


                            <!-- ORDER DATE -->

                            <td
                                width="33.33%"
                                align="center"
                                valign="middle"
                                style="
                                    padding:13px 8px;
                                    border-right:1px solid #e5c9d6;
                                "
                            >
                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:13px;
                                        color:#29205c;
                                        margin-bottom:5px;
                                    "
                                >
                                    Order Date
                                </div>

                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:15px;
                                        color:#17134d;
                                    "
                                >
                                    ' . $e($orderDate) . '
                                </div>
                            </td>


                            <!-- STATUS -->

                            <td
                                width="33.33%"
                                align="center"
                                valign="middle"
                                style="
                                    padding:13px 8px;
                                "
                            >
                                <div
                                    style="
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:13px;
                                        color:#29205c;
                                        margin-bottom:5px;
                                    "
                                >
                                    Order Status
                                </div>

                                <span
                                    style="
                                        display:inline-block;
                                        background:#ffcbd3;
                                        color:#c62e46;
                                        border-radius:20px;
                                        padding:5px 14px;
                                        font-family:Arial,Helvetica,sans-serif;
                                        font-size:14px;
                                        font-weight:700;
                                    "
                                >
                                    ✕&nbsp; Cancelled
                                </span>
                            </td>

                        </tr>
                    </table>


                    <!-- ADDRESSES -->

                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="margin-bottom:15px;"
                    >
                        <tr>

                            <!-- BILLING -->

                            <td
                                width="50%"
                                valign="top"
                                style="padding-right:7px;"
                            >

                                <table
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        border:1px solid #e2d5f2;
                                        border-radius:7px;
                                    "
                                >

                                    <tr>
                                        <td
                                            style="
                                                background:#eee1ff;
                                                padding:9px 14px;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:14px;
                                                font-weight:700;
                                                color:#20154d;
                                            "
                                        >
                                            Billing Address
                                        </td>
                                    </tr>

                                    <tr>
                                        <td
                                            style="
                                                padding:10px 14px;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:14px;
                                                line-height:20px;
                                                color:#29205c;
                                            "
                                        >
                                            ' . $billingHtml . '
                                        </td>
                                    </tr>

                                </table>

                            </td>


                            <!-- SHIPPING -->

                            <td
                                width="50%"
                                valign="top"
                                style="padding-left:7px;"
                            >

                                <table
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        border:1px solid #e2d5f2;
                                        border-radius:7px;
                                    "
                                >

                                    <tr>
                                        <td
                                            style="
                                                background:#eee1ff;
                                                padding:9px 14px;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:14px;
                                                font-weight:700;
                                                color:#20154d;
                                            "
                                        >
                                            Shipping Address
                                        </td>
                                    </tr>

                                    <tr>
                                        <td
                                            style="
                                                padding:10px 14px;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:14px;
                                                line-height:20px;
                                                color:#29205c;
                                            "
                                        >
                                            ' . $shippingHtml . '
                                        </td>
                                    </tr>

                                </table>

                            </td>

                        </tr>
                    </table>


                    <!-- PRODUCTS -->

                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="margin-bottom:15px;"
                    >

                        <tr>

                            <td
                                width="7%"
                                align="center"
                                style="
                                    background:#eee1ff;
                                    border:1px solid #e2d5f2;
                                    padding:9px 5px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:13px;
                                    font-weight:700;
                                    color:#20154d;
                                "
                            >
                                #
                            </td>

                            <td
                                width="53%"
                                align="center"
                                style="
                                    background:#eee1ff;
                                    border:1px solid #e2d5f2;
                                    padding:9px 5px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:13px;
                                    font-weight:700;
                                    color:#20154d;
                                "
                            >
                                Item
                            </td>

                            <td
                                width="14%"
                                align="center"
                                style="
                                    background:#eee1ff;
                                    border:1px solid #e2d5f2;
                                    padding:9px 5px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:13px;
                                    font-weight:700;
                                    color:#20154d;
                                "
                            >
                                Price
                            </td>

                            <td
                                width="8%"
                                align="center"
                                style="
                                    background:#eee1ff;
                                    border:1px solid #e2d5f2;
                                    padding:9px 5px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:13px;
                                    font-weight:700;
                                    color:#20154d;
                                "
                            >
                                Qty
                            </td>

                            <td
                                width="18%"
                                align="center"
                                style="
                                    background:#eee1ff;
                                    border:1px solid #e2d5f2;
                                    padding:9px 5px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    font-size:13px;
                                    font-weight:700;
                                    color:#20154d;
                                "
                            >
                                Total
                            </td>

                        </tr>

                        ' . $productRows . '

                    </table>


                    <!-- SUMMARY + REFUND -->

                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                    >
                        <tr>

                            <!-- ORDER SUMMARY -->

                            <td
                                width="50%"
                                valign="top"
                                style="padding-right:7px;"
                            >

                                <table
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        border:1px solid #e2d5f2;
                                        border-radius:7px;
                                    "
                                >

                                    <tr>
                                        <td
                                            colspan="2"
                                            style="
                                                background:#eee1ff;
                                                padding:9px 14px;
                                                font-family:Arial,Helvetica,sans-serif;
                                                font-size:15px;
                                                font-weight:700;
                                                color:#20154d;
                                            "
                                        >
                                            Order Summary
                                        </td>
                                    </tr>

                                    <tr>
                                        <td
                                            colspan="2"
                                            style="
                                                padding:8px 14px 10px;
                                            "
                                        >

                                            <table
                                                width="100%"
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                            >
                                                ' . $summaryRows . '
                                            </table>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td
                                            colspan="2"
                                            style="
                                                background:#eee1ff;
                                                padding:10px 14px;
                                                border-top:1px solid #e2d5f2;
                                            "
                                        >

                                            <table
                                                width="100%"
                                                cellpadding="0"
                                                cellspacing="0"
                                                border="0"
                                            >
                                                <tr>

                                                    <td
                                                        style="
                                                            font-family:Arial,Helvetica,sans-serif;
                                                            font-size:17px;
                                                            font-weight:700;
                                                            color:#20154d;
                                                        "
                                                    >
                                                        Total Amount
                                                    </td>

                                                    <td
                                                        align="right"
                                                        style="
                                                            font-family:Arial,Helvetica,sans-serif;
                                                            font-size:17px;
                                                            font-weight:700;
                                                            color:#20154d;
                                                        "
                                                    >
                                                        ₹' .
                                                        number_format(
                                                            $grandTotal,
                                                            0
                                                        ) .
                                                    '</td>

                                                </tr>
                                            </table>

                                        </td>
                                    </tr>

                                </table>

                            </td>


                            <!-- REFUND INFORMATION -->

                            <td
                                width="50%"
                                valign="top"
                                style="padding-left:7px;"
                            >

                                <table
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        border:1px solid #ddd2f4;
                                        border-radius:7px;
                                        background:#ffffff;
                                    "
                                >

                                    <tr>

                                        <td
                                            valign="top"
                                            width="48"
                                            style="padding:13px 5px 10px 14px;"
                                        >
                                            <div
                                                style="
                                                    width:40px;
                                                    height:40px;
                                                    background:#f1e4ff;
                                                    border-radius:50%;
                                                    text-align:center;
                                                    line-height:40px;
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:22px;
                                                    color:#9855dc;
                                                "
                                            >
                                                ₹
                                            </div>
                                        </td>

                                        <td
                                            valign="top"
                                            style="
                                                padding:13px 14px 14px 4px;
                                            "
                                        >

                                            <div
                                                style="
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:16px;
                                                    font-weight:700;
                                                    color:#29205c;
                                                    margin-bottom:7px;
                                                "
                                            >
                                                Refund Information
                                            </div>

                                            <div
                                                style="
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:13px;
                                                    line-height:19px;
                                                    color:#29205c;
                                                "
                                            >
                                                Your refund will be added to your
                                                store credit. This store credit does
                                                not expire. You can use this amount
                                                to place another order on this website.
                                            </div>

                                        </td>

                                    </tr>

                                </table>


                                <!-- NEED HELP -->

                                <table
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        margin-top:12px;
                                        border:1px solid #ddd2f4;
                                        border-radius:7px;
                                        background:#ffffff;
                                    "
                                >

                                    <tr>

                                        <td
                                            valign="top"
                                            width="48"
                                            style="padding:13px 5px 10px 14px;"
                                        >
                                            <div
                                                style="
                                                    width:40px;
                                                    height:40px;
                                                    background:#f1e4ff;
                                                    border-radius:50%;
                                                    text-align:center;
                                                    line-height:40px;
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:23px;
                                                    font-weight:700;
                                                    color:#9855dc;
                                                "
                                            >
                                                ?
                                            </div>
                                        </td>

                                        <td
                                            valign="top"
                                            style="
                                                padding:13px 14px 14px 4px;
                                            "
                                        >

                                            <div
                                                style="
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:16px;
                                                    font-weight:700;
                                                    color:#29205c;
                                                    margin-bottom:7px;
                                                "
                                            >
                                                Need Help?
                                            </div>

                                            <div
                                                style="
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:13px;
                                                    line-height:19px;
                                                    color:#29205c;
                                                    margin-bottom:12px;
                                                "
                                            >
                                                If you have any questions regarding
                                                this cancellation, please contact
                                                our support team.
                                            </div>

                                            <a
                                                href="' . base_url() . '"
                                                target="_blank"
                                                style="
                                                    display:inline-block;
                                                    background:#9855dc;
                                                    color:#ffffff;
                                                    padding:9px 18px;
                                                    border-radius:6px;
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:13px;
                                                    font-weight:700;
                                                    text-decoration:none;
                                                "
                                            >
                                                Contact Us
                                            </a>

                                        </td>

                                    </tr>

                                </table>

                            </td>

                        </tr>
                    </table>

                </td>
            </tr>

        ';

        //complete email
        $htmlContent = $this->emailHeader() . $body . $this->emailFooter();

        //brevo
        $data = [
            'sender' => [
                'name'  => 'K-Pop Merch',
                'email' => 'support@kpopmerch.in'
            ],

            'to' => [
                [
                    'email' => $customerEmail
                ]
            ],
            'cc' => [
                [
                    'email' => 'orders@kpopmerch.in'
                ]
            ],

            'subject' =>
                'Your Order #' .
                $orderId .
                ' Has Been Cancelled - K-Pop Merch',

            'htmlContent' => $htmlContent
        ];


        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://api.brevo.com/v3/smtp/email' );
        curl_setopt(  $ch,  CURLOPT_RETURNTRANSFER,  true );
        curl_setopt( $ch, CURLOPT_POST,  true  );
        curl_setopt($ch,  CURLOPT_POSTFIELDS, json_encode($data) );
        curl_setopt( $ch,  CURLOPT_HTTPHEADER,
            [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json'
            ]  );

        $result = curl_exec($ch);
        $httpCode = curl_getinfo(  $ch, CURLINFO_HTTP_CODE   );
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode == 201) {
            return [
                'success'  => true,
                'response' => $result
            ];
        }

        return [
            'success'    => false,
            'response'   => $result,
            'curl_error' => $curlError
        ];
    }
    
    // Send back-in-stock notification email
    public function sendBackInStockEmail($email, $product)
    {
        $e = function ($value) {
            return htmlspecialchars(
                (string) $value,
                ENT_QUOTES,
                'UTF-8'
            );
        };

        $productId = trim((string) ($product['id'] ?? ''));
        $productName = trim((string) ($product['product_name'] ?? ''));
        $productImage = trim((string) ($product['main_image'] ?? ''));
        $price = (float) ($product['price'] ?? 0);

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            $productId === '' ||
            $productName === '' ||
            $productImage === ''
        ) {
            return [
                'success' => false,
                'response' => 'Invalid email or incomplete product details.'
            ];
        }

        // Website product page, not the supplier URL
        $productUrl = base_url(
            'product/' . rawurlencode($productId)
        );

        $iconUrl = base_url('Images/email/ic_email6.png');
        $headingUrl = base_url('Images/email/email6.png');

        $body = '
            <tr>
                <td style="
                    padding:30px 25px 15px;
                    text-align:center;
                    background:#ffffff;
                ">
                    <img
                        src="' . $e($headingUrl) . '"
                        alt="It\'s Back in Stock!"
                        style="
                            display:block;
                            width:100%;
                            max-width:650px;
                            height:auto;
                            margin:0 auto 15px;
                        "
                    >

                    <p style="
                        margin:0 auto 25px;
                        max-width:600px;
                        font-family:Arial,Helvetica,sans-serif;
                        font-size:16px;
                        line-height:25px;
                        color:#21145f;
                    ">
                        Good news! An item you were interested in is
                        now back in stock. Don\'t miss your chance
                        to grab it before it\'s gone again!
                    </p>
                </td>
            </tr>

            <tr>
                <td style="
                    padding:20px 25px 30px;
                    background:#ffffff;
                ">
                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        style="
                            background:#faf7ff;
                            border-radius:12px;
                        "
                    >
                        <tr>
                            <td
                                width="50%"
                                valign="middle"
                                align="center"
                                style="padding:20px;"
                            >
                                <a
                                    href="' . $e($productUrl) . '"
                                    target="_blank"
                                >
                                    <img
                                        src="' . $e($productImage) . '"
                                        alt="' . $e($productName) . '"
                                        style="
                                            display:block;
                                            width:100%;
                                            max-width:300px;
                                            max-height:330px;
                                            height:auto;
                                            margin:0 auto;
                                            object-fit:contain;
                                        "
                                    >
                                </a>
                            </td>

                            <td
                                width="50%"
                                valign="middle"
                                style="
                                    padding:20px 20px 20px 5px;
                                    font-family:Arial,Helvetica,sans-serif;
                                    color:#21145f;
                                "
                            >
                                <div style="
                                    display:inline-block;
                                    margin-bottom:12px;
                                    padding:8px 12px;
                                    border-radius:20px;
                                    background:#eee4ff;
                                    color:#7540cf;
                                    font-size:13px;
                                    font-weight:700;
                                ">
                                    <img
                                        src="' . $e($iconUrl) . '"
                                        alt=""
                                        width="20"
                                        height="20"
                                        style="
                                            display:inline-block;
                                            width:20px;
                                            height:20px;
                                            vertical-align:middle;
                                            margin-right:5px;
                                        "
                                    >
                                    IN STOCK
                                </div>

                                <h2 style="
                                    margin:0 0 8px;
                                    font-size:22px;
                                    line-height:1.35;
                                    color:#21145f;
                                ">
                                    ' . $e($productName) . '
                                </h2>

                                <p style="
                                    margin:0 0 20px;
                                    font-size:25px;
                                    font-weight:700;
                                    color:#8050df;
                                ">
                                    ₹' . number_format($price, 0) . '
                                </p>

                                <a
                                    href="' . $e($productUrl) . '"
                                    target="_blank"
                                    style="
                                        display:block;
                                        padding:15px 10px;
                                        border-radius:8px;
                                        background:#8050df;
                                        color:#ffffff;
                                        text-align:center;
                                        font-size:16px;
                                        font-weight:700;
                                        text-decoration:none;
                                    "
                                >
                                    View Product
                                </a>

                                <p style="
                                    margin:12px 0 0;
                                    text-align:center;
                                    font-size:12px;
                                    line-height:18px;
                                    color:#21145f;
                                ">
                                    Stock is limited. Order now before
                                    it sells out again!
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        ';

        $htmlContent =
            $this->emailHeader() .
            $body .
            $this->emailFooter();

        $data = [
            'sender' => [
                'name'  => 'K-Pop Merch',
                'email' => 'support@kpopmerch.in'
            ],
            'to' => [
                [
                    'email' => $email
                ]
            ],
            'subject' => 'It\'s Back in Stock! - ' . $productName,
            'htmlContent' => $htmlContent
        ];

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            'https://api.brevo.com/v3/smtp/email'
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'api-key: ' . $this->apiKey,
            'content-type: application/json'
        ]);

        // Avoid a cron process hanging indefinitely
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($httpCode === 201) {
            return [
                'success' => true,
                'response' => $result
            ];
        }

        return [
            'success' => false,
            'http_code' => $httpCode,
            'response' => $result,
            'curl_error' => $curlError
        ];
    }

    
    
    
    
    
    
    
    //email header
    public function emailHeader()
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <style>
                @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap");

                body {
                    margin:0;
                    padding:0;
                    background:#ffffff;
                    font-family:"Inter", Arial, Helvetica, sans-serif;
                }

                table {
                    border-collapse:collapse;
                    border-spacing:0;
                }

                img {
                    border:0;
                    display:block;
                }

                a {
                    text-decoration:none;
                }
            </style>
        </head>

        <body>

            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="background:#ffffff;"
            >

                <tr>
                    <td align="center">

                        <table
                            width="700"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                width:700px;
                                max-width:700px;
                                background:#ffffff;
                            "
                        >

                            <!-- TOP BENEFITS BAR -->
                            <tr>
                                <td
                                    style="
                                        background:#dec9f3;
                                        padding:10px 15px;
                                    "
                                >

                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                    >
                                        <tr>

                                            <!-- OFFICIAL PRODUCTS -->
                                            <td
                                                width="33.33%"
                                                align="center"
                                                style="
                                                    border-right:1px solid #b99ad9;
                                                    padding:3px 8px;
                                                "
                                            >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>

                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;"
                                                        >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_official.png') . '"
                                                                width="25"
                                                                height="25"
                                                                alt=""
                                                                style="
                                                                    width:25px;
                                                                    height:25px;
                                                                "
                                                            >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:12px;
                                                                font-weight:600;
                                                                color:#111111;
                                                                white-space:nowrap;
                                                            "
                                                        >
                                                            100% Official Products
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>


                                            <!-- FAST SHIPPING -->
                                            <td
                                                width="33.33%"
                                                align="center"
                                                style="
                                                    border-right:1px solid #b99ad9;
                                                    padding:3px 8px;
                                                "
                                            >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>

                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;"
                                                        >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_ship.png') . '"
                                                                width="25"
                                                                height="25"
                                                                alt=""
                                                                style="
                                                                    width:25px;
                                                                    height:25px;
                                                                "
                                                            >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:12px;
                                                                font-weight:600;
                                                                color:#111111;
                                                                white-space:nowrap;
                                                            "
                                                        >
                                                            Fast Shipping (3-5 Days)
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>


                                            <!-- SECURE PAYMENTS -->
                                            <td
                                                width="33.33%"
                                                align="center"
                                                style="padding:3px 8px;"
                                            >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>

                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;"
                                                        >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_lock.png') . '"
                                                                width="25"
                                                                height="25"
                                                                alt=""
                                                                style="
                                                                    width:25px;
                                                                    height:25px;
                                                                "
                                                            >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:12px;
                                                                font-weight:600;
                                                                color:#111111;
                                                                white-space:nowrap;
                                                            "
                                                        >
                                                            Secure Payments
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                    </table>

                                </td>
                            </tr>


                            <!-- MAIN HEADER -->
                            <tr>
                                <td
                                    style="
                                        padding:20px 35px;
                                        background:#ffffff;
                                    "
                                >

                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                    >
                                        <tr>

                                            <!-- LOGO + BRAND -->
                                            <td
                                                width="70%"
                                                valign="middle"
                                            >

                                                <table
                                                    cellpadding="0"
                                                    cellspacing="0"
                                                    border="0"
                                                >
                                                    <tr>

                                                        <td
                                                            valign="middle"
                                                            style="padding-right:15px;"
                                                        >
                                                            <a
                                                                href="' . base_url() . '"
                                                                target="_blank"
                                                            >
                                                                <img
                                                                    src="' . base_url('Images/logo.png') . '"
                                                                    alt="K-Pop Merch"
                                                                    width="82"
                                                                    style="
                                                                        width:82px;
                                                                        height:auto;
                                                                    "
                                                                >
                                                            </a>
                                                        </td>

                                                        <td valign="middle">

                                                            <div
                                                                style="
                                                                    font-family:Arial,Helvetica,sans-serif;
                                                                    font-size:25px;
                                                                    line-height:30px;
                                                                    font-weight:700;
                                                                    color:#111111;
                                                                "
                                                            >
                                                                K-Pop Merch
                                                            </div>

                                                            <div
                                                                style="
                                                                    margin-top:3px;
                                                                    font-family:Arial,Helvetica,sans-serif;
                                                                    font-size:15px;
                                                                    line-height:20px;
                                                                    color:#202040;
                                                                "
                                                            >
                                                                Your Ultimate K-Pop Store
                                                            </div>

                                                        </td>

                                                    </tr>
                                                </table>

                                            </td>


                                            <!-- GOOD MUSIC IMAGE -->
                                            <td
                                                width="30%"
                                                valign="middle"
                                                align="right"
                                            >

                                                <img
                                                    src="' . base_url('Images/email/email1.png') . '"
                                                    alt="Good Music Brighter Days"
                                                    width="95"
                                                    style="
                                                        width:95px;
                                                        height:auto;
                                                        margin-left:auto;
                                                    "
                                                >

                                            </td>

                                        </tr>
                                    </table>

                                </td>
                            </tr>


                            <!-- NAVIGATION -->
                            <tr>
                                <td
                                    style="
                                        background:#b98adf;
                                        padding:12px 20px;
                                    "
                                >

                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                    >
                                        <tr>

                                            <!-- CATEGORIES -->
                                            <td
                                                width="25%"
                                                align="center"
                                                style="
                                                    border-right:1px solid rgba(255,255,255,0.65);
                                                "
                                            >
                                                <a
                                                    href="' . base_url('categories/1') . '"
                                                    target="_blank"
                                                    style="
                                                        color:#ffffff;
                                                        font-family:Arial,Helvetica,sans-serif;
                                                        font-size:13px;
                                                        font-weight:600;
                                                    "
                                                >
                                                    Categories
                                                </a>
                                            </td>


                                            <!-- BRANDS -->
                                            <td
                                                width="25%"
                                                align="center"
                                                style="
                                                    border-right:1px solid rgba(255,255,255,0.65);
                                                "
                                            >
                                                <a
                                                    href="' . base_url('bands_all') . '"
                                                    target="_blank"
                                                    style="
                                                        color:#ffffff;
                                                        font-family:Arial,Helvetica,sans-serif;
                                                        font-size:13px;
                                                        font-weight:600;
                                                    "
                                                >
                                                    Brands
                                                </a>
                                            </td>


                                            <!-- PRE-ORDER -->
                                            <td
                                                width="25%"
                                                align="center"
                                                style="
                                                    border-right:1px solid rgba(255,255,255,0.65);
                                                "
                                            >
                                                <a
                                                    href="' . base_url('preorder') . '"
                                                    target="_blank"
                                                    style="
                                                        color:#ffffff;
                                                        font-family:Arial,Helvetica,sans-serif;
                                                        font-size:13px;
                                                        font-weight:600;
                                                    "
                                                >
                                                    Pre-order
                                                </a>
                                            </td>


                                            <!-- ON-HAND -->
                                            <td
                                                width="25%"
                                                align="center"
                                            >
                                                <a
                                                    href="' . base_url('onhand') . '"
                                                    target="_blank"
                                                    style="
                                                        color:#ffffff;
                                                        font-family:Arial,Helvetica,sans-serif;
                                                        font-size:13px;
                                                        font-weight:600;
                                                    "
                                                >
                                                    On-Hand
                                                </a>
                                            </td>

                                        </tr>
                                    </table>

                                </td>
                            </tr>


                            <!-- EMAIL BODY BEGINS HERE -->
        ';
    }
    
    //email footer
    public function emailFooter()
    {
        return '

                            <!-- FOOTER -->
                            <tr>
                                <td
                                    style="
                                        background:#f7efff;
                                        padding:28px 30px 25px;
                                    " >

                                    <!-- TOP FOOTER -->
                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0" >
                                        <tr>
                                            <!-- THANK YOU -->
                                            <td
                                                width="25%"
                                                valign="middle"
                                                align="left" >
                                                <img
                                                    src="' . base_url('Images/email/email2.png') . '"
                                                    alt="Thank you for being here"
                                                    width="115"
                                                    style="
                                                        width:115px;
                                                        height:auto;
                                                    " >
                                            </td>

                                            <!-- INSTAGRAM + TAGLINE -->
                                            <td
                                                width="50%"
                                                valign="middle"
                                                align="center" >
                                                <a
                                                    href="https://www.instagram.com/kp0pmerch/"
                                                    target="_blank"  >
                                                    <img
                                                        src="' . base_url('Images/icons/ic_insta.png') . '"
                                                        alt="Instagram"
                                                        width="48"
                                                        height="48"
                                                        style="
                                                            width:48px;
                                                            height:48px;
                                                            margin:0 auto; " >
                                                </a>

                                                <div
                                                    style="
                                                        margin-top:13px;
                                                        font-family:Arial,Helvetica,sans-serif;
                                                        font-size:11px;
                                                        letter-spacing:3px;
                                                        color:#171a31;
                                                        white-space:nowrap; " >
                                                    MUSIC&nbsp;&nbsp; PEOPLE&nbsp;&nbsp; GOOD VIBES&nbsp;&nbsp; ALWAYS&nbsp;&nbsp;♡
                                                </div>

                                            </td>

                                            <!-- COLLECT -->
                                            <td
                                                width="25%"
                                                valign="middle"
                                                align="right" >
                                                <img
                                                    src="' . base_url('Images/email/email3.png') . '"
                                                    alt="Collect Your Happy Moments"
                                                    width="115"
                                                    style="
                                                        width:115px;
                                                        height:auto;
                                                        margin-left:auto; " >
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- DIVIDER -->
                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"  >
                                        <tr>
                                            <td
                                                style="
                                                    padding-top:20px;
                                                    border-bottom:1px solid #d4b8ed;
                                                    font-size:0;
                                                    line-height:0; " >
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- SERVICE FEATURES -->
                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                        style="margin-top:20px;"  >
                                        <tr>

                                            <!-- CONTACT -->
                                            <td
                                                width="25%"
                                                valign="middle"
                                                align="center" >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;" >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_contact.png') . '"
                                                                width="38"
                                                                height="38"
                                                                alt=""
                                                                style="
                                                                    width:38px;
                                                                    height:38px;  " >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:11px;
                                                                line-height:16px;
                                                                color:#202040; " >
                                                            Need Help?<br>
                                                            Contact Us
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>

                                            <!-- SHIPPING -->
                                            <td
                                                width="25%"
                                                valign="middle"
                                                align="center" >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;"  >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_ship.png') . '"
                                                                width="38"
                                                                height="38"
                                                                alt=""
                                                                style="
                                                                    width:38px;
                                                                    height:38px; "  >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:11px;
                                                                line-height:16px;
                                                                color:#202040;  "  >
                                                            Fast &amp; Reliable<br>
                                                            Shipping
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                            <!-- SECURE PAYMENTS -->
                                            <td
                                                width="25%"
                                                valign="middle"
                                                align="center"
                                            >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>

                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;"
                                                        >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_shield.png') . '"
                                                                width="38"
                                                                height="38"
                                                                alt=""
                                                                style="
                                                                    width:38px;
                                                                    height:38px;
                                                                "
                                                            >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:11px;
                                                                line-height:16px;
                                                                color:#202040;
                                                            "
                                                        >
                                                            Secure<br>
                                                            Payments
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>


                                            <!-- K-POP FANS -->
                                            <td
                                                width="25%"
                                                valign="middle"
                                                align="center"
                                            >
                                                <table cellpadding="0" cellspacing="0" border="0">
                                                    <tr>

                                                        <td
                                                            valign="middle"
                                                            style="padding-right:7px;"
                                                        >
                                                            <img
                                                                src="' . base_url('Images/icons/ic_heart.png') . '"
                                                                width="38"
                                                                height="38"
                                                                alt=""
                                                                style="
                                                                    width:38px;
                                                                    height:38px;
                                                                "
                                                            >
                                                        </td>

                                                        <td
                                                            valign="middle"
                                                            style="
                                                                font-family:Arial,Helvetica,sans-serif;
                                                                font-size:11px;
                                                                line-height:16px;
                                                                color:#202040;
                                                            "
                                                        >
                                                            K-Pop Fans<br>
                                                            Just Like You
                                                        </td>

                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                    </table>

                                </td>
                            </tr>


                            <!-- COPYRIGHT -->
                            <tr>
                                <td
                                    style="
                                        background:#c49be7;
                                        padding:14px 25px;
                                    "
                                >

                                    <table
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0"
                                    >
                                        <tr>

                                            <td
                                                width="50%"
                                                align="left"
                                                style="
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:11px;
                                                    color:#ffffff;
                                                    white-space:nowrap;
                                                "
                                            >
                                                &copy; ' . date('Y') . ' K-Pop Merch. All rights reserved.
                                            </td>

                                            <td
                                                width="50%"
                                                align="right"
                                                style="
                                                    font-family:Arial,Helvetica,sans-serif;
                                                    font-size:11px;
                                                    color:#ffffff;
                                                    white-space:nowrap;
                                                "
                                            >
                                                Spread the Love ♡&nbsp;&nbsp;
                                                Keep Supporting ♡&nbsp;&nbsp;
                                                Stay You ♡
                                            </td>

                                        </tr>
                                    </table>

                                </td>
                            </tr>


                        </table>

                    </td>
                </tr>

            </table>

        </body>
        </html>
        ';
    }

}
