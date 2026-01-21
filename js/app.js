const orderId = "152"; // رقم الطلب لتجربة
const orderRef = doc(window.db, "orders", orderId);

onSnapshot(orderRef, (docSnap) => {
  if (docSnap.exists()) {
    const data = docSnap.data();
    document.getElementById("orderStatus").innerText = data.status;

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
