import axios from 'axios';
import { response } from 'express';

const BASE_URL = 'http://localhost:3000/api';

export const getCpuUsage = async () => {
  try {
    const response = await axios.get(`${BASE_URL}/cpu-usage`);
    return response.data.usage;
  } catch (error) {
    console.error('Error fetching CPU usage:', error);
    return null;
  }
};

export const getFreeMem = async () => {
    try {
      const response = await axios.get(`${BASE_URL}/memory-usage`);
      return response.data.freeMemoryInGB; // Access the correct key based on your backend response
    } catch (error) {
      console.error('Error fetching free Memory:', error);
      return null;
    }
};

export const getCpuType = async () => {
    try {
        const response = await axios.get(`${BASE_URL}/cpu-type`);
        return response.data.cpuType;
    } catch (error) {
        console.error('Error fetching cpu type. ', error);
        return null;
    }
};



