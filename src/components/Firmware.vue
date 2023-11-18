<template>
    <div class="firmware-container">
        <h1 class="firmware-header"> Firmware </h1>
        <button class="check-updates-button" @click="checkUpdates">Update</button>

        <div class="space-user">

        </div>

        <div v-if="productID && productVersion" class="product-info">
            <p><strong>Type:</strong> {{ productID }}</p>
            <p><strong>Version:</strong> {{ productVersion }}</p>
        </div>

        <div v-if="updateInfo && updateInfo.statusMessage" class="update-info">
            <p><strong>Status Message:</strong> {{ updateInfo.statusMessage }}</p>
        </div>
        <p v-if="errorMessage" class="error-message"> {{ errorMessage }}</p>
    </div>
</template>

<script>
import axios from 'axios'; // Import Axios

export default {
    data() {
        return {
            updateInfo: null,
            errorMessage: '',
            productID: '',
            productVersion: '',
        };
    },
    methods: {
        async checkUpdates() {
            try {
                const response = await axios.get('http://nextcloud.local/index.php/apps/nextopn/api/firmware/status');
                console.log(response); 
                if (response.data && response.data.statusMessage) {
                    this.updateInfo = response.data;
                } else {
                    this.updateInfo = null;
                    this.errorMessage = 'No updates available.';
                }
            } catch (error) {
                this.updateInfo = null;
                this.errorMessage = 'Error fetching update information.';
                console.error(error);
            }
        },

        async getInfoMethod() {
            try {
                const response = await axios.get('http://nextcloud.local/index.php/apps/nextopn/api/firmware/info');
                console.log(response);

                if (response.data.productID && response.data.productVersion) {
                    this.productID = response.data.productID;
                    this.productVersion = response.data.productVersion;
                } else {
                    this.errorMessage = "No system information available."
                }
            } catch {
                this.errorMessage = "Error fetching system information."
            }
        }

    },

    created() {
        this.getInfoMethod();
        this.checkUpdates();
    }

};
</script>

<style>
 .firmware-container {
    position: relative;
    overflow: auto;
    margin: auto;
    height: 1500px;
    width: 1100px;
 }

 .firmware-header {
    font-size: 50px;
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease;
    text-align: center;
 }

 .space-user {
    height: 50px;
 }

 .check-updates-button {
    position: absolute;
    bottom: 75%;  /* adjust these values */
    left: 50%; /* adjust these values */
    transform: translate(-50%, -50%); /* centers the button */
    
}


.update-info p {
    font-size: 25px;
    color: mintcream;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease;
    text-align: center;
}

.product-info p {
    font-size: 25px;
    color: mintcream;
    padding: 0.5rem 1rem;
    transition: color 0.3s ease;
    text-align: center;
}


.error-message {
    color: red;
}
</style>