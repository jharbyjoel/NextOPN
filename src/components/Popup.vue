<template>
    <div v-if="isOpen" class="popup">
      <div class="popup-inner">
        {{ message }} <br>
        <button class="popup-close" @click="closePopup">
          Close
        </button>
      </div>
    </div>
  </template>
  
<script>
export default {
    props: {
      message: String,
    },
    data() {
      return {
        isOpen: false,
      };
    },
    methods: {
        openPopup() {
            this.isOpen = true;
            this.$emit('updateMessage', 'Your updated message here');
            },
        closePopup() {
            this.isOpen = false;
      },
    },
};
</script>
  
<style lang="scss" scoped>
.popup {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99;
    background-color: rgba(0, 0, 0, 0.2);
    
    display: flex;
    align-items: center;
    justify-content: center;
  
.popup-inner {
      background: #FFF;
      padding: 32px;
    }
  
.popup-close {
      margin-top: 16px;
      cursor: pointer;
    }
  }
</style>