// رقم الطلب اللي تريد تتبعه
const orderId = "152"; // غيّره حسب الطلب

import { doc, onSnapshot } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore.js";

const orderRef = doc(window.db, "orders", orderId);

onSnapshot(orderRef, (docSnap) => {
  if (docSnap.exists()) {
    const data = docSnap.data();

    // تحديث حالة الطلب
    document.getElementById("orderStatus").innerText = data.status || "–";

    // تحديث موقع السائق على الخريطة
    const driverLocation = data.driverLocation;
    if (driverLocation && window.map) {
      new google.maps.Marker({
        position: driverLocation,
        map: window.map,
        label: "🚚",
      });
      window.map.setCenter(driverLocation);
    }
  }
});
