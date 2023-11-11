<template>
    <div>
        <h2>Firewall Categories</h2>
        <ul v-if="categories.length > 0">
            <li v-for = "category in categories" :key="category.uuid">
                <strong>Name:</strong>{{ category.name }}
                <p><strong>Auto:</strong>{{ category.auto }}</p>
                <p><strong>Color:</strong>{{ category.color }}</p>
                <form @submit.prevent="deletecategory()">
                <button type="submit">Delete</button>
            </form>
            </li>
            
        </ul>
        <p v-else>No categories found.</p>
    </div>
</template>
<script>
import axios from "axios";

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
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/categories/getCategories';
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
        deletecategory() {
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/categories/delCategories/';
            const uuid = '8c49a56a-e62f-4c4e-b211-04776ba98e03';
            axios.post(apiUrl+uuid)
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