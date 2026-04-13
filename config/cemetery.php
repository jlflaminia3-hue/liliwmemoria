<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cemetery Locator (Image Map)
    |--------------------------------------------------------------------------
    | This app uses Leaflet in "image coordinate" mode (CRS.Simple) where:
    |   lng = X (pixels), lat = Y (pixels)
    | The same map image is used in the admin lot map and in the public locator.
    */
    // Keep this aligned with the admin lot map overlay image.
    'map_image' => env('CEMETERY_MAP_IMAGE', 'backend/assets/images/map_visitor.jpg'),

    /*
    | Entrance marker coordinates on the image (pixels).
    */
    'entrance_x' => env('CEMETERY_ENTRANCE_X', 60),
    'entrance_y' => env('CEMETERY_ENTRANCE_Y', 80),
    'entrance_label' => env('CEMETERY_ENTRANCE_LABEL', 'Entrance'),

    /*
    |--------------------------------------------------------------------------
    | Walking Lanes (Optional)
    |--------------------------------------------------------------------------
    | To draw an accurate route that follows your map's walkable lanes (e.g. the
    | yellow lanes on your image), provide a JSON file containing lane polylines.
    |
    | Format:
    | [
    |   { "id": "lane-1", "points": [ {"x": 10, "y": 20}, {"x": 40, "y": 20} ] },
    |   { "id": "lane-2", "points": [ {"x": 40, "y": 20}, {"x": 40, "y": 80} ] }
    | ]
    |
    | Coordinates are in image pixels (CRS.Simple): x = lng, y = lat.
    */
    'lanes_json' => env('CEMETERY_LANES_JSON', null), // e.g. 'backend/assets/lanes.json'

    /*
    | Maximum distance (pixels) to "snap" the start/end points to the lane
    | network. If too far, the locator falls back to a straight line.
    */
    'lanes_snap_distance' => (float) env('CEMETERY_LANES_SNAP_DISTANCE', 80),
];
