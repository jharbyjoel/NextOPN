<template>
    <div>
      <h2>Add Alias</h2>
      <form @submit.prevent="addAlias">
        <input v-model="aliasData.enabled" type="text" placeholder="Enabled (e.g., 1 or 0)">
        <input v-model="aliasData.name" type="text" placeholder="Alias Name">
        <input v-model="aliasData.type" type="text" placeholder="Type (e.g., host)">
        <input v-model="aliasData.proto" type="text" placeholder="Protocol (optional)">
        <input v-model="aliasData.categories" type="text" placeholder="Categories (optional)">
        <input v-model="aliasData.updatefreq" type="text" placeholder="Update Frequency (optional)">
        <input v-model="aliasData.content" type="text" placeholder="Content">
        <input v-model="aliasData.authgroup_content" type="text" placeholder="Auth Group Content (optional)">
        <input v-model="aliasData.network_content" type="text" placeholder="Network Content (optional)">
        <button type="submit">Add Alias</button>
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
        aliasData: {
          enabled: "",
          name: "",
          type: "",
          proto: "",
          categories: "",
          updatefreq: "",
          content: "",
          authgroup_content: "",
          network_content: "",
        },
        message: "",
      };
    },
    methods: {
      addAlias() {
        const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/alias/addItem';
        const payload = {
        alias: {
          enabled: this.aliasData.enabled.trim() === "1" ? 1 : 0,
          name: this.aliasData.name.trim(),
          type: this.aliasData.type.trim(),
          proto: this.aliasData.proto.trim(),
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
  