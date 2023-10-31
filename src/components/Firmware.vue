<template>
    <div>
        <h2>Firmware Information</h2>
        <div v-if="loading">Loading...</div>
        <div v-if="error"> {{error}}</div>
        <div v-if="firmwareInfo">
        <p><strong>Upgrade Available:</strong> {{ firmwareInfo.upgradeAvailable }}</p>
        <p><strong>Download Size:</strong> {{ firmwareInfo.downloadSize }}</p>
        <p><strong>Number of Packages:</strong> {{ firmwareInfo.numberOfPackages }}</p>
        <p><strong>Reboot Required:</strong> {{ firmwareInfo.rebootRequired }}</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

axios.defaults.baseURL = 'http://nextcloud.local/index.php/apps/nextopn';


export default {
    data() {
        return {
            loading: true,
            error: null,
            firmwareInfo: null,
        };
    },
    created() {
        axios.get('/api/firmware/status')
        .then(response => {
            if (response.data.errorMessage) {
                this.error = response.data.errorMessage;
            } else {
                this.firmwareInfo = firmwareInfo.response.data;
            }
            this.loading = false;
        });
    }
}
</script>