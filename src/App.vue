<template>
    <div id="content" class="app-nextopn">
        <AppNavigation>
            <Firmware />
        </AppNavigation>
        <AppContent>
            <!-- Possibly add other components or text related to your firewall here -->
        </AppContent>
    </div>
</template>


<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import '@nextcloud/dialogs/styles/toast.scss'
import axios from '@nextcloud/axios'
import Firmware from './components/Firmware.vue'

export default {
	name: 'App',
	components: {
		AppContent,
		AppNavigation,
		Firmware,
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
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}
</style>

