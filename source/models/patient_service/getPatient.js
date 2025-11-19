let patientCache = {}; // RAM cache

document.addEventListener("DOMContentLoaded", () => {
    // Fetch toàn bộ info bệnh nhân 
    fetch("http://localhost/medical-appointment/source/models/patient_service/PatientAPI.php?user_id=" + SESSION_USER_ID)
       .then(res => {
            if (res.status === 503) {
                window.location.href = "/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
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