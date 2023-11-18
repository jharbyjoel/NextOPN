<template>
<<<<<<< HEAD
    <div id="app-container" class="app-nextopn">
        <Navbar/>
=======
    <div id="content" class="app-nextopn">
      <AppNavigation
      ></AppNavigation>
      <AppContent id="navigation">
        <Groups/>
      </AppContent>
>>>>>>> 92c610adf3338f1ab3a2427311572964172e1420

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
import AddGroup from './components/AddGroup.vue'
import DeleteGroup from './components/DeleteGroup.vue'
import Navbar from './components/Navbar.vue'


export default {
	name: 'App',
	components: {
    Categories,
    Alias,
    AddGroup,
    DeleteGroup,
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