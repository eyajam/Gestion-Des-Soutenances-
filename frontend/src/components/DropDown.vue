<template>
    <div>
      <div class="dropdown">
        <input type="text" class="input" v-model="selected" readonly @click="toggleDropdown" required>
        <span class="arrow" @click="toggleDropdown">&#9660;</span>
        <ul v-if="isOpen" class="dropdown-menu">
          <li v-for="option in options" :key="option" @click="selectOption(option)">
            {{ option }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    modelValue: String,
    options: {
    type: Array,
    required: true
    }
  });
  
  const emit = defineEmits(['update:modelValue','change']);
  
  const isOpen = ref(false);
  const selected = ref(props.modelValue || '');
  
  const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
  };
  
  const selectOption = (option) => {
    selected.value = option;
    isOpen.value = false;
    emit('update:modelValue', selected.value);
    emit('change', selected.value);
  };
  
  watch(() => props.modelValue, (newValue) => {
    selected.value = newValue;
  });
  </script>
  
  <style scoped>
  .dropdown {
    position: relative;
    display: inline-block;
  }
  .arrow {
    cursor: pointer;
    position: absolute;
    top: 10px;
    right: 10px;
  }
  .dropdown-menu {
    position: absolute;
    background-color: #fff;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    z-index: 1;
    width: 90%;
    padding: 0;
    margin: 0;
    max-height: 135px; 
    overflow-y: auto; /*le défilement vertical */
    list-style: none;
    
  }
  .dropdown-menu li {
    padding: 10px;
    cursor: pointer;
  }
  .dropdown-menu li:hover {
    background-color: #ddd;
  }
  </style>
  