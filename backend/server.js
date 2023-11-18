const express = require('express');
const osUtils = require('os-utils');
const cors = require('cors');
const os = require('os')

const app = express();
const port = 3000; // You can choose any port that suits your setup

app.use(cors());

app.get('/api/cpu-usage', (req, res) => {
    osUtils.cpuUsage((percentage) => {
        res.json({ usage: percentage * 100 });
    });
});

app.get('/api/memory-usage', (req, res) => {
    const freeMemoryInGB = osUtils.freemem().toFixed(2);

    res.json ({freeMemoryInGB});
})

app.get('/api/cpu-type', (req, res) => {
    const cpuModel = os.cpus()[0].model;

    res.json ( { cpuType: cpuModel } );
});



app.listen(port, () => {
    console.log(`Server running on http://localhost:${port}`);
});
