<div class="main-content" id="main">
    <main>
        <div class="page-header">
            <div>
                <h3>Tìm kiếm sinh viên</h3>
            </div>
            
        </div>
        <div class="content">
            <section class="cart">
                <div class="d-flex justify-content-center">
                    <label class="search-text" for="searchBox">Tìm kiếm:</label>
                    <div class="searchBox">
                        <i class="fa fa-search btn-search"></i>
                        <input id="searchBox" type="text" placeholder="Nhập mã số sinh viên" name="searchBox">
                    </div>
                </div>
            </section>

            <section class="list-customer">
                <div class="card-customer">
                    <h5>Danh sách sinh viên</h5>
                    <table class="cart-table" id="resultTable">
                        <thead>
                            <tr>
                                <th>MSSV</th>
                                <th>Họ tên</th>
                                <th>Khoa</th>
                                <th>Học Phí</th>
                                <th>Trạng thái</th>
                                <th>Hạn nộp</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="customers-info">
                   

                        </tbody>
                    </table>
                </div>
            </section>
                <div class="mt-100">
                    <button onclick="goBack()" class="btn btn-secondary" style="color: white;">
                        <i class="fa fa-arrow-left"></i> Quay lại
                    </button>
                </div>

                <script>
                    function goBack() {
                    
                        window.history.back();

                    
                    }
                </script>

        </div>
    </main>
</div>

 <script>
document.addEventListener("DOMContentLoaded", function () {
    const searchBox = document.getElementById("searchBox");
    const resultTableBody = document.querySelector("#resultTable tbody");

    let typingTimer;
    const delay = 10; // chờ 1 giây sau khi ngừng gõ

    searchBox.addEventListener("input", function () {
        clearTimeout(typingTimer);
        const mssv = this.value.trim();
        if (mssv) {
            typingTimer = setTimeout(() => {
                fetchStudentByMSSV(mssv);
            }, delay);
        } else {
            resultTableBody.innerHTML = "";
        }
    });

    function fetchStudentByMSSV(mssv) {
        fetch(`http://localhost/SOA_GK/source/models/student_service/StudentAPI.php?mssv=${encodeURIComponent(mssv)}`)
            .then(res => res.json())
            .then(studentData => {
                if (!studentData.error) {
                    const fullname = studentData.fullname || "";
                    const khoa = studentData.department || "";

                    // Gọi API lấy học phí
                    fetch(`http://localhost/SOA_GK/source/models/student_service/StudentAPI.php?fee=${encodeURIComponent(mssv)}`)
                    .then(res => res.json())
                    .then(feeData => {
                        let hocphi = "";
                        let trangthai = "Chưa thanh toán";
                        let hienThiTrangThai = '<span class="text-danger fw-bold">Chưa thanh toán</span>';
                        let daThanhToan = false;
                        let tuition_id = "";
                        let hannop = "";

                        if (!feeData.error && feeData.length > 0) {
                            hocphi = feeData[0].amount || "";
                            trangthai = feeData[0].status || "unpaid";
                            tuition_id = feeData[0].tuition_id || "";
                            hannop = feeData[0].due_date || "";

                            if (trangthai.toLowerCase() === "paid") {
                                hienThiTrangThai = '<span class="text-success fw-bold">Đã thanh toán</span>';
                                daThanhToan = true;
                            }
                            else if (hannop) {
                                const today = new Date();
                                const dueDate = new Date(hannop);

                                if (dueDate < today) {
                                    hienThiTrangThai = '<span class="text-danger fw-bold">Hết hạn nộp</span>';
                                    daThanhToan = true;
                                } 
                            }
                        }

                        resultTableBody.innerHTML = `
                            <tr>
                                <td>${mssv}</td>
                                <td>${fullname}</td>
                                <td>${khoa}</td>
                                <td>${hocphi}</td>
                                <td>${hienThiTrangThai}</td>
                                <td>${hannop}</td>
                                <td>
                                    ${daThanhToan
                                        ? `<button class="btn btn-secondary fw-bold" disabled>Thanh toán</button>`
                                        : `<button class="btn btn-primary btn-pay" 
                                            data-mssv="${mssv}" 
                                            data-fullname="${fullname}" 
                                            data-hocphi="${hocphi}"
                                            data-tuition="${tuition_id}">
                                            Thanh toán
                                        </button>`
                                    }
                                </td>
                            </tr>
                        `;

                        // ✅ Gán sự kiện click sau khi render bảng
                        attachPayButtonEvents();
                    })


                        .catch(err => {
                            console.error("Lỗi khi lấy học phí:", err);
                            resultTableBody.innerHTML = `<tr><td colspan="6" class="text-danger">Không thể tải học phí</td></tr>`;
                        });

                } else {
                    resultTableBody.innerHTML = `<tr><td colspan="6" class="text-danger">Không tìm thấy sinh viên với MSSV: ${mssv}</td></tr>`;
                }
            })
            .catch(err => {
                console.error("Lỗi khi gọi Student API:", err);
                resultTableBody.innerHTML = `<tr><td colspan="6" class="text-danger">Lỗi khi tìm kiếm sinh viên</td></tr>`;
            });
    }

   function attachPayButtonEvents() {
    document.querySelectorAll(".btn-pay").forEach(btn => {
        btn.addEventListener("click", () => {
            const mssv = btn.dataset.mssv;
            const fullname = btn.dataset.fullname;
            const hocphi = parseFloat(btn.dataset.hocphi) || 0;
            const tuition_id = btn.dataset.tuition; // 🟢 thêm dòng này

            // 🟢 Hiển thị số tiền theo định dạng Việt Nam
            const hocphiVND = new Intl.NumberFormat('vi-VN').format(hocphi) + ' VNĐ';

            // ✅ Nếu bạn chỉ muốn truyền số gốc
            const params = new URLSearchParams({
                sidebar: "thanhtoan",
                mssv: mssv,
                fullname: fullname,
                hocphi: hocphiVND,
                tuition_id: tuition_id 
            });

            window.location.href = `/SOA_GK/source/views/index.php?${params.toString()}`;
        });
    });
}

});
</script>