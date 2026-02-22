<?php
// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost","DB_USER","DB_PASS","invoices_db");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة التحكم</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Cairo',sans-serif}
body{background:#f4f7f6;color:#333}
header{background:#0b5fa5;color:#fff;padding:20px;text-align:center;font-size:24px;font-weight:bold}
.container{max-width:1100px;margin:auto;padding:20px}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:30px}
table{width:100%;border-collapse:collapse;margin-top:10px}
th,td{border:1px solid #ddd;padding:8px;text-align:center}
th{background:#0b5fa5;color:#fff}
.invoice th{background:#0fa36b}
.invoice tfoot td{background:#6bcf63;color:#fff}
button{padding:8px 12px;border:none;border-radius:8px;cursor:pointer;margin:5px}
input{padding:8px;border-radius:8px;border:1px solid #ccc;width:100%}
.flex{display:flex;gap:10px;margin-bottom:10px}
</style>
</head>
<body>

<header>لوحة تحكم الطلبات</header>

<div class="container">

<!-- فاتورة -->
<div class="card" id="invoiceCard">

<div class="flex">
<input type="text" id="customerName" placeholder="اسم العميل">
<input type="text" id="customerPhone" placeholder="رقم الهاتف بدون 0">
<button onclick="saveCustomerOnly()" style="background:#0fa36b;color:#fff">➕ حفظ عميل</button>
</div>

<h3 style="text-align:center">فاتورة شراء</h3>
<p>رقم الفاتورة: <span id="invoiceNumber"></span></p>
<p>التاريخ: <span id="invoiceDate"></span></p>

<table class="invoice" id="invoiceTable">
<thead>
<tr>
<th>المنتج</th>
<th>الكمية</th>
<th>السعر</th>
<th>الإجمالي</th>
</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr>
<td colspan="3"><strong>المجموع النهائي</strong></td>
<td id="totalAmount">0 د.أ</td>
</tr>
</tfoot>
</table>

<button onclick="saveInvoice()">💾 حفظ الفاتورة</button>
<button onclick="downloadImage()">📷 تنزيل صورة</button>
<button onclick="sendWhatsApp()">📲 واتساب</button>
<button onclick="exportExcel()">📊 تصدير Excel</button>

</div>

<!-- أرشيف -->
<div class="card">
<h3>📁 أرشيف الفواتير</h3>
<table id="archiveTable">
<thead>
<tr>
<th>رقم</th>
<th>العميل</th>
<th>الهاتف</th>
<th>الإجمالي</th>
<th>التاريخ</th>
</tr>
</thead>
<tbody>
<?php
$result = $conn->query("SELECT * FROM invoices ORDER BY id DESC");
while($row = $result->fetch_assoc()){
echo "<tr>
<td>{$row['invoice_number']}</td>
<td>{$row['customer_name']}</td>
<td>{$row['customer_phone']}</td>
<td>{$row['total']}</td>
<td>{$row['created_at']}</td>
</tr>";
}
?>
</tbody>
</table>
</div>

</div>

<script>

// قراءة السلة من صفحة المنتجات
function getCart(){
return JSON.parse(localStorage.getItem("cart")) || [];
}

// عرض الفاتورة مباشرة
function renderInvoice(){
const cart=getCart();
const tbody=document.querySelector("#invoiceTable tbody");
tbody.innerHTML="";
let total=0;

cart.forEach(item=>{
let price=parseFloat(item.price)||0;
let qty=parseFloat(item.quantity)||1;
let itemTotal=price*qty;
total+=itemTotal;

tbody.innerHTML+=`
<tr>
<td>${item.name}</td>
<td>${qty}</td>
<td>${price}</td>
<td>${itemTotal.toFixed(2)}</td>
</tr>`;
});

document.getElementById("totalAmount").innerText=total.toFixed(2)+" د.أ";
document.getElementById("invoiceNumber").innerText="INV-"+Date.now();
document.getElementById("invoiceDate").innerText=new Date().toLocaleString('ar-JO');
}

window.addEventListener("storage",renderInvoice);
renderInvoice();

// حفظ فاتورة كاملة
function saveInvoice(){
fetch("save_invoice.php",{
method:"POST",
headers:{"Content-Type":"application/json"},
body:JSON.stringify({
invoice_number:document.getElementById("invoiceNumber").innerText,
customer_name:document.getElementById("customerName").value,
customer_phone:document.getElementById("customerPhone").value,
total:document.getElementById("totalAmount").innerText,
items:getCart()
})
}).then(()=>location.reload());
}

// حفظ عميل فقط
function saveCustomerOnly(){
fetch("save_invoice.php",{
method:"POST",
headers:{"Content-Type":"application/json"},
body:JSON.stringify({
invoice_number:"CUST-"+Date.now(),
customer_name:document.getElementById("customerName").value,
customer_phone:document.getElementById("customerPhone").value,
total:0,
items:[]
})
}).then(()=>location.reload());
}

// تنزيل صورة
function downloadImage(){
html2canvas(document.querySelector("#invoiceCard"))
.then(canvas=>{
let link=document.createElement("a");
link.download="invoice.png";
link.href=canvas.toDataURL();
link.click();
});
}

// واتساب
function sendWhatsApp(){
let phone=document.getElementById("customerPhone").value;
let msg=`فاتورة شراء\nالعميل: ${customerName.value}\nالإجمالي: ${totalAmount.innerText}`;
window.open(`https://wa.me/962${phone}?text=`+encodeURIComponent(msg));
}

// تصدير Excel
function exportExcel(){
let table=document.getElementById("archiveTable");
let wb=XLSX.utils.table_to_book(table,{sheet:"Invoices"});
XLSX.writeFile(wb,"Invoices.xlsx");
}

</script>

</body>
</html>
