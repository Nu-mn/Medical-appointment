
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: /SOA_GK/source/index.php");
    exit();
}


$mssv = $_GET['mssv'] ?? '';
$fullname = $_GET['fullname'] ?? '';
$hocphi = $_GET['hocphi'] ?? '';


$hasData = (!empty($mssv) && !empty($fullname) && !empty($hocphi)) ? 'true' : 'false';

?>
<div class="main-content" id="main">
    <main>
        <div class="page-header" 
            style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Thanh toán</h3>

            <div class="action-buttons" style="display: flex; gap: 10px;">
                <a href="index.php?sidebar=timkiem" class="btn btn-danger" style="color: white;">
                    <i class="fa fa-search"></i> Tìm kiếm
                </a>
            </div>
        </div>
        <div class="content">
            <section class="cart">
                
                <div class="row">
                     <div class="col-lg-8 col-md-10 col-sm-12">
                        <div class="card-cart">
                            <h5>Chủ tài khoản</h5>
                            <div class="form-group">
                                <label for="fullname">Họ và tên</label>
                                <input id="fullname" type="text" class="form-control" 
                                       value="<?= htmlspecialchars($_SESSION["fullname"]) ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input id="phone" type="text" class="form-control" 
                                       value="<?= htmlspecialchars($_SESSION["phone"]) ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="text" class="form-control" 
                                       value="<?= htmlspecialchars($_SESSION["email"]) ?>" readonly>
                            </div>
                        </div>

                        <div class="card-cart">
                            <h5>Thông tin học phí</h5>
                            <div class="form-group">
                                <label for="mssv">Mã số sinh viên</label>
                               <input type="text" id="mssv" class="form-control mb-2" placeholder="Nhập MSSV" value="<?php echo $mssv; ?>">
                            </div>
                            <div class="form-group">
                                <label for="tensv">Họ và tên sinh viên</label>
                                <input type="text" id="tensv" class="form-control mb-2" readonly value="<?php echo $fullname; ?>">
                            </div>
                            <div class="form-group">
                                <label for="hocphi">Học phí</label>
                                <input type="text" id="hocphi" class="form-control mb-2" readonly value="<?php echo $hocphi; ?>">
                            </div>
                        </div>
                       

                         <div class="card-cart">
                            <h5>Thông tin thanh toán</h5>
                            <div class="form-group">
                                <label for="sodu">Số dư khả dụng</label>
                                <input id="sodu" name="sodu" type="text" class="form-control" value = "Đang tải..." readonly/>
                            </div>

                            <div class="form-group">
                                <label for="hocphi">Học phí cần thanh toán</label>
                                <input id="hocphictt" name="hocphi" type="text" class="form-control" value="<?php echo $hocphi;?> "/>
                            </div>

                            <!-- ✅ Checkbox có id để theo dõi -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="agreeTerms">
                                <label class="form-check-label" for="agreeTerms">
                                    Tôi đồng ý với các điều khoản và điều kiện
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="text-center mt-3">
                <a href="index.php?sidebar=nhapOtp" 
                    class="btn btn-primary w-45 py-2" 
                    id="btnThanhToan"
                    style="font-size: 18px; text-decoration: none; color: white;">
                    Xác nhận thanh toán
                </a>
            </div>
            <input type="hidden" id="tuition_id" name="tuition_id">


        </div>
    </main>
</div>

<!-- Vùng hiển thị toast -->
<div id="toast-container"></div>


<script>
const user_id = <?php echo $_SESSION['id']; ?>;
// 🟢 Gọi API để lấy số dư
fetch(`/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/balance?user_id=${user_id}`)
    .then(res => res.json())
    .then(data => {
        if (data.balance !== undefined && data.balance !== null) {
            // Format hiển thị tiền tệ
            const formatted = new Intl.NumberFormat('vi-VN').format(data.balance) + ' VNĐ';
            document.getElementById("sodu").value = formatted;
        } else if (data.error) {
            document.getElementById("sodu").value = "Lỗi: " + data.error;
        } else {
            document.getElementById("sodu").value = "Không có dữ liệu";
        }
    })
    .catch(err => {
        console.error("Lỗi khi gọi API:", err);
        document.getElementById("sodu").value = "Không thể lấy số dư";
    });
</script>
<script>
// ✅ Đưa ra ngoài, có thể dùng ở mọi script
function clearFields() {

    const tensvInput = document.getElementById("tensv");
    const hocphiInput = document.getElementById("hocphi");
    const hocphiThanhToan = document.getElementById("hocphictt");
    const tuitionIdInput = document.getElementById("tuition_id");


    if (tensvInput) tensvInput.value = "";
    if (hocphiInput) hocphiInput.value = "";
    if (hocphiThanhToan) hocphiThanhToan.value = "";
    if (tuitionIdInput) tuitionIdInput.value = "";

    window.dataLoaded = false; // để reset trạng thái chung
}
</script>
<script>
let dataLoaded = <?= $hasData ?>;
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const mssvInput = document.getElementById("mssv");
    const tensvInput = document.getElementById("tensv");
    const hocphiInput = document.getElementById("hocphi");
    const hocphiThanhToan = document.getElementById("hocphictt");
    const tuitionIdInput = document.getElementById("tuition_id");

    const params = new URLSearchParams(window.location.search);
    const tuitionId = params.get("tuition_id");
    if (tuitionId && tuitionIdInput) {
        tuitionIdInput.value = tuitionId;
    }

    let typingTimer;
    const doneTypingInterval = 10;

    mssvInput.addEventListener("input", function () {
        clearTimeout(typingTimer);
        const mssv = this.value.trim();

        if (mssv) {
            typingTimer = setTimeout(() => {
                fetchStudent(mssv);
            }, doneTypingInterval);
        } else {
            clearFields();
        }
    });

    function fetchStudent(mssv) {
        fetch(`http://localhost/SOA_GK/source/models/student_service/StudentAPI.php?mssv=${encodeURIComponent(mssv)}`)
            .then(res => res.json())
            .then(studentData => {
                if (studentData.error) {
                    clearFields();
                    return;
                }

                fetch(`http://localhost/SOA_GK/source/models/student_service/StudentAPI.php?fee=${encodeURIComponent(mssv)}`)
                    .then(res => res.json())
                    .then(feeData => {
                        if (!feeData.error && feeData.length > 0) {
                            const fee = feeData[0];

                            // ✅ Kiểm tra nếu học phí đã thanh toán
                            if (fee.status && fee.status.toLowerCase() === "paid") {
                                showToast("Mã số sinh viên này đã được thanh toán học phí rồi!", "success");
                                
                                return;
                            }

                            // ✅ Kiểm tra thời hạn nộp phí
                            const today = new Date();
                            const dueDate = new Date(fee.due_date);
                            if (dueDate < today) {
                                showToast("Đã hết hạn nộp học phí, vui lòng liên hệ với trường!", "warning");
                                
                                return;
                            }

                            // ✅ Nếu còn hạn thì hiển thị thông tin
                            tensvInput.value = studentData.fullname || "";
                            const amount = parseFloat(fee.amount) || 0;
                            const formattedFee = new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ';
                            tuitionIdInput.value = fee.tuition_id || "";
                            hocphiInput.value = formattedFee;
                            hocphiThanhToan.value = formattedFee;

                            dataLoaded = true;
                        } else {
                            clearFields();
                        }
                    })
                    .catch(err => {
                        console.error("Lỗi khi lấy học phí:", err);
                        clearFields();
                    });
            })
            .catch(err => {
                console.error("Lỗi khi gọi Student API:", err);
                clearFields();
            });
    }
});
</script>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnThanhToan = document.getElementById("btnThanhToan");
    const hocphiInput = document.getElementById("hocphictt");
    const soduInput = document.getElementById("sodu");
    const tuitionIdInput = document.getElementById("tuition_id");
    const agreeCheckbox = document.getElementById("agreeTerms");
    

    // ✅ Ban đầu vô hiệu hóa nút thanh toán
    btnThanhToan.disabled = true;
    btnThanhToan.style.backgroundColor = "#999";
    btnThanhToan.style.cursor = "not-allowed";

    function updateButtonState() {
        const hocphi = hocphiInput.value.trim();
        const sodu = soduInput.value.trim();
        const tuitionId = tuitionIdInput.value.trim();


        // Chỉ bật khi: đã có dữ liệu học phí, số dư, tuition_id và checkbox được tick
        const canPay = dataLoaded && hocphi !== "" && sodu !== "" && tuitionId !== "" && agreeCheckbox.checked ;

        if (canPay) {
            btnThanhToan.disabled = false;
            btnThanhToan.style.backgroundColor = "#007bff";
            btnThanhToan.style.cursor = "pointer";
        } else {
            btnThanhToan.disabled = true;
            btnThanhToan.style.backgroundColor = "#999";
            btnThanhToan.style.cursor = "not-allowed";
        }

        
    }

    // 🟢 Gọi lại khi dữ liệu thay đổi hoặc checkbox thay đổi
    agreeCheckbox.addEventListener("change", updateButtonState);
    hocphiInput.addEventListener("input", updateButtonState);
    soduInput.addEventListener("input", updateButtonState);
    tuitionIdInput.addEventListener("input", updateButtonState);

    // ✅ Hàm cập nhật tự động khi dữ liệu được load thành công
    window.setDataLoaded = function(value) {
        dataLoaded = value;
        updateButtonState();
    };

    // Khi người dùng nhấn nút thanh toán
    btnThanhToan.addEventListener("click", async function (e) {
        e.preventDefault();

        if (btnThanhToan.disabled) return; // chặn nếu chưa đủ điều kiện

        const hocphi = parseInt(hocphiInput.value.replace(/[^\d]/g, '')) || 0;
        const sodu = parseInt(soduInput.value.replace(/[^\d]/g, '')) || 0;
        const tuition_id = tuitionIdInput.value.trim();
        const user_email = "<?php echo $_SESSION['email']; ?>";
        const user_id = <?php echo $_SESSION['id']; ?>;
       

        if (hocphi === 0) {
  
            showToast("Thông tin chưa đầy đủ, vui lòng điền thông tin", "warning");
            return;
        }

        if (sodu < hocphi) {
            showToast("Số dư không đủ để thanh toán học phí!", "warning");
            return;
        }

        try {
            btnThanhToan.disabled = true;
            btnThanhToan.style.backgroundColor = "#999";
            btnThanhToan.style.cursor = "not-allowed";

            const txRes = await fetch(`/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/create`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    user_id: user_id,
                    tuition_id: tuition_id,
                    amount: hocphi
                   
                })
            });

            const txData = await txRes.json();

            if (txData.error) {
                showToast(txData.error, "error");
                clearFields();
                updateButtonState();
                return;
            }

            const txId = txData.transaction_id;
            showToast((txData.message || "Giao dịch được xử lý") + ". Mã giao dịch: " + txId, "success");

            const params = new URLSearchParams({
                sidebar: "nhapOtp",
                txid: txId,
            });
            window.location.href = `/SOA_GK/source/views/index.php?${params.toString()}`;
        
        } catch (error) {
            console.error("Lỗi khi xử lý thanh toán:", error);
            showToast("Có lỗi xảy ra khi gửi yêu cầu thanh toán!", "error");
            clearFields();
            updateButtonState();
        }
    });
});
</script>

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


.cart {
  display: flex;
  justify-content: center; /* Căn giữa ngang */
  align-items: center;     /* Căn giữa dọc */
  min-height: 100vh;       /* Chiếm toàn bộ chiều cao màn hình */
  background-color: #fffafa;
  padding: 40px 0;
}

.cart .row {
  display: flex;
  flex-direction: column;  /* Sắp xếp các card dọc */
  align-items: center;     /* Căn giữa theo trục ngang */
  gap: 20px;               /* Khoảng cách giữa các card */
  width: 100%;
}

.card-cart {
  width: 80%;              /* Độ rộng mỗi card */
  max-width: 900px;        /* Giới hạn tối đa */
  background-color: #fff;
  border: 1.5px solid #ffb6b6;
  border-radius: 10px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
  padding: 25px 35px;
}

</style>