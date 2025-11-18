<div class="main-content" id="main">
    <main>
        <div class="booking-view">
            <div class="booking-container">

                <div class="header" >
                    <h3 style="margin: 0;">Đặt lịch khám bác sĩ</h3>
                </div>

                <div class="booking-step-1">
                    <!-- Chọn chuyên khoa -->
                    <div class="form-group mt-3">
                        <label>Chọn chuyên khoa</label>
                        <select id="specialty" class="form-control">
                            <option value="">-- Chọn chuyên khoa --</option>
                        </select>
                    </div>

                    <!-- Chọn bác sĩ -->
                    <div class="form-group mt-3">
                        <label>Chọn bác sĩ</label>
                        <select id="doctor" class="form-control">
                            <option value="">-- Chọn bác sĩ --</option>
                        </select>
                    </div>

                    <!-- Chọn ngày -->
                    <div class="form-group mt-3">
                        <label>Chọn ngày khám</label>
                        <input type="date" id="booking_date" class="form-control">
                    </div>

                    <!-- Chọn giờ -->
                    <div class="form-group mt-3">
                        <label>Chọn khung giờ</label>
                        <select id="time_slot" class="form-control">
                            <option value="">-- Chọn giờ khám --</option>
                        </select>
                    </div>

                    <button id="go-to-step-2" class="btn btn-primary w-100 mt-4" onclick="chuyengiaodien('booking-step-1', 'booking-step-2')">TIẾP TỤC</button>
                </div>

                <div class="booking-step-2 d-none">
                    <h4>Danh sách hồ sơ</h4>
                    <div class="container mt-4">
                        <div class="row" id="patientList"></div>
                    </div>
                       <!-- Action Buttons -->
                        <div class="actions col-12 mt-3 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary w-100 mt-4" onclick="chuyengiaodien('booking-step-2', 'booking-step-1')">Quay lại</button> 
                            <button id="confirm-booking" class="btn btn-success w-100 mt-4">XÁC NHẬN ĐẶT LỊCH</button>
                        </div>  
                </div>
                <div class="booking-step-3 d-none" style="text-align:center;">
                    <img src="../images/check.png" width="120">
                    <h3 class="mt-3">Đặt lịch thành công!</h3>
                    <p>Vui lòng thanh toán trong mục Phiếu khám.</p>

                    <button onclick="location.href='index.php?nav=invoice'" class="btn btn-primary w-100 mt-4">
                        XEM LỊCH KHÁM
                    </button>
                </div>

            </div>
        </div>
    </main>
</div>
<div id="toast-container"></div>

<script>
const SESSION_USER_ID = <?php echo json_encode($_SESSION["user_id"]); ?>;

let data = "";
// Click chon patient
function openDetail(patient_id, event) {
    data = patientCache[patient_id];

    // if (!data) return alert("Không tìm thấy dữ liệu!");
    // document.getElementById("detail_full_name").innerText = data.full_name ?? "";
    // document.getElementById("detail_patient_id").innerText = data.patient_id;
    // document.getElementById("detail_date_of_birth").innerText = data.date_of_birth ?? "";
    // document.getElementById("detail_gender").innerText = data.gender ?? "";
    // document.getElementById("detail_email").innerText = data.email ?? "";
    // document.getElementById("detail_phone").innerText = data.phone ?? "";
    // document.getElementById("detail_address").innerText = data.address ?? "";
    // document.getElementById("detail_citizen_id").innerText =
    // data.citizen_id?.trim() || "Chưa cập nhật";
    // document.getElementById("detail_insurance_number").innerText =
    // data.insurance_number?.trim() || "Chưa cập nhật";

    // Nếu muốn chỉ 1 patient được chọn, bỏ chọn các nút khác
   const btn = document.getElementById(`btn-${patient_id}`);

    // Nếu muốn chỉ 1 patient được chọn, reset các nút khác
    document.querySelectorAll('.btn-chon').forEach(b => {
        b.classList.remove('btn-secondary');
        b.classList.add('btn-primary');
        b.innerText = 'Chọn';
    });

    // Đổi màu và text nút hiện tại
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-secondary');
    btn.innerText = 'Đã chọn';

}
// Load danh sách chuyên khoa
function loadSpecializations() {
    fetch("http://localhost/medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/allspecializations")
        .then(res => res.json())
        .then(data => {
            let select = document.getElementById("specialty");
            select.innerHTML = `<option value="">-- Chọn chuyên khoa --</option>`;
            
            data.forEach(sp => {
                select.innerHTML += `<option value="${sp.specialization_id}">${sp.name}</option>`;
            });
        });
}

loadSpecializations();

document.getElementById("specialty").addEventListener("change", function() {
    let id = this.value;
    let doctorSelect = document.getElementById("doctor");

    doctorSelect.innerHTML = `<option value="">-- Chọn bác sĩ --</option>`;

    if(id === "") return;

    fetch(`http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/by-specialization?specialization_id=${id}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(doc => {
                doctorSelect.innerHTML += `<option value="${doc.doctor_id}">${doc.doctor_name}</option>`;
            });
        });
});

let scheduleData = [];

document.getElementById("doctor").addEventListener("change", function() {
    let id = this.value;

    document.getElementById("booking_date").value = "";
    document.getElementById("time_slot").innerHTML = `<option value="">-- Chọn giờ khám --</option>`;

    if(id === "") return;

    fetch(`http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/schedule?doctor_id=${id}`)
        .then(res => res.json())
        .then(data => {
            scheduleData = data;

            // Lấy danh sách ngày
            let dates = [...new Set(data.map(item => item.date))];

            let dateInput = document.getElementById("booking_date");
            dateInput.setAttribute("min", dates[0]);
            dateInput.setAttribute("max", dates[dates.length - 1]);

            // Nếu chỉ muốn show những ngày có lịch, có thể dùng datalist (optional)
        });
});
document.getElementById("booking_date").addEventListener("change", function() {
    let date = this.value;
    let slotSelect = document.getElementById("time_slot");

    slotSelect.innerHTML = `<option value="">-- Chọn giờ khám --</option>`;

    let slots = scheduleData.filter(s => s.date === date);

    slots.forEach(s => {
        let text = s.session === "morning" ? "Buổi sáng" : "Buổi chiều";
        slotSelect.innerHTML += `<option value="${s.session}">${text}</option>`;
    });
});

document.getElementById("confirm-booking").addEventListener("click", () => {

    if (!data.patient_id) {
        alert("Vui lòng chọn hồ sơ bệnh nhân!");
        return;
    }


    const payload = {
        specialization_id: document.getElementById("specialty").value,
        doctor_id: document.getElementById("doctor").value,
        booking_date: document.getElementById("booking_date").value,
        slot_time: document.getElementById("time_slot").value,
        patient_id: data.patient_id,
        amount: 100000,
        status: "pending",
        user_id: SESSION_USER_ID
    };

    fetch("http://localhost/medical-appointment/source/models/booking_service/BookingAPI.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(r => {
        if (r.status) {
            chuyengiaodien('booking-step-2', 'booking-step-3');
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
        alert("Không thể đặt lịch. Kiểm tra API!");
    });

});
</script>
<script src="../models/patient_service/getPatient.js"></script>
