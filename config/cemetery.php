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
    'map_image' => env('CEMETERY_MAP_IMAGE', 'backend/assets/images/map.jpg'),

    /*
    | Entrance marker coordinates on the image (pixels).
    */
    'entrance_x' => env('CEMETERY_ENTRANCE_X', 60),
    'entrance_y' => env('CEMETERY_ENTRANCE_Y', 80),
    'entrance_label' => env('CEMETERY_ENTRANCE_LABEL', 'Entrance'),
];

