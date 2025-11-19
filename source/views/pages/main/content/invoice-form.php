<style>
body { font-family: Arial, sans-serif; background: #f7f8fa; margin: 0; }
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
const userId = <?php echo isset($user_id) ? json_encode($user_id) : 1; ?>;

if (!userId) {
    invoiceList.innerHTML = `<div class="empty-message">Người dùng chưa đăng nhập</div>`;
} else {
    fetch(`http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/history?user_id=${userId}`)
    .then(res => res.json())
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
            let stt = inv.num_order ? inv.num_order : "";
            card.innerHTML = `
                <div class="invoice-header">
                    <div>Mã phiếu: ${inv.invoice_id}</div>
                    <div class="${statusClass}">${inv.status}</div>
                </div>
                <div class="invoice-patient">${inv.patient_name}</div>
                <div class="invoice-hospital">BỆNH VIỆN DA LIỄU TP.HCM</div>
                <div class="invoice-details">
                    <div>STT: ${stt}</div>
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

