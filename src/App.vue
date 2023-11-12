<template>
    <div id="content" class="app-nextopn">
        <AppNavigation>
        </AppNavigation>
        <AppContent>
          <Categories/>
        </AppContent>
    </div>
</template>


<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import '@nextcloud/dialogs/styles/toast.scss'
import axios from '@nextcloud/axios'
import Firmware from './components/Firmware.vue'
import Categories from './components/Categories.vue'
import getAlias from './components/getAlias.vue'
import AddGroup from './components/AddGroup.vue'
import DeleteGroup from './components/DeleteGroup.vue'

export default {
	name: 'App',
	components: {
		Categories,
	},
	data() {
		return {
			loading: true,
		}
	},
	    // You might have other methods or mounted functions related to your firewall
	methods: {
		// If there are methods related to the firewall, retain them
	},
}
</script>

<style scoped>

</style>