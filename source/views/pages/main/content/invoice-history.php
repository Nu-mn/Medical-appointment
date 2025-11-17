
    <title>Lịch sử hóa đơn</title>
    <style>
        body { font-family: Arial, sans-serif; background: #fbfbfbff; }
        h1 { text-align: center; color: #2f3640; margin-bottom: 25px; margin-top: 20px;}
        /* Banner thông báo */
        #banner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            color: #fff;
            z-index: 1000;
        }
        #banner.warning { background-color: #fbc531; color: #2f3640; }
        #banner.error { background-color: #e84118; }
        #banner.info { background-color: #40739e; }

        .invoice-list { max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 15px; }
        .invoice-card { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .invoice-card:hover { transform: translateY(-3px); box-shadow: 0 6px 14px rgba(0,0,0,0.15); }
        .invoice-header { display: flex; justify-content: space-between; font-weight: bold; color: #2f3640; margin-bottom: 10px; flex-wrap: wrap; }
        .invoice-details { display: none; margin-top: 10px; border-top: 1px solid #dcdde1; padding-top: 10px; animation: fadeIn 0.3s ease-in-out; }
        .service-item { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f2f6; }
        .service-item:last-child { border-bottom: none; }
        .total { text-align: right; font-size: 1.2em; font-weight: bold; color: #e84118; margin-top: 5px; }
        .empty-message { text-align: center; color: #e84118; font-weight: bold; margin-top: 50px; }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        @media(max-width:600px){ 
            .invoice-header { flex-direction: column; gap: 5px; } 
        }
    </style>
    </head>
    <body>

    <h1>Lịch sử hóa đơn</h1>

    <!-- Banner thông báo -->
    <div id="banner"></div>

    <div class="invoice-list" id="invoiceList">
    <!-- Hóa đơn sẽ được render bằng JS -->
    </div>



<script>
const invoiceList = document.getElementById("invoiceList");
const banner = document.getElementById("banner");
const userId = <?php echo isset($user_id) ? json_encode($user_id) : 1; ?>;

// Hàm hiển thị banner
function showBanner(message, type = 'info') {
    banner.textContent = message;
    banner.className = type;
    banner.style.display = 'block';
}

if (!userId) {
    invoiceList.innerHTML = `<div class="empty-message">Người dùng chưa đăng nhập</div>`;
} else {
    fetch(`http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/history?user_id=${userId}`)
    .then(res => {
        if (res.status === 503) {
            // Service bảo trì
            return res.json().then(data => { throw new Error(data.message); });
        }
        return res.json();
    })
    .then(invoices => {
        if (!invoices || invoices.length === 0) {
            invoiceList.innerHTML = `<div class="empty-message">Chưa có hóa đơn nào</div>`;
            return;
        }

        invoices.forEach(inv => {
            const card = document.createElement("div");
            card.className = "invoice-card";

            card.innerHTML = `
                <div class="invoice-header">
                    <div>Hóa đơn #${inv.invoice_id}</div>
                    <div>Ngày phát hành: ${inv.issued_date}</div>
                </div>
                <div class="invoice-details">
                    ${inv.services.map(s => `<div class="service-item"><span>${s.service_name}</span><span>${s.cost.toLocaleString()} VNĐ</span></div>`).join('')}
                    <div class="total">Tổng: ${inv.total_amount.toLocaleString()} VNĐ</div>
                </div>
            `;

            // Collapse / Expand
            card.addEventListener('click', () => {
                const details = card.querySelector('.invoice-details');
                details.style.display = details.style.display === 'block' ? 'none' : 'block';
            });

            invoiceList.appendChild(card);
        });
    })
    .catch(err => {
        invoiceList.innerHTML = `<div class="empty-message">${err.message}</div>`;
    });
}
</script>

</body>
</html>
