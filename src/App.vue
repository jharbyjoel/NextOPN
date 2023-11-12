<template>
    <div id="content" class="app-nextopn">
        <a class="active" href="#categories">Categories</a>
        <a href="#firmware"><i class="fa fa-fw fa-search"></i>Firmware</a>
        <a href="#aliases">Aliases</a>
        <a href="#group">Group</a>
        <a href="#system">System</a>
        <a href="#user">User</a>
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
import getCategories from './components/getCategories.vue'
import 'font-awesome/css/font-awesome.min.css';

export default {
	name: 'App',
	components: {
		getCategories,
    Firmware,
    Categories,
    getAlias,
    AddGroup,
    DeleteGroup
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

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
.app-nextopn {
  background-color: #333;
  overflow: hidden;
}

.app-nextopn a {
  font-family: 'Roboto', sans-serif; /* This is a modern font */
  font-size: 16px; /* Adjust size as needed */
  font-weight: 700; /* 700 is for bold text */
  text-transform: uppercase; /* Makes all text uppercase */
  letter-spacing: 0.05em; /* Adds spacing between letters */
  color: #ffffff; /* White text is usually modern and contrasts well with darker backgrounds */
  padding: 14px 16px;
  text-decoration: none;
  transition: color 0.3s ease; /* Smooth transition for hover effects */
}

.app-nextopn a:hover {
  color: #dddddd; /* Slightly lighter color on hover for a subtle effect */
  text-decoration: none; /* Ensures that no underline appears on hover */
}

/* Add a media query for responsive font sizing on smaller screens */
@media (max-width: 768px) {
  .app-nextopn a {
    font-size: 14px; /* Smaller font size on mobile devices */
  }
}


</style>