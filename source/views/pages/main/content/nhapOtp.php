
<div class="main-content" id="main">
    <main>
        <div class="page-header" 
            style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Nhập mã OTP</h3>
  
        </div>
        <div class="content">
            <section class="cart" >
            <div class="row justify-content-center">
                <div class="col-lg-6 col-sm-12">
                    <div class="card-cart text-center p-4" style="height: 375px">
                        <div class="otp-wrapper">
                        <h3>Xác thực OTP</h3>
                        <h6>Vui lòng nhập mã số chúng tôi đã gửi cho bạn qua email</h6>

                        <div class="otp-container">
                            <input type="text" maxlength="1" class="otp-input">
                            <input type="text" maxlength="1" class="otp-input">
                            <input type="text" maxlength="1" class="otp-input">
                            <input type="text" maxlength="1" class="otp-input">
                            <input type="text" maxlength="1" class="otp-input">
                            <input type="text" maxlength="1" class="otp-input">
                        </div>

                            <div class="text-center mt-5">
                                <a href="#" 
                                    class="btn btn-primary w-45 py-2" 
                                    style="font-size: 18px; text-decoration: none; color: white;">
                                    Xác nhận 
                                </a>
                            </div>
                            <div class="text-center mt-4">
                                <p class="resend text-muted mb-0">
                                    Chưa nhận được mã? 
                                    <a href="#" id="resendOtp">Gửi lại</a>
                                    <span id="countdown" class="text-muted" style="display:none;"></span>
                                </p>
                            </div>

                        </div>

                         <div id="errorMsg" class="text-danger mt-2" style="display:none;"></div>
                    </div>  
                </div>     
            </div>
        </section>

            
        </div>
    </main>
</div>
<div id="toast-container"></div>
<style>
    
    .otp-container {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 30px;
    }
    .otp-input {
    width: 50px;
    height: 60px;
    aspect-ratio: 1 / 1;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    border: 2px solid #ccc;
    border-radius: 8px;
    outline: none;
    transition: all 0.2s ease;
    }
    .otp-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

   
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
  const inputs = document.querySelectorAll(".otp-container input");

  inputs.forEach((input, index) => {
    input.addEventListener("keydown", (e) => {
      const key = e.key;

      // Xóa bằng Backspace
      if (key === "Backspace") {
        input.value = "";
        if (index > 0) inputs[index - 1].focus();
        e.preventDefault();
        return;
      }

      if (
        key === "Backspace" ||
        key === "Delete" ||
        key === "Tab" ||
        key === "ArrowLeft" ||
        key === "ArrowRight" ||
        (e.ctrlKey && key.toLowerCase() === "v")
      ) {
        if (key === "Backspace") {
          input.value = "";
          if (index > 0) inputs[index - 1].focus();
          e.preventDefault();
        }
        return; // không chặn mấy phím hợp lệ
      }
      // Nhập số (0–9)
      if (/^[0-9]$/.test(key)) {
        input.value = key;
        if (index < inputs.length - 1) {
          inputs[index + 1].focus();
        } else {
          input.blur(); // Nếu là ô cuối thì bỏ focus
        }
        e.preventDefault(); // tránh nhập trùng
        return;
      }

      // Nếu không phải số → chặn
      e.preventDefault();
    });

    // Dán mã OTP
    input.addEventListener("paste", (e) => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData)
        .getData("text")
        .replace(/\D/g, "")
        .slice(0, inputs.length);

      paste.split("").forEach((char, i) => {
        inputs[i].value = char;
      });

      if (paste.length < inputs.length) {
        inputs[paste.length].focus();
      } else {
        inputs[inputs.length - 1].blur();
      }
    });
  });
</script>

<script>
   

document.addEventListener("DOMContentLoaded", async function () {
    const inputs = document.querySelectorAll(".otp-container input");
    const btnConfirm = document.querySelector(".otp-wrapper .btn-primary");
    const urlParams = new URLSearchParams(window.location.search);
    const txid = urlParams.get("txid"); // transaction_id từ URL
    const errorMsg = document.getElementById("errorMsg");


    const user_id = <?php echo $_SESSION['id']; ?>;
    const user_email = "<?php echo $_SESSION['email']; ?>";

    let transactionCompleted = false;

        // 🧱 Phát hiện reload thật (khi user F5, Ctrl+R, hoặc reload trình duyệt)
        window.addEventListener("load", function () {
        const navType =
            performance.getEntriesByType("navigation")[0]?.type ||
            performance.navigation.type;

        // Nếu reload và giao dịch chưa hoàn tất
        if (navType === "reload" && !transactionCompleted) {
            showToast("Giao dịch chưa hoàn tất. Quay về trang thanh toán...", "warning");
            sessionStorage.removeItem("transaction_id"); // xóa session cũ
            window.location.replace("index.php?sidebar=thanhtoan"); // 👈 về trang index
        }
        });

    // ✅ B1: Gửi OTP ngay khi vào trang
    try {
        const otpRes = await fetch(`/SOA_GK/source/models/otp_service/OtpAPI.php/otp/generate`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                transaction_id: txid,
                user_id: user_id,
                email: user_email
            })
        });

        const otpData = await otpRes.json();

        if (otpData.error) {
            showToast("Không thể gửi OTP: " + otpData.error, "error");
            return;
        }

        showToast("📩 Mã OTP đã gửi đến email của bạn (hết hạn lúc: " + otpData.expired_at + ")", "info");
    } catch (err) {
        console.error("Lỗi khi gửi OTP:", err);
        if (!transactionCompleted && txid) {
            navigator.sendBeacon(
                "/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/fail",
                JSON.stringify({ transaction_id: txid })
            );
            console.log("Giao dịch #"+txid+" bị thất bại.");
            window.location.href = "index.php?sidebar=thanhtoan";
        }
        return;
    }

    // ✅ B2: Khi người dùng nhấn Xác nhận
    btnConfirm.addEventListener("click", async function (e) {
        e.preventDefault();

        errorMsg.style.display = "none";

        // Ghép OTP từ các ô input
        const otp = Array.from(inputs).map(i => i.value.trim()).join('');
        if (otp.length !== inputs.length) {
            errorMsg.textContent = "Vui lòng nhập đầy đủ OTP";
            errorMsg.style.display = "block";
            // alert("⚠️ Vui lòng nhập đầy đủ OTP");
            return;
        }

       try {
            // Bước 1: Gửi OTP để xác thực
            const res = await fetch("/SOA_GK/source/models/otp_service/OtpAPI.php/otp/validate", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ transaction_id: txid, otp: otp })
            });

            if (!res.ok) {
                const text = await res.text();
                throw new Error("Lỗi server khi kiểm tra OTP: " + text);
            }

            let data;
            try {
                data = await res.json();
            } catch (jsonErr) {
                throw new Error("Response OTP không phải JSON: " + jsonErr.message);
            }

            console.log("OTP response:", data);

            if (!data.valid) {
                let msg = "OTP không hợp lệ!";
                if (data.error === "expired") msg = "Mã OTP đã hết hạn!";
                else if (data.error === "not_found") msg = "Không tìm thấy giao dịch! Vui lòng tạo giao dịch mới";
                errorMsg.textContent = msg;
                errorMsg.style.display = "block";
                return;
            }

            // OTP hợp lệ
            showToast("Xác nhận thành công, vui lòng không thoát trang khi đang xử lý giao dịch!", "success");

            // Bước 2: Gửi xác nhận giao dịch (try/catch riêng)
            try {
                const txRes = await fetch("/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/confirm", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ 
                        transaction_id: txid,
                        user_id: user_id,
                        email: user_email
                    })
                });

                if (!txRes.ok) {
                    const text = await txRes.text();
                    throw new Error("Lỗi server khi xác nhận giao dịch: " + text);
                }

                let txData;
                try {
                    txData = await txRes.json();
                } catch (jsonErr) {
                    throw new Error("Response giao dịch không phải JSON: " + jsonErr.message);
                }

                console.log("Transaction response:", txData);

                if (txData.error) {
                    showToast("Lỗi" + txData.error, "error");
                    return;
                }

                showToast((txData.message || "Giao dịch thành công") + ". Mã giao dịch: " + txid, "success");
                transactionCompleted = true;

                // Chuyển trang sau khi thành công
                setTimeout(() => {
                    sessionStorage.removeItem("tuition_id");
                    window.location.href = "index.php?sidebar=thanhtoan";
                }, 1500);

            } catch (txErr) {
                console.error("Lỗi xác nhận giao dịch:", txErr);
                showToast("Có lỗi xảy ra khi xác nhận giao dịch!", "error");
            }

        } catch (err) {
            console.error("Lỗi kiểm tra OTP:", err);
            showToast("Có lỗi xảy ra khi kiểm tra OTP!", "error");
        }

    });
     window.addEventListener("beforeunload", function () {
        if (!transactionCompleted && txid) {
            navigator.sendBeacon(
                "/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/fail",
                JSON.stringify({ transaction_id: txid })
            );
            console.log("🔴 Giao dịch #"+txid+" bị thất bại do người dùng thoát trang.");
        }
    });

});
</script> 


<!-- Gửi lại otp -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const resendLink = document.getElementById("resendOtp");
    const countdownText = document.getElementById("countdown");
    const txid = new URLSearchParams(window.location.search).get("txid"); // Lấy txid từ URL
    const user_email = "<?php echo $_SESSION['email']; ?>";
    const user_id = <?php echo $_SESSION['id']; ?>;
    let countdownTime = 60;

    resendLink.addEventListener("click", async function (e) {
        e.preventDefault();

        if (!txid) {
            showToast("Không tìm thấy giao dịch!", "info");
            return;
        }
        startCountdown(countdownTime);

        try {
            const res = await fetch(`/SOA_GK/source/models/otp_service/OtpAPI.php/otp/generate`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    transaction_id: txid,
                    user_id: user_id,
                    email: user_email
                })
            });

            const data = await res.json();

            if (data.error) {
                showToast("Không thể gửi lại OTP: " + data.error, "error");
                return;
            }

            // alert("📩 OTP đã được gửi lại! Hết hạn lúc: " + data.expired_at);
        } catch (error) {
            console.error("Lỗi gửi lại OTP:", error);
            showToast("Có lỗi xảy ra khi gửi lại OTP!", "error");
        }
    });
    function startCountdown(seconds) {
    resendLink.style.display = "none";
    countdownText.style.display = "inline";
    
    let remaining = seconds;
    countdownText.textContent = ` (Gửi lại sau ${remaining}s)`;

    const timer = setInterval(() => {
      remaining--;
      countdownText.textContent = ` (Gửi lại sau ${remaining}s)`;

      if (remaining <= 0) {
        clearInterval(timer);
        countdownText.style.display = "none";
        resendLink.style.display = "inline";
      }
    }, 1000);
  }
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