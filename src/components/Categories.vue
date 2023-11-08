<template>
    <div>
        <h2>Add Category</h2>
        <form @submit.prevent="addCategories">
            <input v-model="categoryData.auto" type="text" placeholder="Enabled (e.g., 1 or 0)">
            <input v-model="categoryData.name" type="text" placeholder="Category name">
            <input v-model="categoryData.color" type="text" placeholder="Hex Color (e.g., fafafa)">
            <button type="submit">Add Category</button>
        </form>
        
        <div v-if="message" class="message">{{ message }}</div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            categoryData: {
                auto: "",
                color: "",
                name: "",
            },
            message: "",
        };
    },
    methods: {
        addCategories() {
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/categories/addItem';
            const payload = {
                category: {
                    auto: this.categoryData.auto.trim() === "1" ? 1 : 0,
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
        }
    }
}
</script>

<style>
.message {
    color: black;
}
</style>