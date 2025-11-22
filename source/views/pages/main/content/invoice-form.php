<?php



// Hàm gửi POST request
function execPostRequest($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

// ===== LẤY DỮ LIỆU GET TỪ URL =====
$resultCode = $_GET['resultCode'] ?? null;
parse_str($_GET['extraData'] ?? '', $extra);
$payment_id = $extra['payment_id'] ?? null;
parse_str($_GET['orderInfo'] ?? '', $extra);
$booking_id = $extra['booking_id'] ?? null;




// ===== GỬI KẾT QUẢ SANG PAYMENT API ĐỂ LƯU DATABASE =====
$apiUrl = "http://localhost/Medical-appointment/source/models/payment_service/PaymentAPI.php/payments/result";

$postData = [
    'result_code' => $resultCode,
    'payment_id' => $payment_id,
    'booking_id' => $booking_id
];

$response = execPostRequest($apiUrl, $postData);

?>





<style>
body { font-family: Arial, sans-serif; background: #DBEEFD; margin: 0; }
h1 { text-align: center; color: #2f3640; margin: 20px; }

.invoice-list { max-width: 700px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px; }

.invoice-card { background: #fff; border-radius: 8px; padding: 15px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s; }
.invoice-card:hover { transform: translateY(-2px); }

.invoice-header { display: flex; justify-content: space-between; font-weight: bold; color: #2f3640; margin-bottom: 5px; flex-wrap: wrap; }
.invoice-patient { font-size: 1.1em; font-weight: bold; margin-bottom: 5px; }
.invoice-hospital { color: #0097e6; margin-bottom: 10px; }

.invoice-details div { margin-bottom: 4px; }
.status-success { color: #4cd137; font-weight: bold; }
.status-failed { color: #e84118; font-weight: bold; }

.empty-message { text-align: center; color: #e84118; font-weight: bold; margin-top: 50px; }
</style>

<body>

<h1>Lịch sử phiếu khám</h1>
<div class="invoice-list" id="invoiceList"></div>

<script>
const invoiceList = document.getElementById("invoiceList");
const userId =  <?php echo json_encode($_SESSION["user_id"]); ?>;

if (!userId) {
    invoiceList.innerHTML = `<div class="empty-message">Người dùng chưa đăng nhập</div>`;
} else {
    fetch(`http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/history?user_id=${userId}`)
    .then(res => {
        if (res.status === 503) {
            window.location.href = "/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
            return;
        }
        return res.json();
    })
    .then(invoices => {
        if (!invoices || invoices.length === 0) {
            invoiceList.innerHTML = `<div class="empty-message">Chưa có phiếu khám nào</div>`;
            return;
        }

        invoices.forEach(inv => {
            const card = document.createElement("div");
            card.className = "invoice-card";

            // Xác định class trạng thái
            let statusClass = inv.status === "Thành công" ? "status-success" : "status-failed";
            card.innerHTML = `
                <div class="invoice-header">
                    <div>Mã phiếu: ${inv.invoice_id}</div>
                    <div class="${statusClass}">${inv.status}</div>
                </div>
                <div class="invoice-patient">${inv.patient_name}</div>
                <div class="invoice-hospital">BỆNH VIỆN ĐẠI HỌC TÔN ĐỨC THẮNG</div>
                <div class="invoice-details">
                    <div>Chuyên khoa: ${inv.specialization_name}</div>
                    <div>Phí khám: ${parseFloat(inv.fee).toLocaleString()} VNĐ</div>
                </div>
            `;

            invoiceList.appendChild(card);
        });
    })
    .catch(err => {
        invoiceList.innerHTML = `<div class="empty-message">${err.message}</div>`;
    });
}
</script>

</body>



