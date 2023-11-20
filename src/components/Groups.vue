<template>
    <div id="main-panel">
      <div id="input-panel">
        <h2>Add Group</h2>
        <form @submit.prevent="addGroup">
          <label for="groupName" class="non-selectable">Name: </label>
          <input v-model="addGroupData.ifname" type="text" id="groupName" placeholder="Enter name"><br>
          <label for="groupMember" class="non-selectable">Members: </label>
          <select v-model="addGroupData.members" id="groupMember">
            <option value="" selected>Select Type</option>
            <option v-for="(memberItem, key) in groupItem" :key="key" :value="key">{{ memberItem.value }}</option>
          </select><br>
          <label for="groupSequence" class="non-selectable">Sequence: </label>
          <select v-model="addGroupData.sequence" id="groupSequence">
            <option value= 0 selected>0</option>
            <option v-for="number in 10" :key="number" :value="parseInt(number)">{{ number }}</option>
          </select><br>
          <label for="groupNoGroup" class="non-selectable">(no) GUI groups</label>
          <input v-model="addGroupData.nogroup" type="checkbox" id="groupNoGroup"><br>
          <label for="Description-box" class="non-selectable">Description:</label>
          <textarea v-model="addGroupData.descr" placeholder="description()" id="Description-box"></textarea><br>
          <button type="submit">Create Group</button>
        </form>
      </div>
      <div id="item-panel">
        <h2>Firewall Groups</h2>
        <div id="table-container" class="d-flex justify-content-center align-items-center">
          <btable v-if="groups.length >0" id="table-content">
            <thead>
              <tr>
                <!-- WORK ON THIS!!! -->
                <th @click="sortBy('ifname')">Name</th>
                <th @click="sortBy('members')">Members</th>
                <th @click="sortBy('sequence')">Sequence</th>
                <th @click="sortBy('descr')">Description</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="grouped in sortedGroups" :key="grouped.uuid">
                <td>{{ grouped.ifname }}</td>
                <td>{{ grouped.members }}</td>
                <td>{{ grouped.sequence }}</td>
                <td>{{ grouped.descr }}</td>
                <td>
                <button @click="deleteGroup(grouped.uuid)" v-if="grouped.descr !== 'IPsec' && grouped.descr !== 'all OpenVPN interfaces'" style="display: flex; align-items: center;">
                  <span>Delete</span>
                  <img src="./img/bin.png" alt="Delete Icon" id="deleteButton">
                </button>
                <button @click="handleClick()" v-else-if="grouped.descr === 'IPsec' || grouped.descr === 'all OpenVPN interfaces'" style="display: flex; align-items: center;">
                  <img src="./img/restricted.png" alt="Image3" id="imagedisabled2">
                </button>
                </td>
              </tr>
            </tbody>

          </btable>
          <p v-else>No aliases found.</p>
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
    Popup,
    },
    data() {
        return {
          addGroupData: {
            ifname: "",
            members: "",
            nogroup: "",
            sequence: 0,
            descr: "",
          },
          groups: [],
          message: "",
          updateKey: 0,
          groupItem: {},
          sortKey: 'ifname', // Property to store the current sorting key
          sortDirection: 1, // 1 for ascending, -1 for descending
        };
    },
    computed: {
      sortedGroups() {
        return this.groups.slice().sort((a, b) => {
          const modifier = this.sortDirection === 1 ? 1 : -1;
          return modifier * a[this.sortKey].localeCompare(b[this.sortKey]);
        });
      },
    },
    created() {
      this.fetchGroups();
      this.getGroupItem();
    },
    watch: {
      updateKey: function(newMessage) {
          if (newMessage) {
            this.$refs.Popup.openPopup(); // Trigger the popup
          }
      },
    },
    methods: {
      fetchGroups() {
        const apiUrl = OC.generateUrl('/apps/nextopn/api/firewall/group/getGroups');
            axios.get(apiUrl)
            .then(response => {
                if (response.data && Array.isArray(response.data)) {
                    this.groups = response.data;
                } else if (response.data.errorMessage) {
                    console.error(response.data.errorMessage);
                    // Handle error message
                }
            })
            .catch(error => {
            console.error('Error fetching Groups:', error);
            // Handle HTTP error
            });
      },
      addGroup() {
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/group/addGroup';
            const payload = {
                group: {
                    ifname: this.addGroupData.ifname,
                    members: this.addGroupData.members,
                    nogroup: this.addGroupData.nogroup? 1 : 0,
                    sequence: this.addGroupData.sequence,
                    descr: this.addGroupData.descr,
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
          this.fetchGroups();
          this.updateKey++;
          } else {
            this.message = 'Error: ' + response.data.message;
          }
        })
        .catch(error => {
          this.message = 'Error: ' + (error.response && error.response.data.message || error.message);
        });
           
      },
      deleteGroup(uuid) {
          const apiUrl = OC.generateUrl(`apps/nextopn/api/firewall/group/delGroup/${uuid}`)
          
          axios.post(apiUrl)
          .then(response => {
                if(response.data.success) {
                    this.message = response.data.message;
                    this.updateKey++;
                    this.fetchGroups();
                    
                } else {
                    this.message = 'Error: ' + response.data.message;
                }
                })
                .catch(error => {
                    this.message = 'Error: ' + (error.response && error.response.data.message || error.message);
                });
      },
      updateSortedCategories() {
      // Update sortedGroups whenever groups changes
      this.groups = [...this.groups]; // Trigger Vue reactivity
      },
      sortBy(key) {
          if (this.sortKey === key) {
          this.sortDirection = this.sortDirection * -1;
          } else {
          this.sortKey = key;
          this.sortDirection = 1;
          }
      },
      getGroupItem() {
        const apiUrl = OC.generateUrl('/apps/nextopn/api/firewall/group/getGroupItem');
        axios.get(apiUrl)
          .then(response => {
            if (response.data && response.data.members) {
              this.groupItem = response.data.members;
              //this.aliasItem2 = response.data.proto; Possible uses
              console.log(this.aliasItem);
            } else if (response.data.errorMessage) {
              console.error(response.data.errorMessage);
              // Handle error message
            }
          })
          .catch(error => {
            console.error('Error fetching group-members:', error);
            // Handle HTTP error
          });
      },
      handleClick() {
              this.message = 'Resctricted';
              this.updateKey++;
      },
    },
  }
</script>

<style scoped>

form {
    font-size: 15px;
    position: relative;
    text-align: center;
}
th {
  width: 10%;
  cursor: pointer;
}
h2 {
  position: relative;
  text-align: center;
}
.non-selectable {
    user-select: none;
}
#deleteButton {
  margin-left: 5px;
  width: 20px; 
  height: 20px;
}
#imagedisabled2 {
  margin-left: 5px;
  width: 20px; 
  height: 20px;
}
#table-container {
  margin: 2%;
  height: 500px;
  overflow: auto;
}
#item-panel {
  border: 2px solid;
  border-color: black;
  padding: 0.5em;
  border-radius: 2em;
}
th {
  width: 10%;
  cursor: pointer;
  backdrop-filter: blur(10px);
  position: sticky;
}

</style>