
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
                        <label>Chọn chuyên khoa  <span class="text-danger">*</span></label>
                        <select id="specialty" class="form-control">
                            <option value="">-- Chọn chuyên khoa --</option>
                        </select>
                    </div>

                    <!-- Chọn bác sĩ -->
                    <div class="form-group mt-3">
                        <label>Chọn bác sĩ  <span class="text-danger">*</span></label>
                        <select id="doctor" class="form-control">
                            <option value="">-- Chọn bác sĩ --</option>
                        </select>
                    </div>

                    <!-- Chọn ngày -->
                    <div class="form-group mt-3">
                        <label>Chọn ngày khám  <span class="text-danger">*</span></label>
                        <input id="booking_date" class="form-control"  autocomplete="off">
                    </div>

                    <!-- Chọn giờ -->
                    <div class="form-group mt-3">
                        <label>Chọn khung giờ  <span class="text-danger">*</span></label>
                        <select id="time_slot" class="form-control">
                            <option value="">-- Chọn giờ khám --</option>
                        </select>
                    </div>

                    <!-- Tiền khám -->
                    <div class="form-group mt-3">
                        <label>Tiền khám</label>
                        <input type="text" id="exam_fee" class="form-control" disabled value="0">
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
                            

                            <button id="payUrl" class="btn btn-success w-100 mt-4">XÁC NHẬN ĐẶT LỊCH</button>
                        </div>  
                </div>
                <!-- <div class="booking-step-3 d-none" style="text-align:center;">
                    <img src="../images/check.png" width="120">
                    <h3 class="mt-3">Đặt lịch thành công!</h3>
                    <p>Vui lòng thanh toán.</p>

                    <button onclick="location.href='index.php?nav=thanhtoan'" class="btn btn-primary w-100 mt-4">
                        THANH TOÁN NGAY
                    </button>
                </div> -->

            </div>
        </div>
    </main>
</div>
<div id="toast-container"></div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const SESSION_USER_ID = <?php echo json_encode($_SESSION["user_id"]); ?>;

let data = "";
// Click chon patient
function openDetail(patient_id, event) {
    data = patientCache[patient_id];

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
    fetch("http://localhost/medical-appointment/source/models/doctor_service/DoctorAPI.php/specializations")
        .then(res => {
            if (res.status === 503) {
                window.location.href = "/Medical-appointment/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
                return;
            }
            return res.json();
        })
        .then(data => {
            let select = document.getElementById("specialty");
            select.innerHTML = `<option value="">-- Chọn chuyên khoa --</option>`;
            
            data.forEach(sp => {
                select.innerHTML += `<option value="${sp.specialization_id}">${sp.name}</option>`;
            });
        });
}

loadSpecializations();

let baseFee = 0;
let finalFee = 0;
document.getElementById("specialty").addEventListener("change", function() {
    let id = this.value;
    let doctorSelect = document.getElementById("doctor");

    doctorSelect.innerHTML = `<option value="">-- Chọn bác sĩ --</option>`;

    if(id === "") return;

    fetch(`http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/by-specialization?specialization_id=${id}`)
        .then(res => {
            if (res.status === 503) {
                window.location.href = "/Medical-appointment/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
                return;
            }
            return res.json();
        })
        .then(data => {
            data.forEach(doc => {
                doctorSelect.innerHTML += `<option value="${doc.doctor_id}">${doc.doctor_name}</option>`;
                baseFee = doc.specialty_fee;
                updateFee(); 
            });
        });
});

let scheduleData = [];
let fp; // flatpickr instance


document.getElementById("doctor").addEventListener("change", function() {
    const doctorId = this.value;
    const dateInput = document.getElementById("booking_date");
    const timeSlot = document.getElementById("time_slot");

    // Reset input ngày và giờ
    dateInput.value = "";
    timeSlot.innerHTML = `<option value="">-- Chọn giờ khám --</option>`;

    if (!doctorId) return;

    // Fetch lịch bác sĩ
    fetch(`http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/schedule?doctor_id=${doctorId}`)
        .then(res => {
            if (res.status === 503) {
                window.location.href = "/Medical-appointment/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
                return;
            }
            return res.json();
        })
        .then(data => {
            scheduleData = data;

            // Lấy danh sách ngày duy nhất
            const availableDates = [...new Set(data.map(item => item.date))];

            // Destroy flatpickr cũ nếu có
            if (fp) fp.destroy();

            // Init flatpickr
            fp = flatpickr(dateInput, {
                dateFormat: "Y-m-d",
                enable: availableDates,
                onChange: function(selectedDates, dateStr) {
                    // Reset giờ khám
                    timeSlot.innerHTML = `<option value="">-- Chọn giờ khám --</option>`;

                    // Lọc slot theo ngày
                    const slots = scheduleData.filter(s => s.date === dateStr);

                    const sessionMap = {
                        morning: "Buổi sáng (06:30 - 11:30)",
                        afternoon: "Buổi chiều (12:00 - 17:30)",
                        evening: "Buổi tối (18:00 - 20:00)"
                    };

                    slots.forEach(s => {
                        const text = sessionMap[s.session] || "Không xác định";
                        const opt = document.createElement("option");
                        opt.value = s.session;
                        opt.textContent = text;
                        timeSlot.appendChild(opt);
                    });
                }
            });
        });
});


document.getElementById("time_slot").addEventListener("change", function() {
    updateFee();
});

document.getElementById("payUrl").addEventListener("click", () => {

    if (!data.patient_id) {
        showToast("Vui lòng chọn hồ sơ bệnh nhân!", "warning");
        return;
    }

    const payload = {
        specialization_id: document.getElementById("specialty").value,
        doctor_id: document.getElementById("doctor").value,
        booking_date: document.getElementById("booking_date").value,
        slot_time: document.getElementById("time_slot").value,
        patient_id: data.patient_id,
        amount: finalFee,
        user_id: SESSION_USER_ID
    };

    if (!payload.specialization_id || !payload.doctor_id || !payload.booking_date || !payload.slot_time) {
        showToast("Vui lòng chọn đủ thông tin trước khi đặt lịch!", "warning");
        return;
    }

    fetch("http://localhost/Medical-appointment/source/models/booking_service/BookingAPI.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
    })
    .then(res => {
        if (res.status === 503) {
            window.location.href = "/Medical-appointment/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
            return;
        }
        return res.json();
    })
    .then(r => {
        if (r.status === "success") {
            alert("Booking created! ID: " + r.booking_id);

            // POST sang thanhtoan.php
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "pages/main/content/thanhtoan.php";

            const bookingInput = document.createElement("input");
            bookingInput.type = "hidden";
            bookingInput.name = "booking_id";
            bookingInput.value = r.booking_id;
            form.appendChild(bookingInput);

            document.body.appendChild(form);
            form.submit();
        } else {
            alert("Error: " + r.message);
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
    });
});
function updateFee() {
    let session = document.getElementById("time_slot").value;

    if (!baseFee || !session) {
        document.getElementById("exam_fee").value = "0";
        return;
    }

    finalFee = baseFee;

    // Áp x1.5 cho ca tối
    if (session === "evening") {
        finalFee = baseFee * 1.5;
    }

    // Format tiền VNĐ
    document.getElementById("exam_fee").value = formatMoney(finalFee) + " VNĐ";
}

document.addEventListener("DOMContentLoaded", () => {
    // Fetch toàn bộ info bệnh nhân 
    fetch("http://localhost/Medical-appointment/source/models/patient_service/PatientAPI.php?user_id=" +  SESSION_USER_ID + "&_=" + new Date().getTime())
       .then(res => {
            if (res.status === 503) {
                window.location.href = location.origin + "/Medical-appointment/source/views/index.php?nav=404";
                return;
            }
            return res.json();
        })
        .then(list => {
            const container = document.getElementById("patientList");

            list.forEach(p => {
                patientCache[p.patient_id] = p; // Lưu cache

                container.innerHTML += `
                    <div class="col-md-6">
                        <div class=" row patient-card">
                        <div class="col-md-9">
                            <div class=" p-3 mb-3 " onclick="openDetail(${p.patient_id})" style="cursor:pointer;">
                                <div class="info">
                                    <div class="name-line">
                                        <strong>${p.full_name}</strong>
                                        <span class="phone">${p.phone}</span>
                                    </div>

                                    <div class="detail">Ngày sinh: ${p.date_of_birth}</div>
                                    <div class="detail">Địa chỉ: ${p.address}</div>
                                </div>
                            </div>
                            </div>
                              <div class="actions col-md-3">
                                    <button class="btn btn-primary btn-chon" id="btn-${p.patient_id}" onclick="openDetail(${p.patient_id}, event)">Chọn</button>
                                 </div>
                        </div>
                     
                    </div>
                `;
            });
        })
        .catch(err => console.error("Lỗi tải danh sách:", err));
});
</script>
