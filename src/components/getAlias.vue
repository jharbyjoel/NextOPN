<template>
    <div>
      <h2>Firewall Aliases</h2>
      <ul v-if="aliases.length > 0">
        <li v-for="alias in aliases" :key="alias.uuid">
          <strong>Name:</strong> {{ alias.name }}
          <p><strong>Description:</strong> {{ alias.description }}</p>
          <p><strong>Type:</strong> {{ alias.type }}</p>
          <p><strong>Enabled:</strong> {{ alias.enabled }}</p>
          <p><strong>Last Updated:</strong> {{ alias.last_updated }}</p>
        </li>
      </ul>
      <p v-else>No aliases found.</p>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        aliases: [],
      };
    },
    created() {
      this.fetchAliases();
    },
    methods: {
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
          });
      }
    }
  };
  </script>
  
  <style>
    div {
        overflow-y: auto;
    }
  </style>