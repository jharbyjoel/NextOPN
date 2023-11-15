<template>
    <div id="content" class="app-nextopn">
      <AppNavigation
      ></AppNavigation>
      <AppContent>
        <Categories/>
        <Alias/>
        <getAlias/>
        <AddGroup/>
        <DeleteGroup/>
      </AppContent>

      <component :is="currentComponent"></component>
    </div>
    

</template>


<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import '@nextcloud/dialogs/styles/toast.scss'
import axios from '@nextcloud/axios'
import Alias from './components/Alias.vue'
import Categories from './components/Categories.vue'
import getAlias from './components/getAlias.vue'
import AddGroup from './components/AddGroup.vue'
import DeleteGroup from './components/DeleteGroup.vue'

export default {
	name: 'App',
	components: {
    Categories,
    Alias,
    getAlias,
    AddGroup,
    DeleteGroup,

	},
	data() {
		return {
			loading: true,
      activeTab: '',
		}
	},
	    // You might have other methods or mounted functions related to your firewall
	methods: {
    setActiveTab(tabName) {
      this.active = tabName;
    },
	},
  computed: {
    currentComponent() {
      switch (this.activeTab) {
        case 'categories': return Categories;

        default: return null;
      }
    }
  }
}
</script>

<style>



</style>