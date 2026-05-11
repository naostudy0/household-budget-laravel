<?php

use Inertia\Testing\AssertableInertia as Assert;

// TODO: pestの確認のためのテストコード。後で削除する。
it('returns a successful response', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Top', false)->has('errors'));
});
