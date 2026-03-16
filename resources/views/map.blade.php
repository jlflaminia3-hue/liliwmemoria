<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cemetery Map - Find Your Way</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5rem;
        }
        .header a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: #3498db;
            border-radius: 4px;
        }
        #map {
            height: calc(100vh - 70px);
            width: 100%;
        }
        .legend {
            position: absolute;
            bottom: 30px;
            right: 10px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .legend h4 {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 8px;
            border: 2px solid white;
        }
        .search-box {
            position: absolute;
            top: 90px;
            left: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            width: 280px;
        }
        .search-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .search-box button {
            width: 100%;
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-box button:hover {
            background: #2980b9;
        }
        .directions-btn {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 10px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }
        .directions-btn:hover {
            background: #219a52;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cemetery Map</h1>
        <a href="/">Back to Home</a>
    </div>

    <div class="search-box">
        <h4>Search Deceased</h4>
        <input type="text" id="searchInput" placeholder="Enter name...">
        <button onclick="searchLots()">Search</button>
    </div>

    <div id="map"></div>

    <div class="legend">
        <h4>Legend</h4>
        <div class="legend-item">
            <div class="legend-color" style="background: #28a745;"></div>
            <span>Available Lot</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #dc3545;"></div>
            <span>Occupied Lot</span>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var lots = @json($lots);
        
        var map = L.map('map').setView([14.5995, 120.9842], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var markers = [];

        function createMarker(lot) {
            var markerColor = lot.is_occupied ? '#dc3545' : '#28a745';
            var icon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color:' + markerColor + '; width:24px; height:24px; border-radius:50%; border:3px solid white; box-shadow:0 2px 5px rgba(0,0,0,0.3); cursor:pointer;"></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            var popupContent = '<div style="min-width:200px;">';
            popupContent += '<h5 style="margin:0 0 10px;">' + lot.name + '</h5>';
            popupContent += '<p style="margin:5px 0;"><strong>Section:</strong> ' + (lot.section || 'N/A') + '</p>';
            
            if (lot.deceased && lot.deceased.length > 0) {
                lot.deceased.forEach(function(d) {
                    popupContent += '<hr style="margin:10px 0;">';
                    popupContent += '<p style="margin:5px 0;"><strong>Deceased:</strong> ' + d.first_name + ' ' + d.last_name + '</p>';
                    if (d.date_of_birth) popupContent += '<p style="margin:5px 0;"><strong>Born:</strong> ' + d.date_of_birth + '</p>';
                    if (d.date_of_death) popupContent += '<p style="margin:5px 0;"><strong>Died:</strong> ' + d.date_of_death + '</p>';
                    if (d.burial_date) popupContent += '<p style="margin:5px 0;"><strong>Buried:</strong> ' + d.burial_date + '</p>';
                    
                    popupContent += '<a href="https://www.google.com/maps/dir/?api=1&destination=' + lot.latitude + ',' + lot.longitude + '" target="_blank" class="directions-btn">Get Directions</a>';
                });
            } else {
                popupContent += '<p style="color:#7f8c8d;"><em>No deceased recorded</em></p>';
            }
            
            popupContent += '</div>';

            var marker = L.marker([lot.latitude, lot.longitude], {icon: icon}).addTo(map);
            marker.bindPopup(popupContent);
            markers.push({marker: marker, lot: lot});
        }

        lots.forEach(function(lot) {
            createMarker(lot);
        });

        function searchLots() {
            var searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            markers.forEach(function(item) {
                var lot = item.lot;
                var found = false;
                
                if (lot.name.toLowerCase().includes(searchTerm)) {
                    found = true;
                }
                
                if (lot.deceased && lot.deceased.length > 0) {
                    lot.deceased.forEach(function(d) {
                        if (d.first_name.toLowerCase().includes(searchTerm) || 
                            d.last_name.toLowerCase().includes(searchTerm)) {
                            found = true;
                        }
                    });
                }
                
                if (found) {
                    map.setView([lot.latitude, lot.longitude], 18);
                    item.marker.openPopup();
                }
            });
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchLots();
            }
        });
    </script>
</body>
</html>