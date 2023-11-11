<template>
    <div>
        <h2>Firewall Categories</h2>
        <ul v-if="categories.length > 0">
            <li v-for = "category in categories" :key="category.uuid">
                <strong>Name:</strong>{{ category.name }}
                <p><strong>Auto:</strong>{{ category.auto }}</p>
                <p><strong>Color:</strong>{{ category.color }}</p>
                <button @click="deletecategory(category.uuid)">Delete</button>
            </li>
            
        </ul>
        <p v-else>No categories found.</p>
    </div>
</template>
<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router'

export default {
    data() {
        return {
            categories: []
        }
    },
    created() {
        this.fetchCategories();
    },
    methods: {
        fetchCategories() {
            // 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/categories/getCategories'
            const apiUrl = OC.generateUrl('/apps/nextopn/api/firewall/categories/getCategories');
            axios.get(apiUrl)
                .then(response => {
                    if (response.data && Array.isArray(response.data)) {
                        this.categories = response.data;
                    } else if (response.data.errorMessage) {
                        console.error(response.data.errorMessage);
                        // Handle error message
                    }
                })
                .catch(error => {
                console.error('Error fetching Categories:', error);
                // Handle HTTP error
                });
        },
        deletecategory(uuid) {
            const apiUrl = OC.generateUrl(`/apps/nextopn/api/firewall/categories/delItem/${uuid}`);
            axios.post(apiUrl)
                .then(response => {
                if(response.data.success) {
                    this.message = response.data.message;
                } else {
                    this.message = 'Error: ' + response.data.message;
                }
                })
                .catch(error => {
                    this.message = 'Error: ' + (error.response && error.response.data.message || error.message);
                });
        },
        
    }
}
</script>

<style>
/* Styles will be added later */
</style>