/**
 * SPDX-FileCopyrightText: 2018 John Molakvoæ <skjnldsv@protonmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Vue from 'vue'
import App from './App'
import Router from 'vue-router'
import 'font-awesome/css/font-awesome.min.css';
import { generateFilePath } from '@nextcloud/router'

// Import your components here
import Categories from './components/Categories'; 
import Firmware from './components/Firmware';
import Alias from './components/Alias';
__webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.mixin({ methods: { t, n } })


Vue.use(Router)

const router = new Router({
  routes: [
    {
      path: '/categories',
      name: 'Categories',
      component: Categories
    },
    {
      path: '/firmware',
      name: 'Firmware',
      component: Firmware,
    },
    {
    path: '/aliases',
    name: 'Alias',
    component: Alias,
    }
  ]
});


export default new Vue({
  el: '#content',
  router, 
  render: h => h(App),
})
