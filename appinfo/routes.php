<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Jharby <jsara030@fiu.edu>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\NextOPN\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'resources' => [
        'note' => ['url' => '/notes'],
        'note_api' => ['url' => '/api/0.1/notes']
    ],
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'note_api#preflighted_cors', 'url' => '/api/0.1/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
        ['name' => 'firmware#getStatus', 'url' => '/api/firmware/status', 'verb' => 'GET'],
        ['name' => 'firmware#getAlias', 'url' => '/api/firewall/alias/getAlias', 'verb' => 'GET'],
        ['name' => 'firmware#addAlias', 'url' => '/api/firewall/alias/addItem', 'verb' => 'POST'],
        ['name' => 'firmware#addCategories', 'url' => '/api/firewall/categories/addItem', 'verb' => 'POST'],
        ['name' => 'firmware#getCategories', 'url' => '/api/firewall/categories/getCategories', 'verb' => 'GET'],
        ['name' => 'menutree#getStatus', 'url' => '/api/menutree', 'verb' => 'GET'],
        ['name' => 'menutree#getStatus', 'url' => '/api/menutree', 'verb' => 'GET'],
        ['name' => 'menusearch#getStatus', 'url' => '/api/menusearch', 'verb' => 'GET'],
    ],
];
