<?php

test('services index page loads', function () {
    $response = $this->get('/services');

    $response->assertStatus(200);
    $response->assertSee('NARRA LOTS');
    $response->assertSee('GARDEN LOTS');
});

test('individual service page loads', function () {
    $response = $this->get('/services/narra-lots');

    $response->assertStatus(200);
    $response->assertSee('NARRA LOTS');
    $response->assertSee('Inquire Now');
});

test('non-existent service returns 404', function () {
    $response = $this->get('/services/non-existent');

    $response->assertStatus(404);
});
