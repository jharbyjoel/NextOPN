<template>
    
    <div id="main-panel">
        <header><h1 id="title"><strong>Categories</strong></h1></header>
        <!-- <img src="./img/opn-png.png" alt="logo" class="logo"> -->
        <div id="Add-panel" class="div-child">
            <h2>Add Category:</h2>
            <form @submit.prevent="addCategories">
                Category Name:&nbsp&nbsp<input v-model="categoryData.name" type="text" placeholder="Category name" id="cat-name"><br>
                Choose a color:&nbsp&nbsp<input v-model="categoryData.color" type="color" id="cat-color" value="fafafa"><br>
                Auto:&nbsp&nbsp<input v-model="categoryData.auto" type="checkbox" id="autoCheckBox"><br>
                <button type="submit" id="category-added">Add Category</button>
            </form>
        </div>
        <div id="Info-Panel" class="div-child">
            <h2>Firewall Categories:</h2>
            <div id="table-container" class="d-flex justify-content-center align-items-center">
                <btable v-if="categories.length > 0" id="table-content">
                    <thead>
                        <tr>
                            <th @click="sortBy('name')">Name</th>
                            <th @click="sortBy('auto')">Auto</th>
                            <th @click="sortBy('color')">Color</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for = "category in sortedCategories" :key="category.uuid">
                            <td>{{ category.name }}</td>
                            <td>
                                <div v-if="category.auto === '1' " class="circle green"></div>
                                <div v-else-if="category.auto === '0'" class="circle red"></div>
                            </td>
                            <td>
                                <div :style="{ backgroundColor: '#'+category.color }" class="color-box"></div>
                            </td>
                            <td>
                                <button @click="deletecategory(category.uuid)" id="delete-cat">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </btable>
                <p v-else>No categories found.</p>
            </div>
        </div>
        <!-- Displaying messages -->
        <Popup ref="Popup" :message="message"  />
    </div>
    
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import Popup from './Popup.vue'

export default {
    components: {
      Popup, // Register the Popup component
    },
    data() {
        return {
            categoryData: {
                auto: "",
                color: "",
                name: "",
            },
            message: "",
            updateKey: 0,
            categories: [],
            sortKey: 'name', // Property to store the current sorting key
            sortDirection: 1, // 1 for ascending, -1 for descending
        };
    },
    created() {
        this.fetchCategories();
    },
    computed: {
      sortedCategories() {
        return this.categories.slice().sort((a, b) => {
          const modifier = this.sortDirection === 1 ? 1 : -1;
          return modifier * a[this.sortKey].localeCompare(b[this.sortKey]);
        });
      },
    },
    watch: {
        color(value) {
            if (value.toString().match(/#[a-zA-Z0-9]{8}/)) {
                this.color = value.substr(0, 7);
            }
        },
        updateKey: function(newMessage) {
        if (newMessage) {
          this.$refs.Popup.openPopup(); // Trigger the popup
        }
      },
    },
    methods: {
        addCategories() {
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/categories/addItem';
            const payload = {
                category: {
                    auto: this.categoryData.auto? 1 : 0,
                    color: this.categoryData.color.trim().replace(/^#/, ''),
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
                    this.updateKey++;
                    this.fetchCategories();
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
                    this.updateKey++;
                    this.fetchCategories();
                    
                } else {
                    this.message = 'Error: ' + response.data.message;
                }
                })
                .catch(error => {
                    this.message = 'Error: ' + (error.response && error.response.data.message || error.message);
                });
        },
        sortBy(key) {
            if (this.sortKey === key) {
            this.sortDirection = this.sortDirection * -1;
            } else {
            this.sortKey = key;
            this.sortDirection = 1;
            }
        },
        updateSortedCategories() {
        // Update sortedAliases whenever aliases changes
        this.categories = [...this.categories]; // Trigger Vue reactivity
        },
    },
}
</script>

<style scoped>
h1 {
    font-size: 40px;
    text-align: center;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}
#autoCheckBox {
    height: 25px;
    width: 25px;
    background-color: #eee;
}
#main-panel {
    margin: 1rem;
    padding: 2rem 2rem;
    width: 100%;
}
#form {
    font-size: 15px;
    position: relative;
    text-align: center;
}

h2 {
    position: relative;
    text-align: center;
    font-size: 22px;
}
.div-child {
    display: block;
    padding: 1rem 1rem;
}
#item-list {
    position: relative;
    width: 100%;
    display: inline-table;
    
}
#elements-list {
    font-size: 20px;
    display: block;
    padding: 0.75rem 1rem;
    border: solid 8px rgba(0, 0, 0, 0.25);
    margin-bottom: 0.5rem;
}
.circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    margin-left: 5px; /* Adjust the margin as needed */
}

.green {
    background-color: rgb(43, 193, 43);
}
.red {
    background-color: red;
}
.color-box {
    border: 2px solid #000000;
    width: 25px;
    height: 25px;
}
th {
    backdrop-filter: blur(10px);
    position: sticky;
    width: 10%;
    cursor: pointer;
}
#Info-Panel {
    border: 2px solid;
    border-color: black;
    padding: 0.5em;
    border-radius: 2em;
}
#table-container {
    margin: 2%;
    height: 500px;
    overflow: auto;
}
.table-content {
    margin: 4%;
  }
/* elem {
    width: 100%;
    width: -moz-available;           WebKit-based browsers will ignore this.
    width: -webkit-fill-available;   Mozilla-based browsers will ignore this. 
    width: fill-available;
} */

</style>