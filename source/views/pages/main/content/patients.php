<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


?>
<div class="main-content" id="main">
    <main>
        <div class="profile-view">
            <div class="profile-container">

                <div class="header" >
                    <h3 style="margin: 0;">Hồ sơ bệnh nhân của tôi</h3>

                    <div id="btn-add" class="add-btn" style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-primary"  style="color: white;">
                            <i class="fa fa-plus"></i> Thêm hồ sơ mới
                        </button>
                    </div>
                </div>
                <h4>Danh sách hồ sơ</h4>
                <div class="container mt-4">
                    <div class="row" id="patientList"></div>
                </div>
     
            </div>


           <!-- Patient Detail Modal -->
            <div class="modal fade" id="patientDetailModal" tabindex="-1" aria-labelledby="patientDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="patientDetailLabel">Thông tin hồ sơ bệnh nhân</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">
                    <div class="col-md-4 text-center">
                        <img id="patient_avatar" src="../images/user.jpg" class="rounded-circle" width="120" height="120">
                    </div>
                    <div class="col-md-6">
                        <h4 id="detail_full_name" class="fw-bold mb-1">Tên bệnh nhân</h4>
                        <p class="text-muted mb-0"><strong>ID:</strong> <span id="detail_patient_id"></span></p>
                    </div>
                    <div class="col-md-2 text-end">
                        <a onclick="deletePatient()" class="text-danger" style="cursor:pointer;" title="Xóa hồ sơ">
                            <i class="fa fa-trash fa-lg"></i>
                        </a>
                    </div>

                    </div>

                    <hr>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Ngày sinh:</label>
                            <p id="detail_date_of_birth"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Giới tính:</label>
                            <p id="detail_gender"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Email:</label>
                            <p id="detail_email"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Số điện thoại:</label>
                            <p id="detail_phone"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">CCCD / CMND:</label>
                            <p id="detail_citizen_id"></p>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Mã BHYT:</label>
                            <p id="detail_insurance_number"></p>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="fw-bold">Địa chỉ:</label>
                            <p id="detail_address"></p>
                        </div>
                    </div>

                </div>

               <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="modal-update-btn">Cập nhật hồ sơ</button>
                </div>

                </div>
            </div>
            </div>

            <!-- Cập nhật hoặc thêm bệnh nhân -->
          <section class="update-profile d-none"  id="update-profile-section">
            <div class="container">
                <div class="row profile-view">
                    <div class="col-12 col-sm-12 my-4 mx-auto p-3">
                        <div class="profile-container">
                            <h4 id="profile-title">Chỉnh sửa hồ sơ</h4>
                           <form id="update-profile" class="row g-3 position-relative profile-info" action="" method="POST">
                                
                                <!-- Full Name -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="full_name">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" >
                                </div>

                                <!-- Date of Birth -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="date_of_birth">Ngày sinh <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" >
                                </div>

                                <!-- Gender -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="gender">Giới tính <span class="text-danger">*</span></label>
                                    <select class="form-control" id="gender" name="gender" >
                                        <option value="">Chọn giới tính</option>
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>

                                <!-- Email -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" >
                                </div>

                                <!-- Phone -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" >
                                </div>

                                <!-- Address -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="address">Địa chỉ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address" >
                                </div>

                                <!-- Citizen ID -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="citizen_id">CMND/CCCD </label>
                                    <input type="text" class="form-control" id="citizen_id" name="citizen_id">
                                </div>

                                <!-- Insurance Number -->
                                <div class="form-group col-12 col-md-6">
                                    <label for="insurance_number">Số bảo hiểm</label>
                                    <input type="text" class="form-control" id="insurance_number" name="insurance_number">
                                </div>

                                <!-- Action Buttons -->
                                <div class="actions col-12 mt-3 d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="chuyengiaodien('update-profile', 'profile-container')">Quay lại</button> 
                                    <button type="button" class="btn btn-success" id="save-btn">Lưu</button> 
                                </div>       

                            </form>

                        </div>
                    </div>
                </div>
            </div>
         </section>  

        </div>
    </main>
</div>
<div id="toast-container"></div>

<!-- Bootstrap Bundle (chứa Popper) - BẮT BUỘC trước script của bạn -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script preload patient -->
<script>
const SESSION_USER_ID = <?php echo json_encode($_SESSION["user_id"]); ?>;

let data = "";
function openDetail(id) {
    data = patientCache[id];

    if (!data) return alert("Không tìm thấy dữ liệu!");
    document.getElementById("detail_full_name").innerText = data.full_name ?? "";
    document.getElementById("detail_patient_id").innerText = data.patient_id;
    document.getElementById("detail_date_of_birth").innerText = data.date_of_birth ?? "";
    const genderMap = {
        male: "Nam",
        female: "Nữ",
        other: "Khác"
    };

    document.getElementById("detail_gender").innerText = genderMap[data.gender] ?? "";
    document.getElementById("detail_email").innerText = data.email ?? "";
    document.getElementById("detail_phone").innerText = data.phone ?? "";
    document.getElementById("detail_address").innerText = data.address ?? "";
    document.getElementById("detail_citizen_id").innerText =
    data.citizen_id?.trim() || "Chưa cập nhật";
    document.getElementById("detail_insurance_number").innerText =
    data.insurance_number?.trim() || "Chưa cập nhật";

    const modal = new bootstrap.Modal(document.getElementById("patientDetailModal"));
    modal.show();
}

// DELETE
function deletePatient() {
    const id = document.getElementById("detail_patient_id").innerText;

    if (!id) {
        alert("Không tìm thấy ID bệnh nhân!");
        return;
    }

    if (!confirm("Bạn có chắc muốn xóa hồ sơ này?")) return;

    showToast("Đang xử lý..", "info");

    fetch("http://localhost/medical-appointment/source/models/patient_service/PatientAPI.php?id=" + id, {
        method: "DELETE"
    })
    .then(res => res.json())
    .then(data => {
        if (data.message) {
            alert("Đã xóa thành công!");

            // Xóa khỏi cache
            delete patientCache[id];

            // Reload danh sách hoặc tự xóa khỏi UI
            location.reload();
        } else {
            alert("Xóa thất bại!");
        }
    })
    .catch(err => console.error("Lỗi:", err));
}

let form = document.getElementById("update-profile");
const section = document.getElementById("update-profile-section");
const title = document.getElementById("profile-title");
let isAdd = false;

// Khi nhấn "Thêm"
document.getElementById("btn-add").addEventListener("click", () => {
    isAdd = true;
    title.textContent = "Thêm hồ sơ";
    clearAll();
    chuyengiaodien('profile-container','update-profile');
});

// Khi nhấn "Cập nhật"
document.getElementById("modal-update-btn").addEventListener("click", () => {
    isAdd = false;

    if(!data) return alert("Không tìm thấy dữ liệu!");

    // Hiển thị form
    title.textContent = "Chỉnh sửa hồ sơ";
    chuyengiaodien('profile-container','update-profile');

    // Lưu ID vào form
    form.dataset.id = data.patient_id;

    // Điền dữ liệu vào form
    for(const key in data){
        const input = document.getElementById(key);
        if(input) input.value = data[key] ?? "";
    }

    // Đóng modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('patientDetailModal'));
    modal.hide();
});


// Hàm lấy dữ liệu từ form
function getFormData() {
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    data.user_id = SESSION_USER_ID;
    return data;
}

// === Thêm bệnh nhân (POST) ===
function addPatient() {
    const data = getFormData();
    fetch("http://localhost/medical-appointment/source/models/patient_service/PatientAPI.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(async res => {
        const text = await res.text();
        console.log("RAW RESPONSE:", text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            // alert("Backend trả về lỗi HTML, không phải JSON. Mở console để xem chi tiết!");
            return;
        }

        // Nếu JSON hợp lệ
        if (data.message) {
            location.reload();
        } else {
            alert(data.error ?? "Có lỗi xảy ra");
        }
    })
    .catch(err => console.error(err));
}

// === Cập nhật bệnh nhân (PUT) ===
function editPatient() {
    const data = getFormData();
    const patientId = form.dataset.id; 
    if(!patientId) {
        alert("Không tìm thấy ID bệnh nhân!");
        return;
    }
     fetch("http://localhost/medical-appointment/source/models/patient_service/PatientAPI.php?id=" + patientId, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if(result.message) {
            location.reload();
        } else {
            alert("Lỗi: " + result.error);
        }
    })
    .catch(err => console.error(err));
}

document.getElementById("save-btn").addEventListener("click", () => {
    if (!validatePatientForm()) return;
    showToast("Đang xử lý..", "info");
    if(isAdd){
        addPatient();
    }else{
        editPatient();
    }
});

const fieldNames = {
    full_name: "Họ và tên",
    date_of_birth: "Ngày sinh",
    gender: "Giới tính",
    email: "Email",
    phone: "Số điện thoại",
    address: "Địa chỉ"
};

function validatePatientForm() {
    for (const id in fieldNames) {
        const input = document.getElementById(id);

        if (!input || !input.value.trim()) {
            showToast(`Vui lòng nhập ${fieldNames[id]}`, 'warning');
            input.focus();
            return false;
        }
    }
    return true;
}



function clearAll(){
    // Điền dữ liệu vào form
    for(const key in data){
        const input = document.getElementById(key);
        if(input) input.value = "";
    }
}

</script>
<script src="../models/patient_service/getPatient.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
