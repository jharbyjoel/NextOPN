<template>
    <div>
      <h2>Add Alias</h2>
      <form @submit.prevent="addAlias">
        <input v-model="aliasData.enabled" type="text" placeholder="Enabled (e.g., 1 or 0)" required>
        <input v-model="aliasData.name" type="text" placeholder="Alias Name" required>
        <input v-model="aliasData.type" type="text" placeholder="Type (e.g., host)" required>
        <input v-model="aliasData.proto" type="text" placeholder="Protocol (optional)">
        <input v-model="aliasData.categories" type="text" placeholder="Categories (optional)">
        <input v-model="aliasData.updatefreq" type="text" placeholder="Update Frequency (optional)">
        <input v-model="aliasData.content" type="text" placeholder="Content" required>
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
          enabled: '',
          name: '',
          type: '',
          proto: '',
          categories: '',
          updatefreq: '',
          content: '',
          authgroup_content: '',
          network_content: '',
        },
        message: ''
      };
    },
    methods: {
      addAlias() {
        // Replace with your actual API endpoint
        axios.post('http://nextcloud.local/index.php/apps/nextopn/api/firewall/alias/addItem', {
          alias: {
            enabled: this.aliasData.enabled,
            name: this.aliasData.name,
            type: this.aliasData.type,
            proto: this.aliasData.proto,
            categories: this.aliasData.categories,
            updatefreq: this.aliasData.updatefreq,
            content: this.aliasData.content,
          },
          authgroup_content: this.aliasData.authgroup_content,
          network_content: this.aliasData.network_content,
        })
        .then(response => {
          this.message = 'Alias added successfully';
          // Reset the form or handle the response as required
        })
        .catch(error => {
          this.message = 'Error: ' + (error.response.data.message || error.message);
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
  