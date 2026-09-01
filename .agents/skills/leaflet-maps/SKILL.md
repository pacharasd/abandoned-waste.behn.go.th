---
name: leaflet-maps
description: Complete guide and patterns for implementing interactive OpenStreetMap using Leaflet.js. Use for location picking, reverse geocoding, multi-marker maps, popups, and custom pins.
---

# Leaflet.js & OpenStreetMap Integration Skill

## 1. Map Initialization
- Always set appropriate initial coordinates and zoom levels (e.g. Thailand Center: `[13.7563, 100.5018]`, Zoom: `13`).
- Include standard OpenStreetMap tile layers:
  ```javascript
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);
  ```

## 2. Interactive Location Picker (Single Pin)
- Create a draggable marker that updates hidden inputs (`latitude`, `longitude`) upon drag end or map click:
  ```javascript
  let marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  marker.on('dragend', function (e) {
      const position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat.toFixed(7);
      document.getElementById('longitude').value = position.lng.toFixed(7);
  });
  map.on('click', function(e) {
      marker.setLatLng(e.latlng);
      document.getElementById('latitude').value = e.latlng.lat.toFixed(7);
      document.getElementById('longitude').value = e.latlng.lng.toFixed(7);
  });
  ```
- Support HTML5 Geolocation `navigator.geolocation.getCurrentPosition(...)` with a "ค้นหาตำแหน่งปัจจุบัน" (Get Current Location) button.

## 3. Multi-Marker Overview Map
- Color-code markers based on report status (e.g., Yellow = `รอรับเรื่อง`, Blue = `กำลังดำเนินการ`, Green = `จัดเก็บเรียบร้อยแล้ว`, Gray = `ยกเลิก`).
- Add informative custom popups with Report Number, Waste Type, Estimated Weight, Status Badge, and a direct link to the detail page.
- Auto-fit map bounds using `map.fitBounds(markersGroup.getBounds())` when markers exist.
