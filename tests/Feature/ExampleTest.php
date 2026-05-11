<?php

use Inertia\Testing\AssertableInertia as Assert;

it('returns a successful response', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Top')->has('errors'));
});
