<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Giả sử bạn đã lưu user_id vào session sau khi đăng nhập
$user_id = $_SESSION['id'] ?? null;
?>

<div class="main-content" id="main">
    <main>
        <div class="page-header">
            <div>
                <h3>Lịch sử giao dịch</h3>
            </div>
        </div>

        <div class="content">
            <section class="list-customer">
                <div class="card-customer">
                    <h5>Danh sách giao dịch</h5>
                    <table class="cart-table" id="transactionTable">
                        <thead>
                            <tr>
                                <th>Mã giao dịch</th>
                                <th>Số dư còn lại</th>
                                <th>Số tiền thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody id="customers-info">
                            <!-- Dữ liệu sẽ được load bằng JS -->
                        </tbody>
                    </table>
                </div>
            </section>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const userId = <?php echo json_encode($user_id); ?>;
                    if (!userId) {
                        showToast("Không xác định được user_id", "warning");
                        return;
                    }

                    
                    fetch(`http://localhost/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/history?user_id=${userId}`)
                        .then(res => {
                            if (!res.ok) throw new Error("Lỗi kết nối tới API");
                            return res.json();
                        })
                        .then(data => {
                            const tbody = document.getElementById("customers-info");
                            tbody.innerHTML = "";

                            if (!Array.isArray(data) || data.length === 0) {
                                tbody.innerHTML = "<tr><td colspan='6' style='text-align:center'>Không có giao dịch nào</td></tr>";
                                return;
                            }

                          data.forEach(tx => {
                            const row = document.createElement("tr");

                            const balance = tx.balance !== null 
                                ? Number(tx.balance).toLocaleString() + " VND" 
                                : "—";

                            const amount = tx.amount 
                                ? Number(tx.amount).toLocaleString() + " VND" 
                                : "—";

                            row.innerHTML = `
                                <td>${tx.transaction_id || "—"}</td>
                                <td>${balance}</td>
                                <td>${amount}</td>
                                <td>
                                    <span class="status ${tx.status}">
                                        ${tx.status === "success" ? "✅ Thành công" :
                                        tx.status === "failed" ? "❌ Thất bại" :
                                        "⏳ Đang xử lý"}
                                    </span>
                                </td>
                                <td>${tx.created_at || "—"}</td>
                                <td>${tx.updated_at || "—"}</td>
                            `;

                            tbody.appendChild(row);
                        });

                        })
                        .catch(err => {
                            console.error("Lỗi khi tải lịch sử giao dịch:", err);
                            showToast("Không thể tải lịch sử giao dịch. Vui lòng thử lại sau!", "error");
                        });
                });

            </script>
        </div>
    </main>
</div>
<div id="toast-container"></div>
<style>
#toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast {
    min-width: 250px;
    background-color: #333;
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(234, 234, 234, 0.3);
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.4s ease;
    font-size: 14px;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast.success { background-color: #28a745; } /* xanh */
.toast.error   { background-color: rgba(229, 79, 79, 1); } /* đỏ */
.toast.warning { background-color: #f2e71cff; color: #000; } /* vàng */
.toast.info    { background-color: #17a2b8; } /* xanh dương nhạt */

</style>

<script>
function showToast(message, type = "info", duration = 3000) {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerText = message;
    container.appendChild(toast);

    // Hiện ra với hiệu ứng
    setTimeout(() => toast.classList.add("show"), 100);

    // Ẩn và xóa sau duration ms
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 400);
    }, duration);
}
</script>
