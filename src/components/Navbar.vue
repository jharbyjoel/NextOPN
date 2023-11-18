<template>
    <nav class="navbar">
      <div class="navbar-container">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <img src="./img/opn-png.png" alt="Logo" class="navbar-logo">
        <ul class="navbar-list">
          <li class="navbar-item"><router-link to="/firmware" class="navbar-link"> <i class="fa fa-cog"></i>Firmware</router-link></li>
          <li class="navbar-item"><router-link to="/groups" class="navbar-link"><i class="fa fa-users"></i>Groups</router-link></li>
          <li class="navbar-item"><router-link to="/aliases" class="navbar-link"><i class="fa fa-user-secret"></i>Aliases</router-link></li>
          <li class="navbar-item"><router-link to="/firewall-settings" class="navbar-link"><i class="fa fa-fire"></i>Firewall Settings</router-link></li>
          <li class="navbar-item"><router-link to="/users" class="navbar-link"><i class="fa fa-user" style="color: #ffffff;"></i>Users</router-link></li>
          <li class="navbar-item"><router-link to="/changeLogs" class="navbar-link"><i class="fa fa-clipboard"></i>ChangeLogs</router-link></li>
          <li class="navbar-item"><router-link to="/categories" class="navbar-link"><i class="fa fa-bars"></i>Categories</router-link></li>
        </ul>
        <li class="navbar-item"><button class="navbar-link logout-button" @click="logout">Logout</button></li>
      </div>
    </nav>
  </template>
  
  <script>
  export default {
    name: 'Navbar',
    components: {
      
},
    methods: {
      logout() {
        // Implement logout logic
      }
    }
  }
  </script>
  
  <style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.cpu-usage-navbar {

}

.navbar {
width: 100%;
  color: white;
  padding: 0.5rem 0;
}

.navbar-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.navbar-logo {
  max-width: 150px; /* Adjust as needed */
  height: auto; /* Maintains aspect ratio */
  margin-left: 1rem;
}

.navbar-list {
  list-style: none;
  display: flex;
  align-items: center;
  padding: 0;
  margin: 0 2rem; /* Add margin to center the navbar list */
}

.navbar-item {
  margin: 0 10px;
}

.navbar-link {
  font-size: 20px;
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  transition: color 0.3s ease;
}

.navbar-link .fa {
    margin-right: 10px;
}

.navbar-link:hover {
  color: #4caf50;
}

.logout-button {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0.5rem 1rem;
  margin-right: 1rem; /* Add margin if needed */
}

  </style>
  