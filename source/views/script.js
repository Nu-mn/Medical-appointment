var tensp=$("#product_id").val();
var giabanle =  $("#soluong").val();
var bool = true;

let patientCache = {}; // RAM cache

function errormessage(message,id){
    let error = document.getElementById(id)

    if(message == null || message == undefined){
        //hide
        if(!error.classList.contains('d-none')){
            error.classList.add('d-none')
        }
    }else{
        error.classList.remove('d-none')
        error.innerHTML = message
    }
}


function chuyengiaodien(class1,class2){
    let box1 = document.getElementsByClassName(class1) [0]
    let box2 = document.getElementsByClassName(class2) [0]

     if(!box1.classList.contains('d-none')){ 
        //Nếu class1 không có d-none thì thêm vào để ẩn
        box1.classList.add('d-none')
        box2.classList.remove('d-none')
    }else{
        box2.classList.add('d-none')
        box1.classList.remove('d-none')
    }
}

// Tìm kiếm 
function filterTable(input, tableId) {
    const filter = removeVietnameseTones(input.value).toUpperCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName("tr");
  
    for (let i = 0; i < rows.length; i++) {
      const cells = rows[i].getElementsByTagName("td");
      if (cells.length > 1) {
        const value = cells[1].textContent || cells[1].innerText;
        const normalizedText = removeVietnameseTones(value.toUpperCase());
        rows[i].style.display = normalizedText.includes(filter) ? "" : "none";
      }
    }
  }
// Loại bỏ dấu tiếng việt
function removeVietnameseTones(str) {
return str.normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .replace(/Đ/g, "D");
}


function formatMoney(money) {
    return new Intl.NumberFormat('vi-VN').format(money);
}


function showToast(message, type = "info", duration = 5000) {
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


function checkLogin(event, nav) {
    event.preventDefault(); // ngăn link chuyển trang

    // Kiểm tra tồn tại id
    const userElement = document.getElementById('userId');
    const isLoggedIn = userElement && userElement.innerText.trim() !== '';

    if(!isLoggedIn) {
        // Hiện popup yêu cầu đăng nhập
        document.getElementById('loginPopup').style.display = 'flex';
    } else {
        // Nếu đã đăng nhập thì chuyển sang trang đặt lịch
        window.location.href = 'index.php?nav=' + nav;
    }
}


function closePopup() {
    document.getElementById('loginPopup').style.display = 'none';
}

function loginNow() {
    // Chuyển đến trang login
    window.location.href = '../Login/login.php';
}