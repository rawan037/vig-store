// ⭐ js/app.js

import { doc, onSnapshot } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

// رقم الطلب اللي تريد تتبعه
const orderId = "152"; // غيّره حسب طلبك

// مرجع الطلب في Firebase
const orderRef = doc(window.db, "orders", orderId);

// متغير للاحتفاظ بعلامة السائق حتى نحدثها بدون تكرار
let driverMarker = null;

// متابعة التغييرات اللحظية للطلب
onSnapshot(orderRef, (docSnap) => {
  if (docSnap.exists()) {
    const data = docSnap.data();

    // تحديث حالة الطلب على الصفحة
    const statusElement = document.getElementById("orderStatus");
    statusElement.innerText = data.status || "–";

    // تحديث موقع السائق على الخريطة
    const driverLocation = data.driverLocation;
    if (driverLocation && window.map) {
      // حذف أي علامة قديمة قبل إضافة الجديدة
      if (driverMarker) driverMarker.setMap(null);

      driverMarker = new google.maps.Marker({
        position: driverLocation,
        map: window.map,
        label: "🚚",
      });

      // مركز الخريطة على السائق
      window.map.setCenter(driverLocation);
    }
  } else {
    console.warn("الطلب غير موجود في قاعدة البيانات.");
  }
});
