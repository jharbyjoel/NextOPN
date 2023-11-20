<template>
  <div id="app-container" class="app-nextopn">
      <Navbar/>

      <div id="content">
          <router-view></router-view>
      </div>
  </div>
</template>



<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import '@nextcloud/dialogs/styles/toast.scss'
import axios from '@nextcloud/axios'
import Alias from './components/Alias.vue'
import Categories from './components/Categories.vue'
import Groups from './components/Groups.vue'
import Navbar from './components/Navbar.vue'


export default {
	name: 'App',
	components: {
    Categories,
    Alias,
    Groups,
    Navbar,


	},
	data() {
		return {

		}
	},
	    // You might have other methods or mounted functions related to your firewall
	methods: {

}

}
</script>

<style>

#navigation {
  overflow: auto;
  width: 100%;
}

</style>