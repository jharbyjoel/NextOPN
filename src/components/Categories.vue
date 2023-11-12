<template>
    <div id="main-panel">
        <img src="./img/opn-png.png" alt="logo" class="logo">
        <h1 id="header">Categories</h1>
        <div id="Add-panel">
        <h2>Add Category</h2>
            <form @submit.prevent="addCategories">
                Category Name: <input v-model="categoryData.name" type="text" placeholder="Category name">
                Color: <input v-model="categoryData.color" type="text" placeholder="Hex-color">
                Auto <input v-model="categoryData.auto" type="checkbox" id="autoCheckBox">
                <label for="autoCheckbox">Auto Enabled</label>
                <button type="submit">Add Category</button>
            </form>
        </div>
        <div id="Info Panel">
            <h2>Firewall Categories</h2>
            <ul v-if="categories.length > 0">
                <li v-for = "category in categories" :key="category.uuid">
                    <strong>Name: </strong>{{ category.name }}
                    <p><strong>Auto: </strong>{{ category.auto }}</p>
                    <p><strong>Color: </strong>{{ category.color }}</p>
                    <button @click="deletecategory(category.uuid)">Delete</button>
                </li>
            </ul>
            <p v-else>No categories found.</p>
        </div>
        <div v-if="message" class="message">{{ message }}</div>
    </div>
    
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

export default {
    data() {
        return {
            categoryData: {
                auto: "",
                color: "",
                name: "",
            },
            message: "",
            categories: [],
        };
    },
    created() {
        this.fetchCategories();
    },
    methods: {
        addCategories() {
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/categories/addItem';
            const payload = {
                category: {
                    auto: this.categoryData.auto? 1 : 0,
                    color: this.categoryData.color.trim(),
                    name: this.categoryData.name.trim(),
                },
            };
            console.log("Payload before sending: ", payload);

            axios.post(apiUrl, payload, {
                headers: {
                    'Content-Type': 'application/json; charset=UTF-8', 
                },
            })
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
        fetchCategories() {
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
.logo {
    width : 100px;
}
.message {
    color: black;
}
</style>