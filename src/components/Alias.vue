<template>
  <div id="main-panel">
    <div id="input-panel">
      <h2>Add Alias</h2>
      <form @submit.prevent="addAlias">
        <label for="enableCheckBox">Enable</label>
        <input v-model="aliasData.enabled" type="checkbox" id="enableCheckBox"><br>
        <label for="aliasName">Name:</label>
        <input v-model="aliasData.name" type="text" placeholder="Alias Name" id="aliasName"><br>
        <label for="type-selection">Type:</label>
        <select v-model="aliasData.type" id="type-selection">
          <option value="" selected>Select Type</option>
          <option v-for="(typeItem, key) in aliasItem" :key="key" :value="key">{{ typeItem.value }}</option>
        </select><br>
        <label for="proto-selection">Protocol:</label>
        <select v-model="aliasData.proto" id="proto-selection">
          <option value="" selected>Select Protocol</option>
          <option v-for="(protoItem, key) in aliasItem2" :key="key" :value="key">{{ protoItem.value }}</option>
        </select><br>
        <label for="category-selection">Category:</label>
        <select v-model="aliasData.categories" id="category-selection">
          <option value="" selected>Select a Category</option>
          <option v-for="category in aliasCategory" :key="category.uuid" :value="category.uuid">{{ category.name }}</option>
        </select><br>
        <label for="Description-box">Description:</label>
        <textarea v-model="aliasData.description" placeholder="description()" id="Description-box"></textarea>
        <!-- This part below can be use to provide more options to the user -->
        <!-- <input v-model="aliasData.updatefreq" type="text" placeholder="Update Frequency (optional)">
        <input v-model="aliasData.content" type="text" placeholder="Content">
        <input v-model="aliasData.authgroup_content" type="text" placeholder="Auth Group Content (optional)">-->
        <button type="submit">Add Alias</button>
      </form>
      <!-- <div v-if="message" class="message">{{ message }}</div> -->
    </div>
    <div id="item-panel">
      <h2>Firewall Aliases</h2>
      <div id="table-container" class="d-flex justify-content-center align-items-center">
        <btable v-if="aliases.length > 0" id="table-content">
        <thead>
          <tr>
            <th @click="sortBy('name')">Name</th>
            <th @click="sortBy('description')">Description</th>
            <th @click="sortBy('type')">Type</th>
            <th @click="sortBy('enabled')">Enabled</th>
            <th @click="sortBy('last_updated')">Last Updated</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="alias in sortedAliases" :key="alias.uuid">
            <td>{{ alias.name }}</td>
            <td>{{ alias.description }}</td>
            <td>{{ alias.type }}</td>
            <td @click="handleClick(alias.uuid,alias.type)" :class="{ 'circle': true, 'green': alias.enabled === '1', 'red': alias.enabled === '0' }">
            <img v-if="alias.type === 'External (advanced)' || alias.type === 'Internal (automatic)'" src="./img/restricted.png" alt="Image" id="imagedisabled">
            </td>

            <td>{{ alias.last_updated }}</td>
            <td>
              <button @click="deleteAlias(alias.uuid)" v-if="alias.type === 'Host(s)' || alias.type === 'Network(s)'" style="display: flex; align-items: center;">
                  <span>Delete</span>
                  <img src="./img/bin.png" alt="Delete Icon" id="deleteButton">
              </button>
              <button @click="handleClick(alias.uuid,alias.type)" v-else>
                <img src="./img/restricted.png" alt="Image2" id="imagedisabled2">
              </button>
            </td>
          </tr>
        </tbody>
      </btable>
      <p v-else>No aliases found.</p>
      </div>
    </div>
    <Popup ref="Popup" :message="message"  />

  </div>
</template>

<script>
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';
import Popup from './Popup';

export default {
  components: {
    Popup, // Register the Popup component
  },
  data() {
    return {
      aliasData: {
        enabled: true, 
        name: "",
        type: "", 
        proto: "",
        description: "",
        categories: "",
        updatefreq: "",
        content: "",
        authgroup_content: "",
        network_content: "",
      },
      updateKey: 0,
      message: "",
      aliases: [],
      aliasCategory: [],
      aliasItem: {},
      aliasItem2: {},
      sortKey: 'name', // Property to store the current sorting key
      sortDirection: 1, // 1 for ascending, -1 for descending
    };
  },
  watch: {
  updateKey: function(newMessage) {
      if (newMessage) {
        this.$refs.Popup.openPopup(); // Trigger the popup
      }
    },
  },
  created() {
    this.fetchAliases();
    this.getAliasCategories();
    this.getAliasItem();
  },
  computed: {
    sortedAliases() {
      return this.aliases.slice().sort((a, b) => {
        const modifier = this.sortDirection === 1 ? 1 : -1;
        return modifier * a[this.sortKey].localeCompare(b[this.sortKey]);
      });
    },
  },
  methods: {
    addAlias() {
      const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/alias/addItem';
      const payload = {
      alias: {
        enabled: this.aliasData.enabled? 1 : 0,
        name: this.aliasData.name.trim(),
        type: this.aliasData.type.trim(),
        proto: this.aliasData.proto.trim(),
        description: this.aliasData.description.trim(),
        categories: this.aliasData.categories.trim(),
        updatefreq: this.aliasData.updatefreq.trim(),
        content: this.aliasData.content.trim(),
      },
      authgroup_content: this.aliasData.authgroup_content.trim(),
      network_content: this.aliasData.network_content.trim(),
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
        this.fetchAliases();
        this.updateKey++
        } else {
          this.message = 'Error: ' + response.data.message;
        }
      })
      .catch(error => {
        this.message = 'Error: ' + (error.response && error.response.data.message || error.message);
      });
    },
    fetchAliases() {
      const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/alias/getAlias';
      axios.get(apiUrl)
        .then(response => {
          if (response.data && Array.isArray(response.data)) {
            this.aliases = response.data;
          } else if (response.data.errorMessage) {
            console.error(response.data.errorMessage);
            // Handle error message
          }
        })
        .catch(error => {
          console.error('Error fetching aliases:', error);
          // Handle HTTP error
        })
        .finally(() => {
          // Update sortedAliases after fetching data
          this.updateSortedAliases();
        });
    },
    deleteAlias(uuid) {
      const apiUrl = OC.generateUrl(`/apps/nextopn/api/firewall/alias/delItem/${uuid}`);
      axios.post(apiUrl)
              .then(response => {
              if(response.data.success) {
                  this.message = response.data.message;
                  this.updateKey++;
                  this.fetchAliases();
                  
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
    updateSortedAliases() {
      // Update sortedAliases whenever aliases changes
      this.aliases = [...this.aliases]; // Trigger Vue reactivity
    },
    getAliasCategories() {
      const apiUrl = OC.generateUrl('/apps/nextopn/api/firewall/alias/getAliasCategory');
      axios.get(apiUrl)
        .then(response => {
          if (response.data && Array.isArray(response.data)) {
            this.aliasCategory = response.data;
          } else if (response.data.errorMessage) {
            console.error(response.data.errorMessage);
            // Handle error message
          }
        })
        .catch(error => {
          console.error('Error fetching aliases-categories:', error);
          // Handle HTTP error
        });
    },
    getAliasItem() {
      const apiUrl = OC.generateUrl('/apps/nextopn/api/firewall/alias/getAliasItem');
      axios.get(apiUrl)
        .then(response => {
          if (response.data && response.data.type && response.data.proto) {
            this.aliasItem = response.data.type;
            this.aliasItem2 = response.data.proto;
            console.log(this.aliasItem);
          } else if (response.data.errorMessage) {
            console.error(response.data.errorMessage);
            // Handle error message
          }
        })
        .catch(error => {
          console.error('Error fetching aliases-categories:', error);
          // Handle HTTP error
        });
    },
    toggleAliasEnabled(uuid) {
      const apiUrl = OC.generateUrl(`/apps/nextopn/api/firewall/alias/toogleItem/${uuid}`);
      axios.post(apiUrl)
              .then(response => {
              if(response.data.success) {
                  this.message = response.data.message;
                  this.updateKey++;
                  this.fetchAliases();   
              } else {
                  this.message = 'Error: ' + response.data.message;
              }
              })
              .catch(error => {
                  this.message = 'Error: ' + (error.response && error.response.data.message || error.message);
              });
    },
    handleClick(uuid,aliastype) {
        // Check the condition before calling toggleAliasEnabled
        if (aliastype === 'External (advanced)' || aliastype === 'Internal (automatic)') {
            this.message = 'Resctricted';
            this.updateKey++;
        } else {
          this.toggleAliasEnabled(uuid);
        }
    },
  },
}
</script>

<style scoped>
.circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: inline-block;
  margin-left: 5px; /* Adjust 
  the margin as needed */
  cursor: pointer;
}

.green {
    background-color: rgb(43, 193, 43);
}
.red {
    background-color: red;
}
th {
  width: 10%;
  cursor: pointer;
  backdrop-filter: blur(10px);
  position: sticky;
}
#item-panel {
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
h2 {
  position: relative;
  text-align: center;
}
form {
  font-size: 15px;
  position: relative;
  text-align: center;
}
#imagedisabled {
  /* @author:<a href="https://www.flaticon.com/free-icons/error" title="error icons">Error icons created by juicy_fish - Flaticon</a> */
  width: 20px;
  height: 20px;
  cursor: pointer;
}
.image-container {
position: relative;
display: inline-block;
}

.floating-text {
position: absolute;
margin-left: 4em;
top: 30%;
left: 50%;
transform: translate(-50%, -50%);
opacity: 0; /* Initially hidden */
transition: opacity 0.3s ease-in-out; /* Add a smooth transition effect */
}

.image-container:hover .floating-text {
opacity: 1; /* Show the text on hover */
}
#deleteButton {
margin-left: 5px;
width: 20px; 
height: 20px;
}
</style>
