<template>
    <div>
      <button @click="deleteGroup">Delete Group</button>
      <p v-if="message">{{ message }}</p>
    </div>
  </template>
  
<script>
import axios from 'axios';

export default {
    data() {
        return {
            message: ''
        }
    },
    methods: {
        deleteGroup() {
            const groupUID = '2d2fd3ae-0e1a-4444-b040-9ef2a72ffb31';
            const apiUrl = 'http://nextcloud.local/index.php/apps/nextopn/api/firewall/group/delGroup/';

            axios.post(apiUrl + groupUID)
            .then(response => {
                this.message = "Group deleted successfully";
            })
            .catch(error => {
                this.message = "Error deleting group";
                console.error(error);
            });
        }
    }
}
</script>
