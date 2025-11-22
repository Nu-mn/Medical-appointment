<?php
session_start();

// Hàm POST request
function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Kiểm tra booking_id
if (!isset($_POST['booking_id'])) {
    die("Thiếu booking_id");
}

$booking_id = $_POST['booking_id'];




// Lấy thông tin booking từ BookingAPI
$bookingRes = file_get_contents("http://localhost/Medical-appointment/source/models/booking_service/BookingAPI.php?booking_id=" . $booking_id);
$bookingData = json_decode($bookingRes, true);

if (!$bookingData || $bookingData['status'] !== 'success') {
    die("Booking API lỗi hoặc trả về không hợp lệ");
}

if (!isset($bookingData['data']['amount'])) {
    die("Không lấy được thông tin booking hoặc amount");
}

$user_id = (int)$bookingData['data']['user_id'];
$amount = (int)$bookingData['data']['amount'];





// Tạo payment trong PaymentAdiePI
$paymentPayload = [
    "booking_id" => $booking_id,
    "amount" => $amount,
    "user_id" => $user_id
];

$paymentRes = execPostRequest(
    "http://localhost/Medical-appointment/source/models/payment_service/PaymentAPI.php/payments/create",
    json_encode($paymentPayload)
);
$paymentData = json_decode($paymentRes, true);


var_dump($paymentRes);
var_dump($paymentData);

if (!$paymentData || !isset($paymentData['payment']['payment_id'])) {
    die("Không tạo được payment");
}


$payment_id = $paymentData['payment']['payment_id'];


// =========================================
// TẠO THANH TOÁN MOMO
// =========================================
function paymentWithMomo($booking_id, $payment_id, $amount) {
    $endpoint   = "https://test-payment.momo.vn/v2/gateway/api/create";
    $partnerCode = "MOMOBKUN20180529";
    $accessKey   = "klm05TvNBzhg7h7j";
    $secretKey   = "at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa";

    $orderInfo   = "booking_id=$booking_id";
    $orderId     = time() . "_" . $booking_id;
    $requestId   = time() . "_" . $booking_id;
    $extraData   = "payment_id=$payment_id";

    $redirectUrl = "http://localhost/Medical-appointment/source/views/index.php?nav=invoice";
    $ipnUrl      = "http://localhost/Medical-appointment/source/views/index.php?nav=invoice";

    $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=payWithATM";

    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    $data = [
        'partnerCode' => $partnerCode,
        'partnerName' => "MoMoTest",
        'storeId'     => "MoMoStore",
        'requestId'   => $requestId,
        'amount'      => $amount,
        'orderId'     => $orderId,
        'orderInfo'   => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl'      => $ipnUrl,
        'lang'        => "vi",
        'extraData'   => $extraData,
        'requestType' => "payWithATM",
        'signature'   => $signature
    ];

    $result = execPostRequest($endpoint, json_encode($data));
    $jsonResult = json_decode($result, true);

    if (isset($jsonResult['payUrl'])) {
        header('Location: ' . $jsonResult['payUrl']);
        exit;
    } else {
        echo "<h3>Lỗi tạo thanh toán MoMo</h3>";
        echo "<pre>";
        print_r($jsonResult);
        echo "</pre>";
    }
}

// Gọi thanh toán
paymentWithMomo($booking_id, $payment_id, $amount);
?>


