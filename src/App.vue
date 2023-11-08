<template>
    <div id="content" class="app-nextopn">
        <AppNavigation>
        </AppNavigation>
        <AppContent>
			
        
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
import LandingPage from './components/LandingPage.vue'
import getAlias from './components/getAlias.vue'

export default {
	name: 'App',
	components: {
		Firmware,
		getStatus,
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
#content {
  display: flex;
  flex-direction: column;
  height: 100vh; /* Adjust if necessary to fit your layout */
}

#content > * {
  overflow: auto;
  padding: 20px;
  flex-shrink: 0; /* Prevent the children from shrinking */
}

/* Specifically for the AppContent, if it's the scrollable area */
.app-content {
  flex-grow: 1; /* Allow AppContent to fill the space and be scrollable */
  overflow: auto; /* Add scroll to the AppContent */
}
</style>