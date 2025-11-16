var tensp=$("#product_id").val();
var giabanle =  $("#soluong").val();
var bool = true;
function open_closeNav() {
    var sidebar = document.getElementById("mySidebar");
    var main = document.getElementById("main");

    if (!sidebar || !main) return;
    
    if(!bool){
        sidebar.style.width = "220px";
        main.style.marginLeft = "220px";
        
        bool = true;
    }else{    
        sidebar.style.width = "0";
        main.style.marginLeft= "0";
        bool = false;
    }
}


function changeWebsize() {
    var sidebar = document.getElementById("mySidebar");
    var main = document.getElementById("main");

    if (!sidebar || !main) return;

    if(bool){
        if (window.innerWidth <= 768) {
            sidebar.style.width = "65px";
            main.style.marginLeft = "65px";
        }
        else if(sidebar){
            sidebar.style.width = "220px";
            main.style.marginLeft = "220px";
        }
    }
}

// Gọi hàm lặp lại mỗi 2 giây
setInterval(changeWebsize, 100);



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



