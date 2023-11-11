<template>
    <div>
      <h2>Create Group</h2>
      <form @submit.prevent="addGroup">
        <input v-model="addGroupData.ifname" type="text" placeholder="Name">
        <input v-model="addGroupData.members" type="text" placeholder="Members">
        <input v-model="addGroupData.nogroup" type="text" placeholder="Enabled (e.g., 1 or 0)">
        <input v-model="addGroupData.sequence" type="text" placeholder="Sequence">
        <input v-model="addGroupData.descr" type="text" placeholder="Description">
        <button type="submit">Create Group</button>
      </form>
      
      <!-- Displaying messages -->
      <div v-if="message" class="message">{{ message }}</div>
    </div>
  </template>
  <script>
  import axios from 'axios';

  export default {
    data() {
        return {
            addGroupData: {
                ifname: "",
                members: "",
                nogroup: "",
                sequence: "",
                descr: "",
            },
            message: "",
        };
    },
    methods: {
        addGroup() {
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/group/addGroup';
            const payload = {
                group: {
                    ifname: this.addGroupData.ifname,
                    members: this.addGroupData.members,
                    nogroup: this.addGroupData.nogroup === "1" ? 1 : 0,
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
  color: red;
}
</style>