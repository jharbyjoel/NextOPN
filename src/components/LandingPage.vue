<template>
        <div class="sidebar">
            <h1>Nextopn</h1>
            <ul>
                <li><a href="#">Aliases</a> ⮞</li>
                <li><a href="#">Categories</a> ⯈</li>
                <li><a href="#">Groups</a> ⮤</li>
                <li><a href="#">NAT</a> ⮥</li>
                <li><a href="#">Rules</a> ⮧</li>
                <li><a href="#">Shaper</a> ⚙️</li>
                <li><a href="#">Settings</a> 🛠️</li>
                <li><a href="#">Log Files</a> 📜</li>
                <li><a href="#">Diagnostics</a> 🔍</li>
                <li><a href="#">Automation</a> ⚙️</li>
                <li><a href="#">Firmware</a> ⚙️</li>
            </ul>
        </div>
</template>

<script>
import axios from 'axios';

axios.defaults.baseURL = 'http://nextcloud.local/index.php/apps/nextopn';


export default {
    data() {
      
    }
}
</script>

<style scoped>

.sidebar {
    width: 250px;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    height: 100vh;
    position: fixed;
}

.sidebar h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

.sidebar ul {
    list-style: none;
    padding-left: 0;
}

.sidebar ul li {
    margin-bottom: 15px;
}

.sidebar ul li a {
    text-decoration: none;
    color: #555;
    transition: color 0.3s, transform 0.3s, box-shadow 0.3s;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;            
    border-radius: 5px;          
    box-shadow: none; 
    font-size: 20px;           
}

/* Hover effect */
.sidebar ul li a:hover {
    color: #333;                
    transform: translateY(-2px); 
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); 
}

/* Icon & Text Spacing */
.sidebar ul li a > * + * {
    margin-left: 10px;          
}


</style>